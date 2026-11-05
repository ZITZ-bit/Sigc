/**
 * Módulo de filtros en vivo para SIGC.
 *
 * Permite que los formularios de búsqueda y filtrado se envíen vía AJAX
 * sin recargar la página, actualizando únicamente el contenedor de
 * resultados. Soporta búsqueda por nombre (coincidencias parciales,
 * una letra, sin distinguir mayúsculas/minúsculas), selects de estado,
 * fechas, enlaces de filtro y combinaciones de filtros.
 *
 * Uso en Blade:
 *
 *   <form method="GET" action="{{ route('coordinacion.citas') }}"
 *         data-filtros-form="true" data-filtros-target="#coordinacion-citas">
 *     <select name="estado" data-filtros-input>...</select>
 *     <input type="search" name="search" data-filtros-input>
 *     <button type="button" data-filtros-limpiar>Limpiar</button>
 *   </form>
 *
 *   <div id="coordinacion-citas" data-filtros-target>
 *     @include('ui.coordinacion.partials.citas-lista', ...)
 *   </div>
 *
 * Reglas:
 *   - Cualquier cambio en <select> o <input> del formulario dispara el
 *     fetch automático (inputs de texto con debounce de 250 ms).
 *   - El botón "Limpiar" restablece los valores y dispara el fetch.
 *   - Los enlaces con data-filtros-link (ej. recepción Todas/Hoy/Semana)
 *     filtran mediante AJAX.
 *   - La paginación se conserva al navegar (AJAX) y se reinicia a la
 *     página 1 al modificar cualquier filtro.
 *   - Las respuestas se procesan en orden: se cancela la petición previa
 *     del mismo target y se ignoran respuestas obsoletas.
 *   - El servidor debe devolver solo el HTML parcial del contenedor cuando
 *     la petición trae el encabezado X-Requested-With.
 */
