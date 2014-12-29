<?php
/**
 * @author Module Builder 1.1.0
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ModelGentilic
 * @file model_gentilic.php
 * @date 09-06-2014 16:03 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelGentilic extends TheApplicationModel
{
    protected $_id_country; //int(4)
    protected $oCountry; //Model Object
    //por ahora lo comento
//    protected $_id_region; //int(4)
//    protected $oRegion; //Model Object
//    protected $_id_city; //int(4)
//    protected $oCity; //Model Object
    protected $_order_by; //int(4)
    protected $_cru_csvnote; //varchar(500)

    public function __construct
    ($id=NULL,$id_country=NULL,$id_region=NULL,$id_city=NULL,$order_by=NULL,$cru_csvnote=NULL)
    {
        parent::__construct("app_gentilic");
        if($id!=NULL) $this->_id = $id;
        if($id_country!=NULL) $this->_id_country = $id_country;
        if($id_region!=NULL) $this->_id_region = $id_region;
        if($id_city!=NULL) $this->_id_city = $id_city;
        if($order_by!=NULL) $this->_order_by = $order_by;
        if($cru_csvnote!=NULL) $this->_cru_csvnote = $cru_csvnote;
        //$this->arDescConfig = array("id","cru_csvnote","separator"=>" - ");
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
    public function get_id_country(){return $this->_id_country;}
    public function get_country()
    {
        $this->oCountry = new ModelCountry($this->_id_country);
        $this->oCountry->load_by_id();
        return $this->oCountry;
    }
//    public function get_id_region(){return $this->_id_region;}
//    public function get_region()
//    {
//        $this->oRegion = new ModelRegion($this->_id_region);
//        $this->oRegion->load_by_id();
//        return $this->oRegion;
//    }
//    public function get_id_city(){return $this->_id_city;}
//    public function get_city()
//    {
//        $this->oCity = new ModelCity($this->_id_city);
//        $this->oCity->load_by_id();
//        return $this->oCity;
//    }
    public function get_order_by(){return $this->_order_by;}
    //===================
    //       SETS
    //===================
    public function set_id_country($value){$this->_id_country = $value;}
    public function set_country($oValue){$this->oCountry = $oValue;}
//    public function set_id_region($value){$this->_id_region = $value;}
//    public function set_region($oValue){$this->oRegion = $oValue;}
//    public function set_id_city($value){$this->_id_city = $value;}
//    public function set_city($oValue){$this->oCity = $oValue;}
    public function set_order_by($value){$this->_order_by = $value;}
}
