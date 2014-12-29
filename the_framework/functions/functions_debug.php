<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.13
 * @file functions_debug.php 
 * @date 09-10-2014 19:25 (SPAIN)
 * @observations: Functions to print variables
 * @requires functions_string.php 1.0.2
 */
function pr($var="",$sTitle=NULL)
{
    if($sTitle)
        $sTitle=" $sTitle: ";
    
    if(!is_string($var))
        $var .= var_export($var,TRUE);
    
    $sTagPre = "<pre function=\"pr\" style=\"background:#CDE552; padding:0px; color:black; font-size:12px;\">\n";
    $sTagFinPre = "</pre>\n";    
    echo $sTagPre.$sTitle.$var.$sTagFinPre;
}

function bug($var, $sVarName="var", $isDie=false)
{
    if(IS_DEBUG_ALLOWED || $_SESSION["tfw_user_identificator"]==-10 || $_SESSION["tfw_user_identificator"]==1)
    {    
        if(is_string($var))
        {
            $isSQL = false;
            $arSQLWords = array("select","from","inner join","insert into","update","delete");
            $sTmpVar = strtolower($var);
            foreach($arSQLWords as $sWord)
                //var_dump("word:$sWord, string:$sTmpVar",strpos($sWord,$sTmpVar));
                if(strpos($sTmpVar,$sWord)!==false){$isSQL=true; break;}

            //var_dump($isSQL);
            if($isSQL)
            {
                if(!strpos($var,"\nFROM"));
                    $var = str_replace("FROM","\nFROM",$var);
                if(!strpos($var,"\nINNER"));
                    $var = str_replace("INNER","\nINNER",$var);
                if(!strpos($var,"\nLEFT"));
                    $var = str_replace("LEFT","\nLEFT",$var);
                if(!strpos($var,"\nRIGHT"));
                    $var = str_replace("RIGHT","\nRIGHT",$var);
                if(!strpos($var,"\nWHERE"));
                    $var = str_replace("WHERE","\nWHERE",$var);
                if(!strpos($var,"\nAND"));
                    $var = str_replace("AND","\nAND",$var);
                if(!strpos($var,"\nORDER BY"));
                    $var = str_replace("ORDER BY","\nORDER BY",$var);
            }
        }
        $sTagPre = "<pre function=\"bug\" style=\"background:#CDE552; padding:0px; color:black; font-size:12px;\">\n";
        $sTagFinPre = "</pre>\n";
        $nombreVariable = $sTagPre ."VARIABLE <b>$sVarName</b>:";
        $nombreVariable .= $sTagFinPre;
        echo $nombreVariable;
        echo  "<pre style=\" background:#E2EDA8; font-size:12px; padding-left:10px; text-align:left; color:black; font-weight:normal; font-family: \'Courier New\', Courier, monospace !important;\">\n";
        var_dump($var);
        echo  "</pre>";

        if($isDie)die;  
    }
}



function bugpf($sKey)
{
    if($sKey=="")
    {
        $arPG = array();
        $arPG["FILES"] = $_FILES;
        bug($arPG,"FILES");
    }
    else
        bug($_FILES[$sKey],"\$_FILES[$sKey]");      
}

function bugfileipath($sFilePath,$isDie=false)
{
    //if(is_firstchar($sFilePath,"/")||is_firstchar($sFilePath,"\\"))
        //remove_firstchar($sFilePath);
    //$sFilePath = DIRECTORY_SEPARATOR.$sFilePath;        
    
    $arPaths = explode(PATH_SEPARATOR,get_include_path());
    foreach($arPaths as $sDirPath)
    {
        $sTmpPath = $sDirPath.$sFilePath;
        //echo $sTmpPath."<br>";
        if(file_exists($sTmpPath))
        {    
            bug(TRUE,$sTmpPath,$isDie);
            return;
        }
    }
    bug(FALSE,$sFilePath,$isDie);
}

function bugfile($sFilePath, $sNombreVariable="", $isDie=false)
{
    if(!$sNombreVariable) $sNombreVariable = $sFilePath;
    bug(is_file($sFilePath),$sNombreVariable,$isDie);
}

function bugdir($sDirPath, $sNombreVariable="var", $isDie=false)
{
    bug(is_dir($sDirPath),$sNombreVariable,$isDie);
}

function bugpg()
{
    $arPG = array();
    $arPG["POST"] = $_POST;
    $arPG["GET"] = $_GET;
    bug($arPG,"POST | GET");
}

function bugp($sKey="")
{
    if($sKey=="")
    {
        $arPG = array();
        $arPG["POST"] = $_POST;
        bug($arPG,"POST");
    }
    else
        bug($_POST[$sKey],"POST[$sKey]");    
}

function bugg($sKey="")
{
    if($sKey=="")
    {
        $arPG = array();
        $arPG["GET"] = $_GET;
        bug($arPG,"GET");    
    }
    else
        bug($_GET[$sKey],"GET[$sKey]");
}

function bugss($sKey="")
{
    if($sKey=="")
    {
        $arPG = array();
        $arPG["SESSION"] = $_SESSION;
        bug($arPG,"SESSION");    
    }
    else
        bug($_SESSION[$sKey],"SESSION[$sKey]");
}

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
function errorson($sType="all")
{
    ini_set("display_errors", 1);
    switch ($sType) 
    {
        case "e":
        case "error":
            error_reporting(E_ERROR);
        break;
        
        case "w":
        case "warning":
            error_reporting(E_WARNING);
        break;
        
        case "p":
        case "parse":
            error_reporting(E_PARSE);
        break;
        
        case "n":
        case "notice":
            error_reporting(E_NOTICE);
        break;
        
        default:
            error_reporting(E_ALL);
        break;
    }
}


function bugif(){bug(get_included_files(),"included_files");}

function bugversion(){phpversion();}

function bugsysinfo()
{
    $sSysInfo = "DS: ".DIRECTORY_SEPARATOR." \n";
    $sSysInfo .= "LIB EXTENSION: ".PHP_SHLIB_SUFFIX." \n";
    $sSysInfo .= "PATH SEPARATOR: ".PATH_SEPARATOR." \n";
    $sSysInfo .= "SERVER OS: ".php_uname("s")." \n";
    //echo  // \
    //echo "- LSUFIX: ".PHP_SHLIB_SUFFIX;    // dll
    //echo "- PATH SEP: ".PATH_SEPARATOR;      // ;
    // 's': Operating system name. eg. FreeBSD.
    //'n': Host name. eg. localhost.example.com. 
    //echo php_uname();
    //echo PHP_OS;
    bug($sSysInfo);
}

/**
 * Bug cookies
 */
function bugck(){bug($_COOKIE,"cookie");}

function bugipath(){ bug(explode(PATH_SEPARATOR,get_include_path()),"included path:");}

function bugcond($var,$isCheckCondition)
{
    //var_dump($isCheckCondition);
    if($isCheckCondition)
        bug($var);
    else 
        pr("isCheckCondition = FALSE");
}

function bugraw($var,$sVarName=NULL)
{
    $sReturn = "\n";
    if($sVarName)
        $sReturn .= "$sVarName: \n";

    $sReturn .= var_export($var,1);
    echo $sReturn;
}