<?php
/**
 * @author Module Builder 1.1.1
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ModelLanguage
 * @file model_language.php
 * @date 13-06-2014 22:00 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelLanguage extends TheApplicationModel
{
    protected $_order_by; //int(4)
    protected $_id_tosave; //varchar(25)
    protected $oTosave; //Model Object

    public function __construct
    ($id=NULL,$order_by=NULL,$id_tosave=NULL)
    {
        parent::__construct("base_language");
        if($id!=NULL) $this->_id = $id;
        if($order_by!=NULL) $this->_order_by = $order_by;
        if($id_tosave!=NULL) $this->_id_tosave = $id_tosave;
        //$this->arDescConfig = array("id","id_tosave","separator"=>" - ");
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

    //public function get_select_all_ids()
    //{
        //$this->oQuery->set_comment("get_select_all_ids() overriden");
        //$this->oQuery->set_fields("$this->_table_name.id");
        ////si está definido $this->_select_user
        //$this->oQuery->add_joins($this->build_userhierarchy_join($this->_select_user,"customer","id_customer"));
        //$this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        //$this->oQuery->add_where("$this->_table_name.is_enabled=1");
        ////EXTRA AND
        //$this->oQuery->add_and($this->build_sql_filters());
        ////ORDERBY 
        ////default orderby
        //$this->oQuery->set_orderby("$this->_table_name.id DESC");
        //$sOrderByAuto = $this->build_sql_orderby();
        //if($sOrderByAuto) $this->oQuery->set_orderby($sOrderByAuto);
        //$sSQL = $this->oQuery->get_select();
        //$this->oQuery->set_fields($this->sSELECTfields);
        ////bug($sSQL);
        //return $this->query($sSQL);
    //}//get_select_all_ids overriden

    //===================
    //       GETS
    //===================
    public function get_order_by(){return $this->_order_by;}
    public function get_id_tosave(){return $this->_id_tosave;}
    //===================
    //       SETS
    //===================
    public function set_order_by($value){$this->_order_by = $value;}
    public function set_id_tosave($value){$this->_id_tosave = $value;}
}
