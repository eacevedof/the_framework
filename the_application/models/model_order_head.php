<?php
/**
 * @author Module Builder 1.0.2
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name ModelOrderHead
 * @file model_order_head.php     
 * @date 04-10-2013 18:00 (SPAIN)
 * @observations: 
 */
import_appmain("model");

class ModelOrderHead extends TheApplicationModel
{
    private $_id_type_validate; //int(4)
    private $_id_type_payment; //int(4)
    private $_id_seller; //numeric(9)
    private $_id_customer; //numeric(9)
    private $_amount_subtotal; //numeric(9)
    private $_discount; //numeric(9)
    private $_amount_discounted; //numeric(9)
    private $_amount_withtax; //numeric(9)
    private $_amount_total; //numeric(9)
    private $_id_delivery_user; //numeric(9)
    private $_date; //varchar(8)
    private $_hour; //varchar(6)
    private $_delivery_address; //varchar(200)
    private $_delivery_date; //varchar(8)
    private $_delivery_hour; //varchar(4)
    private $_is_payed; //varchar(3)
    private $_visit_hour; //varchar(6)
    private $_time_taking_start; //char(4)
    private $_time_taking_end; //char(4)
    
    public function __construct($id=NULL,
    $id_type_validate=NULL,$id_type_payment=NULL,$id_seller=NULL,$id_customer=NULL,$amount_subtotal=NULL
    ,$amount_withtax=NULL,$amount_total=NULL,$id_delivery_user=NULL,$date=NULL,$delivery_address=NULL
    ,$delivery_date=NULL,$delivery_hour=NULL,$is_payed=NULL,$hour=NULL,$discount=NULL,$amount_discounted=NULL
    ,$time_taking_start=NULL,$time_taking_end=NULL)
    {
        parent::__construct("app_order_head");
//        $this->arDescConfig = array
//        (
//            "id"=>""
//            ,"date"=>array("type"=>"date")
//            ,"separator"=>" - "
//        );
        
        if($id!=NULL) $this->_id = $id;
        if($id_type_validate!=NULL) $this->_id_type_validate = $id_type_validate;
        if($id_type_payment!=NULL) $this->_id_type_payment = $id_type_payment;
        if($id_seller!=NULL) $this->_id_seller = $id_seller;
        if($id_customer!=NULL) $this->_id_customer = $id_customer;
        if($amount_subtotal!=NULL) $this->_amount_subtotal = $amount_subtotal;
        if($discount!=NULL) $this->_discount = $discount;
        if($amount_discounted!=NULL) $this->_amount_discounted = $amount_discounted;
        if($amount_withtax!=NULL) $this->_amount_withtax = $amount_withtax;
        if($amount_total!=NULL) $this->_amount_total = $amount_total;
        if($id_delivery_user!=NULL) $this->_id_delivery_user = $id_delivery_user;
        if($date!=NULL) $this->_date = $date;
        if($hour!=NULL) $this->_hour = $hour;
        if($delivery_address!=NULL) $this->_delivery_address = $delivery_address;
        if($delivery_date!=NULL) $this->_delivery_date = $delivery_date;
        if($delivery_hour!=NULL) $this->_delivery_hour = $delivery_hour;
        if($is_payed!=NULL) $this->_is_payed = $is_payed;
        if($time_taking_start!=NULL) $this->_time_taking_start = $time_taking_start;
        if($time_taking_end!=NULL) $this->_time_taking_end = $time_taking_end;
        
        //Para el listado. Select * from app_order_head
        $this->sSELECTfields ="app_order_head.processflag, app_order_head.insert_platform, app_order_head.insert_user, app_order_head.insert_date, app_order_head.update_user
        , app_order_head.update_date , app_order_head.delete_user, app_order_head.delete_date, app_order_head.is_erpsent, app_order_head.is_enabled
        , app_order_head.id, app_order_head.code_erp, app_order_head.description, app_order_head.date, app_order_head.hour, app_order_head.id_seller, app_order_head.id_customer
        , app_order_head.id_type_validate, app_order_head.id_type_payment
        , app_order_head.amount_subtotal, app_order_head.amount_withtax, app_order_head.amount_total, app_order_head.delivery_address
        , app_order_head.delivery_date, app_order_head.delivery_hour, app_order_head.id_delivery_user, app_order_head.is_payed
        , sel.description AS seller        
        , cus.description AS customer        
        , val.description AS validate
        , pay.description AS payment
        , usr.description AS delivery_user
        , bar.description AS payed
        ";

        if($this->is_db_mssql())
            $this->sSELECTfields.=",url_lines = 'module=orders&section=orderlines&view=get_list_by_head&id_order_head='+CONVERT(VARCHAR,app_order_head.id)";
        else
            $this->sSELECTfields.=",CONCAT('module=orders&section=orderlines&view=get_list_by_head&id_order_head=',CAST(app_order_head.id AS CHAR)) AS url_lines";
        
        $this->oQuery->add_joins("LEFT JOIN app_customer AS cus
        ON app_order_head.id_customer = cus.id
        LEFT JOIN app_seller AS sel
        ON app_order_head.id_seller = sel.id
        LEFT JOIN app_order_array AS val
        ON app_order_head.id_type_validate = val.id 
        LEFT JOIN app_order_array AS pay
        ON app_order_head.id_type_payment = pay.id
        LEFT JOIN base_user AS usr
        ON app_order_head.id_delivery_user = usr.id
        LEFT JOIN base_array AS bar
        ON app_order_head.is_payed = bar.id_tosave");
        
        $this->arFieldsMappingExtra = array
        (
            //"id"=>"app_order_head.id"
            //,"code_erp"=>"app_order_head.code_erp"
            "seller" =>"sel.description"
            ,"customer" =>"cus.description"
            ,"validate" =>"val.description"
            ,"payment" =>"pay.description"
            ,"delivery_user" =>"usr.description"
            ,"payed" =>"bar.description"
        );
    }

    public function insert()
    {
        $id_type_validate = mssqlclean($this->_id_type_validate,1);
        $id_type_payment = mssqlclean($this->_id_type_payment,1);
        $id_seller = mssqlclean($this->_id_seller,1);
        $id_customer = mssqlclean($this->_id_customer,1);
        $amount_subtotal = mssqlclean($this->_amount_subtotal,1);
        $amount_withtax = mssqlclean($this->_amount_withtax,1);
        $amount_total = mssqlclean($this->_amount_total,1);
        $id_delivery_user = mssqlclean($this->_id_delivery_user,1);
        $date = mssqlclean($this->_date);
        $delivery_address = mssqlclean($this->_delivery_address);
        $delivery_date = mssqlclean($this->_delivery_date);
        $delivery_hour = mssqlclean($this->_delivery_hour);
        $is_payed = mssqlclean($this->_is_payed);

        $sSQL = "INSERT INTO $this->_table_name
        (id_type_validate,id_type_payment,id_seller,id_customer,amount_subtotal,amount_withtax,amount_total,id_delivery_user,date,delivery_address,delivery_date,delivery_hour,is_payed)
        VALUES
        ($id_type_validate,$id_type_payment,$id_seller,$id_customer,$amount_subtotal,$amount_withtax,$amount_total,$id_delivery_user,'$date','$delivery_address','$delivery_date','$delivery_hour','$is_payed')";
        $this->execute($sSQL);
    }

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
            //bug($sSQL); DIE;
            $arRow = $this->query($sSQL,1);
        }

