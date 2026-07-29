# Implementation Plan: Admin GUI & UI Polish

**Branch**: `004-admin-gui-and-polish` | **Date**: 2026-07-27 | **Spec**: specs/004-admin-gui-and-polish/spec.md

## Summary

Desarrollar la interfaz de administración gráfica optimizada para Desktop y refinar la experiencia visual de la aplicación móvil. Técnicamente, esto implica la creación de un middleware de seguridad simple basado en variables de entorno, la implementación de un componente Livewire para la carga visual de archivos `.xlsx` y `.csv` (reutilizando la lógica de dominio existente), y la incorporación de un panel lateral (Slide-over) para el análisis cualitativo sin recargas. En el frontend móvil, se añadirán logotipos y transiciones Alpine.js garantizando el cumplimiento estricto de las directrices táctiles.

## Technical Context

**Language/Version**: PHP 8.3 / Laravel 11
**Primary Dependencies**: Livewire v4, Alpine.js v3, Tailwind CSS v3, `maatwebsite/excel` (opcional si se opta por parseo CSV nativo).
**Target Platform**: 
- `/admin/*`: Escritorio (Desktop-first), Grid layout ancho.
- `/`: Aplicación móvil (Mobile-first estricto, Portrait).
**Performance Goals**: Procesamiento de archivo de agenda en < 5 segundos. Renderizado de panel cualitativo < 200ms.

## Constitution Check

*GATE: Passed version 1.2.0*
- **CLI Interface Exception**: Se aplica la enmienda que autoriza una GUI para la carga de configuración en el módulo `/admin`.
- **Mobile-First Exception**: Se aplica la enmienda que exime a las rutas `/admin` del enfoque móvil, habilitando interfaces Desktop.
- **Auth Exception**: Se aplica la enmienda que permite la protección de rutas administrativas mediante un middleware simple contra `.env`.
- **Test-First Imperative**: Todos los componentes nuevos (Middleware y Livewire) serán diseñados tras escribir sus pruebas en PEST.
- **Zero AI Noise**: Se mantiene la prohibición de comentarios de IA en código fuente.

## Project Structure

### Documentation

[INICIO_DIAGRAMA_ARBOL]
specs/004-admin-gui-and-polish/
├── spec.md              # Especificación funcional
├── plan.md              # Este archivo
└── tasks.md             # Lista de ejecución atómica
[FIN_DIAGRAMA_ARBOL]

### Source Code

[INICIO_DIAGRAMA_ARBOL]
app/
├── Http/
│   ├── Middleware/
│   │   └── EnsureAdminPassword.php
│   └── Livewire/
│       ├── AdminSetup.php
│       └── AdminDashboard.php (Modificado)
resources/
└── views/
    ├── components/
    │   └── header.blade.php
    └── livewire/
        ├── admin-setup.blade.php
        ├── admin-dashboard.blade.php (Modificado)
        └── mobile-evaluator.blade.php (Modificado)
[FIN_DIAGRAMA_ARBOL]

## Architectural Decisions

1. **Autenticación Minimalista (`EnsureAdminPassword`)**: Para evitar el sobre-entrenamiento de la base de datos con tablas de usuarios, la ruta administrativa se protegerá comparando un input de sesión contra `env('ADMIN_PASSWORD')`.
2. **Reutilización del Core (`EvaluationService`)**: El componente visual `AdminSetup` no reimplementará lógica de parseo; simplemente validará el archivo temporal (XLSX/CSV) en Livewire y delegará la persistencia y purga de caché al servicio de dominio ya construido en la Feature 001.
3. **Drill-down Cualitativo Asíncrono**: En lugar de navegar a una nueva vista `/admin/talk/{id}`, la vista del dashboard cargará dinámicamente los comentarios de la charla seleccionada en una propiedad del componente y utilizará Alpine.js para deslizar un panel lateral sobre el ranking activo.

## Phase 5: User Story 4 - Edición Cualitativa de Charlas (Drill-down Edit)

**Objective**: Permitir la corrección en vivo de los datos de una charla directamente desde el panel de administración, invalidando la caché de Redis asociada sin afectar las evaluaciones existentes.

- **Autenticación**: Ruta protegida por `EnsureAdminPassword`.
- **Backend/Livewire**: Se extenderá el componente `AdminDashboard` (o un componente anidado `AdminEditTalk`) para manejar el estado del formulario de edición, aplicar reglas de validación estrictas e invalidar las llaves de caché correspondientes en Redis.
- **Frontend**: El formulario vivirá dentro del mismo Slide-over (Panel lateral) construido en la Fase 3, o a través de un modal nativo en la vista Desktop, evitando recargas de página.