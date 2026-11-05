# Guía de Defensa — SIGC

Sistema de Gestión de Citas Clínicas (SIGC) — Aplicación Laravel para la gestión de citas médicas, horarios, historiales clínicos y notificaciones entre pacientes, médicos y coordinadores.

---

## A. Módulos y funcionalidades

### Arquitectura general

SIGC está construido sobre Laravel con tres roles de usuario diferenciados: **paciente**, **médico** y **coordinador**. Cada rol tiene su propio panel de control (dashboard) y rutas protegidas mediante middleware de autenticación y autorización por rol. Existe además un **portal público** sin autenticación.

Los roles se gestionan en la tabla `roles` con los slugs: `paciente`, `medico`, `coordinador`. Cada usuario (`users`) tiene un `role_id` que determina su panel y acciones disponibles. Los médicos cuentan además con un perfil en la tabla `medicos` vinculado 1:1 al usuario.

---

### 1. Portal público

**Ruta:** `/` (nombre: `portal`)

**Qué puede hacer el visitante:**
- Acceder a la página de inicio del portal público.

**Información que consulta:**
- Contenido estático del portal (definido en `resources/views/portal.blade.php` y componentes `portal-navbar`, `portal-hero`, `portal-footer`).

**Acciones disponibles:**
- Navegación por el portal público.
- Redirección a `/login` o `/register` para autenticarse.

**Estado:** Implementado.

---

### 2. Autenticación

**Rutas:** `/login`, `/register`, `/logout`

**Qué puede hacer el usuario:**

| Acción | Descripción |
|--------|-------------|
| Iniciar sesión | Formulario en `/login`. Valida email y contraseña. Tras autenticarse, redirige al dashboard según su rol. |
| Registrarse | Formulario en `/register`. Crea un nuevo usuario con rol `paciente` automáticamente. El registro solo permite crear pacientes; médicos y coordinadores se crean desde coordinación. |
| Cerrar sesión | `/logout` (requiere autenticación). Invalida la sesión y regenera el token CSRF. |

**Información que consulta o modifica:**
- Credenciales de acceso (email, contraseña).
- Datos del nuevo usuario al registrarse (nombre, email, contraseña confirmada).

**Acciones disponibles:**
- `AuthController@showLogin`, `login`, `showRegister`, `register`, `logout`.

**Validaciones del servidor (registro):**
- `name`: requerido, string, max 255.
- `email`: requerido, email, max 255, único en `users`.
- `password`: requerido, string, min 8, confirmado.

**Validaciones del servidor (login):**
- `email`: requerido, email.
- `password`: requerido.

**Manejo de sesión:**
- Tras login exitoso: `session()->regenerate()`.
- Tras logout: `Auth::logout()`, `session()->invalidate()`, `session()->regenerateToken()`.
- El middleware `Authenticate` cierra la sesión y regenera el token si un médico intenta acceder sin su perfil activo.

**Relación con otros módulos:**
- Es el punto de entrada a todos los módulos protegidos (paciente, médico, coordinación).

**Estado:** Implementado. No hay recuperación de contraseña (forgot/reset password no está implementado).

---

### 3. Módulo del paciente

**Rutas protegidas:** `middleware(['auth', 'role:paciente'])`

#### 3.1 Dashboard (`/paciente/dashboard`)
- Muestra resumen: consultas totales, próxima cita, pendientes, confirmadas, notificaciones recientes.
- Consulta las consultas del usuario ordenadas por fecha y hora.

#### 3.2 Agenda (`/paciente/agenda`)
- Lista todas las consultas del paciente con médico, especialidad y estado.
- Indica si cada consulta tiene historial clínico asociado.

#### 3.3 Doctores (`/paciente/doctores`)
- Lista médicos activos con especialidad.
- Búsqueda por nombre de médico o especialidad (filtro en vivo vía AJAX).
- Botón "Solicitar consulta" que redirige al formulario de solicitud.

#### 3.4 Solicitud de consulta (`/paciente/consulta`, GET y POST)
- Formulario para solicitar una nueva cita.
- Campos: especialidad (requerida), fecha (requerida, futura), hora (requerida, futura), motivo (opcional).
- Cálculo dinámico de horas disponibles según horarios de médicos activos de esa especialidad y citas ya ocupadas.
- Validación: la fecha+hora debe ser futura y la hora debe estar disponible.
- Al crear la consulta, notifica a todos los coordinadores (`ConsultaSolicitada`).

