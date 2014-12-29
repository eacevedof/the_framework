THE FRAMEWORK
=============

Framework PHP en arquitectura MVC. 
Versi�n p�blica 1.0.0
Versi�n privada 1.7.12b
�ltima actualizaci�n: 30-10-2014 11:12 (SPAIN)

Resumiendo:

El framework tiene una arquitectura MVC y est� pensado para trabajar con POO y evitar en lo posible el c�digo ESPAGUETI.

La distribuci�n es la siguiente:

    tuproyecto/
        /devdocuments           ->  Esta carpeta tiene como fin guardar el analisis funcional y org�nico del proyecto.
        /logs                   ->  Guarda distintos tipos de logs: customizados, errores, consultas, sesion, shellscripts.
        /shellscripts           ->  Almacena los archivos shell como: .bat, .sh, .cmd etc. Que se ejecutar�n para llamar a un archivo .php.
        /sql                    ->  Consultas que se suelen utilizar frecuentemente.
        /the_application        ->  Guarda la libreria de tu aplicaci�n.
            /behaviours         ->  Clases tipo "behaviour". Son clases a nivel de modelos. Son como ayudantes de los modelos.
            /boot               ->  Archivos de constantes, funciones y rutas. Las primeras a nivel global.
            /components         ->  Clases tipo "component". Son clases a nivel de controlador. Equivalente a ayudantes de controlador.
            /controllers        ->  Clases tipo "controller". Clases "modulares" donde se realizar� la mayor parte de la l�gica de negocio.
            /elements           ->  Son archivos con c�digo html y/o php a utilizar con includes. Se suele utilizar en los layouts
            /helpers            ->  Clases de tipo "helper". Est�n a nivel de las vistas. Ayudantes de pintado de interfaces.
            /models             ->  Clases tipo "model". Su funcion es la interacci�n directa con la tabla asociada 1 tabla = 1 modelo.
            /themes             ->  As� como Wordpress admite temas. La intenci�n es la misma, poder almacenar distintos dise�os con sus respectivas distribuciones.
            /translations       ->  Archivos .php con constantes de traducciones.
                /app            ->  Traducciones a nivel de aplicaci�n
                /english        ->  Traduccion en ingl�s.
            /vendors            ->  Tiene como fin almacenar paquetes de c�digo php de terceros.  Por ejemplo FPDF
            /views              ->  Los archivos que intervienen en la creaci�n de lo que recibir� el cliente.
                /_base          ->  Guarda plantillas estandard. Errores como 401,404 etc. Vistas tipo listado, inserci�n, actualizaci�n, cat�logo etc.
                /_js            ->  Archivos que se volcar�n en la carpeta p�blica (the_public/the_application/)
            version_info.txt    ->  Informaci�n de versi�n de tu proyecto.

        /the_framework          ->  Librerias b�sicas del framework. La teoria es que solo se toque el archivo "theframework_config.php"
            /constants          ->  Constantes globales del framework.
            /exec_onboot        ->  C�digo que se ejecutar� al arrancar.
            /functions          ->  Funciones globales que se pueden utilizar en cualquier parte.
            /html               ->  Archivos y librerias js de terceros y propias que se pueden cargar en las vistas.
            /mvc                ->  Librer�a principal. Componentes etc..
                /components     ->  Contiene las clases hijas del componente principal.
                /custom         ->  De momento no se usa.
                /helpers        ->  Contiene las clases hijas del helper principal.
                /main           ->  Clases troncales tipo MAIN. Son clases primitivas que heredan de theframework.php.
            /vendors            ->  Clases y/o archivos PHP de terceros que vienen con el framework.
            /plugins            ->  Parecido a vendors. Falta terminar su funcionalidad pero la intenci�n es que sea capaz de tratar cierto c�digo como lo hace wordpress.
            /vars               ->  De momento no se usa.  Esta pensada para guardar variables globales a incluir en el arranque.

        /the_public
            /cache              ->  De momento no se usa.   Falta impementar
            /downloads          ->  Los archivos que est�ran disponibles para descargas
            /images             ->  Im�genes varias
                /custom         ->  Im�genes del dise�o original que se han retocado o im�genes nuevas incluidas en el dise�o
                /huraga         ->  Im�genes originales de la plantilla que se est� usando
                /jquery         ->  Im�genes que se usan en la libreria de jquery (por ejemplo jquery.ui)
                /pictures       ->  Im�genes propias de la apliaci�n.
                    /module_x   ->  Im�genes que se usar�n en el m�dulo X (un m�dulo creado en la app)
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
            .htaccess           ->  En caso de querer utilizar "permalink" solo v�lido para apache. No para IIS.
            bootpaths.php       ->
            bootpaths_libs.php  ->
            index.php           ->
            version_info.txt    ->  Versi�n de la distribuci�n de carpetas hijas en "the_public"

Conforme vaya actualizando la versi�n privada ir� a�adiendo los cambios en la p�blica.

[[Eduardo A. F.]]