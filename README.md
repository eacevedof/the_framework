THE FRAMEWORK
=============

Framework PHP en arquitectura MVC. 
Versión pública 1.0.0
Versión privada 1.7.12b
Última actualización: 30-10-2014 11:12 (SPAIN)

Resumiendo:

El framework tiene una arquitectura MVC y está pensado para trabajar con POO y evitar en lo posible el código ESPAGUETI.

La distribución es la siguiente:

    tuproyecto/
        /devdocuments           ->  Esta carpeta tiene como fin guardar el analisis funcional y orgánico del proyecto.
        /logs                   ->  Guarda distintos tipos de logs: customizados, errores, consultas, sesion, shellscripts.
        /shellscripts           ->  Almacena los archivos shell como: .bat, .sh, .cmd etc. Que se ejecutarán para llamar a un archivo .php.
        /sql                    ->  Consultas que se suelen utilizar frecuentemente.
        /the_application        ->  Guarda la libreria de tu aplicación.
            /behaviours         ->  Clases tipo "behaviour". Son clases a nivel de modelos. Son como ayudantes de los modelos.
            /boot               ->  Archivos de constantes, funciones y rutas. Las primeras a nivel global.
            /components         ->  Clases tipo "component". Son clases a nivel de controlador. Equivalente a ayudantes de controlador.
            /controllers        ->  Clases tipo "controller". Clases "modulares" donde se realizará la mayor parte de la lógica de negocio.
            /elements           ->  Son archivos con código html y/o php a utilizar con includes. Se suele utilizar en los layouts
            /helpers            ->  Clases de tipo "helper". Están a nivel de las vistas. Ayudantes de pintado de interfaces.
            /models             ->  Clases tipo "model". Su funcion es la interacción directa con la tabla asociada 1 tabla = 1 modelo.
            /themes             ->  Así como Wordpress admite temas. La intención es la misma, poder almacenar distintos diseños con sus respectivas distribuciones.
            /translations       ->  Archivos .php con constantes de traducciones.
                /app            ->  Traducciones a nivel de aplicación
                /english        ->  Traduccion en inglés.
            /vendors            ->  Tiene como fin almacenar paquetes de código php de terceros.  Por ejemplo FPDF
            /views              ->  Los archivos que intervienen en la creación de lo que recibirá el cliente.
                /_base          ->  Guarda plantillas estandard. Errores como 401,404 etc. Vistas tipo listado, inserción, actualización, catálogo etc.
                /_js            ->  Archivos que se volcarán en la carpeta pública (the_public/the_application/)
            version_info.txt    ->  Información de versión de tu proyecto.

        /the_framework          ->  Librerias básicas del framework. La teoria es que solo se toque el archivo "theframework_config.php"
            /constants          ->  Constantes globales del framework.
            /exec_onboot        ->  Código que se ejecutará al arrancar.
            /functions          ->  Funciones globales que se pueden utilizar en cualquier parte.
            /html               ->  Archivos y librerias js de terceros y propias que se pueden cargar en las vistas.
            /mvc                ->  Librería principal. Componentes etc..
                /components     ->  Contiene las clases hijas del componente principal.
                /custom         ->  De momento no se usa.
                /helpers        ->  Contiene las clases hijas del helper principal.
                /main           ->  Clases troncales tipo MAIN. Son clases primitivas que heredan de theframework.php.
            /vendors            ->  Clases y/o archivos PHP de terceros que vienen con el framework.
            /plugins            ->  Parecido a vendors. Falta terminar su funcionalidad pero la intención es que sea capaz de tratar cierto código como lo hace wordpress.
            /vars               ->  De momento no se usa.  Esta pensada para guardar variables globales a incluir en el arranque.

        /the_public
            /cache              ->  De momento no se usa.   Falta impementar
            /downloads          ->  Los archivos que estáran disponibles para descargas
            /images             ->  Imágenes varias
                /custom         ->  Imágenes del diseño original que se han retocado o imágenes nuevas incluidas en el diseño
                /huraga         ->  Imágenes originales de la plantilla que se está usando
                /jquery         ->  Imágenes que se usan en la libreria de jquery (por ejemplo jquery.ui)
                /pictures       ->  Imágenes propias de la apliación.
                    /module_x   ->  Imágenes que se usarán en el módulo X (un módulo creado en la app)
            /js                 ->  
                /custom
                /highchart
                /huraga
                /jquery
                /the_application
                /the_framework
            /phpdts
            /style
            /uploads
            .htaccess           ->  En caso de querer utilizar "permalink" solo válido para apache. No para IIS.
            bootpaths.php       ->
            bootpaths_libs.php  ->
            index.php           ->
            version_info.txt    ->  Versión de la distribución de carpetas hijas en "the_public"

Conforme vaya actualizando la versión privada iré añadiendo los cambios en la pública.

[[Eduardo A. F.]]