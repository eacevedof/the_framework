<?php
/**
 * @author Module Builder 1.0.0
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name ModelSeller
 * @file model_user.php    
 * @date 18-07-2013 18:44 (SPAIN)
 * @observations: 
 */
include_once("theapplication_model.php");

class ModelSeller extends TheApplicationModel
{
    private $_id_superior; //numeric(9)
    private $oSuperior; //Model Object
    private $_first_name; //varchar(100)
    private $_last_name; //varchar(100)
    private $_email; //varchar(100)
    private $_type; //varchar(15)

    public function __construct(
$id=NULL,$id_superior=NULL,$first_name=NULL,$last_name=NULL,$email=NULL,$type=NULL)
    {
        parent::__construct("app_seller");
        if($id!=NULL) $this->_id = $id;
        if($id_superior!=NULL) $this->_id_superior = $id_superior;
        if($first_name!=NULL) $this->_first_name = $first_name;
        if($last_name!=NULL) $this->_last_name = $last_name;
        if($email!=NULL) $this->_email = $email;
        if($type!=NULL) $this->_type = $type;
    }

    public function insert()
    {
        $id_superior = mssqlclean($this->_id_superior,1);
        $first_name = mssqlclean($this->_first_name);
        $last_name = mssqlclean($this->_last_name);
        $email = mssqlclean($this->_email);
        $type = mssqlclean($this->_type);

        $sSQL = "INSERT INTO $this->_table_name
        (id_superior,first_name,last_name,email,type)
        VALUES
        ($id_superior,'$first_name','$last_name','$email','$type')";
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

        $this->_id_superior = $arRow["id_superior"];
        $this->_first_name = $arRow["first_name"];
        $this->_last_name = $arRow["last_name"];
        $this->_email = $arRow["email"];
        $this->_type = $arRow["type"];
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
    }
    
    public function get_all_emails()
    {
        $sSQL = "SELECT DISTINCT email 
                 FROM $this->_table_name 
                 WHERE delete_date IS NULL
                 AND email IS NOT NULL
                 AND email != ''
                ";
        $arRows = $this->query($sSQL);
        $arRows = $this->get_column_values($arRows,"email");
        return $arRows;
    }

    //===================
    //       GETS
    //===================
    public function get_id_superior(){return $this->_id_superior;}
    public function get_superior()
    {
        $this->oSuperior = new ModelUser($this->_id_superior);
        $this->oSuperior->load_by_id();
        return $this->oSuperior;
    }
    public function get_first_name(){return $this->_first_name;}
    public function get_last_name(){return $this->_last_name;}
    public function get_email(){return $this->_email;}
    public function get_type(){return $this->_type;}
    //===================
    //       SETS
    //===================
    public function set_id_superior($value){$this->_id_superior = $value;}
    public function set_superior($oValue){$this->oSuperior = $oValue;}
    public function set_first_name($value){$this->_first_name = $value;}
    public function set_last_name($value){$this->_last_name = $value;}
    public function set_email($value){$this->_email = $value;}
    public function set_type($value){$this->_type = $value;}
}