#### 3.5 Cancelar cita (`/paciente/agenda/{consulta}/cancelar`, POST)
- Solo permite cancelar consultas con estado `pendiente` o `confirmada`.
- Notifica a coordinación (`ConsultaEstadoActualizado`).

#### 3.6 Eliminar cita (`/paciente/agenda/{consulta}`, DELETE)
- Solo si no tiene historial clínico asociado.
- Elimina la consulta permanentemente.

#### 3.7 Historial clínico (`/paciente/historial-clinico`)
- Lista los registros clínicos del paciente con especialidad y médico.
- Paginado (15 por página).

#### 3.8 Perfil (`/paciente/perfil`)
- Vista de perfil del paciente.

#### 3.9 Notificaciones (`/paciente/notificaciones`)
- Lista notificaciones de tipo `ConsultaEstadoActualizado` con estado `confirmada`.
- Marcar como leída (`/paciente/notificaciones/{id}/leer`, POST).

#### 3.10 Notificaciones nuevas (toast) (`/paciente/notificaciones/nuevas`, GET)
- Endpoint AJAX para polling de notificaciones no leídas.

**Acciones principales del paciente:**
- Ver sus citas y agenda.
- Solicitar nuevas citas.
- Cancelar citas pendientes/confirmadas.
- Eliminar citas sin historial clínico.
- Ver historial clínico.
- Ver médicos disponibles.
- Gestionar notificaciones.

**Relación con otros módulos:**
- Sus consultas son creadas sin médico asignado (`medico_id = null`, estado `pendiente`).
- Coordinación le asigna un médico y confirma la cita.
- El médico atiende y completa la consulta, generando historial clínico.

**Estado:** Implementado.

---

### 4. Módulo del médico

**Rutas protegidas:** `middleware(['auth', 'role:medico'])`

El médico debe tener un perfil en la tabla `medicos` con `estado = 'activo'` para acceder. El middleware `Authenticate` cierra la sesión si el médico no está activo.

#### 4.1 Dashboard (`/medico/dashboard`)
- Resumen: citas hoy, pendientes hoy, completadas hoy, próxima cita.
- Notificaciones recientes.

#### 4.2 Agenda (`/medico/agenda`)
- Lista consultas asignadas al médico, ordenadas por fecha/hora ascendente.
- Filtros: estado, búsqueda por paciente (AJAX en vivo).
- Acciones: completar consulta (si está `confirmada`), registrar nota clínica (si está `completada` y no tiene historial).

#### 4.3 Consultas (`/medico/consultas`)
- Lista todas las consultas del médico (orden descendente).
- Filtros: estado, búsqueda por paciente.
- Misma acción de completar que la agenda.

#### 4.4 Completar consulta (`/medico/consultas/{consulta}/completar`, POST)
- Solo si la consulta está `confirmada` y le pertenece al médico.
- Cambia estado a `completada`.
- Notifica al paciente y a coordinación.

#### 4.5 Historial clínico (`/medico/historial-clinico`)
- Lista registros clínicos creados por el médico.
- Crear registro clínico (`/medico/consultas/{consulta}/historial-clinico/crear` y `store`, POST).
- Solo permite crear historial si la consulta está `completada` y no tiene historial previo.
- Campos: nota, diagnóstico, tratamiento.

#### 4.6 Horarios (`/medico/horario`)
- Muestra los horarios de atención del médico (día de semana, hora inicio, hora fin, activo).

#### 4.7 Pacientes (`/medico/pacientes`)
- Lista pacientes que tienen consultas con este médico.
- Búsqueda por nombre o correo (AJAX en vivo).

#### 4.8 Perfil (`/medico/perfil`)
- Vista de perfil del médico.

#### 4.9 Notificaciones (`/medico/notificaciones`)
- Lista notificaciones del médico.
- Marcar como leída.

#### 4.10 Notificaciones nuevas (toast) (`/medico/notificaciones/nuevas`, GET)

**Acciones principales del médico:**
- Ver su agenda y consultas asignadas.
- Marcar consultas como completadas.
- Crear registros clínicos en consultas completadas.
- Ver sus horarios de atención.
- Ver sus pacientes.
- Gestionar notificaciones.

