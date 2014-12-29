<?php
/**
* @author Module Builder 1.0.2
* @link www.eduardoaf.com
* @version 1.0.2
* @name ModelProductArray
* @file model_ModelProductArray     
* @date 20-04-2014 00:18 (SPAIN)
* @observations: 
*/
include_once("theapplication_model.php");

class ModelProductArray extends TheApplicationModel
{
    private $_orderby; //int(4)
    private $_type; //varchar(15)

    public function __construct($id=NULL,
    $orderby=NULL,$type=NULL)
    {
        parent::__construct("app_product_array");
        if($id!=NULL) $this->_id = $id;
        if($orderby!=NULL) $this->_orderby = $orderby;
        if($type!=NULL) $this->_type = $type;
    }

    public function insert()
    {
        $orderby = mssqlclean($this->_orderby,1);
        $type = mssqlclean($this->_type);

        $sSQL = "INSERT INTO $this->_table_name
        (orderby,type)
        VALUES
        ($orderby,'$type')";
        $this->execute($sSQL);
    }

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

        $this->_orderby = $arRow["orderby"];
        $this->_type = $arRow["type"];
        //BASE FIELDS
        $this->_id = $arRow["id"];
        $this->_insert_user = $arRow["insert_user"];
        $this->_insert_date = $arRow["insert_date"];
        $this->_update_user = $arRow["update_user"];
        $this->_update_date = $arRow["update_date"];
        $this->_code_erp = $arRow["code_erp"];
        $this->_description = $arRow["description"];
        $this->_delete_date = $arRow["delete_date"];
        $this->_delete_user = $arRow["delete_user"];
        $this->_is_enabled = $arRow["is_enabled"];
        $this->_is_erpsent = $arRow["is_erpsent"];
        $this->_insert_platform = $arRow["insert_platform"];
        $this->_processflag = $arRow["processflag"];
    }

    //===================
    //       GETS
    //===================
    public function get_order_by(){return $this->_orderby;}
    public function get_type(){return $this->_type;}
    //===================
    //       SETS
    //===================
    public function set_order_by($value){$this->_orderby = $value;}
    public function set_type($value){$this->_type = $value;}
}
