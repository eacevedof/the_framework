<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.2.6
 * @file theframework_config.php 
 * @tfwversion  1.7.5 16-08-2014 02:06 SPAIN
 * @date 07-10-2014 22:15 (SPAIN)
 * @observations: The Framework config file
 */

//=================
//      CONFIG
//=================
//DIRECTORY SEPARATOR LINUX: / (sensible) - WINDOWS: \\ ó /
//He utilizado el ds de linux en windows y funciona
define("TFW_DS",DIRECTORY_SEPARATOR);
//Indica si el acceso a modulos y la app de los usuarios se realizará utilizando la bd
define("TFW_DB_USERVALIDATE",1);

//=================
//    ENVIROMENT
//=================
//define("TFW_CONFIG_ENVIROMENT","10");     //DEVELOPER MSSQL 
define("TFW_CONFIG_ENVIROMENT","20w");   //DEVELOPER MYSQL WINDOWS
//define("TFW_CONFIG_ENVIROMENT","20l");   //DEVELOPER MYSQL LINUX
//define("TFW_CONFIG_ENVIROMENT","30");   //CLIENT - TEST
//define("TFW_CONFIG_ENVIROMENT","50");   //CLIENT - PRODUCTION

switch(TFW_CONFIG_ENVIROMENT) 
{
    //DEVELOPER MSSQL 
    case "devmmsql":
    case "10":
        define("TFW_DOMAIN","localhost");
        define("TFW_PUBLIC_DOMAINIP","127.0.0.1");
        define("TFW_IS_PERMALINK",0);

        define("TFW_ENVIROMENT","local");
        define("TFW_DB_NAME","theframework");
        define("TFW_DB_USER","sa");
        define("TFW_DB_PASSWORD","pass2005");
        define("TFW_DB_SERVER","MYPCSERVER");
        define("TFW_DB_TYPE","mssql");       
        
        define("TFW_FOLDER_PROJECT","prj_theframework");
        define("TFW_PATH_FOLDER_PROJECT","C:/Inetpub/wwwroot/".TFW_FOLDER_PROJECT);
        define("TFW_PATH_FOLDER_LOG","C:/Inetpub/wwwroot/".TFW_FOLDER_PROJECT."/logs");
        define("TFW_PATH_FOLDER_UPLOAD","C:/Inetpub/wwwroot/".TFW_FOLDER_PROJECT."/the_public/uploads");
        define("TFW_PATH_FOLDER_PICTURES","C:/Inetpub/wwwroot/".TFW_FOLDER_PROJECT."/the_public/images/pictures");
    break;
    //DEVELOPER MYSQL WINDOWS
    case "devmysql_w":
    case "20w":
        define("TFW_DOMAIN","theframework.loc");
        define("TFW_PUBLIC_DOMAINIP","127.0.0.1");
        define("TFW_IS_PERMALINK",1);

        define("TFW_ENVIROMENT","local");
        define("TFW_DB_NAME","theframework");
        define("TFW_DB_USER","root");
        define("TFW_DB_PASSWORD","");
        define("TFW_DB_SERVER","localhost");
        define("TFW_DB_TYPE","mysql");
        
        define("TFW_FOLDER_PROJECT","prj_theframework");
        /*
        define("TFW_PATH_FOLDER_PROJECT","C:/Inetpub/wwwroot/".TFW_FOLDER_PROJECT);
        define("TFW_PATH_FOLDER_LOG","C:/Inetpub/wwwroot/".TFW_FOLDER_PROJECT."/logs");
        define("TFW_PATH_FOLDER_UPLOAD","C:/Inetpub/wwwroot/".TFW_FOLDER_PROJECT."/the_public/uploads");
        define("TFW_PATH_FOLDER_PICTURES","C:/Inetpub/wwwroot/".TFW_FOLDER_PROJECT."/the_public/images/pictures");
        */
        define("TFW_PATH_FOLDER_PROJECT","C:/xampp/htdocs/".TFW_FOLDER_PROJECT);
        define("TFW_PATH_FOLDER_LOG","C:/xampp/htdocs/".TFW_FOLDER_PROJECT."/logs");
        define("TFW_PATH_FOLDER_UPLOAD","C:/xampp/htdocs/".TFW_FOLDER_PROJECT."/the_public/uploads");
        define("TFW_PATH_FOLDER_PICTURES","C:/xampp/htdocs/".TFW_FOLDER_PROJECT."/the_public/images/pictures");
        
    break;
    //DEVELOPER MYSQL LINUX
    case "devmysql_l":
    case "20l":
        define("TFW_DOMAIN","theframework.loc");
        define("TFW_PUBLIC_DOMAINIP","127.0.0.1");
        define("TFW_IS_PERMALINK",1);

        define("TFW_ENVIROMENT","local");
        define("TFW_DB_NAME","theframework");
        define("TFW_DB_USER","root");
        define("TFW_DB_PASSWORD","root");
        define("TFW_DB_SERVER","127.0.0.1");
        define("TFW_DB_TYPE","mysql");
        
        define("TFW_FOLDER_PROJECT","prj_theframework");
        define("TFW_PATH_FOLDER_PROJECT","/Applications/MAMP/htdocs/".TFW_FOLDER_PROJECT);
        define("TFW_PATH_FOLDER_LOG","/Applications/MAMP/htdocs/".TFW_FOLDER_PROJECT."/logs");
        define("TFW_PATH_FOLDER_UPLOAD","/Applications/MAMP/htdocs/".TFW_FOLDER_PROJECT."/the_public/uploads");
        define("TFW_PATH_FOLDER_PICTURES","/Applications/MAMP/htdocs/".TFW_FOLDER_PROJECT."/the_public/images/pictures");        
    break;
    //CLIENT - TEST
    case "clienttest":
    case "30":

    break;
    //CLIENT PRODUCTION
    case "clientproduction":
    case "50":
       
    break;

    default:
    break;
}
//==================
//      DEFAULT 
//==================
/*Constantes para controlar el modulo de inicio en la aplicación*/
define("TFW_DEFAULT_LOGGED_CONTROLLER","suspicions");
define("TFW_DEFAULT_LOGGED_METHOD","get_list");
//Datetime separator
define("TFW_DATS","/");
define("TFW_HURS",":");
/*Controlador y Metodo utilizados en core_router*/
//define("TFW_DEFAULT_CONTROLLER","homes");//Acceso validando usuarios desde bd
define("TFW_DEFAULT_LANGUAGE","english");
//Usado para traducciones en tablas _lang 1:English
define("TFW_DEFAULT_LANGUAGEID","1");
//define("TFW_DEFAULT_CONTROLLER","homesnodb");
define("TFW_DEFAULT_CONTROLLER","homes");
define("TFW_DEFAULT_ACCESSMETHOD","login");
define("TFW_DEFAULT_METHOD","get_list");
define("TFW_DEFAULT_METHOD404","error_404");