**Restricción de seguridad:**
- Solo puede completar consultas que le pertenecen (`consulta->medico_id === $medico->id`).
- Solo puede crear historial clínico en consultas que le pertenecen y están completadas.

**Relación con otros módulos:**
- Recibe consultas asignadas por coordinación.
- Atiende consultas y genera historial clínico visible para el paciente.

**Estado:** Implementado.

---

### 5. Módulo de coordinación

**Rutas protegidas:** `middleware(['auth', 'role:coordinador'])`

#### 5.1 Dashboard (`/coordinacion/dashboard`)
- Métricas del día: citas hoy, pendientes, completadas, pacientes programados, médicos disponibles, ausencias (canceladas).
- Próximas citas del día.
- Notificaciones recientes.

#### 5.2 Citas (`/coordinacion/citas`)
- Lista todas las consultas del sistema con filtros: estado, fecha, búsqueda por paciente.
- Paginación (10 por página), filtros en vivo vía AJAX.
- Acciones por consulta: asignar médico, cambiar estado, eliminar (si no tiene historial clínico).

#### 5.3 Asignar médico (`/coordinacion/citas/{consulta}/asignar-medico`, POST)
- Solo para consultas `pendiente` o `confirmada`.
- Valida que el médico esté activo.
- Valida conflicto de fecha/hora con otras consultas activas del mismo médico.
- Valida que el médico tenga horario activo en ese día/hora.
- Cambia estado a `confirmada`.
- Notifica al paciente y al médico (`ConsultaEstadoActualizado`, `ConsultaAsignada`).

#### 5.4 Cambiar estado (`/coordinacion/citas/{consulta}/estado`, POST)
- Transiciones permitidas:
  - `pendiente` → `confirmada`, `cancelada`
  - `confirmada` → `completada`, `cancelada`
  - `completada` → (ninguna)
  - `cancelada` → (ninguna)
- Al confirmar, requiere médico asignado y horario activo disponible.
- Notifica al paciente al confirmar.

#### 5.5 Eliminar cita (`/coordinacion/citas/{consulta}`, DELETE)
- Solo si no tiene historial clínico asociado.

#### 5.6 Pacientes (`/coordinacion/pacientes`, `/coordinacion/pacientes/{paciente}`)
- Lista pacientes del sistema.
- Ver detalle de un paciente.

#### 5.7 Médicos (`/coordinacion/medicos`)
- Lista médicos con filtros: búsqueda, especialidad, departamento, estado.
- Paginación, filtros AJAX en vivo.

#### 5.8 Registrar médico (`/coordinacion/medicos/registrar`, GET y POST)
- Crea usuario con rol `medico` y perfil en tabla `medicos`.
- Genera contraseña aleatoria de 16 caracteres.
- Crea invitación para activación (token SHA-256, expira en 7 días).
- Campos: nombre, apellido, email, teléfono, número de licencia, especialidad, departamento, fecha incorporación, estado inicial (pendiente/activo).

#### 5.9 Editar médico (`/coordinacion/medicos/{medico}/editar`, PUT)
- Actualiza datos del usuario y perfil médico.

#### 5.10 Desactivar/activar médico (`/coordinacion/medicos/{medico}/desactivar`, `activar`, POST)
- Cambia `estado` a `inactivo` o `activo`.

#### 5.11 Eliminar médico (`/coordinacion/medicos/{medico}`, DELETE)
- Solo si no tiene historiales clínicos ni consultas propias como paciente.
- Elimina usuario y perfil médico en transacción.

#### 5.12 Invitación médica (`/coordinacion/medicos/{medico}/invitacion`, POST)
- Genera/regenera enlace de activación para que el médico establezca su contraseña.

#### 5.13 Horarios médicos (`/coordinacion/medicos/{medico}/horarios`)
- CRUD de horarios: crear, actualizar, desactivar, eliminar.
- Valida solapamiento de horarios activos.
- Valida que no deje citas confirmadas sin cobertura al desactivar/eliminar.

#### 5.14 Recepción (`/coordinacion/recepcion`)
- Lista consultas con filtros: hoy, semana, todas + estado.
- Filtros AJAX en vivo.

#### 5.15 Reportes (`/coordinacion/reportes`)
- Vista de reportes (implementada como vista, sin controlador dedicado con lógica de reportes avanzados).

#### 5.16 Notificaciones (`/coordinacion/notificaciones`)
- Lista notificaciones del coordinador.
- Marcar como leída.

