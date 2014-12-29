<?php
/**
 * @author Module Builder 1.0.22
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name ModelSuspicionsInvolvedDetails
 * @file model_suspicions_involved_details.php
 * @date 03-05-2014 16:34 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelSuspicionsInvolvedDetails extends TheApplicationModel
{
    protected $_id_type; //int(4)
    protected $oType; //Model Object
    protected $_id_involved; //numeric(9)
    protected $oInvolved; //Model Object
    protected $_type; //varchar(15)

    public function __construct
    ($id=NULL,$id_type=NULL,$id_involved=NULL,$cru_csvnote=NULL,$type=NULL)
    {
        parent::__construct("app_suspicions_involved_details");
        if($id!=NULL) $this->_id = $id;
        if($id_type!=NULL) $this->_id_type = $id_type;
        if($id_involved!=NULL) $this->_id_involved = $id_involved;
        if($cru_csvnote!=NULL) $this->_cru_csvnote = $cru_csvnote;
        if($type!=NULL) $this->_type = $type;
        //$this->arDescConfig = array("id","type","separator"=>" - ");
    }//__construct()

    public function load_by_id()
    {
        if($this->_id)
        {
            $this->oQuery->set_comment("load_by_id()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id=$this->_id");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        $this->row_assign($arRow);
    }//load_by_id()

    public function get_by_involved_and_type()
    {
        if($this->_id_involved && $this->_type)
        {
            $this->oQuery->set_comment("get_by_suspicion_and_type()");
            $this->oQuery->set_fields("$this->_table_name.id_type");
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id_involved=$this->_id_involved");
            $this->oQuery->add_and("$this->_table_name.type='$this->_type'");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRows = $this->query($sSQL);
            $arRows = $this->get_column_values($arRows,"id_type");
        }
        return $arRows;
    }
    
    //===================
    //       GETS
    //===================
    public function get_id_type(){return $this->_id_type;}
    public function get_otype()
    {
        $this->oType = new ModelType($this->_id_type);
        $this->oType->load_by_id();
        return $this->oType;
    }
    public function get_id_involved(){return $this->_id_involved;}
    public function get_involved()
    {
        $this->oInvolved = new ModelInvolved($this->_id_involved);
        $this->oInvolved->load_by_id();
        return $this->oInvolved;
    }

    public function get_type(){return $this->_type;}
    //===================
    //       SETS
    //===================
    public function set_id_type($value){$this->_id_type = $value;}
    public function set_otype($oValue){$this->oType = $oValue;}
    public function set_id_involved($value){$this->_id_involved = $value;}
    public function set_involved($oValue){$this->oInvolved = $oValue;}
    public function set_type($value){$this->_type = $value;}
}
