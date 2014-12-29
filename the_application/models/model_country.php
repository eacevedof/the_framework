<?php
/**
 * @author Module Builder 1.1.0
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name ModelCountry
 * @file model_country.php
 * @date 15-06-2014 11:24 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelCountry extends TheApplicationModel
{
    protected $_order_by; //int(4)
    protected $_type; //varchar(15)
    protected $_acronym; //varchar(5)
    protected $_latitude; //varchar(25)
    protected $_longitude; //varchar(25)

    public function __construct
    ($id=NULL,$order_by=NULL,$type=NULL,$acronym=NULL,$latitude=NULL,$longitude=NULL)
    {
        parent::__construct("app_country");
        if($id!=NULL) $this->_id = $id;
        if($order_by!=NULL) $this->_order_by = $order_by;
        if($type!=NULL) $this->_type = $type;
        if($acronym!=NULL) $this->_acronym = $acronym;
        if($latitude!=NULL) $this->_latitude = $latitude;
        if($longitude!=NULL) $this->_longitude = $longitude;
        //$this->arDescConfig = array("id","longitude","separator"=>" - ");
    }//__construct()

    public function load_by_id()
    {
        if($this->_id)
        {
            $this->oQuery->set_comment("load_by_id()");
            $this->oQuery->set_fields("$this->get_all_fields()");
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins(NULL);
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id=$this->_id");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        $this->row_assign($arRow);
    }//load_by_id()
    
    public function load_by_erp()
    {
        if($this->_code_erp)
        {
            $this->oQuery = new ComponentQuery();
            $this->oQuery->set_comment("load_by_erptype()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins();
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->add_and("$this->_table_name.code_erp='$this->_code_erp'");
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
    public function get_type(){return $this->_type;}
    public function get_acronym(){return $this->_acronym;}
    public function get_latitude(){return $this->_latitude;}
    public function get_longitude(){return $this->_longitude;}
    //===================
    //       SETS
    //===================
    public function set_order_by($value){$this->_order_by = $value;}
    public function set_type($value){$this->_type = $value;}
    public function set_acronym($value){$this->_acronym = $value;}
    public function set_latitude($value){$this->_latitude = $value;}
    public function set_longitude($value){$this->_longitude = $value;}
}