#### 5.17 Notificaciones nuevas (toast) (`/coordinacion/notificaciones/nuevas`, GET)

#### 5.18 Perfil (`/coordinacion/perfil`)
- Vista de perfil del coordinador.

#### 5.19 Personal (`/coordinacion/personal`)
- Vista de personal.

#### 5.20 Configuración (`/coordinacion/configuracion`)
- Vista de configuración.

**Acciones principales del coordinador:**
- Ver y gestionar todas las citas del sistema.
- Asignar médicos a citas pendientes.
- Cambiar estados de citas.
- Gestionar médicos (alta, edición, activación/desactivación, eliminación).
- Gestionar horarios de médicos.
- Ver recepción (citas del día/semana).
- Ver reportes.
- Gestionar notificaciones.

**Relación con otros módulos:**
- Conecta pacientes (solicitudes) con médicos (asignación).
- Supervisa el flujo completo de citas.

**Estado:** Implementado.

---

### 6. Notificaciones

SIGC usa el sistema de notificaciones de Laravel almacenado en la tabla `notifications`.

**Clases de notificación:**
- `ConsultaSolicitada`: notifica a coordinadores cuando un paciente solicita una consulta.
- `ConsultaEstadoActualizado`: notifica al paciente y coordinación cuando cambia el estado de una consulta (asignación, confirmación, completación, cancelación).
- `ConsultaAsignada`: notifica al médico cuando se le asigna una consulta.

**Flujo:**
1. Se dispara la notificación desde un controlador (ej. `SolicitudConsultaController@store` notifica a coordinadores).
2. El usuario consulta sus notificaciones en su panel de notificaciones.
3. Puede marcar individualmente como leída (`/[:rol]/notificaciones/{id}/leer`).
4. Endpoint `/[:rol]/notificaciones/nuevas` devuelve notificaciones no leídas para toasts en tiempo real (polling cada 15s vía JavaScript).

**Estado:** Implementado.

---

### 7. Gestión de citas — flujo completo

1. **Paciente solicita consulta:** rellena especialidad, fecha, hora, motivo. Se crea con estado `pendiente` y `medico_id = null`. Notifica a coordinadores.
2. **Recepción:** coordinación ve las solicitudes pendientes en `/coordinacion/recepcion`.
3. **Asignación de médico:** coordinación asigna un médico activo con horario disponible. Estado pasa a `confirmada`. Notifica al paciente y al médico.
4. **Atención:** el médico ve la consulta en su agenda, la marca como `completada`. Notifica al paciente y coordinación.
5. **Historial clínico:** el médico crea un registro clínico (nota, diagnóstico, tratamiento) asociado a la consulta completada.
6. **Paciente:** ve el estado actualizado, puede cancelar si está pendiente/confirmada, y ver su historial clínico.

---

### 8. Gestión de horarios, pacientes, médicos e historiales clínicos

#### Horarios
- Pertenecen a un médico (`medicos` → `horarios`, 1:N).
- Campos: día_semana (0-6), hora_inicio, hora_fin, activo.
- Coordinación gestiona los horarios de cada médico.
- Validación de solapamiento y de cobertura de citas confirmadas.

#### Pacientes
- Usuarios con rol `paciente`.
- Coordinación puede listar y ver detalle.
- Médico ve sus pacientes vinculados mediante consultas.

#### Médicos
- Usuarios con rol `medico` + perfil en tabla `medicos`.
- Coordinación gestiona el ciclo completo: alta, edición, activación, desactivación, eliminación.
- El médico debe activar su cuenta mediante invitación (token) para establecer contraseña y pasar a estado `activo`.

#### Historiales clínicos
- Pertenecen a una consulta (`consultas` → `historial_clinico`, 1:1).
- Campos: nota, diagnostico, tratamiento.
- Solo el médico asignado puede crear historial, y solo en consultas `completadas`.
- Visible para el paciente y el médico.

---

## B. Seguridad

### 1. Autenticación y manejo de sesiones

**Implementado:**
- `AuthController@login` usa `Auth::attempt()` con credenciales email/password.
- Regeneración de sesión tras login (`session()->regenerate()`).
- `AuthController@logout` usa `Auth::logout()`, `session()->invalidate()`, `session()->regenerateToken()`.
- Middleware `Authenticate` redirige a `/login` si no está autenticado.
- Middleware `Authenticate` cierra sesión y invalida si un médico no tiene perfil activo.

