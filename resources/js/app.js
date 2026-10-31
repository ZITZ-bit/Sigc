import '../modules/dashboard-navigation.js';
import '../modules/portal-navigation.js';

/**
 * Módulo de sincronización automática de consultas entre Paciente, Médico y
 * Coordinación sin recargar la página.
 *
 * Usa polling autenticado e incremental sobre endpoints existentes:
 *   GET /consultas/sincronizar?since=<ts>  -> consultas actualizadas del usuario
 *   GET /:rol/notificaciones/nuevas        -> notificaciones no leídas (toasts)
 *   POST /:rol/notificaciones/{id}/leer    -> marcar notificación como leída
 *
 * No modifica estilos, animaciones ni la apariencia de las pantallas. Solo
 * actualiza texto, estados (clases de insignia ya existentes) e inserta filas
 * nuevas usando las mismas clases CSS que las vistas Blade.
 *
 * Características de robustez:
 *   - Intervalo reducido a 15 segundos.
 *   - Pausa automática cuando la pestaña está oculta (visibilitychange).
 *   - Bloqueo de solicitudes simultáneas (una petición a la vez).
 *   - Reintento con retroceso exponencial ante errores de red.
 *   - Marcado de notificación como leída al hacer clic en el toast.
 */
const SYNC_INTERVAL_MS = 15000;
const MAX_RETRIES = 3;

let lastSyncAt = null;
let pollingTimer = null;
let isPolling = false;
let visibilityHidden = false;
let retryCount = 0;

function estadoLabel(estado) {
  return String(estado || '').charAt(0).toUpperCase() + String(estado || '').slice(1);
}

