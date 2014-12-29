<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.3
 * @name index
 * @file index.php 
 * @date 02-11-2014 00:31 (SPAIN)
 * @observations: 
 */
ob_start();
set_time_limit(1200);//20 minutos

ini_set("display_errors",0);
error_reporting(1);
//ini_set("display_errors",1);
//error_reporting(E_ALL);  
include("bootstrap_libs.php");

//En este punto ya existe la constante
if(TFW_PHPDEBUG==1)
{
    ini_set("display_errors",1);
    //error_reporting(E_ALL ^E_NOTICE ^E_WARNING);
    error_reporting(E_ALL);    
}

//errorson("e");
//bugsysinfo();
//$time = date("Y-m-d H:i:s");
//$time = "1970-01-01 01:00:00";
//bug(strtotime($time),$time);
//bug(gmdate('His',55393));
//foreach($argv as $mxKey => $value){ echo "\n$mxKey:$value";}
//llamado desde linea de comandos
//timer_on();
$isCalledFromConsole = defined("STDIN");
if($isCalledFromConsole)
{
    session_start();
    $_SESSION["tfw_user_identificator"] = "command_line";
    session_write_close();
    $sIndexController = $argv[1];//0: nombre del archivo 
    $sIndexMethod = $argv[2];
} 
//por get o post
else 
{ 
    if(TFW_IS_PERMALINK==1)
    {
        include_once("component_router.php");
        global $arRoutes;
        //bug($arRoutes,"arRoutes");
        $oRouter = new ComponentRouter($arRoutes);
        
        $arRun = $oRouter->get_controller();
        //bug($arRun,"arRun");exit();
        $_GET["module"] = $arRun["controller"];
        $_GET["section"] = $arRun["partial"];
        $_GET["view"] = $arRun["method"];
        unset($arRoutes);
        unset($oRouter);
        unset($arRun);
    }    
    //bugg();DIE;
    if($_GET["module"]) $sIndexController = $_GET["module"];//0: nombre del archivo 
    if($_GET["controller"]) $sIndexController = $_GET["controller"];
    
    if($_GET["section"]) $sIndexPartial = $_GET["section"];
    if($_GET["partial"]) $sIndexPartial = $_GET["partial"];

    if($_GET["view"]) $sIndexMethod = $_GET["view"];
    if($_GET["method"]) $sIndexMethod = $_GET["method"];
}

//PARAMS TO CONFIG
//bug($sIndexController.",".$sIndexPartial.",".$sIndexMethod);
$arControllerData = core_router($sIndexController,$sIndexPartial,$sIndexMethod);
//bug($arControllerData);die;
$sIndexController = $arControllerData["controller_name"];
$sIndexMethod = $arControllerData["controller_method"];
$sIndexType = $arControllerData["controller_type"]; 
$sIndexControllerPath = $arControllerData["controller_path"];

//die($sIndexType);
$sPathTranslation = TFW_DEFAULT_LANGUAGE;
//bug($_SESSION);
if($_SESSION["tfw_user_language"]) $sPathTranslation=$_SESSION["tfw_user_language"];
$sPathTranslation .= TFW_DS;
//bugfile($sPathTranslation,$sPathTranslation);
//Translate main
$sPathIncludeFile = $sPathTranslation."translate_main.php";
//bugfile($sPathIncludeFile,$sPathIncludeFile);
//bug(core_isfile_includepath($sPathIncludeFile), $sPathIncludeFile);
if(core_isfile_includepath($sPathIncludeFile))
    include_once($sPathIncludeFile);

//Translate controller
$sPathIncludeFile = $sPathTranslation."translate_$sIndexController.php";
//bug($sPathIncludeFile);
//bug(core_isfile_includepath($sPathIncludeFile), $sPathIncludeFile);
if(core_isfile_includepath($sPathIncludeFile))
    include_once($sPathIncludeFile);

if($_GET["section"]||$_GET["partial"])
{
    $sPartial = $_GET["section"];
    if(!$sPartial) $sPartial = $_GET["partial"];
    $sPathIncludeFile = $sPathTranslation."translate_$sPartial.php";
    if(core_isfile_includepath($sPathIncludeFile)) 
        include_once($sPathIncludeFile);
}

//if($sIndexType=="partial" && core_isfile_includepath($sPathIncludeFile))
    //$sPathIncludeFile = $sPathTranslation."translate_$sIndexController.php";
//bug($sIndexControllerPath);die;
include($sIndexControllerPath);

$sTfwClassName = $sIndexType.ucfirst($sIndexController);
//bug($sTfwClassName,"objeto a crear");
$oTfwController = new $sTfwClassName();
if(method_exists($oTfwController,$sIndexMethod))
    $oTfwController->{$sIndexMethod}();
else
    die("method $sIndexMethod does not exist!");

if(!$oTfwController->is_ajax())    
    if(IS_DEBUG_ALLOWED  || $_SESSION["tfw_user_identificator"]=="1"//user bo system
        || $_SESSION["tfw_user_identificator"]=="-10" /*developer=-10*/)
        ComponentDebug::get_sqls_in_html_table();

//bugss("tfw_message");
if($_SESSION["tfw_message"]["a"])
{
    import_helper("javascript");
    $oJavascript = new HelperJavascript();
    $sJs = implode(" ",$_SESSION["tfw_message"]["a"]);
    unset($_SESSION["tfw_message"]["a"]);
    $sJs = str_replace("'","\\'",$sJs);
    $oJavascript->add_js_line("alert('$sJs');");
    $oJavascript->show();
}
    
//bugif();
ob_end_flush();
//bugss();
//timer_off();