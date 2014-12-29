<?php
/**
 * @author Module Builder 1.0.2
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name ModelOrderPromotion
 * @file model_order_promotion.php     
 * @date 04-10-2014 12:41 (SPAIN)
 * @observations: 
 */
import_appmain("model");

class ModelOrderPromotion extends TheApplicationModel
{
    private $_id_type_payment; //int(4)
    private $_min_units; //int(4)
    private $_units_for_free; //int(4)
    private $_units_free; //int(4)
    private $_min_units_discount; //numeric(5)

    public function __construct(
    $id_type_payment=NULL,$min_units=NULL,$units_for_free=NULL,$units_free=NULL,$min_units_discount=NULL
    )
    {
        parent::__construct("app_order_promotion");
        if($id_type_payment!=NULL) $this->_id_type_payment = $id_type_payment;
        if($min_units!=NULL) $this->_min_units = $min_units;
        if($units_for_free!=NULL) $this->_units_for_free = $units_for_free;
        if($units_free!=NULL) $this->_units_free = $units_free;
        if($min_units_discount!=NULL) $this->_min_units_discount = $min_units_discount;
    }

    public function insert()
    {
        $id_type_payment = mssqlclean($this->_id_type_payment,1);
        $min_units = mssqlclean($this->_min_units,1);
        $units_for_free = mssqlclean($this->_units_for_free,1);
        $units_free = mssqlclean($this->_units_free,1);
        $min_units_discount = mssqlclean($this->_min_units_discount,1);

        $sSQL = "INSERT INTO $this->_table_name
        (id_type_payment,min_units,units_for_free,units_free,min_units_discount,update_platform,update_date,delete_platform,is_enabled)
        VALUES
        ($id_type_payment,$min_units,$units_for_free,$units_free,$min_units_discount,'$update_platform','$update_date','$delete_platform','$is_enabled')";
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

        $this->_id_type_payment = $arRow["id_type_payment"];
        $this->_min_units = $arRow["min_units"];
        $this->_units_for_free = $arRow["units_for_free"];
        $this->_units_free = $arRow["units_free"];
        $this->_min_units_discount = $arRow["min_units_discount"];

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
        $this->_update_platform = $arRow["update_platform"];
        $this->_update_date = $arRow["update_date"];
        $this->_delete_platform = $arRow["delete_platform"];
        $this->_is_enabled = $arRow["is_enabled"];		
    }

    public function load_by_id_payment()
    {
        $sSQL = "-- load_by_id_payment() 
        SELECT * FROM $this->_table_name
        WHERE delete_date IS NULL 
        AND is_enabled='1'
        AND id_type_payment=$this->_id_type_payment";
        //bug($sSQL);
        $arRow = $this->query($sSQL,1);

        $this->_id_type_payment = $arRow["id_type_payment"];
        $this->_min_units = $arRow["min_units"];
        $this->_units_for_free = $arRow["units_for_free"];
        $this->_units_free = $arRow["units_free"];
        $this->_min_units_discount = $arRow["min_units_discount"];

        //BASE FIELDS
        $this->_id = $arRow["id"];
        $this->_insert_user = $arRow["insert_user"];
        $this->_insert_date = $arRow["insert_date"];
        $this->_update_user = $arRow["update_user"];
        $this->_update_da1te = $arRow["update_date"];
        $this->_code_erp = $arRow["code_erp"];
        $this->_description = $arRow["description"];
        $this->_delete_date = $arRow["delete_date"];
        $this->_delete_user = $arRow["delete_user"];
        $this->_is_enabled = $arRow["is_enabled"];
        $this->_is_erpsent = $arRow["is_erpsent"];
        $this->_insert_platform = $arRow["insert_platform"];
        $this->_processflag = $arRow["processflag"];
        $this->_update_platform = $arRow["update_platform"];
        $this->_update_date = $arRow["update_date"];
        $this->_delete_platform = $arRow["delete_platform"];		
    }
    
    //===================
    //       GETS
    //===================
    public function get_id_type_payment(){return $this->_id_type_payment;}
    public function get_min_units(){return $this->_min_units;}
    public function get_units_for_free(){return $this->_units_for_free;}
    public function get_units_free(){return $this->_units_free;}
    public function get_min_units_discount(){return $this->_min_units_discount;}
    //===================
    //       SETS
    //===================
    public function set_id_type_payment($value){$this->_id_type_payment = $value;}
    public function set_min_units($value){$this->_min_units = $value;}
    public function set_units_for_free($value){$this->_units_for_free = $value;}
    public function set_units_free($value){$this->_units_free = $value;}
    public function set_min_units_discount($value){$this->_min_units_discount = $value;}
}
