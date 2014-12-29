<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.6
 * @name index
 * @file functions_boot.php 
 * @date 01-10-2014 21:27 (SPAIN)
 * @observations: 
 */
function core_isfile_includepath($sFilePath)
{
    //bug(get_include_path());
    $arIncludedPaths = explode(PATH_SEPARATOR,get_include_path());
    //bug($arIncludedPaths); die;
    foreach($arIncludedPaths as $sPath) 
    {
        $sFullPath = $sPath.DIRECTORY_SEPARATOR.$sFilePath;
        //bugfileipath($sFullPath);
        if(is_file($sFullPath)) 
            return true;    
    }
    return false;
}

function core_router
($sModule,$sSection,$sView
,$sDefController="homes",$sAccessMethod="login",$sDefMethod="get_list",$sDefMethod404="error_404")
{
    if(defined("TFW_DEFAULT_CONTROLLER"))
        if(TFW_DEFAULT_CONTROLLER!="")
            $sDefController = TFW_DEFAULT_CONTROLLER;
    
    if(defined("TFW_DEFAULT_ACCESSMETHOD"))
        if(TFW_DEFAULT_ACCESSMETHOD!="")
            $sAccessMethod = TFW_DEFAULT_ACCESSMETHOD;
        
    if(defined("TFW_DEFAULT_METHOD"))
        if(TFW_DEFAULT_METHOD!="")
            $sDefMethod = TFW_DEFAULT_METHOD;
    
    if(defined("TFW_DEFAULT_METHOD404"))
        if(TFW_DEFAULT_METHOD404!="")
            $sDefMethod404 = TFW_DEFAULT_METHOD404; 
        
    //TFW_DEFAULT_METHOD404    
    $arReturn = array
    (
        "controller_name"=>""
        ,"controller_method"=>""
        ,"controller_path"=>""
        ,"controller_type"=>"Controller"//partial
    );

    //variables a utilizar
    $sPathController = "";
    $sController = $sModule;
    $sPartialController = $sSection;
    $sMethod = $sView;
        
    if($sController)
    {
        $sPathController = $sController;
        if($sPartialController)
        {
            $sPathController .= DIRECTORY_SEPARATOR."partial_$sPartialController";
            $arReturn["controller_name"] = $sPartialController;
            $arReturn["controller_type"] = "Partial";
        }
        //no partialcontroller (tab)
        else
        {    
            $sPathController .= DIRECTORY_SEPARATOR."controller_$sController";
            $arReturn["controller_name"] = $sController;
        }
        
        if($sMethod)
            $arReturn["controller_method"] = $sMethod;
        else
            $arReturn["controller_method"] = $sDefMethod;
    }
    //No hay controlador
    else
    {
        $arReturn["controller_name"] = $sDefController;
        $sPathController = $sDefController;
        $sPathController .= DIRECTORY_SEPARATOR."controller_$sDefController";
        //No hay partial (tab) ni metodo => homes->login
        if(!$sPartialController && !$sMethod)
            $arReturn["controller_method"] = $sAccessMethod;
        else
            $arReturn["controller_method"] = $sDefMethod404;
    }
    //$sPathController = ".".DIRECTORY_SEPARATOR."$sPathController.php";
    $sPathController = "$sPathController.php";
    //bug($sPathController);
    if(core_isfile_includepath($sPathController))
        $arReturn["controller_path"] = $sPathController;
    else
        $arReturn = array
        (
            "controller_type"=>"Controller"
            ,"controller_name"=>$sDefController
            ,"controller_method"=>$sDefMethod404
            ,"controller_path"=>$sDefController.DIRECTORY_SEPARATOR."controller_$sDefController.php"
            ,"error"=>"$sPathController not found!"
        );
    //bug($arReturn);die;
    //tengo que abrir la session para comprobar si hay un usuario registrado
    session_start();
    if(!$_SESSION["tfw_user_identificator"])
        $arReturn = array
        (
            "controller_type"=>"Controller"
            ,"controller_name"=>$sDefController//homes
            ,"controller_method"=>$sAccessMethod//login
            ,"controller_path"=>$sDefController.DIRECTORY_SEPARATOR."controller_$sDefController.php"
            ,"warning"=>"User not in sesssion!"
        );
    //al terminar la comprobación la cierro para evitar que tenga abierta una session
    //antes de hacer includes no ha funcionado :(
    //session_destroy();
    session_write_close();
    
    //bug($arReturn);
    if($arReturn["controller_type"]=="Partial")
    {    
        if($_GET["module"])
        {
            $_GET["controller"] = $_GET["module"];
        }
        elseif($_GET["controller"]) 
        {
            $_GET["module"] = $_GET["controller"];
        }
        $_GET["partial"] = $arReturn["controller_name"];
        $_GET["section"] = $_GET["partial"];
    }
    else
    {
        //globales
        $_GET["controller"] = $arReturn["controller_name"];
        $_GET["module"] = $_GET["controller"];        
    }
    
    $_GET["method"] = $arReturn["controller_method"];
    $_GET["view"] = $_GET["method"];
    //bugg();
    return $arReturn;
}

function get_fixed_syspath($sPathDir="")
{
    if($sPathDir)
    {
        $sPathDir = str_replace("/",DIRECTORY_SEPARATOR,$sPathDir);
        $sPathDir = str_replace("\\",DIRECTORY_SEPARATOR,$sPathDir);
    }
    return $sPathDir;
}

function get_absolute_path($sSubPath)
{
    if(is_firstchar($sSubPath,"/")||is_firstchar($sSubPath,"\\"))
        remove_firstchar($sSubPath);
    $sSubPath = DIRECTORY_SEPARATOR.$sSubPath;        
    
    $arPaths = explode(";",get_include_path());
    foreach($arPaths as $sDirPath)
    {
        $sTmpPath = $sDirPath.$sSubPath;
        //echo $sTmpPath."<br>";
        if(is_file($sTmpPath))
            return $sTmpPath;
    }
    return "path not found";
}