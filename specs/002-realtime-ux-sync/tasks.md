# Tasks: 002-realtime-ux-sync

**Entrada**: Documentos de diseño desde `specs/002-realtime-ux-sync/` y el plano técnico `plan.md`.

**Prerrequisitos**: `plan.md` (requerido), `spec.md` (requerido para historias de usuario).

**Pruebas**: Incluidas según el mandato del principio "Test-First" en la constitución del proyecto.

**Organización**: Las tareas se estructuran de forma secuencial por fases e historias de usuario para permitir la validación continua en aislamiento.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Puede ejecutarse en paralelo (archivos diferentes, sin dependencias cruzadas).
- **[Story]**: Vínculo directo a la historia de usuario correspondiente.
- Rutas exactas de archivos incluidas en cada descripción para mitigar ambigüedades.

---

## Phase 1: Setup & Shared Infrastructure (WebSockets & Persistencia)

**Propósito**: Inicializar los canales de Reverb, migraciones de auditoría y controladores base.

- [ ] T001 Configurar las reglas de transmisión de WebSockets e inicializar el canal `modules.dashboard` en `routes/channels.php`
- [ ] T002 Crear la migración para la tabla de auditoría de alertas de orden de base de datos en `database/migrations/2026_07_17_000001_create_ranking_alerts_table.php`
- [ ] T003 [P] Implementar el modelo de datos relacional para alertas en `app/Models/RankingAlert.php` definiendo sus propiedades fillable

---

## Phase 2: User Story 1 - Transmisión Reactiva e Inmediata (Dashboard Orquestador)

**Objetivo**: Transmisión viva de eventos de votación e invalidación/congelamiento dinámico del panel.

### Tests for User Story 1 ⚠️ (Test-First Imperative)
- [ ] T004 Crear la prueba de integración `tests/Feature/DashboardBroadcastTest.php` validando que la mutación de una evaluación dispare con éxito el evento broadcast a través de Laravel Reverb
- [ ] T005 Crear la prueba de componente Livewire `tests/Feature/AdminDashboardComponentTest.php` comprobando la asimilación del payload incremental y la reactividad de la vista sin recarga HTTP

### Implementation for User Story 1
- [ ] T006 Crear el evento de transmisión broadcast en `app/Core/Evaluation/Events/EvaluationReceived.php` estructurando el payload agregado incremental (promedios, conteos)
- [ ] T007 Implementar el controlador reactivo del panel de administración en `app/Http/Livewire/AdminDashboard.php` acoplando los listeners de Laravel Echo para interceptar el evento de transmisión
- [ ] T008 Diseñar la interfaz del panel de administración en `resources/views/livewire/admin-dashboard.blade.php` renderizando dinámicamente las barras de progreso del ranking utilizando componentes nativos de Tailwind CSS

---

## Phase 3: User Story 2 - Resiliencia y Almacenamiento Local Offline (Cliente Móvil)

**Objetivo**: Intercepción de fallas de red celular en el cliente mediante Alpine.js y despacho automático tras reconexión.

### Tests for User Story 2 ⚠️ (Test-First Imperative)
- [ ] T0009 Crear la prueba automatizada de frontend en `tests/Feature/MobileOfflineStorageTest.php` inyectando estados de red simulados para forzar el encolamiento y verificar la persistencia de la firma efímera en aislamiento
- [ ] T0010 Crear la prueba unitaria en `tests/Unit/RankReconciliationServiceTest.php` validando que el servidor procese transacciones tardías aplicando las exclusiones de firmas duplicadas registradas en la caché rápida de Redis

### Implementation for User Story 2
- [ ] T0011 Implementar la lógica pura de negocio en `app/Core/Evaluation/Services/RankReconciliationService.php` para la de-duplicación atómica de firmas de dispositivo entrantes
- [ ] T0012 Modificar la vista Blade móvil táctil en `resources/views/livewire/mobile-evaluator.blade.php` integrando el Store global de Alpine.js (`vorticeCache`) para interceptar la sumisión del formulario ante pérdidas de red
- [ ] T0013 [P] Crear el componente visual reactivo en `resources/views/livewire/components/offline-status-indicator.blade.php` encargado de alertar visualmente al usuario el estado "Envío pendiente por conexión" con un área de acción táctil mínima de 44x44px

---

## Phase 4: User Story 3 - Alertas de Alteración Crítica del Ranking por Sincronización Tardía

**Objetivo**: Notificación visual explícita en el Dashboard administrativo ante permutaciones del podio por datos offline encolados.

### Tests for User Story 3 ⚠️ (Test-First Imperative)
- [ ] T0014 Añadir casos de prueba dentro de `tests/Feature/AdminDashboardComponentTest.php` comprobando que el panel del organizador inyecte un banner destacado al recibir eventos de permutación del podio
- [ ] T0015 Agregar aserciones en `tests/Unit/RankReconciliationServiceTest.php` verificando la correcta instanciación de alertas en la persistencia MySQL cuando el vector de orden cambia entre los puestos 1, 2 y 3

### Implementation for User Story 3
- [ ] T0016 Crear el evento de transmisión en tiempo real de alteración en `app/Core/Evaluation/Events/RankingOrderAltered.php`
- [ ] T0017 Desarrollar el algoritmo de comparación de vectores de ordenación antes y después de la inserción masiva dentro de `RankReconciliationService.php` disparando la persistencia y la notificación de la alerta
- [ ] T0018 Modificar `resources/views/livewire/admin-dashboard.blade.php` para renderizar el banner interactivo de advertencia asíncrona cuando se activa el evento del sistema

---

## Phase 5: CLI Interface & Stress Validation (Shared & Polish Gates)

**Propósito**: Cumplir con el mandato CLI inyectando ráfagas masivas antes del cierre formal de la iteración.

- [ ] T0019 Crear la clase para el comando personalizado CLI de Artisan en `app/Console/Commands/SimulateOfflineSync.php`
- [ ] T0020 Implementar en `SimulateOfflineSync.php` la simulación transaccional concurrente de vaciado de colas para 300 terminales móviles de forma secuencial, evaluando tiempos de respuesta del backend
- [ ] T0021 [P] Ejecutar la suite general de validación cruzada mediante el comando de la CLI de control del repositorio para garantizar convergencia limpia antes del cierre de la rama

---

## Dependencies & Execution Order

### Phase Dependencies
1. **Phase 1 (Setup)**: Sin dependencias iniciales. Bloqueante crítico para la creación de canales e infraestructura base.
2. **Phase 2 (US1 - Reactividad)**: Requiere finalizar Phase 1 para disponer del canal configurado en Reverb.
3. **Phase 3 (US2 - Offline)**: Puede ejecutarse de forma independiente en el cliente móvil una vez montada la lógica del Core del repositorio.
4. **Phase 4 (US3 - Alertas)**: Depende estrictamente de la lógica de reconciliación de Phase 3, ya que consume los datos resultantes del vaciado de colas tardías.
5. **Phase 5 (CLI & Polish)**: Bloqueada hasta completar la lógica de servicio de todas las historias anteriores para poder ejecutar simulaciones realistas.