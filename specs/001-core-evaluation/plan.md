# Implementation Plan: 001-core-evaluation

**Branch**: 001-core-evaluation | **Date**: 2026-07-16 | **Spec**: specs/001-core-evaluation/spec.md

**Input**: Feature specification from /specs/001-core-evaluation/spec.md

**Note**: This template is filled in by the /speckit.plan command; its definition describes the execution workflow.

## Summary

Establecer la infraestructura de persistencia, comandos y componentes en tiempo real para Vórtice Pulse. El enfoque técnico consiste en un comando Artisan de Laravel optimizado para deserializar y validar el archivo de configuración JSON, un modelo relacional respaldado por MySQL para las charlas y evaluaciones, y un componente móvil interactivo Livewire v4 potenciado con Alpine.js para gestionar la interfaz táctil. El control de ventanas temporales de votación y la prevención de colisiones por firmas de dispositivo se optimizarán mediante almacenamiento persistente y caché rápida en Redis.

## Technical Context

**Language/Version**: PHP 8.3 / Laravel 11

**Primary Dependencies**: Livewire v4, Alpine.js, Tailwind CSS, Laravel Reverb, Predis

**Storage**: MySQL 8.0 (Persistencia transaccional), Redis 7.2 (Caché rápida de firmas de dispositivo y control de ventanas de tiempo)

**Testing**: PEST PHP (Pruebas unitarias, de integración y de componentes Livewire)

**Target Platform**: Mobile-first Web Application (Optimizado para Safari iOS y Chrome Android en orientación vertical)

**Project Type**: web-service / mobile-app

**Performance Goals**: Latencia de registro de evaluación <100ms, soporte de ráfagas concurrentes de hasta 300 peticiones por segundo en el cierre de bloques horários.

**Constraints**: <150ms p95 para procesamiento de WebSocket a través de Laravel Reverb, cero almacenamiento de datos personales (PII) bajo cumplimiento estricto de anonimato.

**Scale/Scope**: 1 evento principal, ~20 charlas, ~1000 asistentes activos concurrentes evaluando de manera simultánea.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

* **Library-First Principle**: La lógica de negocio principal para la gestión de evaluaciones e importación se encapsulará en un Service class desacoplado (Core/Evaluation/Services) antes de exponerse en controladores, componentes de Livewire o comandos de consola.
* **CLI Interface Mandate**: Toda la inicialización y administración de la agenda del evento es exclusivamente operable a través del comando Artisan pulse:import-agenda.
* **Test-First Imperative**: Se generará la estructura de pruebas en PEST validando el service de importación y el registro de firmas efímeras antes de escribir la lógica del componente Livewire o del front-end.
* **Zero AI Noise**: Queda prohibida la introducción de cualquier comentario explicativo o redundante de IA dentro del código PHP o Javascript generado.

## Project Structure

### Documentation (this feature)

[INICIO_DIAGRAMA_ARBOL]
specs/001-core-evaluation/
├── plan.md              # This file
├── research.md          # Investigación de rendimiento del Hash de firmas en PHP/Redis
├── data-model.md        # Definición detallada del esquema de base de datos
├── quickstart.md        # Guía de inicialización rápida del entorno local
├── contracts/           
│   └── ImportAgendaInterface.php
└── tasks.md             # Sequencia ordenada de tareas atómicas para implementación
[FIN_DIAGRAMA_ARBOL]

### Source Code (repository root)

[INICIO_DIAGRAMA_ARBOL]
app/
├── Console/
│   └── Commands/
│       └── ImportAgenda.php
├── Core/
│   └── Evaluation/
│       ├── Services/
│       │   └── EvaluationService.php
│       └── Contracts/
│           └── EvaluationRepositoryInterface.php
├── Http/
│   └── Livewire/
│       └── MobileEvaluator.php
└── Models/
    ├── Talk.php
    └── Evaluation.php

database/
├── migrations/
│   ├── 2026_07_16_000001_create_talks_table.php
│   └── 2026_07_16_000002_create_evaluations_table.php
└── seeders/

resources/
└── views/
    ├── livewire/
    │   └── mobile-evaluator.blade.php
    └── layouts/
        └── app.blade.php

tests/
├── Feature/
│   ├── ImportAgendaTest.php
│   └── MobileEvaluatorTest.php
└── Unit/
    └── EvaluationServiceTest.php
[FIN_DIAGRAMA_ARBOL]

**Structure Decision**: Se ha seleccionado la estructura estándar de monolito Laravel 11 adaptando un directorio de dominio aislado (app/Core/Evaluation) para encapsular las reglas de negocio, aislando así la capa de transporte (Consola/Livewire) de la lógica pura del servicio de evaluación.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

*No se registran violaciones a la constitución en este diseño técnico.*