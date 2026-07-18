# Tasks: 002-realtime-ux-sync

**Inputs**: Documentos de diseño desde `specs/002-realtime-ux-sync/` y el plano técnico `plan.md`.

**Prerequisites**: `plan.md` (requerido), `spec.md` (requerido para historias de usuario).

**Tests**: Incluidas según el mandato del principio "Test-First" en la constitución del proyecto.

**Organization**: Las tareas se estructuran de forma secuencial por fases e historias de usuario para permitir la validación continua en aislamiento.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Puede ejecutarse en paralelo (archivos diferentes, sin dependencias cruzadas).
- **[Story]**: Vínculo directo a la historia de usuario correspondiente.
- Rutas exactas de archivos incluidas en cada descripción para mitigar ambigüedades.

---

## Phase 1: Setup & Shared Infrastructure (WebSockets & Persistence)

**Purpose**: Inicializar los canales de Reverb, migraciones de auditoría y controladores base.

- [x] T001 Configurar las reglas de transmisión de WebSockets e inicializar el canal `modules.dashboard` en `routes/channels.php`
- [x] T002 Crear la migración para la tabla de auditoría de alertas de orden de base de datos en `database/migrations/2026_07_17_000003_create_ranking_alerts_table.php`
- [x] T003 [P] Implementar el modelo de datos relacional para alertas en `app/Models/RankingAlert.php` definiendo sus propiedades fillable

---

## Phase 2: User Story 1 - Reactive and Immediate Transmission (Orchestrator Dashboard)

**Objective**: Transmisión viva de eventos de votación e invalidación/congelamiento dinámico del panel.

### Tests for User Story 1 ⚠️ (Test-First Imperative)
- [x] T004 Crear la prueba de integración `tests/Feature/DashboardBroadcastTest.php` validando que la mutación de una evaluación dispare con éxito el evento broadcast a través de Laravel Reverb
- [x] T005 Crear la prueba de componente Livewire `tests/Feature/AdminDashboardComponentTest.php` comprobando la asimilación del payload incremental y la reactividad de la vista sin recarga HTTP

### Implementation for User Story 1
- [x] T006 Crear el evento de transmisión broadcast en `app/Core/Evaluation/Events/EvaluationReceived.php` estructurando el payload agregado incremental (promedios, conteos)
- [x] T007 Implementar el controlador reactivo del panel de administración en `app/Http/Livewire/AdminDashboard.php` acoplando los listeners de Laravel Echo para interceptar el evento de transmisión
- [x] T008 Diseñar la interfaz del panel de administración en `resources/views/livewire/admin-dashboard.blade.php` renderizando dinámicamente las barras de progreso del ranking utilizando componentes nativos de Tailwind CSS

---

## Phase 3: User Story 2 - Resilience and Local Offline Storage (Mobile Client)

**Objective**: Intercepción de fallas de red celular en el cliente mediante Alpine.js y despacho automático tras reconexión.

### Tests for User Story 2 ⚠️ (Test-First Imperative)
- [x] T009 Crear la prueba automatizada de frontend en `tests/Feature/MobileOfflineStorageTest.php` inyectando estados de red simulados para forzar el encolamiento y verificar la persistencia de la firma efímera en aislamiento
- [x] T010 Crear la prueba unitaria en `tests/Feature/RankReconciliationServiceTest.php` validando que el servidor procese transacciones tardías aplicando las exclusiones de firmas duplicadas registradas en la caché rápida de Redis

### Implementation for User Story 2
- [x] T011 Implementar la lógica pura de negocio en `app/Core/Evaluation/Services/RankReconciliationService.php` para la de-duplicación atómica de firmas de dispositivo entrantes
- [x] T012 Modificar la vista Blade móvil táctil en `resources/views/livewire/mobile-evaluator.blade.php` integrando el store global de Alpine.js (`vorticeCache`) para interceptar la sumisión del formulario ante pérdidas de red
- [x] T013 [P] Crear el componente visual reactivo en `resources/views/livewire/components/offline-status-indicator.blade.php` encargado de alertar visualmente al usuario el estado "Envío pendiente por conexión" con un área de acción táctil mínima de 44x44px

---

## Phase 4: User Story 3 - Critical Ranking Alteration Alerts Due to Delayed Synchronization

**Objective**: Notificación visual explícita en el dashboard administrativo ante permutaciones del podio por datos offline encolados.

