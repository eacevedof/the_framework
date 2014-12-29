<?php
/**
 * @author Module Builder 1.0.22
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ModelProjectType
 * @file model_project_type.php
 * @date 10-12-2013 22:09 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelProjectType extends TheApplicationModel
{
    protected $_id_parent; //int(4)
    protected $oParent; //Model Object
    protected $_order_by; //int(4)
    protected $_type; //varchar(15)
    protected $_id_tosave; //varchar(25)
    protected $oTosave; //Model Object

    public function __construct
    ($id=NULL,$id_parent=NULL,$order_by=NULL,$type=NULL,$id_tosave=NULL)
    {
        parent::__construct("app_project_type");
        if($id!=NULL) $this->_id = $id;
        if($id_parent!=NULL) $this->_id_parent = $id_parent;
        if($order_by!=NULL) $this->_order_by = $order_by;
        if($type!=NULL) $this->_type = $type;
        if($id_tosave!=NULL) $this->_id_tosave = $id_tosave;
        //$this->arDescConfig = array("id","id_tosave","separator"=>" - ");
    }//__construct()

    public function insert()
    {
        $id_parent = mssqlclean($this->_id_parent,1);
        $order_by = mssqlclean($this->_order_by,1);
        $type = mssqlclean($this->_type);
        $id_tosave = mssqlclean($this->_id_tosave);

        $sSQL = "INSERT INTO $this->_table_name
        (id_parent,order_by,type,id_tosave)
        VALUES
        ($id_parent,$order_by,'$type','$id_tosave')";
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

        $this->_id_parent = $arRow["id_parent"];
        $this->_order_by = $arRow["order_by"];
        $this->_type = $arRow["type"];
        $this->_id_tosave = $arRow["id_tosave"];
        //BASE FIELDS
        $this->_id = $arRow["id"];
        $this->_insert_platform = $arRow["insert_platform"];
        $this->_insert_user = $arRow["insert_user"];
        $this->_insert_date = $arRow["insert_date"];
        $this->_update_platform = $arRow["update_platform"];
        $this->_update_user = $arRow["update_user"];
        $this->_update_date = $arRow["update_date"];
        $this->_code_erp = $arRow["code_erp"];
        $this->_description = $arRow["description"];
        $this->_delete_platform = $arRow["delete_platform"];
        $this->_delete_date = $arRow["delete_date"];
        $this->_delete_user = $arRow["delete_user"];
        $this->_is_enabled = $arRow["is_enabled"];
        $this->_is_erpsent = $arRow["is_erpsent"];
        $this->_processflag = $arRow["processflag"];
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
    public function get_id_parent(){return $this->_id_parent;}
    public function get_parent()
    {
        $this->oParent = new ModelParent($this->_id_parent);
        $this->oParent->load_by_id();
        return $this->oParent;
    }
    public function get_order_by(){return $this->_order_by;}
    public function get_type(){return $this->_type;}
    public function get_id_tosave(){return $this->_id_tosave;}
    public function get_tosave()
    {
        $this->oTosave = new ModelTosave($this->_id_tosave);
        $this->oTosave->load_by_id();
        return $this->oTosave;
    }
    //===================
    //       SETS
    //===================
    public function set_id_parent($value){$this->_id_parent = $value;}
    public function set_parent($oValue){$this->oParent = $oValue;}
    public function set_order_by($value){$this->_order_by = $value;}
    public function set_type($value){$this->_type = $value;}
    public function set_id_tosave($value){$this->_id_tosave = $value;}
    public function set_tosave($oValue){$this->oTosave = $oValue;}
}
