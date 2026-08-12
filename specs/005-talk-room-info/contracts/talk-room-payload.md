# Data Contract: JSON Agenda Import & Livewire Talk Payload

**Feature**: `005-talk-room-info`
**Created**: 2026-08-12

## 1. JSON Agenda Import Schema (`pulse:import-agenda`)

### Example Agenda Payload with Room Info

```json
{
  "time_blocks": [
    {
      "id": "block-1",
      "start_time": "2026-08-11 09:00:00",
      "end_time": "2026-08-11 10:00:00"
    }
  ],
  "talks": [
    {
      "id": "talk-1",
      "time_block_id": "block-1",
      "title": "Arquitecturas Reactivas con Laravel y Livewire",
      "speaker": "Ana Martínez",
      "room": "Auditorio Principal",
      "start_time": "2026-08-11 09:00:00",
      "end_time": "2026-08-11 10:00:00"
    },
    {
      "id": "talk-2",
      "time_block_id": "block-1",
      "title": "Optimización de Redis en Producción",
      "speaker": "Carlos Gómez",
      "start_time": "2026-08-11 09:00:00",
      "end_time": "2026-08-11 10:00:00"
    }
  ]
}
```

### Schema Rules

- `talks[].room`: Optional string. If provided, must be a valid non-empty string or null.
- If omitted, default stored value in database is `NULL`.

---

## 2. Livewire Component Talk State Array Contract

### Array Structure in `PublicLeaderboard` & `AdminDashboard`

```php
[
    'id' => 'talk-1',
    'title' => 'Arquitecturas Reactivas con Laravel y Livewire',
    'speaker' => 'Ana Martínez',
    'room' => 'Auditorio Principal', // or null / 'Por confirmar'
    'time_block_id' => 'block-1',
    'average' => 4.8,
]
```
