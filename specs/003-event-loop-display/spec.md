# Feature Specification: Event Loop & Public Display

**Feature Branch**: `003-event-loop-display`
**Status**: Draft
**Related MVP Scope**: Generación de accesos QR, Enrutamiento inteligente de casos borde y Transparencia pública.

## User Scenarios & Testing

### User Story 1 - Generación automatizada de accesos QR (Priority: P1)
Como Organizador, quiero generar todos los códigos QR de la agenda activa ejecutando un comando de consola, para poder exportarlos e imprimirlos/proyectarlos sin necesidad de una interfaz gráfica administrativa compleja.

*   **Independent Test**: Ejecutar el comando Artisan, verificar que se procesan las charlas activas y se generan los archivos `.svg` en el disco local de almacenamiento.
*   **Acceptance Scenarios**:
    1. **Given** una base de datos con bloques y charlas activas, **When** el administrador ejecuta `pulse:generate-qrs`, **Then** el sistema crea un archivo SVG por cada charla nombrado con su `slug_or_uuid` en la carpeta `storage/app/public/qrs`.

### User Story 2 - Enrutamiento inteligente de Asistentes (Priority: P1)
Como Asistente, quiero que si escaneo un QR genérico del evento o entro a la ruta principal de la aplicación, el sistema me muestre automáticamente las charlas del bloque horario activo, para no quedar bloqueado si el código QR específico de la ponencia falla.

*   **Independent Test**: Navegar a la ruta raíz `/` simulando un dispositivo móvil, validar que el sistema detecta el bloque horario activo y renderiza la lista de charlas disponibles.
*   **Acceptance Scenarios**:
    1. **Given** que existe un bloque horario activo, **When** el usuario navega a la raíz del sitio, **Then** visualiza una lista móvil con las ponencias exclusivas de ese bloque.
    2. **Given** que un usuario intenta acceder a la URL de una charla mediante un código QR de un bloque ya cerrado (Edge Case 04), **When** se procesa la petición, **Then** el sistema lo redirige a la lista activa con un mensaje de expiración.

### User Story 3 - Dashboard Público de Transparencia (Priority: P1)
Como Organizador y Asistente, quiero visualizar un tablero público proyectado en el recinto que anime en tiempo real el podio general de ponencias, para garantizar la transparencia del pulso del evento.

*   **Independent Test**: Abrir la ruta `/public-leaderboard`, enviar votos desde un cliente móvil, y verificar que las posiciones y barras se animan sin recarga.
*   **Acceptance Scenarios**:
    1. **Given** el tablero público expuesto en una pantalla, **When** se emiten y transmiten nuevas evaluaciones vía Reverb, **Then** el tablero actualiza las métricas (FR011-FR015) y reordena el ranking de manera reactiva, omitiendo alertas críticas de uso exclusivo del panel de administración.

## Functional Requirements
*   **FR-016**: El sistema MUST permitir la generación de QRs en bloque vía CLI.
*   **FR-017**: El sistema MUST redirigir peticiones inválidas a una "Landing Page" móvil (Edge Case 04).
*   **FR-018**: El sistema MUST exponer una vista pública de solo lectura que consuma el canal de WebSockets `modules.dashboard`.