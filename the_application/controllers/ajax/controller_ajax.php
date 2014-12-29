<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name ControllerAjax
 * @file controller_ajax.php 
 * @date 04-10-2014 17:51 (SPAIN)
 * @observations: 
 */
import_model("user");
import_component("page,filter");
import_appcomponent("pdf_lstnbrief");

import_helper("form,form_fieldset,form_legend,input_text,textarea");
import_helper("select,label,anchor,table_typed");
import_apphelper("controlgroup");

import_appmain("controller");
class ControllerAjax extends TheApplicationController
{
    
    public function __construct()
    {
        //For permission validation
        $this->sModuleName = "ajax";
        //crea oLog, oView, oSession
        parent::__construct();
        $this->oSessionUser = $this->oSession->get("oSessionUser");
        $this->oView->set_session_user($this->oSessionUser);        
        $this->oView->set_layout("twocolumn");

    }

}
