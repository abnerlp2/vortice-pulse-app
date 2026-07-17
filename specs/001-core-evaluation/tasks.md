# Tasks: 001-core-evaluation

**Entrada**: Documentos de diseño desde `/specs/001-core-evaluation/`

**Prerrequisitos**: plan.md (requerido), spec.md (requerido para historias de usuario)

**Pruebas**: Incluidas según el mandato del principio "Test-First" en la constitución del proyecto.

**Organización**: Las tareas se agrupan por historia de usuario para permitir la implementación y prueba independiente de cada historia.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Puede ejecutarse en paralelo (archivos diferentes, sin dependencias)
- **[Story]**: A qué historia de usuario pertenece esta tarea (ej., US1, US2, US3)
- Incluir rutas exactas de archivos en las descripciones

## Path Conventions

- Este proyecto sigue la estructura de proyecto único.
- Las rutas asumen las estructuras de directorios de nivel raíz `app/`, `tests/`, `database/` y `resources/`.

---

## Phase 1: Setup (Shared Infrastructure)

**Propósito**: Inicialización del proyecto y definición de la estructura de directorios base.

- [x] T001 Crear la estructura de directorios del core: `app/Core/Evaluation/Services/`, `app/Core/Evaluation/Contracts/` y `tests/Unit/`
- [x] T002 Configurar los parámetros de conexión para Redis dentro de `config/database.php`
- [x] T003 [P] Configurar y validar las reglas de estilo y formato de código para PHP y Blade

---

## Phase 2: Foundational (Blocking Prerequisites)

**Propósito**: Infraestructura central de persistencia y modelos requerida antes de implementar cualquier historia de usuario.

**⚠️ CRÍTICO**: Ningún trabajo de historia de usuario puede comenzar hasta que esta fase esté completa.

- [x] T004 Crear la migración para la tabla `time_blocks` en `database/migrations/2026_07_16_000001_create_talks_table.php`
- [x] T005 Crear la migración para la tabla `talks` en `database/migrations/2026_07_16_000001_create_talks_table.php`
- [x] T006 Crear la migración de base de datos para la tabla `evaluations` incorporando el índice compuesto único contra duplicados por firma de dispositivo en `database/migrations/2026_07_16_000002_create_evaluations_table.php`
- [x] T007 [P] Implementar los modelos elocuentes base: `app/Models/TimeBlock.php`, `app/Models/Talk.php` y `app/Models/Evaluation.php` definiendo formalmente sus relaciones mutuas
- [x] T008 Configurar el driver y helper de caché de Redis en `config/cache.php`

**Punto de control**: Base lista - la implementación de historias de usuario puede comenzar.

---

## Phase 3: User Story 1 - Configuración y Carga de Agenda (Priority: P1) 🎯 MVP

**Objetivo**: Importar charlas y bloques horarios a la persistencia desde un JSON plano usando la CLI de Laravel.

**Prueba independiente**: Ejecutar `php artisan pulse:import-agenda {path}` y corroborar mediante PEST la persistencia limpia y la invalidación de la caché.

### Tests for User Story 1 ⚠️

- [x] T009 [P] [US1] Crear la prueba unitaria `tests/Unit/EvaluationServiceTest.php` para validar el parseo del payload JSON y las reglas del Service
- [x] T010 [US1] Crear la prueba de integración `tests/Feature/ImportAgendaTest.php` para verificar la ejecución del comando Artisan, persistencia en base de datos y la limpieza de caché en Redis

### Implementation for User Story 1

- [x] T011 [P] [US1] Crear la interfaz de contrato para el servicio en `app/Core/Evaluation/Contracts/EvaluationRepositoryInterface.php`
- [x] T012 [US1] Implementar la lógica del caso de uso en el servicio de dominio `app/Core/Evaluation/Services/EvaluationService.php` resolviendo las llamadas al repositorio
- [x] T013 [US1] Crear la clase para el comando personalizado CLI de Artisan en `app/Console/Commands/ImportAgenda.php`
- [x] T014 [US1] Incorporar transacciones de base de datos y salidas limpias por consola de comandos dentro de `ImportAgenda.php`

**Punto de control**: En este punto, la historia de usuario 1 es completamente funcional y puede probarse de manera independiente.

---

## Phase 4: User Story 2 - Evaluación Instantánea por Corazones (Priority: P1)

**Objetivo**: Permitir la votación de 1 a 5 corazones en interfaz vertical móvil sin autenticación con firma de dispositivo.

**Prueba independiente**: Interactuar con el componente Livewire activo y verificar que la evaluación se registre en DB ligada al hash único.

### Tests for User Story 2 ⚠️

- [x] T015 [US2] Crear la prueba de integración de componente `tests/Feature/MobileEvaluatorTest.php` validando renderizado inicial de formulario, límites de puntaje y persistencia del voto

### Implementation for User Story 2

- [x] T016 [P] [US2] Crear el controlador del componente reactivo en `app/Http/Livewire/MobileEvaluator.php` para gestionar estado y validaciones básicas
- [x] T017 [US2] Diseñar la vista Blade móvil táctil en `resources/views/livewire/mobile-evaluator.blade.php` garantizando objetivos táctiles mínimos de 44x44px en Tailwind CSS
- [x] T018 [US2] Acoplar la firma de dispositivo cliente usando un UUID efímero persistido en el localStorage mediante Alpine.js
- [x] T019 [US2] Implementar la verificación y hashing SHA-256 en el servidor usando IP, User-Agent y sal del evento para asegurar anonimato sin colisiones

