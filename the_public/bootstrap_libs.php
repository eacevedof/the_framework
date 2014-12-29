<?php
/**
 * @author Eduardo A. F
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name The Public Bootstrap
 * @file bootstrap_libs.php   
 * @date 01-10-2014 18:27 (SPAIN)
 * @observations: 
 *     
 * @requires:
 */
//Archivo en "the_public". Crea "set_include_paths" rutas de the_framework y rutas dentro de the_application
include("bootpaths.php");
//print_r(explode(";",get_include_path()));
//Archivo en "the_framework". Hace includes de todos los archivos iniciales principalmente dentro de la carpeta the_framework
include("theframework_bootstrap.php");
//print_r(get_included_files());die;