function cleanClass(token) {
  return String(token).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function badgePrefix(row) {
  const badge = row.querySelector('[class*="badge--"]');
  if (!badge) return 'coordinacion';
  const match = String(badge.className).match(/\b([a-zA-Z]+)-badge--/);
  return match ? match[1] : 'coordinacion';
}

function updateBadge(row, nuevoEstado) {
  const badge = row.querySelector('[class*="badge--"]');
  if (!badge) return;
  const viejoEstado = row.dataset.consultaEstado;
  if (viejoEstado === nuevoEstado) return;
  const prefix = badgePrefix(row);
  badge.className = String(badge.className).replace(
    new RegExp(`badge--${cleanClass(viejoEstado)}(\\b|\\s)`, 'g'),
    `badge--${nuevoEstado}$1`
  );
  badge.textContent = estadoLabel(nuevoEstado);
  row.dataset.consultaEstado = nuevoEstado;
}

function updateMedicoTexto(row, nombreMedico) {
  if (!nombreMedico) return;

  // Coordinación: el <strong> que sigue a la etiqueta "Médico".
  const etiquetaMedico = Array.from(row.querySelectorAll('span, label')).find((el) =>
    /médico/i.test(el.textContent || ''),
  );
  if (etiquetaMedico) {
    const padre = etiquetaMedico.parentElement;
    if (padre) {
      const strong = padre.querySelector('strong');
      if (strong) {
        strong.textContent = nombreMedico;
        return;
      }
    }
  }

  // Paciente agenda: campo dedicado del médico.
  const doctor = row.querySelector('.paciente-agenda-doctor');
  if (doctor) {
    doctor.textContent = nombreMedico;
  }
}

function sincronizarConsulta(contenedor, consulta) {
  const id = String(consulta.id);
  let row = contenedor.querySelector(`[data-consulta-id="${id}"]`);

  if (!row) {
    // Consulta nueva: insertar fila usando las mismas clases CSS de la vista.
    row = document.createElement('div');
    row.className = contenedor.classList.contains('coordinacion-citas-list')
      ? 'coordinacion-cita-card'
      : contenedor.classList.contains('paciente-agenda-list')
        ? 'paciente-agenda-item'
        : '';
    row.dataset.consultaId = id;
    row.dataset.consultaEstado = consulta.estado;
    row.innerHTML = plantillaFila(contenedor, consulta);
    contenedor.prepend(row);
    return;
  }

  // Fila existente: actualizar solo lo que cambió.
  updateBadge(row, consulta.estado);
  updateMedicoTexto(row, consulta.medico || null);
  row.dataset.consultaEstado = consulta.estado;
}

function plantillaFila(contenedor, c) {
  const fecha = c.fecha ? new Date(`${c.fecha}T00:00:00`).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '';
  const hora = c.hora ? new Date(`1970-01-01T${c.hora}:00`).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }) : '';
  const etiquetaEstado = estadoLabel(c.estado);
  const medico = c.medico || 'Pendiente de asignación';
  const especialidad = c.especialidad || 'Sin especialidad';
  const paciente = c.paciente || '—';

  if (contenedor.classList.contains('coordinacion-citas-list')) {
    return `
      <div class="coordinacion-cita-card__header">
        <div class="coordinacion-cita-card__when">
          <span class="coordinacion-cita-card__date">${fecha}</span>
          <span class="coordinacion-cita-card__time">${hora}</span>
        </div>
        <span class="coordinacion-badge coordinacion-badge--${c.estado}">${etiquetaEstado}</span>
      </div>
      <div class="coordinacion-cita-card__details">
        <div>
          <span class="coordinacion-cita-card__label">Paciente</span>
          <strong>${paciente}</strong>
          <span class="coordinacion-cita-card__muted"></span>
        </div>
        <div>
          <span class="coordinacion-cita-card__label">Médico</span>
          <strong>${medico}</strong>
          <span class="coordinacion-cita-card__muted">${especialidad}</span>
        </div>
      </div>`;
  }

  if (contenedor.classList.contains('coordinacion-recepcion')) {
    return `
      <td>${fecha}</td>
      <td>${hora}</td>
      <td>${paciente}</td>
      <td>${especialidad}</td>
      <td>${medico}</td>
      <td><span class="coordinacion-badge coordinacion-badge--${c.estado}">${etiquetaEstado}</span></td>`;
  }

  if (contenedor.classList.contains('medico-consultas') || contenedor.classList.contains('medico-agenda')) {
    return `
      <td>${hora}</td>
      <td>${paciente}</td>
      <td>${especialidad}</td>
      <td>${c.motivo || 'Sin motivo registrado'}</td>
      <td><span class="medico-badge medico-badge--${c.estado}">${etiquetaEstado}</span></td>
      <td><div class="medico-agenda-actions"></div></td>`;
  }

  if (contenedor.classList.contains('paciente-agenda-list')) {
    const mes = c.fecha ? new Date(`${c.fecha}T00:00:00`).toLocaleDateString('es-ES', { month: 'short' }) : '';
    const dia = c.fecha ? new Date(`${c.fecha}T00:00:00`).getDate() : '';
    return `
      <div class="paciente-agenda-date-time" aria-label="Fecha y hora de la consulta">
        <span class="paciente-agenda-month">${mes}</span>
        <span class="paciente-agenda-date">${dia}</span>
        <span class="paciente-agenda-time">${hora}</span>
      </div>
      <div class="paciente-agenda-info">
        <span class="paciente-agenda-doctor">${medico}</span>
        <span class="paciente-agenda-specialty">${especialidad}</span>
      </div>
      <div class="paciente-agenda-side">
        <span class="paciente-badge paciente-badge--${c.estado}">${etiquetaEstado}</span>
        <div class="paciente-agenda-actions"></div>
      </div>`;
  }

  return '';
}

/**
 * Ejecuta una sola sincronización. Bloquea mientras hay una petición en vuelo
 * para evitar solicitudes simultáneas o duplicadas.
 */
async function sincronizarUnaVez() {
  if (isPolling || visibilityHidden) return;

  const contenedores = document.querySelectorAll('[data-sync-list]');
  if (contenedores.length === 0) return;

  isPolling = true;

  try {
    const url = new URL('/consultas/sincronizar', window.location.origin);
    if (lastSyncAt) url.searchParams.set('since', lastSyncAt);

    const controlador = new AbortController();
    const temporizador = setTimeout(() => controlador.abort(), 10000);

    const respuesta = await fetch(url, {
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'Cache-Control': 'no-store' },
      signal: controlador.signal,
    });
    clearTimeout(temporizador);

    if (!respuesta.ok) {
      throw new Error(`HTTP ${respuesta.status}`);
    }

    const payload = await respuesta.json();
    lastSyncAt = payload.since || lastSyncAt;
    retryCount = 0;

    const consultas = Array.isArray(payload.consultas) ? payload.consultas : [];
    for (const consulta of consultas) {
      for (const contenedor of contenedores) {
        sincronizarConsulta(contenedor, consulta);
      }
    }
  } catch (error) {
    // Silencioso: el polling se reintentará con retroceso exponencial.
    retryCount += 1;
    if (retryCount <= MAX_RETRIES) {
      const retraso = Math.min(1000 * Math.pow(2, retryCount - 1), 30000);
      setTimeout(() => {
        isPolling = false;
        sincronizarUnaVez();
      }, retraso);
      return;
    }
  } finally {
    isPolling = false;
  }
}

