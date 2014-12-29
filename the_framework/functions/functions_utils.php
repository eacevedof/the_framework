<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.0
 * @file functions_utils.php 
 * @date 20-05-2014 10:40 (SPAIN)
 * @observations: Utils
 * @requires
 */
function use_file_from_includedpath($mxFileName,$sPrefix="",$sExtension="php",$sPathFolder="")
{
    //bug($sPathFolder,"pathfolder");
    if($sPathFolder && !is_pathendds($sPathFolder)) 
        $sPathFolder .= DIRECTORY_SEPARATOR;
    
    if(is_array($mxFileName))
        foreach($mxFileName as $sFileName)
        {    
            $sFileName = "$sPathFolder$sPrefix$sFileName.$sExtension";
            include_once $sFileName;
        }
    else//string
    {
        if(strstr($mxFileName,","))
        {
            $arNames = explode(",",$mxFileName);
            foreach($arNames as $mxFileName)
            {
                $sFileName = "$sPathFolder$sPrefix$mxFileName.$sExtension";
                //bug($sFileName);
                include_once $sFileName;                
            }
        }
        else//string sin separación
        {
            $sFileName = "$sPathFolder$sPrefix$mxFileName.$sExtension";
            //bug($sFileName,"import");
            //bugfile($sFileName,$sFileName);
            //bugfileipath($sFileName);
            include_once $sFileName;
        }
    }
    //if($sPrefix=="model_"){bug($mxFileName);bugif();die;}
}

//CORE
function import_model($mxFileName,$sPathFolder=""){use_file_from_includedpath($mxFileName,"model_","php",$sPathFolder);}
function import_component($mxFileName,$sPathFolder=""){use_file_from_includedpath($mxFileName,"component_","php",$sPathFolder);}
/**
 * 
 * @param mixto $mxFileName array, csv string or simple string
 * @param string $sPathFolder Used for unusual controllers name. Example modulebulder
 */
function import_controller($mxFileName,$sPathFolder=""){use_file_from_includedpath($mxFileName,"controller_","php",$sPathFolder);}
function import_helper($mxFileName){use_file_from_includedpath($mxFileName,"helper_");}
function import_view($mxFileName){use_file_from_includedpath($mxFileName,"view_");}
function import_behaviour($mxFileName){use_file_from_includedpath($mxFileName,"behaviour_");}
function import_plugin($mxFileName,$sPathFolder=""){use_file_from_includedpath($mxFileName,"","php",$sPathFolder);}

//APP
//function import_appmodel($mxFileName){use_file_from_includedpath($mxFileName,"appmodel_");}
function import_appcomponent($mxFileName){use_file_from_includedpath($mxFileName,"appcomponent_");}
function import_appcontroller($mxFileName,$sPathFolder="",$sExtension="php")
{
    //customernotes\controller_customernotes.php" lo encuentra.
    // \ o /customernotes\controller_customernotes.php" no lo encuentra
    //$sPathFolder .= DIRECTORY_SEPARATOR;
    $sPathFolder .= $mxFileName;
    use_file_from_includedpath($mxFileName,"controller_",$sExtension,$sPathFolder);
}
function import_apphelper($mxFileName){use_file_from_includedpath($mxFileName,"apphelper_");}
function import_appbehaviour($mxFileName){use_file_from_includedpath($mxFileName,"appbehaviour_");}
function import_appview($mxFileName){use_file_from_includedpath($mxFileName,"appview_");}

/**
 * If $sLanguage is empty it tries to recover language from session if it is still empty then applies default language = "english"
 * @param mixed $mxModule array|string|csvstring
 * @param string $sLanguage User language
 */
function import_apptranslate($mxModule,$sLanguage=NULL)
{
    if(!$sLanguage)
    {   
        //tengo que abrir la session para comprobar si hay un usuario registrado
        session_start();
        $sLanguage = $_SESSION["tfw_user_language"];
        //al terminar la recuperación del idioma del usuario, cierro para evitar error con posteriores includes
        session_write_close();
        if(!$sLanguage) $sLanguage = TFW_DEFAULT_LANGUAGE;
    }
    use_file_from_includedpath($mxModule,"$sLanguage/translate_");
}

//APP MAIN
function import_appmain($mxFileName){use_file_from_includedpath($mxFileName,"theapplication_");}

function array_key_position($sKey,$arSearch){return array_search($sKey,array_keys($arSearch));}

function is_pathendds($sPathPath){return is_lastchar($sPathPath,"/") && is_lastchar($sPathPath,"\\");}

function size_inbytes($sValue) 
{
    $sValue = trim($sValue);
    $sLastChar = strtolower($sValue[strlen($sValue)-1]);
    switch($sLastChar) 
    {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g': $sValue *= 1024;  
        case 'm': $sValue *= 1024;
        case 'k': $sValue *= 1024;
    }
    return $sValue;
}

/**
 * TODO
 * @param string $sTimeFrom
 * @param string $sTimeTo
 * @param string $sType
 */
function time_interval($sTimeFrom="01-01-2013 07:35:00",$sTimeTo="01-01-2013 08:21:00",$sType="His")
{
    $iReturn = 0;
    
    $iSecsFrom = strtotime($sTimeFrom);
    $iSecsTo = strtotime($sTimeTo);

    $iSecs = $iSecsTo-$iSecsFrom;
    $iMins = ceil($iSecs/60);
    var_dump($iSecsFrom,$iSecsTo,$iSecs,$iMins);
}