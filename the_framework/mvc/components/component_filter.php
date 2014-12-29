<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.4
 * @name ComponentFilter
 * @file component_filter.php
 * @date 01-11-2014 17:45
 */
include_once("component_session.php");
class ComponentFilter extends TheFrameworkComponent
{
    protected $arRecoverFields;
    protected $arOrderByFields;
    protected $useFieldPrefix;
    protected $arPrefix;
    
    public function __construct($arFields=array())
    {
        parent::__construct();
        $this->arRecoverFields = $arFields;
        $this->arOrderByFields = array("hidOrderBy","hidOrderType","selPage","page","selItemsPerPage");
        $this->sCurrentUrl = $this->get_current_url();
        //bug($this->sCurrentUrl,"currurl in filterconstruct");
        $sPrefix = "txa,txt,pas,hid,sel,chk,rad,dat,fil";
        $this->arPrefix = explode(",",$sPrefix);
        //$this->refresh();
    }

    protected function save_session_prefix()
    {        
        //bug($this->arRecoverFields,"save_session_prefix");
        foreach($this->arRecoverFields as $sFieldName)
        {
            //bug($sFieldName,"fieldname");
            foreach($this->arPrefix as $sPrefix)
            {
                $sTmpField = $sPrefix.sep_to_camel($sFieldName);
                //if($this->is_inpost($sTmpField))
                //{    
                    $mxFieldValue = $this->get_post($sTmpField);
                    //bug($mxFieldValue,$sTmpField);
                    if(!empty($mxFieldValue)||$mxFieldValue==="0")
                        $this->set_insession($sTmpField,$mxFieldValue);
                    else 
                    {
                        $this->unset_insession($sTmpField);
                    }
                //}
            }//foreach arPrefix
        }//foreach arRecoverFields
    }//save_session_prefix()
    
    protected function save_post_prefix()
    {
        //bug($this->arRecoverFields,"save_post_prefix");
        foreach($this->arRecoverFields as $sFieldName)
        {
            foreach($this->arPrefix as $sPrefix)
            {
                $sTmpField = $sPrefix.sep_to_camel($sFieldName);
                //bug($this->sCurrentUrl,"currurl");
                $mxFieldValue = $this->get_fieldvalue_insession($sTmpField);
                //bug($mxFieldValue,$sTmpField);
                if(!empty($mxFieldValue)||$mxFieldValue==="0")
                    $this->set_post($sTmpField,$mxFieldValue);
                else 
                    $this->unset_inpost($sTmpField);
            }
        }
    }
    
    protected function load_insession()
    {
        //si hay valores en post se llama a este metodo para guardarlos en sesion
        $this->set_post_get_page();
        //bug("load_insession");
        if($this->useFieldPrefix)
        {
            //bug($this->useFieldPrefix,"useFieldPrefix");
            $this->save_session_prefix();
        }
        else
            foreach($this->arRecoverFields as $sFieldName)
            {
                $mxFieldValue = $this->get_post($sFieldName);
                //bug($mxFieldValue,"value");
                if(!empty($mxFieldValue)||$mxFieldValue==="0")
                    $this->set_insession($sFieldName,$mxFieldValue);
                else
                    $this->set_insession($sFieldName,NULL);
            }
            
        //ORDER BY DATA
        foreach($this->arOrderByFields as $sFieldName)
        {
            $mxFieldValue = $this->get_post($sFieldName);
            if(!empty($mxFieldValue)||$mxFieldValue==="0")
                $this->set_insession($sFieldName,$mxFieldValue);
        }
        //bugss($this->sCurrentUrl);
    }
    
    protected function load_inpost()
    {
        //bug("load_in_post");
        if($this->useFieldPrefix)
            $this->save_post_prefix();
        else
            foreach($this->arRecoverFields as $sFieldName)
            {
                $mxFieldValue = $this->get_fieldvalue_insession($sFieldName);
                if(!empty($mxFieldValue)||$mxFieldValue==="0")
                    $this->set_post($sFieldName,$mxFieldValue);
                else 
                {
                    $this->set_post($sFieldName,NULL);    
                }
            }
            
        foreach($this->arOrderByFields as $sFieldName)
        {
            $mxFieldValue = $this->get_fieldvalue_insession($sFieldName);
            if(!empty($mxFieldValue)||$mxFieldValue==="0")
                $this->set_post($sFieldName,$mxFieldValue);
        }
        //Actualiza la página
        $this->set_post_get_page();
    }//load_inpost()


    public function refresh()
    {
        //bugp();
        //  bug($this->is_post(),"refresh escribe en sesion");
        if($this->is_post())
            $this->load_insession();
        //No hay post. 
        else
            //Se vuelca en post si hay algo guardado en session
            $this->load_inpost();
    }//refresh()

    //**********************************
    //             GETS
    //**********************************    
    /**
     * Comprueba en url->filters->key
     * @param mixed $mxKey
     * @return boolean
     */
    protected function is_insession($mxKey){return (boolean)$_SESSION[$this->sCurrentUrl]["filters"][$mxKey];}
    protected function is_field_insession($sFieldName){return (boolean)$_SESSION[$this->sCurrentUrl]["filters"][$sFieldName];}
    protected function get_fieldvalue_insession($sFieldName){return $_SESSION[$this->sCurrentUrl]["filters"][$sFieldName];} 
    //**********************************
    //             SETS
    //**********************************
    protected function unset_inpost($sKey){if($sKey)unset($_POST[$sKey]);}
    protected function unset_insession($sKey){if($sKey)unset($_SESSION[$this->sCurrentUrl]["filters"][$sKey]);}
    protected function set_insession($sKey,$mxValue){if($sKey)$_SESSION[$this->sCurrentUrl]["filters"][$sKey]=$mxValue;}
    public function use_field_prefix($isOn=TRUE){$this->useFieldPrefix=$isOn;}
    public function set_fieldnames($arFieldNames){$this->arRecoverFields=$arFieldNames;}
    /**
     * Rescribe la url para poder recuperar sus valores en sesion
     * @param string $sUrl tipo [moudle section view] sin espacios
     */
    public function set_currenturl($sUrl){$this->sCurrentUrl = $sUrl;}
}