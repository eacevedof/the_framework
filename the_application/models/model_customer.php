<?php
/**
 * @author Module Builder 1.0.0
 * @link www.eduardoaf.com
 * @version 1.0.4
 * @name ModelCustomer
 * @file model_customer.php    
 * @date 14-08-2013 09:45 (SPAIN)
 * @observations: 
 */
include_once("theapplication_model.php");

class ModelCustomer extends TheApplicationModel
{
    private $_id_seller; //int(4)
    private $_id_country; //numeric(9)
    private $_id_type; //numeric(9)
    private $_last_sale; //numeric(9)
    private $_first_name; //varchar(100)
    private $_last_name; //varchar(100)
    private $_company; //varchar(100)
    private $_address; //varchar(200)
    private $_email; //varchar(50)
    private $_phone_1; //varchar(15)
    private $_phone_2; //varchar(15)
    private $_contact; //varchar(100)
    private $_contact_phone; //varchar(15)
    private $_is_robinson_email; //varchar(3)
    private $_is_validated; //varchar(3)

    public function __construct($id=NULL,
$id_seller=NULL,$id_country=NULL,$id_type=NULL,$last_sale=NULL,$first_name=NULL,$last_name=NULL
            ,$company=NULL,$address=NULL,$email=NULL,$phone_1=NULL,$phone_2=NULL,$contact=NULL
            ,$contact_phone=NULL,$is_robinson_email=NULL,$is_validated=NULL)
    {
        parent::__construct("app_customer");
        if($id!=NULL) $this->_id = $id;
        if($id_seller!=NULL) $this->_id_seller = $id_seller;
        if($id_country!=NULL) $this->_id_country = $id_country;
        if($id_type!=NULL) $this->_id_type = $id_type;
        if($last_sale!=NULL) $this->_last_sale = $last_sale;
        if($first_name!=NULL) $this->_first_name = $first_name;
        if($last_name!=NULL) $this->_last_name = $last_name;
        if($company!=NULL) $this->_company = $company;
        if($address!=NULL) $this->_address = $address;
        if($email!=NULL) $this->_email = $email;
        if($phone_1!=NULL) $this->_phone_1 = $phone_1;
        if($phone_2!=NULL) $this->_phone_2 = $phone_2;
        if($contact!=NULL) $this->_contact = $contact;
        if($contact_phone!=NULL) $this->_contact_phone = $contact_phone;
        if($is_robinson_email!=NULL) $this->_is_robinson_email = $is_robinson_email;
        if($is_validated!=NULL) $this->_is_validated = $is_validated;
        
         $this->sSELECTfields = "app_customer.processflag
, app_customer.insert_platform
, app_customer.insert_user
, app_customer.insert_date
, app_customer.update_platform
, app_customer.update_user
, app_customer.update_date
, app_customer.delete_platform
, app_customer.delete_user
, app_customer.delete_date
, app_customer.is_erpsent
, app_customer.is_enabled
, app_customer.code_erp
, app_customer.id
, app_customer.first_name
, app_customer.last_name
, app_customer.description
, app_customer.company
, app_customer.address
, app_customer.email
, app_customer.phone_1
, app_customer.phone_2
, app_customer.contact
, app_customer.contact_phone
, app_customer.is_robinson_email
, app_customer.id_country
, app_customer.id_seller
, app_customer.id_type
, app_customer.last_sale
, app_customer.is_validated
, sel.description AS seller
        ";
        
        $this->oQuery->add_joins("LEFT JOIN app_seller AS sel
        ON app_customer.id_seller = sel.id");         
        $this->arDescConfig = array("id","company","separator"=>" - ");
        
        $this->arFieldsMappingExtra["seller"] = "sel.description";
    }

    public function insert()
    {
        $id_seller = mssqlclean($this->_id_seller,1);
        $id_country = mssqlclean($this->_id_country,1);
        $id_type = mssqlclean($this->_id_type,1);
        $last_sale = mssqlclean($this->_last_sale,1);
        $first_name = mssqlclean($this->_first_name);
        $last_name = mssqlclean($this->_last_name);
        $company = mssqlclean($this->_company);
        $address = mssqlclean($this->_address);
        $email = mssqlclean($this->_email);
        $phone_1 = mssqlclean($this->_phone_1);
        $phone_2 = mssqlclean($this->_phone_2);
        $contact = mssqlclean($this->_contact);
        $contact_phone = mssqlclean($this->_contact_phone);
        $is_robinson_email = mssqlclean($this->_is_robinson_email);
        $is_validated = mssqlclean($this->_is_validated);

        $sSQL = "INSERT INTO $this->_table_name
        (id_seller,id_country,id_type,last_sale,first_name,last_name,company,address
        ,email,phone_1,phone_2,contact,contact_phone,is_robinson_email,is_validated)
        VALUES
        ($id_seller,$id_country,$id_type,$last_sale,'$first_name','$last_name','$company','$address'
         ,'$email','$phone_1','$phone_2','$contact','$contact_phone','$is_robinson_email','$is_validated')";
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

        $this->_id_seller = $arRow["id_seller"];
        $this->_id_country = $arRow["id_country"];
        $this->_id_type = $arRow["id_type"];
        $this->_last_sale = $arRow["last_sale"];
        $this->_first_name = $arRow["first_name"];
        $this->_last_name = $arRow["last_name"];
        $this->_company = $arRow["company"];
        $this->_address = $arRow["address"];
        $this->_email = $arRow["email"];
        $this->_phone_1 = $arRow["phone_1"];
        $this->_phone_2 = $arRow["phone_2"];
        $this->_contact = $arRow["contact"];
        $this->_contact_phone = $arRow["contact_phone"];
        $this->_is_robinson_email = $arRow["is_robinson_email"];
        $this->_is_validated = $arRow["is_validated"];
        //fixed
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

    public function get_select_all_ids()
    {
        $this->oQuery->set_comment("get_select_all_ids() overridden");
        $this->oQuery->set_top($this->_top);
        $this->oQuery->set_fields("$this->_table_name.id");
        $this->oQuery->set_fromtables($this->_table_name);
        //Por defecto se aplica la jerarquia por cliente
        $this->oQuery->add_joins($this->build_userhierarchy_join($this->_select_user,"customer","id"));
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
        //bug($sSQL);
        return $this->query($sSQL);
    }//get_select_all_ids overriden   
    
    //===================
    //       GETS
    //===================
    public function get_id_seller(){return $this->_id_seller;}
    public function get_id_country(){return $this->_id_country;}
    public function get_id_type(){return $this->_id_type;}
    public function get_last_sale(){return $this->_last_sale;}
    public function get_first_name(){return $this->_first_name;}
    public function get_last_name(){return $this->_last_name;}
    public function get_company(){return $this->_company;}
    public function get_address(){return $this->_address;}
    public function get_email(){return $this->_email;}
    public function get_phone_1(){return $this->_phone_1;}
    public function get_phone_2(){return $this->_phone_2;}
    public function get_contact(){return $this->_contact;}
    public function get_contact_phone(){return $this->_contact_phone;}
    public function get_is_robinson_email(){return $this->_is_robinson_email;}
    public function get_is_validated(){return $this->_is_validated;}
    //===================
    //       SETS
    //===================
    public function set_id_seller($value){$this->_id_seller = $value;}
    public function set_id_country($value){$this->_id_country = $value;}
    public function set_id_type($value){$this->_id_type = $value;}
    public function set_last_sale($value){$this->_last_sale = $value;}
    public function set_first_name($value){$this->_first_name = $value;}
    public function set_last_name($value){$this->_last_name = $value;}
    public function set_company($value){$this->_company = $value;}
    public function set_address($value){$this->_address = $value;}
    public function set_email($value){$this->_email = $value;}
    public function set_phone_1($value){$this->_phone_1 = $value;}
    public function set_phone_2($value){$this->_phone_2 = $value;}
    public function set_contact($value){$this->_contact = $value;}
    public function set_contact_phone($value){$this->_contact_phone = $value;}
    public function set_is_robinson_email($value){$this->_is_robinson_email = $value;}
    public function set_is_validated($value){$this->_is_validated = $value;}
}
    