**Punto de control**: En este punto, las historias de usuario 1 y 2 deberían funcionar de manera independiente.

---

## Phase 5: User Story 3 - Limitación por Bloques de Tiempo (Priority: P2)

**Objetivo**: Validar temporalmente las peticiones restringiendo votaciones a la charla en curso y hasta 10 minutos posteriores.

**Prueba independiente**: Evaluar una charla simulando desfase temporal de más de 10 minutos y verificar que la petición sea rechazada.

### Tests for User Story 3 ⚠️

- [x] T020 [US3] Agregar casos de prueba dentro de `tests/Feature/MobileEvaluatorTest.php` comprobando el bloqueo por acceso temprano y expiración de ventana tras 10 minutos de cierre

### Implementation for User Story 3

- [x] T021 [US3] Implementar las reglas de comparación temporal dinámica contra `start_time` y `end_time` de la charla activa en `EvaluationService.php`
- [x] T022 [US3] Configurar pantallas y layouts visuales de cuenta regresiva en `resources/views/livewire/mobile-evaluator.blade.php` para próximas charlas agendadas

**Punto de control**: Todas las historias de usuario ahora deberían ser funcionalmente independientes.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Propósito**: Mejoras transversales y documentación del sistema.

- [x] T023 [P] Escribir la documentación técnica local de los esquemas y payloads esperados en `specs/001-core-evaluation/data-model.md` (Adaptado a diseño de Consola)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sin dependencias - puede iniciar inmediatamente.
- **Foundational (Phase 2)**: Depende de la finalización del Setup - BLOQUEA todas las historias de usuario.
- **User Stories (Phase 3+)**: Todas dependen de la finalización de la fase Foundational.
- **Polish (Final Phase)**: Depende de que todas las historias de usuario deseadas estén completas.

### User Story Dependencies

- **User Story 1 (P1)**: Puede iniciar tras Foundational (Phase 2) - Sin dependencias de otras historias.
- **User Story 2 (P2)**: Puede iniciar una vez que US1 sea funcional para que las charlas puedan importarse y seleccionarse.
- **User Story 3 (P3)**: Depende de la implementación de US2, ya que envuelve la validación alrededor del evento de votación.

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Completar Phase 1: Setup
2. Completar Phase 2: Foundational (CRÍTICO - bloquea todas las historias)
3. Completar Phase 3: User Story 1
4. **DETENERSE Y VALIDAR**: Probar la historia de usuario 1 de manera independiente.

### Incremental Delivery

1. Completar Setup + Foundational → Base lista
2. Agregar User Story 1 → Probar de forma independiente → Desplegar/Demo (¡MVP!)
3. Agregar User Story 2 → Probar de forma independiente → Desplegar/Demo
4. Agregar User Story 3 → Probar de forma independiente → Desplegar/Demo

## Phase 7: Convergence

**Propósito**: Resolver brechas funcionales y arquitectónicas identificadas entre el estado actual y la especificación técnica/constitución del proyecto.

- [x] T026 Eliminar la clase envoltorio no solicitada en `app/Services/EvaluationService.php` (no requerida por la arquitectura de dominio) para preservar la pureza del diseño propuesto.
- [x] T027 [US2] Implementar los campos cualitativos opcionales de texto libre (`liked_aspects`, `improvement_aspects`) en la migración de evaluaciones y el componente Livewire, según dicta el requerimiento FR-003.
- [x] T028 [P] Crear el comando Artisan específico para vaciar la caché y limpiar el estado de Redis, dando cumplimiento al principio II de la Constitución (CLI Interface).
- [x] T029 [P] Desarrollar el comando Artisan simulador de estrés para inyectar concurrencia masiva (hasta 300 asistentes) a fin de validar el rendimiento previo al frontend, estipulado en la Constitución.

## Phase 8: Convergence

**Propósito**: Resolver deudas técnicas detectadas en la validación post-implementación de los comandos de consola.

- [x] T031 Refactorizar `app/Console/Commands/ClearRedisState.php` para eliminar las llaves quemadas (`vortice:signatures:talk-1:hash123` y `vortice:time_blocks:active`) empleadas exclusivamente para forzar el paso de la prueba, resolviendo el anti-patrón de pruebas (test-only production code).

## Deferred Tasks (Deferred to 002-realtime-ux-sync)

**Propósito**: Tareas extraídas (descoped) del MVP Core para ser abordadas en la próxima iteración de sincronización y experiencia de usuario avanzada.

- [ ] T024 Diseñar estados interactivos visuales offline en Blade utilizando eventos y listeners de conexión de Alpine.js
- [ ] T025 Validar el archivo de arranque rápido local ejecutando el pipeline descrito en `specs/001-core-evaluation/quickstart.md`
- [ ] T030 Integrar Laravel Reverb y diseñar la vista del dashboard público para cumplir con la sincronización reactiva de métricas estipulada en las restricciones técnicas de la arquitectura.