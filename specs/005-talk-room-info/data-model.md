# Data Model Artifact: Feature 005 - Talk Room/Auditorium Information

**Feature**: `005-talk-room-info`
**Created**: 2026-08-12

## Entities

### Entity: Talk

Represents a presentation or session in the event agenda.

#### Fields

| Field Name | Type | Constraints | Description |
|------------|------|-------------|-------------|
| `id` | `string` | Primary Key | Unique identifier for the talk (e.g., `"talk-1"`). |
| `time_block_id` | `string` | Foreign Key (`time_blocks.id`) | Reference to assigned time block. |
| `title` | `string` | Required, Max 255 | Title of the talk. |
| `speaker` | `string` | Required, Max 255 | Speaker or presenter name. |
| `room` | `string?` | Nullable, Max 255 | Physical room or auditorium name (e.g., `"Auditorio Principal"`, `"Sala B"`). |
| `start_time` | `datetime` | Required | Start date and time. |
| `end_time` | `datetime` | Required | End date and time. |
| `created_at` | `timestamp` | System | Record creation timestamp. |
| `updated_at` | `timestamp` | System | Record update timestamp. |

#### Model Validation Rules (Admin Edit)

- `editTitle`: `required|string|min:1|max:255`
- `editSpeaker`: `required|string|min:1|max:255`
- `editRoom`: `nullable|string|max:255`
- `editTimeBlockId`: `required|string|exists:time_blocks,id`

#### Default Fallback Behavior

When `room` is `null` or empty string `""`, the system renders `"Por confirmar"` across all presentation layers (`ActiveAgendaLanding`, `MobileEvaluator`, `PublicLeaderboard`, `AdminDashboard`).