### Tests for User Story 3 ⚠️ (Test-First Imperative)
- [x] T014 Añadir casos de prueba dentro de `tests/Feature/AdminDashboardComponentTest.php` comprobando que el panel del organizador inyecte un banner destacado al recibir eventos de permutación del podio
- [x] T015 Agregar aserciones en `tests/Feature/RankReconciliationServiceTest.php` verificando la correcta instanciación de alertas en la persistencia MySQL cuando el vector de orden cambia entre los puestos 1, 2 y 3

### Implementation for User Story 3
- [x] T016 Crear el evento de transmisión en tiempo real de alteración en `app/Core/Evaluation/Events/RankingOrderAltered.php`
- [x] T017 Desarrollar el algoritmo de comparación de vectores de ordenación antes y después de la inserción masiva dentro de `RankReconciliationService.php` disparando la persistencia y la notificación de la alerta
- [x] T018 Modificar `resources/views/livewire/admin-dashboard.blade.php` para renderizar el banner interactivo de advertencia asíncrona cuando se activa el evento del sistema

---

## Phase 5: CLI Interface & Stress Validation (Shared & Polish Gates)

**Purpose**: Cumplir con el mandato CLI inyectando ráfagas masivas antes del cierre formal de la iteración.

- [x] T019 Crear la clase para el comando personalizado CLI de Artisan en `app/Console/Commands/SimulateOfflineSync.php`
- [x] T020 Implementar en `SimulateOfflineSync.php` la simulación transaccional concurrente de vaciado de colas para 300 terminales móviles de forma secuencial, evaluando tiempos de respuesta del backend
- [x] T021 [P] Ejecutar la suite general de validación cruzada mediante el comando de la CLI de control del repositorio para garantizar convergencia limpia antes del cierre de la rama

---

## Phase 6: Convergence

**Purpose**: Detectar las brechas reales entre la implementación actual y los criterios de aceptación del spec, y colocar tareas de remediación trazables.

- [x] T022 Implementar el manejo de eventos en `app/Http/Livewire/AdminDashboard.php` y `resources/views/livewire/admin-dashboard.blade.php` para actualizar dinámicamente las métricas del dashboard en respuesta a `EvaluationReceived` y activar el banner de alerta al recibir `RankingOrderAltered`.
- [x] T023 Extender `app/Core/Evaluation/Services/RankReconciliationService.php` para validar el campo `created_at` de los payloads offline frente a la ventana de expiración del bloque horario y descartar las evaluaciones tardías inválidas antes de persistir.
- [x] T024 Añadir el estado de bloque inactivo y el congelamiento de actualizaciones en `app/Http/Livewire/AdminDashboard.php` y la vista del dashboard para los bloques de tiempo finalizados, según la aceptación de la historia de usuario 1.
- [x] T025 Crear una prueba de componente adicional en `tests/Feature/AdminDashboardComponentTest.php` que verifique la propagación de eventos reales hacia el estado del dashboard y la activación del banner de alertas de ranking.

---

## Dependencies & Execution Order

### Phase Dependencies
1. **Phase 1 (Setup)**: Sin dependencias iniciales. Bloqueante crítico para la creación de canales e infraestructura base.
2. **Phase 2 (Reactive Transmission)**: Requiere finalizar Phase 1 para disponer del canal configurado en Reverb.
3. **Phase 3 (Offline Storage)**: Puede ejecutarse de forma independiente en el cliente móvil una vez montada la lógica del core del repositorio.
4. **Phase 4 (Alerts)**: Depende estrictamente de la lógica de reconciliación de Phase 3, ya que consume los datos resultantes del vaciado de colas tardías.
5. **Phase 5 (CLI & Polish)**: Bloqueada hasta completar la lógica de servicio de todas las historias anteriores para poder ejecutar simulaciones realistas.

### Infrastructure Impediments
- [x] [BLOCKER] La validación visual (UAT) del canal de WebSockets no puede completarse en el entorno de GitHub Codespaces debido a la ausencia de la extensión nativa de PHP `pcntl` (Error: Undefined constant "SIGINT").

---

## Phase 7: Convergence

