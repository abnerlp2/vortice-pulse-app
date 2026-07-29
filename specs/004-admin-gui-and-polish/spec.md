# Feature Specification: Admin GUI & UI Polish

**Feature Branch**: `004-admin-gui-and-polish`
**Created**: 2026-07-27
**Status**: Draft
**Input**: Requerimiento táctico de habilitar interfaz gráfica de configuración (carga de Excel/CSV), panel de retroalimentación cualitativa en Desktop y refinamiento visual de la app móvil.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Importación visual de la agenda vía Excel/CSV (Priority: P1)
Como Organizador del evento, quiero subir el listado oficial de charlas y bloques horarios mediante un archivo Excel o CSV desde el panel de administración web, para configurar el evento fácilmente antes de iniciar sin depender de la terminal.

**Why this priority**: Habilita la autogestión de la plataforma por parte de usuarios no técnicos durante las pruebas y el demo.
**Independent Test**: Navegar a la ruta protegida `/admin/setup`, subir un archivo `.xlsx` o `.csv` con el formato esperado y verificar que los datos se reflejen correctamente en la base de datos y en la vista de inventario.

**Acceptance Scenarios**:
1. **Given** que el administrador se encuentra autenticado en `/admin/setup`, **When** arrastra o selecciona un archivo Excel/CSV válido y presiona "Importar", **Then** el sistema procesa el archivo, persiste los bloques y charlas, e informa el número total de registros cargados exitosamente.
2. **Given** que el administrador intenta subir un archivo con un formato incorrecto (ej. `.pdf` o `.xlsx` sin las columnas requeridas), **When** el sistema procesa el formulario, **Then** la importación se detiene, no se altera la base de datos y se muestra un mensaje de error detallando el problema de formato.

---

### User Story 2 - Exploración Cualitativa (Drill-down) en Dashboard (Priority: P1)
Como Organizador del evento, quiero poder hacer clic en cualquier charla del ranking (Leaderboard) en mi pantalla de escritorio para desplegar un panel lateral con los comentarios de "Lo que más gustó" y "Qué cambiarías", y así obtener el contexto cualitativo detrás de la calificación numérica.

**Why this priority**: Cierra la brecha del MVP inicial, permitiendo la lectura de los aprendizajes que habilitan la agilidad y mejora continua.
**Independent Test**: En el dashboard de administración, hacer clic en la fila de una charla que posea comentarios registrados y verificar que el Slide-over aparece dinámicamente con los textos agrupados sin recargar el sitio.

**Acceptance Scenarios**:
1. **Given** que el Organizador visualiza el ranking en su monitor de escritorio, **When** hace clic en la acción "Ver detalle" de una ponencia, **Then** un panel lateral (Slide-over) emerge sobre la misma vista exponiendo dos columnas claras con los comentarios textuales positivos y las oportunidades de mejora de esa charla específica.
2. **Given** que el panel lateral de detalles cualitativos está abierto, **When** el Organizador presiona la tecla "Escape" o hace clic fuera del panel, **Then** el Slide-over se cierra de manera fluida regresando el foco íntegro a la tabla del ranking principal.

---

### User Story 3 - UI/UX Polish Mobile (Priority: P2)
Como Asistente del evento, quiero visualizar el logotipo oficial de Vórtice 2026 en la cabecera de la aplicación y disfrutar de transiciones más suaves, para tener una experiencia de evaluación que se sienta profesional, nativa e inmersiva.

**Why this priority**: Mejora la percepción de calidad del producto final y refuerza la identidad del evento sin sacrificar el rendimiento.
**Independent Test**: Ingresar a la aplicación desde un dispositivo móvil, verificar la presencia de la cabecera gráfica y confirmar que las validaciones de error y pantallas de éxito operan con transiciones CSS/Alpine.

**Acceptance Scenarios**:
1. **Given** que el Asistente abre el enlace de evaluación, **When** la pantalla de calificación se renderiza, **Then** se muestra claramente el logotipo oficial en la parte superior manteniendo intactos los objetivos táctiles obligatorios de 44x44px para las estrellas/corazones.
2. **Given** que el Asistente envía su evaluación exitosamente, **When** el sistema procesa la petición, **Then** la vista transiciona de manera suave (fade-in/out) hacia la tarjeta de agradecimiento en lugar de un cambio de pantalla brusco.

---

### User Story 4 - Edición individual de charlas (Priority: P1)
Como Organizador del evento, quiero editar los detalles de una charla específica (nombre de la charla, expositor, horario) desde el panel de administración, para registrar cambios de última hora en la agenda sin tener que importar nuevamente un archivo de Excel.

