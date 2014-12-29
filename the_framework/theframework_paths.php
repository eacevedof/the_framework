<?php
/**
 * @author Eduardo A. F
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name The Framework Paths
 * @file theframework_paths.php   
 * @date 01-10-2014 21:23 (SPAIN)
 * @observations: 
 *     
 * @requires:
 */
//=========================
//  THE FRAMEWORK PATHS
//=========================
$sPathRootTfw = dirname(__FILE__);
$sPathRootTfw = $sPathRootTfw.TFW_DS;

$arTfwPaths = array();
$arTfwPaths[]=get_include_path();
//componentes creados por mi
$arTfwPaths[]=$sPathRootTfw."mvc".TFW_DS."components";
//clases de terceros customizadas, mejoradas o componentes genericos derivados de los anteriores
$arTfwPaths[]=$sPathRootTfw."mvc".TFW_DS."custom";
//ayudantes para UI
$arTfwPaths[]=$sPathRootTfw."mvc".TFW_DS."helpers";
//clases de terceros sin modificación
$arTfwPaths[]=$sPathRootTfw."mvc".TFW_DS."vendors";
//clases de tipo TFW
$arTfwPaths[]=$sPathRootTfw."mvc".TFW_DS."main";
//constantes (boot_constants.php) se incluye al principio
$arTfwPaths[]=$sPathRootTfw."constants";
//funciones (boot_functions.php) se incluye al principio
$arTfwPaths[]=$sPathRootTfw."functions";
//funciones (boot_vars.php) se incluye al principio
$arTfwPaths[]=$sPathRootTfw."vars";
$arTfwPaths[]=$sPathRootTfw."plugins";
$sIncludePath = implode(PATH_SEPARATOR,$arTfwPaths);
set_include_path($sIncludePath);
