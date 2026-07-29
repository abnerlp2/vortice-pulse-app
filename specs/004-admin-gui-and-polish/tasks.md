# Tasks: 004-admin-gui-and-polish

**Inputs**: Documentos de diseño desde `specs/004-admin-gui-and-polish/`.
**Prerequisites**: `plan.md`, `spec.md`.
**Tests**: Mandato "Test-First" obligatorio.
**Format**: `[ID] [P?] [Story] Description`

---

## Phase 1: Security & Setup (Blocking Prerequisite)

**Purpose**: Asegurar el perímetro del módulo administrativo antes de exponer interfaces gráficas.

- [x] T001 Crear prueba de integración `tests/Feature/EnsureAdminPasswordTest.php` validando el bloqueo HTTP 403/Redirect si la sesión no posee la bandera de acceso y el éxito si el input coincide con el `.env`.
- [x] T002 Implementar el middleware `app/Http/Middleware/EnsureAdminPassword.php` y registrarlo en el Kernel o archivo de rutas para el grupo de rutas `/admin`.
- [x] T003 Crear una vista simple en `resources/views/admin/login.blade.php` con un único campo de contraseña para alimentar la sesión del middleware.

---

## Phase 2: User Story 1 - Importación Visual de Agenda (Excel/CSV)

**Objective**: Interfaz drag & drop o input file para inicializar el evento sin CLI.

- [x] T004 Instalar dependencia para soporte XLSX mediante `./vendor/bin/sail composer require maatwebsite/excel` (o equivalente nativo si se opta 100% por CSV plano).
- [x] T005 Crear prueba de componente `tests/Feature/AdminSetupComponentTest.php` validando la carga de un archivo de prueba, la validación de extensiones y el llamado exitoso al `EvaluationService`.
- [x] T006 Implementar el componente `app/Http/Livewire/AdminSetup.php` con manejo de carga temporal de archivos (`WithFileUploads`).
- [x] T007 Diseñar la interfaz `resources/views/livewire/admin-setup.blade.php` optimizada para Desktop, con alertas de error de formato y contadores de éxito.
- [x] T008 Configurar en `routes/web.php` la ruta `/admin/setup` apuntando al nuevo componente y protegida por el middleware.

---

## Phase 3: User Story 2 - Exploración Cualitativa (Drill-down)

**Objective**: Refactorizar el dashboard para Desktop e incorporar el panel de retroalimentación de texto.

- [x] T009 Ampliar `tests/Feature/AdminDashboardComponentTest.php` comprobando que el componente puede aislar y cargar los comentarios cualitativos de una charla específica cuando se acciona su ID.
- [x] T010 Refactorizar `app/Http/Livewire/AdminDashboard.php` añadiendo métodos (`loadQualitativeData($talkId)`) y propiedades públicas para almacenar los textos positivos y de mejora.
- [x] T011 Refactorizar `resources/views/livewire/admin-dashboard.blade.php` para adoptar un contenedor ancho (`max-w-7xl` o similar) aprovechando pantallas de escritorio.
- [x] T012 Implementar en la vista un Slide-over (Panel lateral derecho) manejado por variables de Livewire/Alpine (`x-data="{ open: @entangle('showSlideOver') }"`) que renderice en dos columnas los textos de la charla seleccionada o el estado vacío.

---

## Phase 4: User Story 3 - UI/UX Polish Mobile

**Objective**: Refinamiento estético, identidad de marca y transiciones fluidas en la aplicación del asistente.

- [x] T013 [P] Crear componente Blade `resources/views/components/header.blade.php` que renderice el logotipo oficial de Vórtice 2026. El agente MUST utilizar la imagen física que ya se encuentra disponible en la ruta `public/images/vortice-logo.svg` empleando el helper nativo `asset()`.
- [x] T014 Integrar el componente `header` en `resources/views/livewire/mobile-evaluator.blade.php` y `resources/views/livewire/active-agenda-landing.blade.php`.
- [x] T015 Refinar `resources/views/livewire/mobile-evaluator.blade.php` añadiendo directivas `x-transition` de Alpine.js en los mensajes de validación (FR-008) y en la tarjeta de éxito final para suavizar la aparición.
- [x] T016 [P] Auditar el diseño final simulando un dispositivo móvil para asegurar que las estrellas y botones mantienen un objetivo táctil >44px y generar un commit final de estabilización.

---

## Phase 5: User Story 4 - Edición individual de charlas

**Objective**: Formulario de edición en el dashboard para mutar datos en caliente sin romper la integridad de las evaluaciones previas.

- [ ] T017 Crear la prueba de integración en PEST `tests/Feature/AdminEditTalkTest.php` validando que la mutación de campos actualiza MySQL, purga correctamente la caché de la charla en Redis y rechaza campos en blanco. *(Test-First Imperative)*
- [ ] T018 Refactorizar `app/Http/Livewire/AdminDashboard.php` (o crear un componente hijo) para inyectar los métodos de edición (`editTalk()`, `updateTalk()`), aplicando tipado estricto y reglas de validación en los campos `title`, `speakers` y `time_block_id`.
- [ ] T019 Implementar la interfaz del formulario de edición dentro de `resources/views/livewire/admin-dashboard.blade.php` (dentro del Slide-over o un modal Desktop), asegurando manejo de errores y estados de carga (`wire:loading`).
- [ ] T020 [P] Ejecutar la suite de pruebas completa de la Feature 004 para garantizar que la nueva funcionalidad de edición no generó regresiones en la importación masiva ni en el visor cualitativo.