(function () {
  'use strict';

  const FORM_ATTR = 'data-filtros-form';
  const TARGET_ATTR = 'data-filtros-target';
  const LINK_ATTR = 'data-filtros-link';
  const CLEAR_ATTR = 'data-filtros-limpiar';
  const BOUND_MARKER = 'data-filtros-bound';

  const DEBOUNCE_MS = 250;
  const TIMEOUT_MS = 10000;

  let requestCounter = 0;
  const inFlight = new Map(); // targetEl -> AbortController

  /**
   * Lee el token CSRF de la cookie XSRF-TOKEN que Laravel establece.
   */
  function getCsrfToken() {
    const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
  }

  /**
   * Construye la URL de solicitud a partir de un formulario de filtros.
   * Descarta page y valores vacíos, y fuerza page=1 al cambiar un filtro.
   */
  function buildFormUrl(formEl) {
    const url = new URL(formEl.action, window.location.origin);
    const formData = new FormData(formEl);

    for (const [key, value] of formData.entries()) {
      if (key !== 'page' && value !== '') {
        url.searchParams.set(key, value);
      }
    }

    url.searchParams.set('page', '1');
    return url.toString();
  }

  /**
   * Construye la URL de un enlace de filtro fusionando sus parámetros con
   * los filtros vigentes de la URL actual (sin la página).
   */
  function buildLinkUrl(linkEl) {
    const url = new URL(window.location.href);
    url.searchParams.delete('page');

    const linkUrl = new URL(linkEl.href, window.location.origin);
    for (const [key, value] of linkUrl.searchParams) {
      if (value !== '') {
        url.searchParams.set(key, value);
      } else {
        url.searchParams.delete(key);
      }
    }

    return url.toString();
  }

  /**
   * Ejecuta una solicitud de filtrado y reemplaza el contenido del target.
   */
  async function loadUrl(targetEl, url) {
    const requestId = ++requestCounter;

    // Cancelar petición previa aún en vuelo para este target
    const previo = inFlight.get(targetEl);
    if (previo) {
      previo.abort();
    }

    const controlador = new AbortController();
    inFlight.set(targetEl, controlador);

    const temporizador = setTimeout(() => controlador.abort(), TIMEOUT_MS);

    try {
      const respuesta = await fetch(url, {
        credentials: 'same-origin',
        headers: {
          Accept: 'text/html',
          'X-Requested-With': 'XMLHttpRequest',
          'X-XSRF-TOKEN': getCsrfToken(),
          'Cache-Control': 'no-store',
        },
        signal: controlador.signal,
      });
      clearTimeout(temporizador);

      // Ignorar respuestas de solicitudes obsoletas
      if (requestId !== requestCounter) return;

      if (!respuesta.ok) {
        throw new Error(`HTTP ${respuesta.status}`);
      }

      const html = await respuesta.text();

      // Ignorar respuestas de solicitudes obsoletas tras la espera
      if (requestId !== requestCounter) return;

      // Reemplazar solo el contenido del contenedor de resultados
      targetEl.innerHTML = html;

      // Volver a inicializar comportamientos JS dentro del target
      attachTargetBehaviors(targetEl);
    } catch (error) {
      clearTimeout(temporizador);

      if (requestId !== requestCounter) return;
      if (error.name === 'AbortError') return;

      console.error('Error al filtrar:', error);
    } finally {
      if (inFlight.get(targetEl) === controlador) {
        inFlight.delete(targetEl);
      }
    }
  }

  /**
   * Carga los resultados filtrados de un formulario.
   */
  function loadForm(formEl, targetEl) {
    loadUrl(targetEl, buildFormUrl(formEl));
  }

  /**
   * Encuentra el formulario de filtros asociado a un contenedor target.
   */
  function encontrarFormularioParaTarget(targetEl) {
    const targetId = targetEl.id || targetEl.getAttribute(TARGET_ATTR);
    if (!targetId) return null;

    const formularios = document.querySelectorAll(`[${FORM_ATTR}]`);
    for (const form of formularios) {
      const formTarget = form.getAttribute(TARGET_ATTR);
      if (formTarget === targetId || formTarget === '#' + targetId) {
        return form;
      }
    }
    return null;
  }

  /**
   * Vincula inputs, selects y botones limpiar de un formulario de filtros.
   */
  function vincularFormulario(formEl, targetEl) {
    if (formEl.dataset[BOUND_MARKER] === 'true') return;
    formEl.dataset[BOUND_MARKER] = 'true';

    const inputs = formEl.querySelectorAll(
      'select, input[type="search"], input[type="text"], input[type="date"]'
    );
    for (const input of inputs) {
      if (input.dataset[BOUND_MARKER] === 'true') continue;
      input.dataset[BOUND_MARKER] = 'true';

      const disparar = () => {
        if (input.tagName === 'INPUT') {
          clearTimeout(input.dataset.filtrosTimer);
          input.dataset.filtrosTimer = setTimeout(() => {
            loadForm(formEl, targetEl);
          }, DEBOUNCE_MS);
        } else {
          loadForm(formEl, targetEl);
        }
      };

      input.addEventListener('input', disparar);
      input.addEventListener('change', disparar);
    }

    const botonesLimpiar = formEl.querySelectorAll(`[${CLEAR_ATTR}]`);
    for (const btn of botonesLimpiar) {
      if (btn.dataset[BOUND_MARKER] === 'true') continue;
      btn.dataset[BOUND_MARKER] = 'true';
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const todosLosInputs = formEl.querySelectorAll('input, select');
        for (const input of todosLosInputs) {
          if (input.type === 'select-one') {
            input.selectedIndex = 0;
          } else {
            input.value = '';
          }
        }
        loadForm(formEl, targetEl);
      });
    }
  }

  /**
   * Inicializa los comportamientos JS dentro de un contenedor target
   * (útil tras reemplazar el HTML vía AJAX).
   */
  function attachTargetBehaviors(targetEl) {
    const form = encontrarFormularioParaTarget(targetEl);
    if (form) {
      vincularFormulario(form, targetEl);
    }
  }

  /**
   * Inicializa todos los formularios de filtros de la página.
   */
  function inicializarFiltrosVivos() {
    document.querySelectorAll(`[${FORM_ATTR}]`).forEach((form) => {
      const targetSelector = form.getAttribute(TARGET_ATTR);
      if (!targetSelector) return;

      const targetEl = document.querySelector(targetSelector);
      if (!targetEl) return;

      vincularFormulario(form, targetEl);
    });

    // Botones "Limpiar" fuera de formularios (ej. recepción)
    document.querySelectorAll(`[${CLEAR_ATTR}]`).forEach((btn) => {
      if (btn.dataset[BOUND_MARKER] === 'true') return;
      btn.dataset[BOUND_MARKER] = 'true';
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const targetSelector = btn.getAttribute(TARGET_ATTR);
        if (!targetSelector) return;
        const targetEl = document.querySelector(targetSelector);
        if (!targetEl) return;
        loadUrl(targetEl, window.location.pathname);
      });
    });
  }

  /**
   * Delegación de clics para enlaces de filtro y paginación AJAX.
   */
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link) return;

    // Enlaces con data-filtros-link (filtros rápidos tipo Todas/Hoy/Semana)
    if (link.getAttribute(LINK_ATTR)) {
      e.preventDefault();
      const targetSelector = link.getAttribute(TARGET_ATTR);
      if (!targetSelector) return;
      const targetEl = document.querySelector(targetSelector);
      if (!targetEl) return;
      loadUrl(targetEl, buildLinkUrl(link));
      return;
    }

    // Enlaces de paginación dentro de un contenedor de resultados
    const paginacion = link.closest('.coordinacion-pagination, .medico-pagination');
    if (!paginacion) return;
    const targetEl = link.closest('[data-filtros-target]');
    if (!targetEl) return;
    e.preventDefault();
    loadUrl(targetEl, link.href);
  });

  document.addEventListener('DOMContentLoaded', () => {
    inicializarFiltrosVivos();
  });

  // Exportar para uso global si se necesita
  window.filtrosVivos = {
    cargar: loadUrl,
    recargarFormulario: loadForm,
    reiniciar: inicializarFiltrosVivos,
  };
})();
