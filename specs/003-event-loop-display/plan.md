# Implementation Plan: Event Loop & Public Display

**Branch**: `003-event-loop-display`

## Summary
Cerrar el ciclo de experiencia del MVP implementando la generación de QRs para acceso, resolviendo el enrutamiento de casos borde (Edge Case 04) hacia una Landing Page móvil, y acoplando un componente Livewire de visualización pública para pantallas grandes que consuma los mismos eventos de Laravel Reverb ya existentes.

## Technical Context
*   **Dependencies**: `simplesoftwareio/simple-qrcode` (para generación SVG libre de dependencias pesadas).
*   **Reusability**: Se reutilizará el evento `EvaluationReceived` de la Feature 2. El Dashboard Público será un "hermano menor" del `AdminDashboard`.

## Constitution Check
*   **Library-First**: La generación de QRs se delegará a un `QrGeneratorService` puro.
*   **CLI Interface Mandate**: Los QRs se generan estrictamente vía el comando `pulse:generate-qrs`.
*   **Mobile-First**: La vista de enrutamiento (Landing de Charlas) aplicará reglas estrictas de 44x44px. El `PublicLeaderboard` es la única excepción de diseño (optimizado para Desktop/TV).
*   **Zero AI Noise**: Rigurosidad en el código limpio sin comentarios predictivos.

## Architectural Decisions
1.  **Generación de QR**: Utilizaremos formato SVG almacenado en el disco público de Laravel. Esto permite escalabilidad infinita sin costos de procesamiento en tiempo real durante el evento.
2.  **Manejo de Raíz y Casos Borde**: Se creará un componente `ActiveAgendaLanding` (Livewire/Blade) que será el *fallback* para peticiones huérfanas.
3.  **Segregación de Dashboards**: El `PublicLeaderboard` compartirá el canal de WebSockets pero tendrá un estado Livewire propio, garantizando que datos administrativos no se filtren al frontend público.