**Parcialmente implementado:**
- No hay verificación de email (`MustVerifyEmail` comentado en User model).
- No hay recuperación de contraseña (no existen rutas de forgot/reset password).

**Pendiente:**
- Autenticación de dos factores (2FA).
- Bloqueo de cuenta por intentos fallidos.

### 2. Hashing de contraseñas

**Implementado:**
- `Hash::make()` se usa en: registro de usuarios, creación de médicos, activación de invitación médica, seeder de datos iniciales.
- El modelo `User` tiene cast `password => 'hashed'`.
- El campo `password` está oculto en serialización (`#[Hidden]`).

**Verificación:** Sí, comprobado en el código. No hay almacenamiento de contraseñas en texto plano.

### 3. Autorización mediante roles y middleware

**Implementado:**
- Middleware `Role` verifica `Auth::check()` y que el `slug` del rol del usuario esté en la lista de roles permitidos.
- Si no tiene el rol requerido, redirige al dashboard correspondiente a su propio rol (no devuelve 403 explícitamente en todos los casos).
- Middleware `Authenticate` aplica restricción adicional para médicos inactivos.

**Rutas protegidas por rol:**
- `role:paciente`: rutas de `/paciente/*`.
- `role:medico`: rutas de `/medico/*`.
- `role:coordinador`: rutas de `/coordinacion/*`.
- `auth` (sin rol): `/consultas/sincronizar`.

**Rutas públicas:**
- `/` (portal), `/login`, `/register`, `/medico/activar/{token}` (mostrar y activar).

### 4. Protección de rutas

**Implementado:**
- Todas las rutas funcionales requieren autenticación excepto portal, login, register y activación médica.
- Las rutas de cada rol están agrupadas con `role:X`.
- Model binding implícito en rutas con `{consulta}`, `{medico}`, `{horario}`, `{paciente}`.
- Verificación de propiedad en acciones del médico (`consulta->medico_id === $medico->id`).
- Verificación de propiedad en acciones del paciente (`consulta->user_id === auth()->id()`).

**Parcialmente implementado:**
- El middleware `Role` redirige en lugar de devolver 403 cuando el rol no coincide (puede ser confuso para el usuario).

### 5. Validaciones del servidor

**Implementado:**
- `SolicitudConsultaController@store`: especialidad (existe), fecha (formato Y-m-d, futuro), hora (formato H:i, futuro), motivo (nullable, max 1000). Validación adicional de hora disponible.
- `CitasController@asignarMedico`: medico_id (requerido, existe en medicos).
- `CitasController@cambiarEstado`: estado (requerido, in:pendiente,confirmada,completada,cancelada).
- `MedicosController@store`: validación completa de campos del médico y usuario.
- `MedicosController@update`: validación de campos editables.
- `HorariosController@store|update`: dia_semana (0-6), hora_inicio/hora_fin (formato H:i, hora_fin > hora_inicio), solapamiento.
- `AuthController@register`: name, email (único), password (min 8, confirmado).
- `InvitacionController@activate`: password (min 8, confirmado).

**Verificación:** Sí, comprobado en los controladores.

### 6. Protección CSRF

**Implementado:**
- Laravel incluye protección CSRF por defecto en formularios POST/PUT/DELETE.
- Los formularios Blade usan `@csrf`.
- Endpoints AJAX usan el token `X-XSRF-TOKEN` leído de la cookie.
- Middleware `VerifyCsrfToken` (estándar de Laravel) activo.

**Verificación:** Sí, comprobado (uso de `@csrf` en vistas y `X-XSRF-TOKEN` en fetch/AJAX).

### 7. Restricciones de acceso a información médica

**Implementado:**
- Paciente solo ve sus propias consultas (`where('user_id', auth()->id())`).
- Médico solo ve consultas asignadas a su perfil (`where('medico_id', $medico->id)`).
- Coordinador ve todas las consultas.
- Historial clínico solo visible para el paciente dueño y el médico creador.
- Médico no puede completar consultas de otros médicos.
- Médico no puede crear historial en consultas de otros médicos o no completadas.

**Verificación:** Sí, comprobado en controladores y consultas Eloquent.

### 8. Protección de formularios y operaciones sensibles

