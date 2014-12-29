<?php
/**
 * @author Eduardo A. F
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name The Public Bootpaths
 * @file bootpaths.php   
 * @date 01-10-2014 20:22 (SPAIN)
 * @observations: 
 *     
 * @requires:
 */
//echo "bootpaths.php";
//ruta de este archivo: proyecto/the_public/
if(!defined("TFW_DS")) define("TFW_DS",DIRECTORY_SEPARATOR);
//$sPathRootDs=$_SERVER["DOCUMENT_ROOT"];
define("TFW_PATH_FOL_THEPUBLICDS",dirname(__FILE__).TFW_DS);
define("TFW_PATH_FOL_ROOTDS",TFW_PATH_FOL_THEPUBLICDS."..".TFW_DS);

$sPathRootDs = TFW_PATH_FOL_ROOTDS;
$arTfwPaths = array();
$arTfwPaths[]=get_include_path();

//THE FRAMEWORK
//No se como funciona en windows, pero boot_constants,start_object y functions_ se incluyen sin necesidad de indicar la subcarpetas
$arTfwPaths[]=$sPathRootDs."the_framework";

//THE APPLICATION
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."boot";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."components";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."controllers";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."themes";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."models";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."behaviours";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."views";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."helpers";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."elements";
$arTfwPaths[]=$sPathRootDs."the_application".TFW_DS."translations";
//Es necesario usar PATH_SEPARATOR
$sIncludePath = implode(PATH_SEPARATOR,$arTfwPaths);
set_include_path($sIncludePath);
//var_dump(TFW_PATH_FOL_ROOTDS);
//var_dump($_SERVER["DOCUMENT_ROOT"]);
//var_dump(realpath("."));
//print_r(get_included_files());
//var_dump(get_include_path());die;