//Developement user. User for module building
define("TFW_DEVELOPER_USER","dev");//user_identificator == -10
define("TFW_DEVELOPER_PASSWORD","dev");

//==================
// LIBRARY VERSIONS
//==================
define("TFW_VERSION_APPLICATION","1.5.12c");
define("TFW_VERSION_THEFRAMEWORK","1.7.13");
define("TFW_VERSION_DATABASE","3.1.8-1.0.2");
define("TFW_VERSION_PUBLIC","1.2.5");

//==================
//      DEBUG
//==================
//muestra los mensajes de apache en el navegador
define("TFW_PHPDEBUG",0);
//Indica si el debug a nivel general está activado
define("TFW_DEBUG_ISON",1);
//Indica si el debug remoto está habilitado y la ip remota cumple con la configurada
define("TFW_DEBUG_ISREMOTE",1);
define("TFW_DEBUG_REMOTEIP","87.219.172.100");
//si se pasa este valor por GET se mostrarán todas las consultas aún cuando los otros flags estén desactivados
define("TFW_DEBUG_GET_KEY","debug");

//==================
//      GOOGLE
//==================
define("TFW_GOOGLEAPIKEY","");

//==================
//    AUTOPATHS
//==================
define("DS",TFW_DS);
define("TFW_FOLDER_PROJECTDS",TFW_FOLDER_PROJECT.TFW_DS);
define("TFW_PATH_FOLDER_PROJECTDS",TFW_PATH_FOLDER_PROJECT.TFW_DS);
define("TFW_PATH_FOLDER_LOGDS",TFW_PATH_FOLDER_LOG.TFW_DS);
define("TFW_PATH_FOLDER_UPLOADDS",TFW_PATH_FOLDER_UPLOAD.TFW_DS);
define("TFW_PATH_FOLDER_PICTURESDS",TFW_PATH_FOLDER_PICTURES.TFW_DS);