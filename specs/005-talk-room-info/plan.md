# Implementation Plan: Feature 005 - Talk Room/Auditorium Information

**Branch**: `005-talk-room-info` | **Date**: 2026-08-12 | **Spec**: [spec.md](spec.md)

## Summary

Agregar la información de la sala o auditorio (`room`) a cada charla en el modelo de datos y desplegarla en las tres vistas principales del sistema: la aplicación móvil de asistentes (`ActiveAgendaLanding` y `MobileEvaluator`), el leaderboard público (`PublicLeaderboard`) y el panel de administración (`AdminDashboard` y `AdminSetup` / `ImportAgenda`). La implementación incluye una migración de base de datos para añadir la columna nullable `room`, la actualización del parser de agenda en el núcleo (`EvaluationService`), la actualización de mapeos en los componentes Livewire y las plantillas Blade correspondientes, y el manejo tolerante a fallos con la etiqueta por defecto "Por confirmar" si la sala no estuviera presente.

## Technical Context

**Language/Version**: PHP 8.3 / Laravel 11
**Primary Dependencies**: Livewire v4, Alpine.js v3, Tailwind CSS v3, Laravel Reverb, Redis
**Storage**: MySQL (tabla `talks`, columna `room` nullable), Redis (invalidación de caché de charlas)
**Testing**: Pest PHP (Pruebas de integración y componentes Livewire)
**Target Platform**:
- `/`: Aplicación móvil de asistentes (Mobile-first, Portrait, áreas táctiles ≥44x44px)
- `/leaderboard`: Leaderboard público (Pantallas grandes/TVs, Reverb WebSockets)
- `/admin`: Panel de administración (Desktop-first)
**Performance Goals**: Sin impacto en el tiempo de respuesta (<200ms) ni en transmisiones WebSockets en tiempo real.
**Constraints**: Manejo amigable de valores nulos con "Por confirmar" y compatibilidad retroactiva con archivos JSON/Excel de agenda existentes.

## Constitution Check

*GATE: Passed*

- **I. Library-First**: La lógica de mapeo de la nueva columna "Sala" desde Excel/CSV residirá estrictamente en el servicio puro `App\Core\Evaluation\Services\EvaluationService`.
- **II. Admin GUI Amendment (2026-07-27)**: Se omite la carga vía CLI JSON. La ingesta de la sala se procesará a través de la interfaz gráfica de `AdminSetup` utilizando archivos Excel/CSV, respetando la enmienda de la constitución.
- **III. Test-First (NON-NEGOTIABLE)**: Se crearán pruebas aisladas en Pest PHP: Unitarias para el servicio de importación y de Integración/Feature para los componentes Livewire.
- **IV. Simplicity Over Cleverness**: Uso de un atributo nativo `room` en la base de datos y un Accessor en Eloquent para el fallback.
- **V. Mobile-First**: Se garantiza el respeto absoluto al área táctil de $\ge 44 \times 44\text{ px}$ en `ActiveAgendaLanding` y `MobileEvaluator`.
- **VI. Zero AI Noise**: Prohibición estricta de comentarios generados por IA en PHP o Blade.

## Project Structure

### Documentation

```text
specs/005-talk-room-info/
├── spec.md              # Especificación funcional
├── plan.md              # Este archivo (Plan de implementación)
├── research.md          # Investigación y decisiones de arquitectura
├── data-model.md        # Definición de entidad y modelo de datos
├── quickstart.md        # Guía de validación y comandos de prueba
└── contracts/
    └── talk-room-payload.md # Contratos de datos JSON y estados Livewire
```

### Source Code (repository root)

```text
database/
└── migrations/
    └── 2026_08_11_000001_add_room_to_talks_table.php (Nuevo)

app/
├── Models/
│   └── Talk.php (Modificado)
├── Core/
│   └── Evaluation/
│       └── Services/
│           └── EvaluationService.php (Modificado - parsePayload)
└── Livewire/
    ├── ActiveAgendaLanding.php (Modificado)
    ├── MobileEvaluator.php (Modificado)
    ├── PublicLeaderboard.php (Modificado)
    └── AdminDashboard.php (Modificado)

resources/
└── views/
    └── livewire/
        ├── active-agenda-landing.blade.php (Modificado)
        ├── mobile-evaluator.blade.php (Modificado)
        ├── public-leaderboard.blade.php (Modificado)
        └── admin-dashboard.blade.php (Modificado)

tests/
├── Unit/
│   └── EvaluationServiceRoomTest.php (Nuevo - Prueba de mapeo de Excel/CSV)
└── Feature/
    ├── MobileRoomDisplayTest.php (Nuevo - Prueba de UI Móvil y áreas táctiles)
    └── AdminRoomManagementTest.php (Nuevo - Prueba de edición en Admin Dashboard)
```

## Architectural Decisions

1. **Migración No Destructiva Nullable**: La columna `room` será de tipo `string()->nullable()` en la tabla `talks`. De este modo, no afectará registros previamente sembrados o creados en pruebas existentes.
2. **Fallback Centralizado en el Modelo (Accessor)**: Para mantener limpias las vistas Blade y Livewire, el manejo de valores nulos no se hará en el frontend. Se creará un Accessor (Casting) en el modelo `App\Models\Talk.php` (ej. `getRoomAttribute()`) que retornará "Por confirmar" automáticamente si la base de datos devuelve `null` o vacío.
3. **Mantenimiento del Flujo de Edición en Admin**: Se añadirá la propiedad `$editRoom` en `AdminDashboard` siguiendo el patrón existente para `editTitle` y `editSpeaker`. En el modal de edición, el administrador podrá escribir o actualizar la sala y guardar los cambios invalidando la caché de Redis del id correspondiente.

## Complexity Tracking

> No hay violaciones a la constitución que requieran justificación de complejidad.