**Implementado:**
- Eliminación de citas bloqueada si tiene historial clínico asociado.
- Eliminación de médicos bloqueada si tiene historiales clínicos o consultas propias.
- Desactivación/eliminación de horarios bloqueada si deja citas confirmadas sin cobertura.
- Confirmación de citas requiere médico asignado y horario activo disponible.
- Transición de estados restringida (máquina de estados definida en `CitasController@cambiarEstado`).

### 9. Manejo de errores y posibles vulnerabilidades

**Implementado:**
- `findOrFail` en consultas que deben existir (paciente cancelar/destroy).
- `abort(403)` en historial clínico si el médico no es el dueño.
- Try/catch implícito en transacciones de base de datos (registro de médico, activación de invitación con `lockForUpdate`).
- Validación de formato de fecha/hora antes de crear consultas.

**Posibles mejoras / pendientes:**
- No hay rate limiting en login (posible fuerza bruta).
- No hay 2FA.
- No hay auditoría de acciones (quién modificó qué y cuándo).
- Las invitaciones médicas se muestran una sola vez al coordinador (no se reenvían por correo automático; hay un TODO en el código).
- No hay CSP ni headers de seguridad adicionales configurados explícitamente.
- El middleware `Role` redirige en lugar de 403, lo que puede revelar existencia de rutas.

**Resumen de seguridad:**
| Área | Estado |
|------|--------|
| Autenticación | Implementado |
| Hashing de contraseñas | Implementado |
| Autorización por roles | Implementado |
| Protección de rutas | Implementado |
| Validaciones del servidor | Implementado |
| CSRF | Implementado |
| Restricción información médica | Implementado |
| Recuperación de contraseña | Pendiente (no implementado) |
| 2FA | Pendiente |
| Rate limiting login | Pendiente |

---

## C. Flujos de funcionamiento

### Flujo 1: Un paciente solicita una cita

1. El paciente inicia sesión en `/login`.
2. Es redirigido a `/paciente/dashboard`.
3. Navega a `/paciente/doctores` para ver médicos disponibles por especialidad.
4. Accede a `/paciente/consulta` y selecciona especialidad, fecha y hora.
5. El sistema calcula horas disponibles basado en horarios de médicos activos de esa especialidad y citas ya ocupadas.
6. El paciente envía el formulario (incluyendo motivo opcional).
7. Se crea una `Consulta` con `estado = 'pendiente'` y `medico_id = null`.
8. Se notifica a todos los coordinadores mediante `ConsultaSolicitada`.
9. El paciente es redirigido a su agenda con mensaje de éxito.

### Flujo 2: Coordinación gestiona una cita

1. El coordinador inicia sesión y accede a `/coordinacion/dashboard`.
2. Ve las citas pendientes del día o navega a `/coordinacion/recepcion` para ver solicitudes.
3. Accede a `/coordinacion/citas` y filtra por estado `pendiente`.
4. Selecciona una consulta y hace clic en "Asignar médico".
5. Elige un médico activo con horario disponible en esa fecha/hora.
6. El sistema valida que no haya conflicto de citas y que el médico tenga horario activo.
7. La consulta pasa a `confirmada` con el médico asignado.
8. El paciente y el médico reciben notificación (`ConsultaEstadoActualizado`, `ConsultaAsignada`).
9. El coordinador puede cambiar el estado posteriormente (completada, cancelada) respetando las transiciones permitidas.

### Flujo 3: Un médico consulta su agenda y atiende una consulta

1. El médico inicia sesión (su cuenta debe estar activa, estado `activo` en tabla `medicos`).
2. Es redirigido a `/medico/dashboard`.
3. Ve su próxima cita y resumen del día.
4. Navega a `/medico/agenda` para ver todas sus consultas asignadas.
5. Filtra por estado `confirmada` si desea ver solo las pendientes de atención.
6. Selecciona una consulta y marca como `completada`.
7. El sistema notifica al paciente y a coordinación.
8. Si es necesario, el médico accede a `/medico/consultas/{consulta}/historial-clinico/crear` y registra nota, diagnóstico y tratamiento.
9. El registro clínico queda asociado a la consulta y visible para el paciente.

### Flujo 4: Un usuario inicia sesión y accede a su panel

