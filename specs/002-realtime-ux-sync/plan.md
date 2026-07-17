# Implementation Plan: Realtime UI Synchronization and Offline Resilience

**Branch**: `002-realtime-ux-sync` | **Date**: 2026-07-17 | **Spec**: specs/002-realtime-ux-sync/spec.md

## Summary

Establecer la infraestructura de sincronización reactiva bidireccional y resiliencia offline para la Feature 002 de Vórtice Pulse. La solución técnica integra Laravel Reverb como servidor de WebSockets de alta velocidad para la actualización instantánea del Dashboard de organización sin recargas de página. En la capa del cliente móvil, se implementará un mecanismo de persistencia local impulsado por un Store de Alpine.js que capturará el estado de la red celular, encolará las evaluaciones en el almacenamiento local durante pérdidas de conectividad y automatizará su reconciliación transaccional una vez recuperado el enlace. El backend procesará estas transacciones tardías mediante servicios desacoplados en el Core de la aplicación, evaluando ventanas temporales de expiración en Redis e inyectando alertas visuales en vivo si el orden del ranking consolidado sufre alteraciones críticas.

## Technical Context

**Language/Version**: PHP 8.3 / Laravel 11

**Primary Dependencies**: Livewire v4, Alpine.js v3, Tailwind CSS v3, Laravel Reverb, Predis v2

**Storage**: MySQL 8.0 (Persistencia transaccional de evaluaciones definitivas y alertas de ranking), Redis 7.2 (Caché de firmas de dispositivo, marcas de tiempo de bloques y control rápido de estados de sincronización)

**Testing**: PEST PHP (Pruebas de canales de transmisión Reverb, pruebas de integración de colas de reconciliación y simulación de latencia de red)

**Target Platform**: Mobile-first Web Application (Vistas móviles optimizadas en orientación vertical Portrait) y Dashboard de Organización (Escritorio / Pantalla de visualización pública)

**Performance Goals**: Difusión y renderizado de votos en el Dashboard < 2 segundos en condiciones óptimas. Intercepción y almacenamiento local offline en el cliente móvil < 300ms.

**Constraints**: Tolerancia estricta a condiciones de carrera durante la sincronización masiva de colas móviles al finalizar bloques horarios. Cero almacenamiento de información de identificación personal (PII).

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Library-First Principle**: La lógica de cálculo para la detección de permutaciones en las posiciones del podio de oradores y la validación de marcas temporales de payloads offline se encapsulará en servicios puros dentro de `app/Core/Evaluation/Services/RankReconciliationService.php` de forma completamente aislada de Livewire y de las capas de transporte HTTP/WebSocket.
- **CLI Interface Mandate**: Se proveerá un comando Artisan personalizado (`pulse:simulate-offline-sync`) para simular la llegada tardía y masiva de ráfagas de colas offline de hasta 300 terminales móviles para auditar la estabilidad de la persistencia y la activación de alertas antes de acoplar la interfaz del frontend.
- **Test-First Imperative**: Toda clase de servicio de reconciliación, middlewares de validación de WebSockets y manejadores de persistencia local en local-storage contarán con sus correspondientes suites de pruebas en PEST antes de la construcción de las interfaces de usuario del Dashboard o el componente móvil.
- **Zero AI Noise**: Queda terminantemente prohibido incluir comentarios redundantes o explicativos generados por inteligencia artificial en el código de producción.

## Project Structure

### Documentation (this feature)

[INICIO_DIAGRAMA_ARBOL]
specs/002-realtime-ux-sync/
├── plan.md              # Este archivo
├── data-model.md        # Extensiones del esquema y diseño de la entidad RankingAlert
└── tasks.md             # Secuencia de tareas atómicas para ejecución en TDD
[FIN_DIAGRAMA_ARBOL]

### Source Code (repository root)

