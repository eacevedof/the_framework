<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name ControllerDownloads
 * @file controller_downloads.php 
 * @date 26-08-2013 07:38 (SPAIN)
 * @observations: Download files
 * @requires
 */
import_appmain("view,controller");

class ControllerDownloads extends TheApplicationController
{
    public function __construct()
    {
        $this->sModuleName = "downloads";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
    }
   
    public function get_shellscripts_log()
    {
        $sFileName = $this->get_get("name");
        $sPathLogDs = TFW_PATH_FOLDER_LOGDS."shellscripts/";
        $sPathFile = $sPathLogDs.$sFileName;
        $this->send_to_client($sPathFile);
    }
    
    public function get_custom_log()
    {
        $sFileName = $this->get_get("name");
        $sPathLogDs = TFW_PATH_FOLDER_LOGDS."custom/";
        $sPathFile = $sPathLogDs.$sFileName;
        $this->send_to_client($sPathFile);
    }

    public function get_error_log()
    {
        $sFileName = $this->get_get("name");
        $sPathLogDs = TFW_PATH_FOLDER_LOGDS."errors/";
        $sPathFile = $sPathLogDs.$sFileName;
        $this->send_to_client($sPathFile);
    }
    
    private function send_to_client($sPathFile)
    {
        if(is_file($sPathFile))
        {
            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=".basename($sPathFile));
            header("Content-Transfer-Encoding: binary");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: public");
            header("Content-Length: ".filesize($sPathFile));
            ob_clean();
            flush();
            readfile($sPathFile);
            return true;
        }
        echo "File: ".$this->get_get("name")." Not found!";
        return false;
    }
}