1. El usuario accede a `/login`.
2. Ingresa email y contraseña.
3. `Auth::attempt()` valida las credenciales contra la tabla `users` (contraseña hasheada).
4. Si es médico, se verifica que su perfil en `medicos` tenga `estado = 'activo'`. Si no, se cierra la sesión y se muestra error.
5. `session()->regenerate()` se ejecuta.
6. Se redirige según el rol:
   - `paciente` → `/paciente/dashboard`
   - `medico` → `/medico/dashboard`
   - `coordinador` → `/coordinacion/dashboard`
7. Si ya está autenticado y accede a `/login` o `/register`, se redirige a su dashboard correspondiente.

---

## D. Preparación para la defensa

### Preguntas posibles y respuestas

**1. ¿Qué roles tiene el sistema y qué puede hacer cada uno?**

R: Tres roles: paciente, médico y coordinador. El paciente solicita y gestiona sus citas. El médico atiende consultas asignadas y registra historial clínico. El coordinador supervisa todo: asigna médicos, cambia estados de citas, gestiona médicos y horarios, y ve recepción y reportes.

**2. ¿Cómo se protegen las rutas de cada rol?**

R: Mediante el middleware `Role` que verifica que el usuario esté autenticado y que su rol coincida con el requerido por la ruta. Además, el middleware `Authenticate` cierra la sesión de médicos inactivos. Las rutas están agrupadas por rol en `routes/web.php`.

**3. ¿Cómo se almacenan las contraseñas?**

R: Con `Hash::make()` de Laravel (bcrypt/argon2 según configuración). Nunca se almacenan en texto plano. El modelo `User` tiene el cast `password => 'hashed'` y el campo está oculto en serialización.

**4. ¿Cuál es el flujo completo de una cita?**

R: El paciente solicita una cita (estado pendiente, sin médico). Coordinación la ve en recepción, le asigna un médico activo con horario disponible (estado confirmada). El médico la atiende y la marca como completada, y opcionalmente crea un historial clínico. El paciente ve el estado actualizado en todo momento.

**5. ¿Puede un paciente eliminar cualquier cita?**

R: No. Solo puede eliminar citas que le pertenecen y que no tengan historial clínico asociado. Además, solo puede cancelar citas con estado pendiente o confirmada.

**6. ¿Puede un médico completar la consulta de otro médico?**

R: No. El controlador verifica que `consulta->medico_id === $medico->id` antes de permitir marcar como completada o crear historial clínico.

**7. ¿Qué pasa si un coordinador desactiva el horario de un médico que tiene citas confirmadas?**

R: El sistema impide desactivar o eliminar un horario si dejaría citas confirmadas sin cobertura, verificando si otros horarios activos del médico cubren esas citas.

**8. ¿Cómo funciona la activación de médicos?**

R: Coordinación registra al médico con contraseña aleatoria y genera una invitación con token (SHA-256, expira en 7 días). El médico accede al enlace `/medico/activar/{token}`, establece su contraseña y su perfil pasa a estado `activo`. Solo entonces puede iniciar sesión.

**9. ¿Hay protección CSRF en los formularios?**

R: Sí. Laravel incluye `VerifyCsrfToken` por defecto. Los formularios Blade incluyen `@csrf` y las peticiones AJAX envían el encabezado `X-XSRF-TOKEN` leído de la cookie.

**10. ¿Qué módulos JavaScript tiene el sistema y para qué sirven?**

R: Tres módulos en `resources/modules/`:
- `dashboard-navigation.js`: control del sidebar de los dashboards (plegar/desplegar, overlay).
- `portal-navigation.js`: navegación del portal público (menú móvil, navbar).
- `filtros-vivos.js`: búsquedas y filtros en vivo vía AJAX para citas, médicos, pacientes, recepción, agenda y doctores, sin recargar la página.

**11. ¿El sistema tiene recuperación de contraseña?**

R: No implementado. No existen rutas de forgot password ni reset password. Solo se puede establecer contraseña mediante el registro de pacientes o la activación de invitación médica.

**12. ¿Cómo se manejan las notificaciones?**

R: Con el sistema de notificaciones de Laravel en tabla `notifications`. Hay tres tipos: `ConsultaSolicitada` (a coordinadores), `ConsultaEstadoActualizado` (a paciente y coordinación), y `ConsultaAsignada` (al médico). Se consultan en paneles de notificaciones y se pueden marcar como leídas individualmente. También hay un endpoint para toasts en tiempo real mediante polling.

---

*Documento generado para la defensa del proyecto SIGC. Toda la información se basa en inspección directa del código fuente del proyecto.*
