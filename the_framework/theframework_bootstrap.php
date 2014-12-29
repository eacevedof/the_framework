<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.2
 * @name theframework Bootstrap
 * @file theframework_bootstrap.php 
 * @date 07-10-2014 22:19 (SPAIN)
 * @observations: Includes
 * @requires
 */
//echo "theframework_bootstrap.php";
//session_start(); esto genera __PHP_Incomplete_Class 
//si se desea recuperar un objeto guardado en session
include("theframework_config.php");
include("theframework_paths.php");

include("boot_constants.php");
include("boot_vars.php");

include("functions_boot.php");
include("functions_debug.php");
include("functions_io.php");
include("functions_utils.php");
include("functions_format.php");
include("functions_string.php");

include("app_constants.php");
include("app_functions.php");
include("app_routes.php");

//MVC TFW MAINS
include("theframework.php");
include("theframework_model.php");
include("theframework_behaviour.php");
include("theframework_component.php");
include("theframework_controller.php");
include("theframework_helper.php");
include("theframework_view.php");

//THE APP MAINS. 
//Con once pq asi se marca como único en la pila de includes
//include_once("theapplication_model.php");
//include_once("theapplication_component.php");
//include_once("theapplication_controller.php");//bugif();die;
//include_once("theapplication_helper.php");
//include_once("theapplication_view.php");

//COMPONENTS
include("component_debug.php");
include("component_database.php");
//include("component_mailing.php");
include("component_file.php");
include("component_session.php");

//HELPERS
//include("helper_css.php");
//include("helper_input_date.php");
//include("helper_input_hidden.php");
//include("helper_input_text.php");
//include("helper_javascript.php");
//include("helper_radio.php");
//include("helper_checkbox.php");
//include("helper_select.php");
//include("helper_button_submit.php");
//include("helper_textarea.php");
//include("helper_button_basic.php");
//include("helper_google_maps_3.php");

//PLUGINS
//include("fpdf/fpdf.php");

//VENDORS
//include("oleread".TFW_DS."component_oleread.php");
//include("excel_reader".TFW_DS."component_spreadsheet_excel_reader.php");

//OBJETOS GLOBALES
include("exec_onboot/start_objects.php");

//session_start(); //Se debe abrir la sesion "siempre" despues de la inclusion de las librerias
//para evitar las clases anonimas.  Esto se hace en el main controller
