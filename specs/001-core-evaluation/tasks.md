# Tasks: 001-core-evaluation

**Input**: Design documents from `/specs/001-core-evaluation/`

**Prerequisites**: plan.md (required), spec.md (required for user stories)

**Tests**: Included as mandated by the Test-First Principle in the project Constitution.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- This project follows the Single Project layout.
- Paths assume root-level `app/`, `tests/`, `database/`, and `resources/` directory structures.

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic directory structure.

- [ ] T001 Create core evaluation directories: `app/Core/Evaluation/Services/`, `app/Core/Evaluation/Contracts/`, and `tests/Unit/`
- [ ] T002 Configure Redis connection settings in `config/database.php`
- [ ] T003 [P] Setup and verify code formatting configuration rules for PHP and Blade

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [ ] T004 Create database migration for `time_blocks` table in `database/migrations/2026_07_16_000001_create_talks_table.php`
- [ ] T005 Create database migration for `talks` table in `database/migrations/2026_07_16_000001_create_talks_table.php`
- [ ] T006 Create database migration for `evaluations` table with composition index for device signatures in `database/migrations/2026_07_16_000002_create_evaluations_table.php`
- [ ] T007 [P] Implement base Eloquent models: `app/Models/TimeBlock.php`, `app/Models/Talk.php`, and `app/Models/Evaluation.php` with relations
- [ ] T008 Configure Redis cache driver helper inside `config/cache.php`

**Checkpoint**: Foundation ready - user story implementation can now begin.

---

## Phase 3: User Story 1 - Configuración y Carga de Agenda (Priority: P1) 🎯 MVP

**Goal**: Importar charlas y bloques horarios a la persistencia desde un JSON plano usando la CLI de Laravel.

**Independent Test**: Ejecutar `php artisan pulse:import-agenda {path}` y corroborar mediante PEST la persistencia limpia y la invalidación de la caché.

### Tests for User Story 1 ⚠️

- [ ] T009 [P] [US1] Create unit test `tests/Unit/EvaluationServiceTest.php` to validate JSON payload parsing and service layer handling
- [ ] T010 [US1] Create feature test `tests/Feature/ImportAgendaTest.php` to verify Artisan command outcomes, database state change, and Redis clearing

### Implementation for User Story 1

- [ ] T011 [P] [US1] Create domain service contract interface `app/Core/Evaluation/Contracts/EvaluationRepositoryInterface.php`
- [ ] T012 [US1] Implement domain logic service `app/Core/Evaluation/Services/EvaluationService.php` resolving repository contracts
- [ ] T013 [US1] Create custom CLI Artisan command class in `app/Console/Commands/ImportAgenda.php`
- [ ] T014 [US1] Add robust transactional database guards and console log messaging output into the command

**Checkpoint**: At this point, User Story 1 is fully functional and testable independently.

---

## Phase 4: User Story 2 - Evaluación Instantánea por Corazones (Priority: P1)

**Goal**: Permitir la votación de 1 a 5 corazones en interfaz vertical móvil sin autenticación con firma de dispositivo.

**Independent Test**: Interactuar con el componente Livewire activo y verificar que la evaluación se registre en DB ligada al hash único.

### Tests for User Story 2 ⚠️

- [ ] T015 [US2] Create integration test `tests/Feature/MobileEvaluatorTest.php` asserting form rendering, rating constraints, and successful vote persistence

### Implementation for User Story 2

- [ ] T016 [P] [US2] Create Livewire component class in `app/Http/Livewire/MobileEvaluator.php` handling state and validation
- [ ] T017 [US2] Implement Blade markup in `resources/views/livewire/mobile-evaluator.blade.php` applying mobile-friendly interactive tap targets (min 44x44px)
- [ ] T018 [US2] Integrate client-side device signature capture using a UUID persisted in Alpine.js localStorage
- [ ] T019 [US2] Implement SHA-256 server-side verification using IP, User-Agent, and event salt to protect evaluations

**Checkpoint**: At this point, User Stories 1 AND 2 should both work independently.

---

## Phase 5: User Story 3 - Limitación por Bloques de Tiempo (Priority: P2)

**Goal**: Validar temporalmente las peticiones restringiendo votaciones a la charla en curso y hasta 10 minutos posteriores.

**Independent Test**: Evaluar una charla simulando desfase temporal de más de 10 minutos y verificar que la petición sea rechazada.

### Tests for User Story 3 ⚠️

- [ ] T020 [US3] Add tests within `tests/Feature/MobileEvaluatorTest.php` asserting early access block and post-session 10-minute expiration block

### Implementation for User Story 3

- [ ] T021 [US3] Implement dynamic date comparison checks against `start_time` and `end_time` of the associated Talk in `EvaluationService.php`
- [ ] T022 [US3] Create countdown screen visual states in `resources/views/livewire/mobile-evaluator.blade.php` for scheduled upcoming talks

**Checkpoint**: All user stories should now be independently functional.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories.

- [ ] T023 [P] Generate local documentation detailing the JSON schemas under `specs/001-core-evaluation/data-model.md`
- [ ] T024 Add offline feedback UI components in Blade triggered by Alpine.js network listeners
- [ ] T025 Run quickstart.md validation to ensure zero-setup local environment replication works

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after US1 is functional so Talks can be imported and selected
- **User Story 3 (P3)**: Depends on US2 implementation as it wraps validation around the voting event

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1
4. **STOP and VALIDATE**: Test User Story 1 independently

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy/Demo (MVP!)
3. Add User Story 2 → Test independently → Deploy/Demo
4. Add User Story 3 → Test independently → Deploy/Demo