        $this->_id_type_validate = $arRow["id_type_validate"];
        $this->_id_type_payment = $arRow["id_type_payment"];
        $this->_id_seller = $arRow["id_seller"];
        $this->_id_customer = $arRow["id_customer"];
        $this->_amount_subtotal = $arRow["amount_subtotal"];
        $this->_discount = $arRow["discount"];
        $this->_amount_discounted = $arRow["amount_discounted"];
        $this->_amount_withtax = $arRow["amount_withtax"];
        $this->_amount_total = $arRow["amount_total"];
        $this->_id_delivery_user = $arRow["id_delivery_user"];
        $this->_date = $arRow["date"];
        $this->_hour = $arRow["hour"];
        $this->_delivery_address = $arRow["delivery_address"];
        $this->_delivery_date = $arRow["delivery_date"];
        $this->_delivery_hour = $arRow["delivery_hour"];
        $this->_is_payed = $arRow["is_payed"];
        $this->_time_taking_start = $arRow["time_taking_start"];
        $this->_time_taking_end = $arRow["time_taking_end"];
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

    /**
     * Carga el total (amount_total) de lineas no borradas ni gratuitas (amount_subtotal)
     * a este total se aplica los calculos de descuento por promocion en cabecera (amount_discounted)
     * y se carga en los atributos.
     */
    public function load_amounts()
    {
        $sSQL = "SELECT SUM(amount) AS Total 
        FROM app_order_line
        WHERE delete_date IS NULL 
        AND id_order_head=$this->_id
        AND is_free='NO' ";
        //$this->_discount = float_round($this->_discount);
        $this->_amount_subtotal = (float)$this->query($sSQL,1,1);
        $this->_amount_discounted = ($this->_amount_subtotal * (float)$this->_discount)/100;
        $this->_amount_total = $this->_amount_subtotal -  $this->_amount_discounted;
    }
    
    public function get_select_all_ids()
    {
        $this->oQuery->set_comment("get_select_all_ids() overriden");
        $this->oQuery->set_fields("$this->_table_name.id");
        //si está definido $this->_select_user
        $this->oQuery->add_joins($this->build_userhierarchy_join($this->_select_user,"customer","id_customer"));
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        //EXTRA AND
        $this->oQuery->add_and($this->build_sql_filters());
        //ORDERBY 
        //default orderby
        $this->oQuery->set_orderby("$this->_table_name.id DESC");
        $sOrderByAuto = $this->build_sql_orderby();
        if($sOrderByAuto) $this->oQuery->set_orderby($sOrderByAuto);
        $sSQL = $this->oQuery->get_select();
        $this->oQuery->set_fields($this->sSELECTfields);
        //bug($sSQL);
        return $this->query($sSQL);
    }
    
