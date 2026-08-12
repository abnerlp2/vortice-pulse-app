# Tasks: Feature 005 - Talk Room/Auditorium Information

**Input**: Design documents from `/specs/005-talk-room-info/`

**Prerequisites**: plan.md (required), spec.md (required), research.md, data-model.md, contracts/

**Tests**: Tests are included per Constitution Rule III (Test-First Imperative) using Pest PHP.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `- [ ] [ID] [P?] [Story] Description with file path`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Includes exact file paths in descriptions

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Verification of workspace state and feature setup

- [ ] T001 Verify workspace state and environment configuration for feature branch `005-talk-room-info` in `specs/005-talk-room-info/`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core database migration, Eloquent model updates, and service payload parsing

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T002 [P] Create database migration `database/migrations/2026_08_11_000001_add_room_to_talks_table.php` adding nullable `room` column to `talks` table
- [x] T003 [P] Update `app/Models/Talk.php` to include `'room'` in `$fillable` attributes
- [x] T004 [P] Write unit and integration tests in `tests/Feature/TalkRoomInfoTest.php` for `EvaluationService::parsePayload` with optional `room` attribute
- [x] T005 Update `app/Core/Evaluation/Services/EvaluationService.php` to parse `room` attribute in `parsePayload()` and save it in `saveTalks()`

**Checkpoint**: Foundation ready - database schema and core parsing support room attribute. User story implementation can now begin.

---

## Phase 3: User Story 1 - Visualización de la sala en la app móvil de asistentes (Priority: P1) 🎯 MVP

**Goal**: Display the assigned room or auditorium (with fallback "Por confirmar") on the active agenda landing screen and mobile evaluator page for event attendees.

**Independent Test**: Navigate to `/` and `/talk/{id}` in vertical mobile view to verify room information is displayed on talk cards and evaluator headers.

### Tests for User Story 1 ⚠️

- [x] T006 [P] [US1] Write Pest component tests in `tests/Feature/TalkRoomInfoTest.php` for `ActiveAgendaLanding` and `MobileEvaluator` room visibility and "Por confirmar" fallback

### Implementation for User Story 1

- [x] T007 [US1] Update `app/Livewire/ActiveAgendaLanding.php` to include room information when rendering active block talks
- [x] T008 [US1] Update `resources/views/livewire/active-agenda-landing.blade.php` to render the talk room badge/pill with "Por confirmar" fallback
- [x] T009 [US1] Update `app/Livewire/MobileEvaluator.php` to pass room information to view
- [x] T010 [US1] Update `resources/views/livewire/mobile-evaluator.blade.php` to display talk room info near the header

**Checkpoint**: At this point, User Story 1 is fully functional and independently testable on mobile views.

---

## Phase 4: User Story 2 - Ubicación de la charla en el leaderboard público (Priority: P1)

**Goal**: Display room or auditorium information on public leaderboard podium cards projected during the event.

**Independent Test**: Open `/leaderboard` and verify each ranked talk card exhibits its room badge alongside title and speaker name.

### Tests for User Story 2 ⚠️

- [x] T011 [P] [US2] Write Pest component tests in `tests/Feature/TalkRoomInfoTest.php` for `PublicLeaderboard` room mapping and display

### Implementation for User Story 2

- [x] T012 [US2] Update `app/Livewire/PublicLeaderboard.php` to map `room` property into the `$talks` state array
- [x] T013 [US2] Update `resources/views/livewire/public-leaderboard.blade.php` to display room badge on podium talk cards

**Checkpoint**: User Stories 1 AND 2 are functional and display room information in mobile and public views.

---

## Phase 5: User Story 3 - Gestión de sala/auditorio en el panel de administración (Priority: P2)

**Goal**: Display, edit, and import talk room info in the admin dashboard and setup import tools.

**Independent Test**: Navigate to `/admin`, edit a talk's room field, verify persistence, Redis cache invalidation, and test JSON agenda import via CLI.

