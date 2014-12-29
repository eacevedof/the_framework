<?php
/**
 * @author Eduardo Acevedo Farje
 * @link www.eduardoaf.com
 * @version 1.0.4
 * @name ModelOrderLine
 * @file model_order_line.php     
 * @date 05-10-2014 03:01 (SPAIN)
 * @observations: 
 */
include_once("theapplication_model.php");

class ModelOrderLine extends TheApplicationModel
{
    private $_id_order_head; //numeric(9)
    private $_id_product; //numeric(9)
    private $_num_items; //numeric(5)
    private $_is_free; //numeric(5)
    private $_unit_price; //numeric(9)
    private $_discount; //numeric(9)
    private $_amount; //numeric(9)
    private $_product; //varchar(200)
    private $_line; //int(4)
    
    private $_sold_items;
    private $_free_items;
    
    public function __construct(
    $id_order_head=NULL,$id_product=NULL,$num_items=NULL,$is_free=NULL,$unit_price=NULL
            ,$discount=NULL,$amount=NULL,$product=NULL)
    {
        parent::__construct("app_order_line");
        if($id_order_head!=NULL) $this->_id_order_head = $id_order_head;
        if($id_product!=NULL) $this->_id_product = $id_product;
        if($num_items!=NULL) $this->_num_items = $num_items;
        if($is_free!=NULL) $this->_is_free = $is_free;
        if($unit_price!=NULL) $this->_unit_price = $unit_price;
        if($discount!=NULL) $this->_discount = $discount;
        if($amount!=NULL) $this->_amount = $amount;
        if($product!=NULL) $this->_product = $product;
    }

    public function insert()
    {
        $id_order_head = mssqlclean($this->_id_order_head,1);
        $id_product = mssqlclean($this->_id_product,1);
        $num_items = mssqlclean($this->_num_items,1);
        $unit_price = mssqlclean($this->_unit_price,1);
        $discount = mssqlclean($this->_discount,1);
        $amount = mssqlclean($this->_amount,1);
        $product = mssqlclean($this->_product);

        $sSQL = "INSERT INTO $this->_table_name
        (id_order_head,id_product,num_items,unit_price,discount,amount,product)
        VALUES
        ($id_order_head,$id_product,$num_items,$unit_price,$discount,$amount,'$product')";
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
        
        $this->_id_order_head = $arRow["id_order_head"];
        $this->_id_product = $arRow["id_product"];
        $this->_num_items = $arRow["num_items"];
        $this->_is_free = $arRow["is_free"]; 
        $this->_unit_price = $arRow["unit_price"];
        $this->_discount = $arRow["discount"];
        $this->_amount = $arRow["amount"];
        $this->_product = $arRow["product"];
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
        $this->_line = $arRow["line"];
        //bug($arRow,"lines.loadbyid()");bug($this->get_id_product(),"get_id_product");die;
    }

    public function get_select_ids_by_head($id_order_head)
    {
        
        $this->oQuery->set_comment("get_select_by_head()");
        $this->oQuery->set_top($this->_top);
        $this->oQuery->set_fields("$this->_table_name.id");
        $this->oQuery->set_fromtables($this->_table_name);
        //Por defecto se aplica la jerarquia por cliente
        //$this->oQuery->add_joins($this->build_userhierarchy_join($this->_select_user,"customer","id"));
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.id_order_head=$id_order_head");

        //EXTRA AND
        //$this->oQuery->add_and($this->build_sql_filters());
        //ORDERBY 
        //default orderby
        $this->oQuery->set_orderby("$this->_table_name.id DESC");
        $sOrderByAuto = $this->build_sql_orderby();
        if($sOrderByAuto) $this->oQuery->set_orderby($sOrderByAuto);
        $sSQL = $this->oQuery->get_select();
        //bug($sSQL);
        return $this->query($sSQL);
    }
    
    public function load_new_line()
    {
        $sSQL=
        "SELECT MAX(line)+10 AS newline
         FROM $this->_table_name
         WHERE id_order_head=$this->_id_order_head
        ";
        $this->_line = $this->query($sSQL,1,1);
        if(!$this->_line) $this->_line = 10;
    }
    
   
    public function quarantine_by_head()
    {
        if($this->_id_order_head)
        {
            $sTimeNow = date("YmdHis");
            $sSQL = " /*quarantine_by_head()*/
                UPDATE $this->_table_name
                SET delete_user='$this->iSessionUserId'
                ,delete_date='$sTimeNow'
                ,delete_platform='$this->_platform'
                WHERE id_order_head='$this->_id_order_head'
                ";
            $this->execute($sSQL);
        }
    }

    //===================
    //       GETS
    //===================
    public function get_id_order_head(){return $this->_id_order_head;}
    public function get_id_product(){return $this->_id_product;}
    public function get_num_items(){return $this->_num_items;}
    public function get_is_free(){return $this->_is_free;}
    public function get_unit_price(){return $this->_unit_price;}
    public function get_discount(){return $this->_discount;}
    public function get_amount(){return $this->_amount;}
    public function get_product(){return $this->_product;}
    public function get_line(){return $this->_line;}
    public function get_sold_items()
    {
        $sSQL = "SELECT SUM(num_items) AS sold
                FROM app_order_line 
                WHERE id_order_head=$this->_id_order_head
                AND is_free != 'YES'
                AND delete_date IS NULL";
        $this->_sold_items = $this->query($sSQL,1,1);
        return $this->_sold_items;
    }
    
    public function get_free_items()
    {
        $sSQL = "SELECT SUM(num_items) AS sold
                FROM app_order_line 
                WHERE id_order_head=$this->_id_order_head
                AND is_free != 'NO'
                AND delete_date IS NULL";
        $this->_free_items = $this->query($sSQL,1,1);
        return $this->_free_items;        
    }
    public function is_free(){return ($this->_is_free=="YES")? TRUE: FALSE;}
    
    //===================
    //       SETS
    //===================
    public function set_id_order_head($value){$this->_id_order_head = $value;}
    public function set_id_product($value){$this->_id_product = $value;}
    public function set_num_items($value){$this->_num_items = $value;}
    public function set_is_free($value){$this->_is_free = $value;}
    public function set_unit_price($value){$this->_unit_price = $value;}
    public function set_discount($value){$this->_discount = $value;}
    public function set_amount($value){$this->_amount = $value;}
    public function set_product($value){$this->_product = $value;}
    public function set_line($value){$this->_line = $value;}
    
}