    public function get_select_top10()
    {
        $idCustomer = $this->get_filter("id_customer");
        
        $sSQL = " /*get_select_top10()*/
        SELECT DISTINCT TOP 10 oh.id 
        , oh.date
        , ol.id_product, ol.product
        , ol.num_items
        , sel.description AS seller
        , cus.description AS customer
        , val.description AS validate 
        , pay.description AS payment         
        FROM app_order_head AS oh      
        INNER JOIN app_order_line AS ol
        ON oh.id = ol.id_order_head
        LEFT JOIN app_seller AS sel
        ON oh.id_seller = sel.id
        LEFT JOIN app_customer AS cus
        ON oh.id_customer = cus.id
        LEFT JOIN app_order_array AS val 
        ON oh.id_type_validate = val.id
        LEFT JOIN app_order_array AS pay 
        ON oh.id_type_payment = pay.id          
        WHERE oh.delete_date IS NULL
        AND oh.is_enabled='1'
        AND ol.delete_date IS NULL 
        AND ol.is_enabled='1'
        AND oh.id_customer='$idCustomer'
        /*1 TAKING ,2 PROCESSS , 7 VISIT,9 CANCELED,*/
        AND oh.id_type_validate NOT IN (1,2,7,9)
        ORDER BY oh.date DESC";
        //bug($sSQL); die;
        return $this->query($sSQL);
    }    
        
    public function has_lines()
    {
        $sSQL = "SELECT COUNT(id) AS numlines
            FROM app_order_line 
            WHERE id_order_head=$this->_id 
            AND delete_date IS NULL";
        return (boolean) $this->query($sSQL,1,1);
    }
    
    public function has_min_saleitems()
    {
        $sSQL = "SELECT SUM(num_items) AS numitems
            FROM app_order_line 
            WHERE id_order_head=$this->_id 
            AND is_free = 'NO'
            AND delete_date IS NULL";
        $iSoldItems = $this->query($sSQL,1,1);
        
        $sSQL = "SELECT min_units 
        FROM app_order_promotion        
        WHERE delete_date IS NULL 
        AND id_type_payment=$this->_id_type_payment";
        $iMinItems = $this->query($sSQL,1,1);
        
        if($iSoldItems<$iMinItems)
            return FALSE;
        return TRUE;
    }
    
    //===================
    //       GETS
    //===================
    public function get_id_type_validate(){return $this->_id_type_validate;}
    public function get_id_type_payment(){return $this->_id_type_payment;}
    public function get_id_seller(){return $this->_id_seller;}
    public function get_id_customer(){return $this->_id_customer;}
    public function get_amount_subtotal(){return $this->_amount_subtotal;}
    public function get_discount(){return $this->_discount;}
    public function get_amount_discounted(){return $this->_amount_discounted;}
    public function get_amount_withtax(){return $this->_amount_withtax;}
    public function get_amount_total(){return $this->_amount_total;}
    public function get_id_delivery_user(){return $this->_id_delivery_user;}
    public function get_date(){return $this->_date;}
    public function get_delivery_address(){return $this->_delivery_address;}
    public function get_delivery_date(){return $this->_delivery_date;}
    public function get_delivery_hour(){return $this->_delivery_hour;}
    public function get_is_payed(){return $this->_is_payed;}
    public function get_visit_hour(){return $this->_visit_hour;}
    public function get_hour(){return $this->_hour;}
    public function get_time_taking_start(){return $this->_time_taking_start;}
    public function get_time_taking_end(){return $this->_time_taking_end;}
    
    //===================
    //       SETS
    //===================
    public function set_id_type_validate($value){$this->_id_type_validate = $value;}
    public function set_id_type_payment($value){$this->_id_type_payment = $value;}
    public function set_id_seller($value){$this->_id_seller = $value;}
    public function set_id_customer($value){$this->_id_customer = $value;}
    public function set_amount_subtotal($value){$this->_amount_subtotal = $value;}
    public function set_discount($value){$this->_discount = $value;}
    public function set_amount_discounted($value){$this->_amount_discounted = $value;}
    public function set_amount_withtax($value){$this->_amount_withtax = $value;}
    public function set_amount_total($value){$this->_amount_total = $value;}
    public function set_id_delivery_user($value){$this->_id_delivery_user = $value;}
    public function set_date($value){$this->_date = $value;}
    public function set_delivery_address($value){$this->_delivery_address = $value;}
    public function set_delivery_date($value){$this->_delivery_date = $value;}
    public function set_delivery_hour($value){$this->_delivery_hour = $value;}
    public function set_is_payed($value){$this->_is_payed = $value;}
    public function set_visit_hour($value){$this->_visit_hour = $value;}
    public function set_hour($value){$this->_hour = $value;}
    public function set_time_taking_start($value){$this->_time_taking_start = $value;}
    public function set_time_taking_end($value){$this->_time_taking_end = $value;}
}
