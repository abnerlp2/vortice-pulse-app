# Tasks: 003-event-loop-display

**Prerequisites**: Ejecutar desde la nueva rama `003-event-loop-display`.

## Phase 1: User Story 1 - Smart Routing & Landing (Mobile-First)
- [x] T006 Crear prueba `tests/Feature/ActiveAgendaLandingTest.php` validando que acceder a `/` devuelve las charlas del bloque temporalmente activo.
- [x] T007 Implementar el componente Livewire `app/Http/Livewire/ActiveAgendaLanding.php` que resuelva el bloque activo.
- [x] T008 [P] Diseñar la vista móvil `resources/views/livewire/active-agenda-landing.blade.php` listando las charlas disponibles con áreas táctiles de 44x44px.
- [x] T009 Refactorizar `app/Http/Livewire/MobileEvaluator.php` (de Feature 1/2) para capturar el Edge Case 04: Si el usuario busca una URL inválida o huérfana, redirigir vía Livewire/Laravel hacia la Landing Page.

## Phase 2: User Story 2 - Public Leaderboard
- [x] T010 Crear prueba `tests/Feature/PublicLeaderboardComponentTest.php` garantizando que el componente reacciona al evento `EvaluationReceived` vía WebSockets y oculta alertas de permutación.
- [x] T011 Crear componente `app/Http/Livewire/PublicLeaderboard.php` replicando los listeners de Echo del Admin, omitiendo dependencias administrativas.
- [x] T012 Diseñar vista de TV en `resources/views/livewire/public-leaderboard.blade.php` con enfoque UI cinemático para proyectores (Tailwind).
- [x] T013 [P] Registrar las nuevas rutas en `routes/web.php` (Landing Page en `/` y Leaderboard en `/public`).

## Phase 3: Convergence & QA
- [x] T014 Validar flujos de red: ejecutar `./vendor/bin/sail npm run build` y correr las suites de PEST unificadas.
- [x] T015 [UAT Fix] Corregir BindingResolutionException en MobileEvaluator alineando el parámetro '$talk' del método mount con la firma de la ruta.
- [x] T016 [UAT Fix] Crear/corregir el layout principal de Livewire (components/layouts/app.blade.php) para resolver el InvalidArgumentException en componentes Full-Page.