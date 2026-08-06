# Feature Specification: 001-core-evaluation

**Feature Branch**: `001-core-evaluation`

**Created**: 2026-07-16

**Status**: Draft

**Input**: User description: "Configurar el repositorio del proyecto Vórtice Pulse para la carga de charlas de la agenda por consola y permitir la votación móvil inmediata de los asistentes mediante corazones (1 a 5) de forma anónima y sin autenticación."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Configuración y carga de charlas (Priority: P1)

Como organizador del evento, quiero importar el listado oficial de charlas y bloques horarios a través de la línea de comandos para que la base de datos se inicialice correctamente y el sistema exponga la información sin necesidad de una interfaz web administrativa.

**Why this priority**: Es la base de datos de datos de la que depende toda la aplicación. Sin charlas ni bloques horarios cargados, es imposible permitir cualquier tipo de votación.

**Independent Test**: Puede probarse de manera independiente ejecutando el comando de consola Artisan pasándole una ruta de archivo JSON estructurada y validando que los registros existan en la persistencia y la caché de Redis se limpie de inmediato.

**Acceptance Scenarios**:

1. **Given** que el organizador dispone de un archivo estructurado JSON con la agenda del día, **When** ejecuta el comando Artisan de inicialización apuntando a dicho archivo, **Then** el sistema procesa la información, persiste las charlas y asocia cada una a su bloque horario asignado.
2. **Given** que la base de datos contenía charlas previas de pruebas, **When** se ejecuta de nuevo el comando Artisan de carga, **Then** el sistema realiza un refresco limpio eliminando la caché de Redis y los registros anteriores para evitar colisiones.

---

### User Story 2 - Evaluación instantánea por corazones (Priority: P1)

Como asistente de Vórtice 2026, quiero calificar la charla que acabo de escuchar seleccionando una valoración de 1 a 5 corazones de manera móvil y táctil para que mi retroalimentación sea instantánea y sin la fricción de tener que registrarme o iniciar sesión.

**Why this priority**: Es el flujo de valor primordial del asistente en el evento. Representa el núcleo mínimo de interacción de la aplicación móvil que cumple con el objetivo de capturar el "latido" en tiempo real.

**Independent Test**: Puede probarse de forma independiente navegando directamente a la ruta de evaluación de una charla activa desde un navegador web móvil, seleccionando un valor de corazones y enviando el formulario.

**Acceptance Scenarios**:

1. **Given** que el asistente ha accedido a la interfaz táctil de una charla habilitada, **When** selecciona una puntuación entre 1 y 5 corazones y presiona el botón de confirmación, **Then** el sistema almacena el voto vinculándolo de manera única a la firma efímera de su dispositivo, muestra una animación de éxito y bloquea futuros envíos.
2. **Given** que un asistente intenta enviar el formulario de evaluación de una charla activa, **When** no selecciona ninguna puntuación de corazones (valor nulo o cero), **Then** el sistema bloquea el envío, resalta visualmente el selector táctil de corazones y no altera los registros de la base de datos.

---

### User Story 3 - Limitación por bloques de tiempo (Priority: P2)

Como organizador del evento, quiero restringir la evaluación de cada charla para que solo se permita votar durante la duración de la ponencia y hasta un máximo de 10 minutos posteriores a su finalización programada.

**Why this priority**: Garantiza la integridad y frescura de los datos recopilados, evitando que los asistentes alteren las métricas calificando charlas de bloques pasados horas después de haber finalizado.

**Independent Test**: Puede probarse manipulando temporalmente la hora del sistema en el entorno de pruebas para intentar evaluar una charla fuera de su ventana activa de 10 minutos.

**Acceptance Scenarios**:

1. **Given** que un bloque de horario finalizó hace más de 10 minutos, **When** un asistente ingresa a la URL de una charla de ese bloque e intenta votar, **Then** el sistema bloquea el envío, deshabilita los controles interactivos y muestra un mensaje amigable indicando que la ventana de tiempo ha expirado.

---

### Edge Cases

- **Acceso anticipado a la evaluación**: ¿Qué ocurre si un asistente ingresa al enlace antes de la hora de inicio oficial? El sistema denegará la votación, inhabilitará el formulario y desplegará una vista informativa móvil con la cuenta regresiva hacia el inicio de la sesión.
- **Pérdida de conectividad durante el envío**: Si el dispositivo móvil del asistente se queda sin conexión al pulsar "Enviar", Alpine.js capturará el estado sin conexión y retendrá la petición encolada temporalmente en memoria para reintentar de forma segura en cuanto se restablezca el enlace.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: El sistema MUST permitir la carga e inicialización de la agenda mediante consola utilizando comandos de Laravel Artisan.
- **FR-002**: El sistema MUST proporcionar una interfaz móvil optimizada estrictamente para orientación vertical (Portrait) de fácil uso táctil.
- **FR-003**: El sistema MUST admitir valoraciones obligatorias de 1 a 5 estrellas/corazones acompañadas de preguntas cualitativas opcionales de texto libre.
- **FR-004**: El sistema MUST calcular una firma anónima de dispositivo (`device_signature`) mediante persistencia local de token en el cliente y cifrado hash en backend para mitigar la duplicidad de votos de manera transparente sin almacenar información de identificación personal.
- **FR-005**: El sistema MUST restringir la ventana activa de votación impidiendo evaluaciones antes de la hora de inicio de la charla o pasados los 10 minutos de la hora de finalización establecida.

### Key Entities

- **Talk**: Representa una ponencia del evento. Atributos: `id`, `title`, `speaker`, `start_time` (ISO 8601), `end_time` (ISO 8601).
- **Evaluation**: Representa el voto emitido por un asistente. Atributos: `id`, `talk_id` (relación), `rating` (entero del 1 al 5), `liked_aspects` (opcional), `improvement_aspects` (opcional), `device_signature` (hash anónimo).

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Un asistente puede completar todo el flujo táctil de evaluación de una charla en menos de 1 minuto bajo condiciones de red móvil normales del evento.
- **SC-002**: La base de datos puede asimilar de manera segura ráfagas masivas de hasta 300 evaluaciones concurrentes en los minutos de cierre de los bloques sin pérdida de datos ni caídas de rendimiento.
- **SC-003**: El mecanismo de firma de dispositivo mitiga al menos el 95% de los intentos informales de duplicidad de votos en una misma charla desde el mismo terminal móvil.

## Assumptions

- Se asume que los asistentes disponen de un dispositivo inteligente con navegador web móvil estándar.
- Se asume que el servidor de base de datos de producción cuenta con una base de datos relacional MySQL y una instancia de Redis configurada y disponible de forma local.