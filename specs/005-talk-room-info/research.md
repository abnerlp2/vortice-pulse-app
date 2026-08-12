# Research Artifact: Feature 005 - Talk Room/Auditorium Information

**Feature**: `005-talk-room-info`
**Created**: 2026-08-12

## Research Findings & Architectural Decisions

### Decision 1: Database Migration & Schema
- **Decision**: Add a nullable `room` column of type `string` to the `talks` table via migration `database/migrations/2026_08_11_000001_add_room_to_talks_table.php`.
- **Rationale**: Making `room` nullable ensures zero disruption to existing database seeds, tests, and active records. Migration can be rolled back safely if needed (`dropColumn('room')`).
- **Alternatives Considered**: Making `room` required was rejected because it would break existing test seeders and legacy JSON agenda imports.

### Decision 2: Core Payload Parsing & Backward Compatibility
- **Decision**: In `App\Core\Evaluation\Services\EvaluationService::parsePayload()`, extract `$talk['room'] ?? null`. If `$talk['room']` is set, ensure it is a string or null; otherwise default to `null`.
- **Rationale**: Preserves compatibility with existing JSON agenda files while seamlessly supporting new agenda files containing `"room": "Auditorio Principal"`.
- **Alternatives Considered**: Rejecting JSON files without `"room"` key was rejected per specification requirements (FR-005).

### Decision 3: UI Fallback Handling Across Views
- **Decision**: Render `$talk['room'] ?? 'Por confirmar'` or `$talk->room ?? 'Por confirmar'` with a Tailwind CSS pill/badge component in all three views:
  1. **Mobile Agenda / Evaluator**: Displayed below or next to the speaker name.
  2. **Public Leaderboard**: Displayed inside each podium/ranking card alongside title and speaker.
  3. **Admin Dashboard**: Displayed as a column in the talks list and as an editable field in the talk edit modal.
- **Rationale**: Ensures immediate visual clarity for attendees and admins without layout breakage if the string is empty or missing.

### Decision 4: Admin Dashboard State & Editing
- **Decision**: Extend `App\Livewire\AdminDashboard` with a `$editRoom` property, binding it in `editTalk()`, `updateTalk()`, `cancelEdit()`, and validation (`'editRoom' => 'nullable|string|max:255'`).
- **Rationale**: Follows the established Livewire state management pattern in `AdminDashboard` (`$editTitle`, `$editSpeaker`).