/**
 * Consulta notificaciones no leídas y las muestra como toasts.
 */
async function consultarNotificacionesNuevas() {
  if (visibilityHidden) return;

  const contenedor = document.querySelector('[data-sync-list]');
  if (!contenedor) return;

  const nombre = contenedor.dataset.syncList || '';
  let rol = 'paciente';
  if (nombre.startsWith('coordinacion')) rol = 'coordinador';
  else if (nombre.startsWith('medico')) rol = 'medico';

  try {
    const url = `/${rol}/notificaciones/nuevas`;
    const controlador = new AbortController();
    const temporizador = setTimeout(() => controlador.abort(), 10000);

    const respuesta = await fetch(url, {
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'Cache-Control': 'no-store' },
      signal: controlador.signal,
    });
    clearTimeout(temporizador);

    if (!respuesta.ok) return;

    const payload = await respuesta.json();
    const notificaciones = Array.isArray(payload.notifications) ? payload.notifications : [];
    for (const notif of notificaciones) {
      mostrarToast(notif.id, notif.title || 'Nueva notificación', notif.data?.message || '', rol);
    }
  } catch (error) {
    // Silencioso.
  }
}

/**
 * Marca una notificación como leída en el servidor.
 */
async function marcarLeida(notificacionId, rol) {
  try {
    const controlador = new AbortController();
    const temporizador = setTimeout(() => controlador.abort(), 10000);

    await fetch(`/${rol}/notificaciones/${notificacionId}/leer`, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'X-XSRF-TOKEN': getCsrfToken(),
        'Cache-Control': 'no-store',
      },
      signal: controlador.signal,
    });
    clearTimeout(temporizador);
  } catch (error) {
    // Silencioso: la notificación seguirá mostrándose hasta que se marque manualmente.
  }
}

let toastContenedor = null;
function asegurarToastContenedor() {
  if (toastContenedor) return toastContenedor;
  toastContenedor = document.createElement('div');
  Object.assign(toastContenedor.style, {
    position: 'fixed',
    top: '1rem',
    right: '1rem',
    zIndex: '9999',
    display: 'flex',
    flexDirection: 'column',
    gap: '0.5rem',
    pointerEvents: 'auto',
  });
  document.body.appendChild(toastContenedor);
  return toastContenedor;
}

function mostrarToast(id, titulo, cuerpo, rol) {
  const contenedor = asegurarToastContenedor();
  const toast = document.createElement('div');
  Object.assign(toast.style, {
    background: '#ffffff',
    border: '1px solid #e5e7eb',
    borderLeft: '4px solid #2563eb',
    borderRadius: '0.5rem',
    padding: '0.75rem 1rem',
    minWidth: '260px',
    boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
    fontSize: '0.875rem',
    lineHeight: '1.4',
    color: '#111827',
    cursor: 'pointer',
  });
  toast.dataset.notificacionId = id;
  toast.dataset.rol = rol;
  toast.innerHTML = `<div style="font-weight:600; font-size:0.9rem; margin-bottom:0.25rem;">${escapeHtml(titulo)}</div>${cuerpo ? `<div style="color:#4b5563; font-size:0.8rem;">${escapeHtml(cuerpo)}</div>` : ''}`;
  contenedor.appendChild(toast);

  // Al hacer clic, marca como leída en el servidor y elimina el toast.
  toast.addEventListener('click', () => {
    marcarLeida(id, rol);
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  });

  setTimeout(() => {
    if (toast.isConnected) {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }
  }, 5000);
}

function escapeHtml(texto) {
  const div = document.createElement('div');
  div.textContent = texto;
  return div.innerHTML;
}

/**
 * Lee el token CSRF de la cookie XSRF-TOKEN que Laravel establece automáticamente.
 */
function getCsrfToken() {
  const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
  return match ? decodeURIComponent(match[1]) : '';
}

function iniciarPolling() {
  if (pollingTimer) return;
  sincronizarUnaVez();
  consultarNotificacionesNuevas();
  pollingTimer = setInterval(() => {
    sincronizarUnaVez();
    consultarNotificacionesNuevas();
  }, SYNC_INTERVAL_MS);
}

function pausarPolling() {
  visibilityHidden = true;
}

function reanudarPolling() {
  visibilityHidden = false;
  sincronizarUnaVez();
  consultarNotificacionesNuevas();
}

document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('[data-sync-list]')) {
    iniciarPolling();

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        pausarPolling();
      } else {
        reanudarPolling();
      }
    });
  }
});
