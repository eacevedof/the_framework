<?php
/**
 * @author Module Builder 1.0.12
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name ModelCustomerNote
 * @file model_customer_note.php
 * @date 27-08-2013 07:53 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelCustomerNote extends TheApplicationModel
{
    protected $_id_user; //numeric(9)
    protected $oUser; //Model Object
    protected $_id_customer; //numeric(9)
    /**
     * @var ModelCustomer 
     */
    protected $oCustomer; //Model Object
    protected $_id_order; //numeric(9)
    protected $oOrder; //Model Object
    protected $_note; //varchar(255)
    protected $_date; //char(8)
    protected $_hour; //char(6)

    public function __construct
    ($id=NULL,$id_user=NULL,$id_customer=NULL,$id_order=NULL,$note=NULL,$date=NULL,$hour=NULL)
    {
        parent::__construct("app_customer_note");
        if($id!=NULL) $this->_id = $id;
        if($id_user!=NULL) $this->_id_user = $id_user;
        if($id_customer!=NULL) $this->_id_customer = $id_customer;
        if($id_order!=NULL) $this->_id_order = $id_order;
        if($note!=NULL) $this->_note = $note;
        if($date!=NULL) $this->_date = $date;
        if($hour!=NULL) $this->_hour = $hour;
        
        //$this->sSELECTfields = "$this->_table_name.id";
        $this->sSELECTfields ="app_customer_note.processflag,app_customer_note.insert_platform
        ,app_customer_note.insert_user,app_customer_note.insert_date
        ,app_customer_note.update_platform,app_customer_note.update_user,app_customer_note.update_date,app_customer_note.delete_platform
        ,app_customer_note.delete_user,app_customer_note.delete_date,app_customer_note.is_erpsent,app_customer_note.is_enabled
        ,app_customer_note.id,app_customer_note.code_erp,app_customer_note.description,app_customer_note.date,app_customer_note.hour
        ,app_customer_note.id_user,app_customer_note.id_customer,app_customer_note.id_order,app_customer_note.note
        , ord.description AS orderhead
        , cus.description AS customer        
        , usr.description AS notifier
        ";
        $this->oQuery->set_fields($this->sSELECTfields);
        $this->oQuery->add_joins("LEFT JOIN app_customer AS cus
        ON app_customer_note.id_customer = cus.id
        LEFT JOIN app_order_head AS ord
        ON app_customer_note.id_order = ord.id
        LEFT JOIN base_user AS usr
        ON app_customer_note.id_user = usr.id");
        
        $this->arFieldsMappingExtra = array
        (
            "orderhead" =>"ord.description"
            ,"customer" =>"cus.description"
            ,"notifier" =>"usr.description"
        );        
        //$this->arDescConfig = array("id","hour","separator"=>" - ");
    }//__construct()

    public function insert()
    {
        $id_user = mssqlclean($this->_id_user,1);
        $id_customer = mssqlclean($this->_id_customer,1);
        $id_order = mssqlclean($this->_id_order,1);
        $note = mssqlclean($this->_note);
        $date = mssqlclean($this->_date);
        $hour = mssqlclean($this->_hour);

        $sSQL = "INSERT INTO $this->_table_name
        (id_user,id_customer,id_order,note,date,hour)
        VALUES
        ($id_user,$id_customer,$id_order,'$note','$date','$hour')";
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

        $this->_id_user = $arRow["id_user"];
        $this->_id_customer = $arRow["id_customer"];
        $this->_id_order = $arRow["id_order"];
        $this->_note = $arRow["note"];
        $this->_date = $arRow["date"];
        $this->_hour = $arRow["hour"];
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
    public function get_id_user(){return $this->_id_user;}
    public function get_user()
    {
        $this->oUser = new ModelUser($this->_id_user);
        $this->oUser->load_by_id();
        return $this->oUser;
    }
    public function get_id_customer(){return $this->_id_customer;}
    public function get_customer()
    {
        $this->oCustomer = new ModelCustomer($this->_id_customer);
        $this->oCustomer->load_by_id();
        return $this->oCustomer;
    }
    public function get_id_order(){return $this->_id_order;}
    public function get_order()
    {
        $this->oOrder = new ModelOrder($this->_id_order);
        $this->oOrder->load_by_id();
        return $this->oOrder;
    }
    public function get_note(){return $this->_note;}
    public function get_date(){return $this->_date;}
    public function get_hour(){return $this->_hour;}
    //===================
    //       SETS
    //===================
    public function set_id_user($value){$this->_id_user = $value;}
    public function set_user($oValue){$this->oUser = $oValue;}
    public function set_id_customer($value){$this->_id_customer = $value;}
    public function set_customer($oValue){$this->oCustomer = $oValue;}
    public function set_id_order($value){$this->_id_order = $value;}
    public function set_order($oValue){$this->oOrder = $oValue;}
    public function set_note($value){$this->_note = $value;}
    public function set_date($value){$this->_date = $value;}
    public function set_hour($value){$this->_hour = $value;}
}
