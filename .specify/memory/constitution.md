# Vórtice Pulse Constitution

## Core Principles

### I. Library-First
Toda funcionalidad debe comenzar como una biblioteca independiente y autónoma. La lógica crítica de negocio —como el cálculo de promedios de votación, la generación segura de firmas efímeras de dispositivos (`device_signature`) y la evaluación de las ventanas de tiempo de 10 minutos— se diseñará en forma de clases puras de PHP. Esto garantiza que el núcleo de la aplicación permanezca desacoplado de las dependencias directas de la capa de presentación del framework.

### II. CLI Interface
Toda biblioteca debe exponer su funcionalidad principal a través de una interfaz de línea de comandos. El conjunto completo de servicios del sistema se estructurará para interactuar a través de comandos Artisan personalizados de Laravel. Se garantizará la existencia de comandos específicos para vaciar caché, forzar el estado de Redis y simular cargas masivas de estrés (hasta 300 usuarios concurrentes enviando evaluaciones de manera simultánea) antes de acoplar el frontend.
*(Excepción: El módulo de configuración administrativa puede consumir los Services directamente vía GUI Livewire para facilitar la carga de archivos Excel y la gestión del evento).*

### III. Test-First (NON-NEGOTIABLE)
Se exige de forma estricta un ciclo de desarrollo guiado por pruebas. Queda prohibido escribir código de producción sin contar previamente con una prueba automatizada escrita en PEST PHP que falle. El ciclo de desarrollo Red-Green-Refactor gobernará la construcción de cada controlador, clase de servicio y componente reactivo.

### IV. Integration-First Testing
Priorizar el comportamiento del sistema en entornos reales. Se dará preferencia a la suite de pruebas de integración que validen de manera integral la persistencia en la base de datos de la entidad `evaluations` y el comportamiento reactivo en tiempo real de los componentes Livewire v4, evitando el uso excesivo de dobles de prueba (mocks).

### V. Simplicity Over Cleverness
Evitar funcionalidades especulativas y abstracciones complejas. La simplicidad del código de producción tiene prioridad absoluta sobre la generalización innecesaria. Se prohíbe el diseño de patrones arquitectónicos pensados para prever futuros frameworks o motores alternativos de persistencia. El desarrollo se acoplará de forma natural a las características estándar de Laravel 11 y Livewire v4 para mantener el contexto del agente de IA predecible y eficiente.

## Technical Constraints

### Architecture Stack Requirements
*   **Backend framework**: Laravel 11 (PHP 8.3) con tipado estricto a nivel de métodos, propiedades y retornos.
*   **Reactividad y frontend**: Livewire v4 y Alpine.js para un dinamismo ágil.
*   **Estilos visuales**: Tailwind CSS optimizado de manera estricta para resoluciones de dispositivos móviles.
*   **Tiempo real**: Laravel Reverb integrado para la sincronización reactiva de métricas agregadas hacia el dashboard público.
*   **Caché e infraestructura**: Redis para mitigar lecturas masivas y gestionar la cola de la base de datos MySQL frente a la concurrencia de accesos.

### Code Documentation Policy (Zero AI Noise)
Queda estrictamente prohibido incluir comentarios explicativos, justificaciones contextuales o diarios de desarrollo generados por el agente de IA dentro de los archivos de código fuente de producción (`.php`, `.blade.php`, etc.). El código expresará su propósito mediante un nombrado semántico estructurado. Toda la documentación conceptual de negocio e ingeniería residirá exclusivamente en archivos Markdown dentro del repositorio para preservar su valor académico.

## Development Workflow

### Frictionless and Mobile-First Gates
*   **Optimización móvil**: El diseño del frontend se optimizará con un enfoque estricto Mobile-First en orientación vertical (Portrait). *(Excepción: El dashboard de administración `/admin` está exento de esta regla y se optimizará exclusivamente para pantallas de escritorio/Desktop).*
*   **Áreas interactivas**: Todos los elementos táctiles interactivos (corazones de calificación, campos de formulario y botones de envío) contarán con un área interactiva de acción mínima de **44x44** píxeles para evitar toques accidentales con el pulgar.
*   **Sin autenticación**: Se prohíbe el uso de sistemas tradicionales de inicio de sesión o recopilación de datos personales para los asistentes del evento. La validación de unicidad de voto se resolverá de forma transparente mediante la firma segura del dispositivo. *(Excepción: El panel de administración `/admin` debe estar protegido por un middleware de contraseña o autenticación básica para prevenir acceso público no autorizado).*

## Governance

*   La especificación (`spec.md`) constituye la única verdad funcional del proyecto. Ninguna tarea o línea de código se generará si no cuenta con un criterio de aceptación respaldado por la especificación.
*   Cualquier excepción de complejidad o cambio sobre la arquitectura del stack deberá estar explícitamente documentada en el registro de la especificación técnica correspondiente.

## Amendments

*   **2026-07-16**: Queda estrictamente prohibido introducir lógica condicional o valores estáticos hardcodeados en los archivos de producción de la aplicación (app/) con el único propósito de satisfacer un entorno de testing. Los datos de prueba deben ser inyectados dinámicamente y de forma realista por el propio framework de pruebas (en las clases *Test.php) antes de la ejecución del flujo a probar.
*   **2026-07-16**: El flujo de desarrollo debe respetar la separación de responsabilidades. Los prompts de interacción diaria con el agente de VS Code no deben instruir de forma explícita qué sintaxis usar, qué archivos crear, o qué dependencias inyectar de manera manual si esta información ya reside en plan.md o tasks.md. El agente debe derivar siempre su comportamiento leyendo autónomamente el espacio de trabajo local para evitar la sobreespecificación y la degradación de la calidad del plan.
*   **2026-07-27**: Se relaja la restricción "CLI Interface" y "Mobile-First" exclusivamente para el módulo de administración (`/admin`), permitiendo la creación de una GUI en formato Desktop para la importación visual de Excel y el drill-down cualitativo. Se autoriza el uso de un mecanismo de protección básico (Auth) para esta ruta.

**Version**: 1.2.0 | **Ratified**: 2026-07-16 | **Last Amended**: 2026-07-27