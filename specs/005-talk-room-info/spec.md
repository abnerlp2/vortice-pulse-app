# Feature Specification: 005-talk-room-info

**Feature Branch**: `005-talk-room-info`

**Created**: 2026-08-11

**Status**: Draft

**Input**: User description: "Agregar la información de la sala/auditorio a cada charla en las tres vistas: app móvil de asistentes, leaderboard público y panel de admin"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Visualización de la sala en la app móvil de asistentes (Priority: P1)

Como asistente al evento desde mi dispositivo móvil, quiero ver claramente la sala o auditorio asignado a cada charla en la agenda activa y en la vista de evaluación, para poder ubicar físicamente el lugar donde se imparte cada ponencia.

**Why this priority**: Es la vista principal para los asistentes durante el evento. Conocer la ubicación física de la charla permite a los usuarios orientarse rápidamente dentro de la sede.

**Independent Test**: Se MUST escribir una prueba en PEST PHP que verifique el renderizado del campo `room` en los componentes Livewire móviles. Manualmente, verificar en orientación Portrait que el nombre de la sala se muestra sin solapar las áreas táctiles de 44x44px.

**Acceptance Scenarios**:

1. **Given** que una charla tiene asignada una sala (por ejemplo, "Auditorio Principal"), **When** un asistente consulta la agenda móvil o la pantalla de evaluación, **Then** el sistema muestra visiblemente "Auditorio Principal" en la tarjeta de la charla junto al nombre del expositor y el horario.
2. **Given** que una charla no tiene asignada una sala (campo nulo o vacío), **When** el asistente visualiza la charla en la interfaz móvil, **Then** el sistema muestra un indicador por defecto amigable (por ejemplo, "Sala por confirmar") sin alterar el diseño visual.

---

### User Story 2 - Ubicación de la charla en el leaderboard público (Priority: P1)

Como participante o espectador del evento que observa la pantalla pública del leaderboard, quiero que cada charla listada en el ranking y en los bloques destacados muestre la sala o auditorio correspondiente, para identificar fácilmente dónde se llevaron a cabo o se imparten las charlas mejor valoradas.

**Why this priority**: El leaderboard público se proyecta en pantallas grandes del evento para mantener informada a la audiencia. Incluir la sala en las tarjetas de charla brinda contexto espacial inmediato a los asistentes.

**Independent Test**: Puede probarse accediendo al leaderboard público y validando que todas las charlas calificadas o activas proyectadas incluyen la etiqueta con el nombre de su sala o auditorio sin solapar otros elementos de la pantalla.

**Acceptance Scenarios**:

1. **Given** que el leaderboard público proyecta las mejores charlas o el estado en vivo, **When** las tarjetas de charla se renderizan en pantalla, **Then** cada tarjeta exhibe de forma prominente la sala o auditorio asignado.
2. **Given** que se actualiza el ranking en tiempo real, **When** una charla cambia de posición o se actualiza su promedio, **Then** la información de la sala permanece visible y consistente en la tarjeta de la charla.

---

### User Story 3 - Gestión de sala/auditorio en el panel de administración (Priority: P2)

Como administrador del evento en el panel `/admin`, quiero ver, editar e importar la información de la sala o auditorio para cada charla, de modo que pueda mantener actualizada la ubicación física de las ponencias en todo el sistema.

**Why this priority**: Permite a los organizadores cargar las ubicaciones masivamente (vía Excel/configuración) o corregir la sala de una charla desde la interfaz de gestión administrativa.

**Independent Test**: Puede probarse importando un archivo de charlas con columna de sala o editando el campo de sala de una charla desde el dashboard de administración, verificando que el cambio se persista y se refleje en las vistas públicas.

**Acceptance Scenarios**:

1. **Given** que un administrador accede al panel de administración `/admin`, **When** consulta el listado de charlas o la vista de configuración, **Then** el sistema muestra la columna/campo de la sala asignada a cada charla y permite modificarla.
2. **Given** que el administrador realiza una importación masiva de la agenda (vía Excel o JSON), **When** el archivo incluye la columna de sala o auditorio, **Then** el sistema asocia correctamente cada ubicación a su respectiva charla durante el proceso de carga.

---

### Edge Cases

- **Nombres de sala extensos**: Si el nombre de una sala es inusualmente largo (por ejemplo, "Auditorio Internacional Polivalente Piso 3"), el diseño debe truncarlo o ajustarlo responsivamente sin romper la estructura de las tarjetas en móvil ni en el leaderboard.
- **Charlas sin sala especificada (Compatibilidad con datos existentes)**: Si existen registros previos de charlas en la base de datos sin valor de sala, el sistema debe asignar o mostrar un valor por defecto ("Por confirmar") sin generar errores de ejecución o valores nulos no formateados.
- **Importación con columnas faltantes**: Si se realiza una importación masiva y la columna de sala no está presente en el archivo, la importación debe completarse exitosamente dejando el campo de sala como nulo o asignando el valor por defecto.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001 (Persistencia)**: El sistema MUST almacenar la sala (`room`, nullable string) mediante una **nueva migración** de Laravel que altere la tabla `talks` existente, preservando la data actual.
- **FR-002 (UI Móvil y Touch Targets)**: El sistema MUST desplegar la sala en la app móvil (`ActiveAgendaLanding` y `MobileEvaluator`). La adición de esta etiqueta visual **no debe alterar ni invadir el área táctil mínima de 44x44 píxeles** de los botones de calificación (corazones).
- **FR-003 (Leaderboard)**: El sistema MUST incluir la sala en las tarjetas de charla proyectadas en el leaderboard público (`PublicLeaderboard`) a través del canal de WebSockets (Reverb) existente.
- **FR-004 (Admin GUI & Library-First)**: El panel administrativo MUST permitir ver y editar la sala. La importación de Excel MUST ser delegada a la capa de servicios del dominio (ej. `EvaluationService`), respetando el principio Library-First.
- **FR-005 (Fallback)**: El sistema MUST manejar la ausencia de sala mostrando la etiqueta por defecto "Por confirmar" desde la capa del Modelo o Accessor, evitando lógica condicional redundante en las vistas Blade.

### Key Entities

- **Talk**: Ponencia del evento. Atributos: `id`, `time_block_id`, `title`, `speaker`, `room` (cadena de texto opcional que indica la sala o auditorio, e.g., "Auditorio A"), `start_time`, `end_time`.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: El 100% de las charlas mostradas en la app móvil, el leaderboard público y el panel de administración exhiben su sala o el indicador por defecto sin inconsistencias visuales.
- **SC-002**: Los asistentes pueden identificar la ubicación física (sala/auditorio) de una charla en la vista móvil en menos de 2 segundos.
- **SC-003**: La adición del campo de sala no afecta el rendimiento de carga ni las sincronizaciones en tiempo real del leaderboard público.
- **SC-004**: Toda la funcionalidad cuenta con pruebas de integración en PEST PHP en estado "Green" antes de dar la característica por terminada, respetando la Ley Test-First.

## Assumptions

- Los nombres de las salas/auditorios son etiquetas de texto corto o mediano (e.g. "Sala 1", "Auditorio Principal").
- El formato de importación masiva existente (Excel/JSON) se extenderá opcionalmente con la columna/propiedad para la sala sin romper archivos de versión anterior.