**Why this priority**: En un evento en vivo, los nombres de ponentes o títulos cambian a último minuto. Depender de reimportar el archivo completo genera riesgo de sobrescribir datos de evaluaciones activas.

**Independent Test**: En el dashboard de administración `/admin`, seleccionar una charla, hacer clic en "Editar", modificar su título/speaker y presionar "Guardar". Verificar en la base de datos y en Redis que los datos se actualizaron y que las evaluaciones previas de esa charla se mantienen intactas.

**Acceptance Scenarios**:
1. **Given** que el Organizador se encuentra autenticado en `/admin`, **When** selecciona una charla, modifica su título o conferencista y guarda los cambios, **Then** el sistema actualiza la base de datos, purga la caché afectada en Redis y refleja la información corregida en el Dashboard y en la app móvil.
2. **Given** que el Organizador está editando una charla, **When** intenta dejar el campo de título o el bloque horario en blanco y presiona "Guardar", **Then** el sistema detiene la transacción, resalta los campos obligatorios con un mensaje de validación y mantiene los datos originales.

---

### Edge Cases
*   **Acceso no autorizado al panel administrativo**: Si un usuario regular intenta navegar a `/admin` o `/admin/setup`, el sistema MUST interceptar la petición vía middleware y exigir una contraseña. El sistema validará el acceso contra la variable de entorno `ADMIN_PASSWORD` definida en el servidor.
*   **Comentarios cualitativos vacíos**: Si el Organizador inspecciona una charla donde los asistentes solo enviaron corazones pero ningún comentario de texto, el panel lateral MUST mostrar un mensaje de estado vacío (ej. "No hay comentarios cualitativos registrados para esta sesión").
*   **Edición de una charla en plena votación**: Si el organizador edita el título de una charla mientras el bloque horario está activo y los usuarios están votando, el sistema MUST aplicar una transacción de actualización sobre la tabla talks sin alterar ni borrar los registros vinculados en la tabla evaluations.

## Requirements *(mandatory)*

### Functional Requirements
*   **FR-019**: El sistema MUST proveer una interfaz gráfica de carga de archivos que soporte las extensiones `.xlsx` y `.csv`, reutilizando la lógica de persistencia del `EvaluationService` para parsear los datos de manera estructurada.
*   **FR-020**: El sistema MUST proteger todas las rutas bajo el prefijo `/admin` empleando un middleware de autenticación simple (`EnsureAdminPassword`) que contraste el input del usuario contra la variable de entorno `ADMIN_PASSWORD`, prescindiendo de tablas de usuarios en la base de datos.
*   **FR-021**: El layout del Dashboard Administrativo (`/admin`) MUST prescindir de las restricciones Mobile-First, adoptando una estructura Grid/Flexbox diseñada para pantallas de alta resolución (Desktop).
*   **FR-022**: La visualización de retroalimentación cualitativa MUST ocurrir de forma asíncrona y sobre la misma vista del ranking, empleando componentes modales o Slide-overs nativos de Livewire/Alpine.
*   **FR-023**: El sistema MUST permitir la actualización en tiempo real de los campos `title`, `speakers` y `time_block_id` de una charla existente desde el panel `/admin`, invalidando la caché de Redis correspondiente para que los asistentes vean los cambios al instante sin afectar sus votos registrados.

## Success Criteria *(mandatory)*

### Measurable Outcomes
*   **SC-004**: Se logra inicializar un evento completo (múltiples bloques y charlas) subiendo un Excel/CSV estándar en menos de 5 segundos de procesamiento de interfaz.
*   **SC-005**: El Organizador puede leer el 100% de los comentarios cualitativos sin abandonar ni recargar la ruta `/admin`.
*   **SC-006**: La auditoría de Lighthouse o herramientas de accesibilidad móvil confirma que, a pesar de los refinamientos visuales (logo, estilos), el formulario de votación mantiene los hit-targets superiores a 44x44px.

## Assumptions
*   Se asume que la estructura de columnas del archivo proporcionado por la organización de Vórtice 2026 será predecible y estandarizada (ej. Título, Speaker, Hora Inicio, Hora Fin).
*   Se asume que el panel administrativo será operado exclusivamente desde computadores personales o tablets en orientación horizontal.
*   Se asume que el equipo técnico configurará correctamente la variable `ADMIN_PASSWORD` en el archivo `.env` del servidor de producción.