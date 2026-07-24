# Tasks: 003-event-loop-display

**Prerequisites**: Ejecutar desde la nueva rama `003-event-loop-display`.

## Phase 1: QR Library Integration & Service (Library-First)
- [x] T001 Instalar la librería de generación de QRs ejecutando `./vendor/bin/sail composer require simplesoftwareio/simple-qrcode`.
- [x] T002 Crear la prueba unitaria en `tests/Unit/QrGeneratorServiceTest.php` comprobando que el servicio crea correctamente un archivo SVG en el Storage fake.
- [x] T003 Implementar el contrato y la clase `app/Core/Event/Services/QrGeneratorService.php` para encapsular la creación de los códigos aislados del framework.

## Phase 2: User Story 1 - CLI Generation
- [x] T004 Crear prueba de integración `tests/Feature/Commands/GenerateQrsCommandTest.php` validando que la consola procesa el inventario de la DB y reporta éxito.
- [x] T005 [P] Crear el comando Artisan en `app/Console/Commands/GenerateQrs.php` que orqueste la lectura de ponencias y delegue la generación al Service.

## Phase 3: User Story 2 - Smart Routing & Landing (Mobile-First)
- [x] T006 Crear prueba `tests/Feature/ActiveAgendaLandingTest.php` validando que acceder a `/` devuelve las charlas del bloque temporalmente activo.
- [x] T007 Implementar el componente Livewire `app/Http/Livewire/ActiveAgendaLanding.php` que resuelva el bloque activo.
- [x] T008 [P] Diseñar la vista móvil `resources/views/livewire/active-agenda-landing.blade.php` listando las charlas disponibles con áreas táctiles de 44x44px.
- [x] T009 Refactorizar `app/Http/Livewire/MobileEvaluator.php` (de Feature 1/2) para capturar el Edge Case 04: Si el usuario busca un QR huérfano, redirigir vía Livewire/Laravel hacia la Landing Page.

## Phase 4: User Story 3 - Public Leaderboard
- [x] T010 Crear prueba `tests/Feature/PublicLeaderboardComponentTest.php` garantizando que el componente reacciona al evento `EvaluationReceived` vía WebSockets y oculta alertas de permutación.
- [x] T011 Crear componente `app/Http/Livewire/PublicLeaderboard.php` replicando los listeners de Echo del Admin, omitiendo dependencias administrativas.
- [x] T012 Diseñar vista de TV en `resources/views/livewire/public-leaderboard.blade.php` con enfoque UI cinemático para proyectores (Tailwind).
- [x] T013 [P] Registrar las nuevas rutas en `routes/web.php` (Landing Page en `/` y Leaderboard en `/public`).

##  : Convergence & QA
- [x] T014 Validar flujos de red: ejecutar `./vendor/bin/sail npm run build` y correr las suites de PEST unificadas.