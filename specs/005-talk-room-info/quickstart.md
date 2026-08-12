# Quickstart Validation Guide: Feature 005 - Talk Room/Auditorium Information

**Feature**: `005-talk-room-info`
**Created**: 2026-08-12

## Prerequisites

- Local Laravel Sail or PHP 8.3 environment running MySQL and Redis.
- Active database populated or refreshed via Artisan.

## Runnable Validation Scenarios

### Scenario 1: Import Agenda JSON with Room Data

1. **Execution**:
   ```bash
   ./vendor/bin/sail artisan pulse:import-agenda storage/app/agenda.json
   ```
2. **Verification**:
   Check database records in `talks` table:
   ```bash
   ./vendor/bin/sail artisan tinker --execute="App\Models\Talk::pluck('room', 'title');"
   ```
   **Expected Outcome**: Talks imported from JSON have their assigned `room` string persisted.

### Scenario 2: Test Suite Execution

Run Pest feature tests specifically for room visibility across views:
```bash
./vendor/bin/sail pest tests/Feature/TalkRoomInfoTest.php
```
**Expected Outcome**: All tests pass for mobile landing, mobile evaluator, public leaderboard, and admin dashboard.

### Scenario 3: UI Visual Validation

1. **Mobile Landing (`/`)**: Open browser in vertical mobile view.
   - Verify room badge/pill (e.g., "Auditorio Principal") appears on each talk card.
2. **Mobile Evaluator (`/talk/{id}`)**: Open active talk evaluation view.
   - Verify talk room/auditorium is visible near the talk header.
3. **Public Leaderboard (`/leaderboard`)**:
   - Verify ranking cards display the room information for each talk.
4. **Admin Dashboard (`/admin`)**:
   - Verify talks table displays room column.
   - Click "Editar", update room name, and save.
   - Verify updated room name reflects instantly on dashboard and public views.