[INICIO_DIAGRAMA_ARBOL]
app/
├── Broadcasters/
│   └── ReverbBroadcaster.php
├── Console/
│   └── Commands/
│       └── SimulateOfflineSync.php
├── Core/
│   └── Evaluation/
│       ├── Services/
│       │   └── RankReconciliationService.php
│       └── Events/
│           ├── EvaluationReceived.php
│           └── RankingOrderAltered.php
├── Http/
│   └── Livewire/
│       └── AdminDashboard.php
└── Models/
    └── RankingAlert.php

database/
└── migrations/
    └── 2026_07_17_000001_create_ranking_alerts_table.php

resources/
└── views/
    └── livewire/
        ├── admin-dashboard.blade.php
        └── components/
            └── offline-status-indicator.blade.php
[FIN_DIAGRAMA_ARBOL]

## Architectural Decisions

### 1. Sincronización en Tiempo Real mediante WebSockets Nativos (Laravel Reverb)
Para cumplir con el requerimiento de inmediatez del Dashboard (SC-001) sin sobrecargar el servidor con sondeos cíclicos (polling HTTP), se opta por Laravel Reverb. Cada voto procesado exitosamente por el servidor disparará el evento broadcast `EvaluationReceived`, el cual transmitirá a través de un canal público (`modules.dashboard`) un payload optimizado que contendrá los promedios recalculados de forma incremental y el conteo de votos. El componente Livewire `AdminDashboard` escuchará este canal vía JavaScript nativo de Echo y actualizará reactivamente su estado interno en el cliente.

### 2. Resiliencia Offline en Cliente vía Alpine.js Store y LocalStorage
La captura de evaluaciones sin red se resolverá estrictamente en el frontend (Edge Case Handling). Se definirá un Store global de Alpine.js (`vorticeCache`) que actuará como el interceptor primario del evento submit del formulario móvil. 
- **En línea**: Envía mediante los métodos reactivos estándar de Livewire.
- **Fuera de línea (detectado vía `navigator.onLine` y fallos de timeout)**: Almacena el payload estructurado en el `localStorage` del dispositivo dentro de una estructura indexada por el UUID seguro de la charla, transicionando la interfaz al estado visual "Envío pendiente por conexión".
Un listener del navegador para el evento `online` automatizará el vaciado de la cola recorriendo el almacenamiento local y despachando las evaluaciones retenidas mediante peticiones asíncronas estructuradas hacia el backend.

### 3. Mitigación de Fraude en Sincronizaciones Tardías y Reconciliación
Cuando una petición de sincronización offline llegue al servidor, el servicio `RankReconciliationService` ejecutará de manera atómica dos verificaciones bajo aislamiento transaccional:
1. **Idempotencia Transparente**: Comprobación en la caché rápida de Redis de la firma única del dispositivo (`device_signature`) contra la charla destino. Si la firma ya existía en los registros reales, la petición offline entrante se descarta inmediatamente como duplicada silenciosa para bloquear ataques informales por manipulación de caché local.
2. **Validación Temporal Estricta**: Se leerá la marca de tiempo de creación del voto generada en el cliente y se contrastará contra la hora de cierre del bloque horario almacenada en Redis. Si el desfase excede los 10 minutos de gracia paramétricos fijados por las reglas de negocio, el voto es rechazado en el servidor y se purga del cliente.

### 4. Detección y Alertas de Alteración Crítica de Rankings
Durante el proceso de vaciado de colas asíncronas, el servicio calculará el vector de orden del ranking de ponencias del bloque antes y después de aplicar el lote de transacciones pendientes. Si se detecta una permutación en las posiciones del podio oficial (puestos 1, 2 o 3), el sistema persistirá una instancia de `RankingAlert` en MySQL y emitirá un broadcast inmediato del evento `RankingOrderAltered` a través de Reverb. El Dashboard administrativo capturará esta señal e inyectará de forma reactiva un banner de advertencia visual resaltando las ponencias modificadas.

## Complexity Tracking

*No se registran violaciones a los principios generales de la constitución del repositorio en este diseño técnico.*