- [x] T026 Escribir la prueba PEST faltante para la exclusión de firmas duplicadas en Redis, según el mandato Test-First de la constitución.
- [x] T027 Escribir la prueba PEST faltante para la validación de la ventana de gracia de expiración de 10 minutos, según el mandato Test-First de la constitución.
- [x] T028 Aplicar la ventana de gracia de 10 minutos (en lugar de 30) para la expiración del payload offline, según FR-005.
- [x] T029 Utilizar la caché de Redis directamente para las comprobaciones de idempotencia de `device_signature`, según el plan de arquitectura.
- [x] T030 Mover la lógica de la cola offline de Alpine.js al store global `Alpine.store('vorticeCache')`, según el plan de arquitectura.
- [x] T031 Medir y registrar los tiempos de respuesta del backend durante la simulación de sincronización offline en la CLI.
- [x] T032 Refactorizar la inicialización del listener de eventos de Echo para usar atributos nativos de Livewire v3/v4 o `livewire:initialized` en lugar de `livewire:load`.

### Divergence Correction: Session DB Scheme

- [x] T033 Auditar el estado de los esquemas de persistencia en la base de datos ejecutando `./vendor/bin/sail artisan migrate:status` en el entorno local. Identificar si las migraciones base de Laravel 11 se encuentran en estado "Pending".
- [x] T034 Re-aprovisionar la estructura relacional obligatoria del framework ejecutando `./vendor/bin/sail artisan migrate:fresh --seed` asegurando que el output de consola confirme la creación de la tabla `sessions`.
- [x] T035 Validar la resolución de excepciones en el punto de entrada efectuando una petición GET a `http://localhost` y recibiendo un código HTTP 200 (garantizando la desaparición del Error 500).

---

## Phase 8: Convergence (Routing & Layout)

- [x] T036 Crear prueba funcional (Feature Test) para asegurar que la ruta raíz (`/`) responde con HTTP 200 y renderiza el componente Livewire `AdminDashboard`.
- [x] T037 Generar el componente Livewire `AdminDashboard` (`app/Livewire/AdminDashboard.php` y `resources/views/livewire/admin-dashboard.blade.php`) definiendo su estado base.
- [x] T038 Crear el layout principal de la aplicación (`resources/views/components/layouts/app.blade.php`) inyectando obligatoriamente las directivas de compilación de Vite y los scripts de Livewire.
- [x] T039 Eliminar la respuesta JSON de estado en `routes/web.php` y enrutar explícitamente la raíz (`/`) hacia la clase `AdminDashboard`.

## Phase 9: Convergence (Frontend Assets & Vite)

**Purpose**: Resolver la divergencia de infraestructura en la capa de presentación mediante la instalación y configuración de la tubería de compilación de Vite y Tailwind CSS bajo el entorno de Docker (Sail).

- [x] T040 Ejecutar la instalación de dependencias del ecosistema Node.js corriendo `./vendor/bin/sail npm install` para asegurar la disponibilidad de los paquetes.
- [x] T041 Inyectar las directivas obligatorias de Tailwind (`@tailwind base; @tailwind components; @tailwind utilities;`) en el archivo `resources/css/app.css`.
- [x] T042 Modificar `vite.config.js` añadiendo la configuración `server: { host: '0.0.0.0', hmr: { host: 'localhost' } }` para permitir que el host macOS consuma los activos estáticos desde el contenedor Sail.
- [x] T043 Validar la compilación exitosa de los activos ejecutando `./vendor/bin/sail npm run build` para garantizar que no existan errores de sintaxis antes de levantar el servidor HMR de desarrollo.

## Phase 10: Divergence Correction (Repository Contract Integrity)

**Purpose**: Sincronizar el repositorio concreto de evaluaciones con los métodos abstractos exigidos por su interfaz para restablecer la estabilidad del contenedor IoC y permitir la ejecución del simulador de estrés.

- [ ] T044 Desmarcar temporalmente la tarea T020 en tasks.md hasta que el contrato de compilación sea completamente válido.
- [ ] T045 Implementar el método `getTalkRatings($talkId)` en `app/Core/Evaluation/Repositories/EvaluationRepository.php` para calcular promedios de corazones.
- [ ] T046 Implementar el método `getTalksByTimeBlock($timeBlockId)` en `app/Core/Evaluation/Repositories/EvaluationRepository.php` para recuperar las ponencias agendadas.
- [ ] T047 Implementar el método `hasEvaluation($talkId, $deviceSignature)` en `app/Core/Evaluation/Repositories/EvaluationRepository.php` utilizando Redis/MySQL para el control de fraude de una opinión por persona.
- [ ] T048 Ejecutar la suite de pruebas unitarias mediante `./vendor/bin/sail pest` para verificar que el contenedor IoC resuelva todas las dependencias sin excepciones.