<?php
/**
 * @author Module Builder 1.0.22
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ModelSuspicionsDetails
 * @file model_suspicions_details.php
 * @date 19-04-2014 15:49 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelSuspicionsDetails extends TheApplicationModel
{
    protected $_id_type; //int(4)
    protected $oType; //Model Object
    protected $_id_suspicion; //numeric(9)
    protected $oSuspicion; //Model Object
    protected $_type; //varchar(15)

    public function __construct
    ($id=NULL,$id_type=NULL,$id_suspicion=NULL,$type=NULL)
    {
        parent::__construct("app_suspicions_details");
        if($id!=NULL) $this->_id = $id;
        if($id_type!=NULL) $this->_id_type = $id_type;
        if($id_suspicion!=NULL) $this->_id_suspicion = $id_suspicion;
        if($type!=NULL) $this->_type = $type;
        //$this->arDescConfig = array("id","type","separator"=>" - ");
    }//__construct()

    public function insert()
    {
        $id_type = mssqlclean($this->_id_type,1);
        $id_suspicion = mssqlclean($this->_id_suspicion,1);
        $type = mssqlclean($this->_type);

        $sSQL = "INSERT INTO $this->_table_name
        (id_type,id_suspicion,type)
        VALUES
        ($id_type,$id_suspicion,'$type')";
        $this->execute($sSQL);
    }//insert()

    public function load_by_id()
    {
        if($this->_id)
        {
            $this->oQuery->set_comment("load_by_id()");
            $this->oQuery->set_fields($this->get_all_fields());
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

    public function get_by_suspicion_and_type()
    {
        if($this->_id_suspicion && $this->_type)
        {
            $this->oQuery->set_comment("get_by_suspicion_and_type()");
            $this->oQuery->set_fields("$this->_table_name.id_type");
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id_suspicion=$this->_id_suspicion");
            $this->oQuery->add_and("$this->_table_name.type='$this->_type'");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRows = $this->query($sSQL);
            $arRows = $this->get_column_values($arRows,"id_type");
        }
        return $arRows;
    }//get_by_suspicion_and_type
    
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
    public function get_id_type(){return $this->_id_type;}
    //	public function get_type()
    //	{
    //		$this->oType = new ModelType($this->_id_type);
    //		$this->oType->load_by_id();
    //		return $this->oType;
    //	}
    public function get_id_suspicion(){return $this->_id_suspicion;}
    public function get_suspicion()
    {
        $this->oSuspicion = new ModelSuspicion($this->_id_suspicion);
        $this->oSuspicion->load_by_id();
        return $this->oSuspicion;
    }
    public function get_type(){return $this->_type;}
    //===================
    //       SETS
    //===================
    public function set_id_type($value){$this->_id_type = $value;}
    //public function set_type($oValue){$this->oType = $oValue;}
    public function set_id_suspicion($value){$this->_id_suspicion = $value;}
    public function set_suspicion($oValue){$this->oSuspicion = $oValue;}
    public function set_type($value){$this->_type = $value;}
}
