<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name PartialPictures
 * @file partial_pictures.php 
 * @date 21-09-2013 13:15 (SPAIN)
 * @observations: UC - Crea carpetas y vuelca imagenes en estas
 */

include_once("controller_customers.php");
include_once("model_clientes.php");
include_once("component_file.php");

class PartialPictures extends ControllerCustomers
{
    
    public function __construct()
    {
        $this->sModuleName = "pictures";
        //crea oLog, oView, oSession, sClientBrowser y guarda en session
        parent::__construct();
    }
   
    public function get_list()
    {
        //bug("getlist en partial pictures");
        $this->oView->set_layout("twocolumn");
        $this->oView->show_page();              
    }
    
    public function make_folders()
    {
        $iRowFrom = $this->get_get("rowfrom");
        $iNumRows = $this->get_get("rows");
        if(!$iNumRows) $iNumRows = 5000;
        if(!$iRowFrom) $iRowFrom = 1;
        //Fotos 
        $sSourceFolImageDs = "C:/DS_GYREX/TESTING/DOCS/";
        
        //Carpetas finales con fotos
        $sTargetFolderDs = "C:/temp/";
        
        //$sTargetFolderDs = $sTargetFolder.DS;
        
        preopen();
        pr("STARTING MAKING FOLDERS ROWFROM=$iRowFrom NUMROWS=$iNumRows");
        if(is_dir($sTargetFolderDs))
        {
            pr("Target folder: $sTargetFolderDs exists!");
            $oModelClients = new ModelClientes();
            $arCicodclis = $oModelClients->get_select_all_ids($iRowFrom,$iNumRows);
            $this->log_custom($arCicodclis);
            foreach($arCicodclis as $sCicodcli)
            {
                pr("CICODCLI: $sCicodcli");
                $sPathFolderClient = $sTargetFolderDs.$sCicodcli;
                if(is_dir($sPathFolderClient))
                    //$this->log_error("pictures: Folder $sCicodcli not created. It already exists!");
                    pr("Folder $sCicodcli not created. It already exists!");
                else
                {
                    $sResult = mkdir($sPathFolderClient);
                    //$this->log_error("picutes: result of making $sCicodcli: $sResult");
                    pr("result of making folder $sCicodcli=$sResult");
                    if($sResult==false)
                        $this->log_error("picutes: result of making $sCicodcli: $sResult");
                }
            }//fin arCicodclis
        }
        //TargetFolder no existe
        else
        {    
            $this->log_error("pictures: $sTargetFolderDs is not a folder");
            exit("$sTargetFolderDs does not exist!");
        }
        
        pr("END MAKING FOLDERS!");
        pr(" - - - - - - - - - - - - - - - - - ");
        pr(" - - - - - - - - - - - - - - - - - ");
        pr("START COPYING IMAGES");
        
        $oFile = new ComponentFile("windows");
        $oFile->set_path_folder_source($sSourceFolImageDs);
    
        foreach($arCicodclis as $sCicodcli)
        {
            pr("CICODCLI: $sCicodcli");
            $sTargetFolderClient = $sTargetFolderDs.$sCicodcli;

            if(is_dir($sTargetFolderClient))
            {   
                $oFile->set_path_folder_target($sTargetFolderClient);
                for($i=0; $i<25; $i++)
                {
                    $bFile1=false; $bFile2=false;
                    $sImageFile = "$sCicodcli-$i.jpg";
                    $sPathToCheck = "$sSourceFolImageDs$sImageFile";
                    if(is_file($sPathToCheck))
                    {
                        pr("image file: $sPathToCheck exists!");
                        $bFile1=true;
                        $oFile->set_filename_source($sImageFile);
                        $oFile->set_filename_target($sImageFile);
                        $oFile->copy();
                        if($oFile->is_error())
                            pr("Error copying $sImageFile");
                        //despues de copiar el archivo comprobamos que exista en el 
                        //destino para proceder a borrar del origen
                        if($oFile->target_exists())
                            $oFile->source_remove();
                        else
                            $this->log_error("pictures: $sImageFile not copied!");
                    }
                    else
                    {
                        pr("image file not found: $sPathToCheck!");
                        $this->log_error("pictures: $sPathToCheck not found");
                    }
                    
                    $sImageFile = "$sCicodcli-$i-0.jpg";
                    $sPathToCheck = "$sSourceFolImageDs$sImageFile";
                    if(is_file($sPathToCheck))
                    {
                        pr("image file: $sPathToCheck exists!");
                        $bFile2=true;
                        $oFile->set_filename_source($sImageFile);
                        $oFile->set_filename_target($sImageFile);
                        $oFile->copy();
                        if($oFile->is_error())
                            pr("Error copying $sImageFile");

                        //despues de copiar el archivo comprobamos que exista en el 
                        //destino para proceder a borrar del origen
                        if($oFile->target_exists())
                            $oFile->source_remove();
                        else
                            $this->log_error("pictures: $sImageFile not copied!");
                    }
                    else
                    {
                        pr("image file not found: $sPathToCheck!");
                        $this->log_error("pictures: $sPathToCheck not found");
                    }
                    
                    //si se cumple que no existen ninguno de los dos es que no existirán
                    //los restantes asi pues se sale de este bucle
                    //if(!($bFile1||$bFile2)) break;
                }//fin for i[0-200]
            }
            //no existe directorio destino
            else
                $this->log_error("Folder $sCicodcli does not exist","make_folder");
        }//fin foreach $arCodClis
        pr("END COPYING IMAGES");
        preclose();
    }
}