<?php
/**
 * @author Module Builder 1.1.1
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ModelSuspicionArrayLang
 * @file model_suspicion_array_lang.php
 * @date 13-06-2014 21:34 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelSuspicionArrayLang extends TheApplicationModel
{
    protected $oSource; //Model Object
    protected $oLanguage; //Model Object
    protected $_order_by; //int(4)

    public function __construct
    ($id_language=NULL,$id=NULL,$id_source=NULL,$order_by=NULL)
    {
        parent::__construct("app_suspicion_array_lang");
        if($id!=NULL) $this->_id = $id;
        if($id_source!=NULL) $this->_id_source = $id_source;
        if($id_language!=NULL) $this->_id_language = $id_language;
        if($order_by!=NULL) $this->_order_by = $order_by;
        //$this->arDescConfig = array("id","cru_csvnote","separator"=>" - ");
    }//__construct()

    public function load_by_id()
    {
        if($this->_id)
        {
            $this->oQuery->set_comment("load_by_id()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins();
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id=$this->_id");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        $this->row_assign($arRow);
    }//load_by_id()

    public function load_by_src_and_lang()
    {
        if($this->_id_source && $this->_id_language)
        {
            $this->oQuery->set_comment("load_by_src_and_lang()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins();
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id_source=$this->_id_source");
            $this->oQuery->add_and("$this->_table_name.id_language=$this->_id_language");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        $this->row_assign($arRow);
    }//load_by_src_and_lang()
        
    //===================
    //       GETS
    //===================
    public function get_source()
    {
        $this->oSource = new ModelSuspicionArray($this->_id_source);
        $this->oSource->load_by_id();
        return $this->oSource;
    }
    public function get_language()
    {
        $this->oLanguage = new ModelLanguage($this->_id_language);
        $this->oLanguage->load_by_id();
        return $this->oLanguage;
    }
    public function get_order_by(){return $this->_order_by;}
    //===================
    //       SETS
    //===================
    public function set_source($oValue){$this->oSource = $oValue;}
    public function set_language($oValue){$this->oLanguage = $oValue;}
    public function set_order_by($value){$this->_order_by = $value;}
}