### Tests for User Story 3 ⚠️

- [x] T014 [P] [US3] Write Pest component tests in `tests/Feature/TalkRoomInfoTest.php` for `AdminDashboard` talk editing with room field and `pulse:import-agenda` CLI command with room data

### Implementation for User Story 3

- [x] T015 [US3] Update `app/Livewire/AdminDashboard.php` to include `$editRoom` state property, validation, and update handling
- [x] T016 [US3] Update `resources/views/livewire/admin-dashboard.blade.php` to display room column in talks table and input field in edit modal
- [x] T017 [US3] Verify `App\Services\EvaluationService::importAgendaFromCsv` and `pulse:import-agenda` command for room persistence

**Checkpoint**: All user stories are fully implemented, functional, and testable independently.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: End-to-end regression testing and validation

- [x] T018 [P] Run full Pest test suite (`./vendor/bin/sail pest` or `php artisan test`) and ensure zero regressions across all features
- [x] T019 Execute quickstart validation steps in `specs/005-talk-room-info/quickstart.md`

---

## Phase 7: Add Time Information to Talks

**Purpose**: Make visible the start and end time for each talk

- [x] T020 [P] Crear prueba unitaria en PEST para validar que el modelo `Talk` convierte y expone `start_time` y `end_time` al formato amigable (ej. 9:00 am).
- [x] T021 Implementar los Accessors `formatted_start_time` y `formatted_end_time` en `app/Models/Talk.php` utilizando Carbon.
- [x] T022 [P] Crear/actualizar pruebas de integración en PEST asegurando que el rango de horas (inicio - fin) sea visible en el renderizado de los componentes Livewire.
- [x] T023 Actualizar las vistas Blade (`active-agenda-landing`, `mobile-evaluator`, `public-leaderboard` y `admin-dashboard`) para hacer visible la información del rango de horas, manteniendo los estándares de diseño Mobile-First.

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - starts immediately.
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories.
- **User Stories (Phase 3+)**: Depend on Foundational phase (T002-T005) completion.
  - User Stories can proceed sequentially in priority order (P1 → P1 → P2).
- **Polish (Phase 6)**: Depends on all user stories being complete.

### User Story Dependencies

- **User Story 1 (P1)**: Starts after Phase 2 (Foundational).
- **User Story 2 (P1)**: Starts after Phase 2 (Foundational). Independent of US1.
- **User Story 3 (P2)**: Starts after Phase 2 (Foundational). Independent of US1/US2.

### Parallel Opportunities

- **Phase 2**: T002, T003, and T004 can be created in parallel.
- **User Story 1**: T006 test task can run in parallel before implementation tasks.
- **User Story 2**: T011 test task can run in parallel.
- **User Story 3**: T014 test task can run in parallel.
- **Phase 6**: T018 can run in parallel with final verification.

---

## Parallel Execution Example: User Story 1

```text
Stream A (Backend / Tests):
  - T006: Write Pest tests for ActiveAgendaLanding and MobileEvaluator room display in tests/Feature/TalkRoomInfoTest.php
  - T007: Update app/Livewire/ActiveAgendaLanding.php
  - T009: Update app/Livewire/MobileEvaluator.php

Stream B (Frontend / Views):
  - T008: Update resources/views/livewire/active-agenda-landing.blade.php
  - T010: Update resources/views/livewire/mobile-evaluator.blade.php
```

---

## Implementation Strategy & MVP Scope

1. **Foundational Phase**: Run migration and update core model/service (`T002`-`T005`).
2. **MVP Delivery (User Story 1)**: Implement mobile attendee agenda landing and evaluator room display (`T006`-`T010`). Validate MVP independently.
3. **Public Leaderboard (User Story 2)**: Implement public leaderboard room cards (`T011`-`T013`).
4. **Admin Management (User Story 3)**: Implement admin table display, talk editing modal, and import validation (`T014`-`T017`).
5. **Final Validation**: Execute full test suite (`T018`-`T019`).
