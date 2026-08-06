# Implementation Plan: Event Loop & Public Display

**Branch**: `003-event-loop-display`

## Summary
Cerrar el ciclo de experiencia del MVP resolviendo el enrutamiento de casos borde (Edge Case 04) hacia una Landing Page móvil, y acoplando un componente Livewire de visualización pública para pantallas grandes que consuma los mismos eventos de Laravel Reverb ya existentes.

## Technical Context
*   **Reusability**: Se reutilizará el evento `EvaluationReceived` de la Feature 2. El Dashboard Público será un "hermano menor" del `AdminDashboard`.

## Constitution Check
*   **Mobile-First**: La vista de enrutamiento (Landing de Charlas) aplicará reglas estrictas de 44x44px. El `PublicLeaderboard` es la única excepción de diseño (optimizado para Desktop/TV).
*   **Zero AI Noise**: Rigurosidad en el código limpio sin comentarios predictivos.

## Architectural Decisions
1.  **Manejo de Raíz y Casos Borde**: Se creará un componente `ActiveAgendaLanding` (Livewire/Blade) que será el *fallback* para peticiones huérfanas y el punto de entrada principal.
2.  **Segregación de Dashboards**: El `PublicLeaderboard` compartirá el canal de WebSockets pero tendrá un estado Livewire propio, garantizando que datos administrativos no se filtren al frontend público.