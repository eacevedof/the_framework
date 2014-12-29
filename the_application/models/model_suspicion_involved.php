<?php
/**
 * @author Module Builder 1.0.22
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name ModelSuspicionInvolved
 * @file model_suspicion_involved.php
 * @date 03-05-2014 16:27 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelSuspicionInvolved extends TheApplicationModel
{
    protected $_number; //int(4)
    protected $_id_suspicion; //numeric(9)
    protected $oSuspicion; //Model Object
    protected $_nr_account; //varchar(50)
    protected $_nr_creditcard; //varchar(50)
    protected $_nr_mtcn; //varchar(50)
    protected $_nr_police; //varchar(50)
    protected $_nr_brief; //varchar(50)
    protected $_nr_client; //varchar(50)
    protected $_address_zip; //varchar(10)
    protected $_address_type_country; //varchar(50)
    protected $_phone; //varchar(25)
    protected $_phone_type; //varchar(15)
    protected $_phone_type_country; //varchar(50)
    protected $_iddoc_issue_country; //varchar(50)
    protected $_iddoc_nationality; //varchar(50)
    protected $_address; //varchar(125)
    protected $_address_nr; //varchar(15)
    protected $_address_chars; //varchar(15)
    protected $_address_place; //varchar(50)
    protected $_subj_type_profession; //varchar(125)
    protected $_iddoc; //varchar(200)
    protected $_iddoc_type; //varchar(15)
    protected $_iddoc_issue_date; //varchar(8)
    protected $_iddoc_expiry_date; //varchar(8)
    protected $_iddoc_issue_place; //varchar(50)
    protected $_subj_full_name; //varchar(125)
    protected $_subj_type_sex; //varchar(15)
    protected $_subj_birthdate; //varchar(8)
    protected $_subj_birthplace; //varchar(50)
    protected $_subj_birth_country; //varchar(50)
    protected $_subj_type_nation; //varchar(15)
    protected $_nr_extra; //varchar(50)
    protected $_subj_name; //varchar(100)
    protected $_subj_infix1; //varchar(15)
    protected $_subj_married_name; //varchar(50)
    protected $_subj_infix2; //varchar(15)
    protected $_subj_initials; //varchar(10)

    public function __construct
    ($id=NULL,$number=NULL,$id_suspicion=NULL,$nr_account=NULL,$nr_creditcard=NULL,$nr_mtcn=NULL,$nr_police=NULL,$nr_brief=NULL,$nr_client=NULL,$address_zip=NULL,$address_type_country=NULL,$phone=NULL,$phone_type=NULL,$phone_type_country=NULL,$iddoc_issue_country=NULL,$iddoc_nationality=NULL,$address=NULL,$address_nr=NULL,$address_chars=NULL,$address_place=NULL,$subj_type_profession=NULL,$iddoc=NULL,$iddoc_type=NULL,$iddoc_issue_date=NULL,$iddoc_expiry_date=NULL,$iddoc_issue_place=NULL,$subj_full_name=NULL,$subj_type_sex=NULL,$subj_birthdate=NULL,$subj_birthplace=NULL,$subj_birth_country=NULL,$subj_type_nation=NULL,$nr_extra=NULL,$subj_name=NULL,$subj_infix1=NULL,$subj_married_name=NULL,$subj_infix2=NULL,$subj_initials=NULL)
    {
        parent::__construct("app_suspicion_involved");
        if($id!=NULL) $this->_id = $id;
        if($number!=NULL) $this->_number = $number;
        if($id_suspicion!=NULL) $this->_id_suspicion = $id_suspicion;
        if($nr_account!=NULL) $this->_nr_account = $nr_account;
        if($nr_creditcard!=NULL) $this->_nr_creditcard = $nr_creditcard;
        if($nr_mtcn!=NULL) $this->_nr_mtcn = $nr_mtcn;
        if($nr_police!=NULL) $this->_nr_police = $nr_police;
        if($nr_brief!=NULL) $this->_nr_brief = $nr_brief;
        if($nr_client!=NULL) $this->_nr_client = $nr_client;
        if($address_zip!=NULL) $this->_address_zip = $address_zip;
        if($address_type_country!=NULL) $this->_address_type_country = $address_type_country;
        if($phone!=NULL) $this->_phone = $phone;
        if($phone_type!=NULL) $this->_phone_type = $phone_type;
        if($phone_type_country!=NULL) $this->_phone_type_country = $phone_type_country;
        if($iddoc_issue_country!=NULL) $this->_iddoc_issue_country = $iddoc_issue_country;
        if($iddoc_nationality!=NULL) $this->_iddoc_nationality = $iddoc_nationality;
        if($address!=NULL) $this->_address = $address;
        if($address_nr!=NULL) $this->_address_nr = $address_nr;
        if($address_chars!=NULL) $this->_address_chars = $address_chars;
        if($address_place!=NULL) $this->_address_place = $address_place;
        if($subj_type_profession!=NULL) $this->_subj_type_profession = $subj_type_profession;
        if($iddoc!=NULL) $this->_iddoc = $iddoc;
        if($iddoc_type!=NULL) $this->_iddoc_type = $iddoc_type;
        if($iddoc_issue_date!=NULL) $this->_iddoc_issue_date = $iddoc_issue_date;
        if($iddoc_expiry_date!=NULL) $this->_iddoc_expiry_date = $iddoc_expiry_date;
        if($iddoc_issue_place!=NULL) $this->_iddoc_issue_place = $iddoc_issue_place;
        if($subj_full_name!=NULL) $this->_subj_full_name = $subj_full_name;
        if($subj_type_sex!=NULL) $this->_subj_type_sex = $subj_type_sex;
        if($subj_birthdate!=NULL) $this->_subj_birthdate = $subj_birthdate;
        if($subj_birthplace!=NULL) $this->_subj_birthplace = $subj_birthplace;
        if($subj_birth_country!=NULL) $this->_subj_birth_country = $subj_birth_country;
        if($subj_type_nation!=NULL) $this->_subj_type_nation = $subj_type_nation;
        if($nr_extra!=NULL) $this->_nr_extra = $nr_extra;
        if($subj_name!=NULL) $this->_subj_name = $subj_name;
        if($subj_infix1!=NULL) $this->_subj_infix1 = $subj_infix1;
        if($subj_married_name!=NULL) $this->_subj_married_name = $subj_married_name;
        if($subj_infix2!=NULL) $this->_subj_infix2 = $subj_infix2;
        if($subj_initials!=NULL) $this->_subj_initials = $subj_initials;
        //$this->arDescConfig = array("id","subj_initials","separator"=>" - ");
    }//__construct()

    public function insert()
    {
        $number = mssqlclean($this->_number,1);
        $id_suspicion = mssqlclean($this->_id_suspicion,1);
        $nr_account = mssqlclean($this->_nr_account);
        $nr_creditcard = mssqlclean($this->_nr_creditcard);
        $nr_mtcn = mssqlclean($this->_nr_mtcn);
        $nr_police = mssqlclean($this->_nr_police);
        $nr_brief = mssqlclean($this->_nr_brief);
        $nr_client = mssqlclean($this->_nr_client);
        $address_zip = mssqlclean($this->_address_zip);
        $address_type_country = mssqlclean($this->_address_type_country);
        $phone = mssqlclean($this->_phone);
        $phone_type = mssqlclean($this->_phone_type);
        $phone_type_country = mssqlclean($this->_phone_type_country);
        $iddoc_issue_country = mssqlclean($this->_iddoc_issue_country);
        $iddoc_nationality = mssqlclean($this->_iddoc_nationality);
        $address = mssqlclean($this->_address);
        $address_nr = mssqlclean($this->_address_nr);
        $address_chars = mssqlclean($this->_address_chars);
        $address_place = mssqlclean($this->_address_place);
        $subj_type_profession = mssqlclean($this->_subj_type_profession);
        $iddoc = mssqlclean($this->_iddoc);
        $iddoc_type = mssqlclean($this->_iddoc_type);
        $iddoc_issue_date = mssqlclean($this->_iddoc_issue_date);
        $iddoc_expiry_date = mssqlclean($this->_iddoc_expiry_date);
        $iddoc_issue_place = mssqlclean($this->_iddoc_issue_place);
        $subj_full_name = mssqlclean($this->_subj_full_name);
        $subj_type_sex = mssqlclean($this->_subj_type_sex);
        $subj_birthdate = mssqlclean($this->_subj_birthdate);
        $subj_birthplace = mssqlclean($this->_subj_birthplace);
        $subj_birth_country = mssqlclean($this->_subj_birth_country);
        $subj_type_nation = mssqlclean($this->_subj_type_nation);
        $nr_extra = mssqlclean($this->_nr_extra);
        $subj_name = mssqlclean($this->_subj_name);
        $subj_infix1 = mssqlclean($this->_subj_infix1);
        $subj_married_name = mssqlclean($this->_subj_married_name);
        $subj_infix2 = mssqlclean($this->_subj_infix2);
        $subj_initials = mssqlclean($this->_subj_initials);

        $sSQL = "INSERT INTO $this->_table_name
        (number,id_suspicion,nr_account,nr_creditcard,nr_mtcn,nr_police,nr_brief,nr_client,address_zip,address_type_country,phone,phone_type,phone_type_country,iddoc_issue_country,iddoc_nationality,address,address_nr,address_chars,address_place,subj_type_profession,iddoc,iddoc_type,iddoc_issue_date,iddoc_expiry_date,iddoc_issue_place,subj_full_name,subj_type_sex,subj_birthdate,subj_birthplace,subj_birth_country,subj_type_nation,nr_extra,subj_name,subj_infix1,subj_married_name,subj_infix2,subj_initials)
        VALUES
        ($number,$id_suspicion,'$nr_account','$nr_creditcard','$nr_mtcn','$nr_police','$nr_brief','$nr_client','$address_zip','$address_type_country','$phone','$phone_type','$phone_type_country','$iddoc_issue_country','$iddoc_nationality','$address','$address_nr','$address_chars','$address_place','$subj_type_profession','$iddoc','$iddoc_type','$iddoc_issue_date','$iddoc_expiry_date','$iddoc_issue_place','$subj_full_name','$subj_type_sex','$subj_birthdate','$subj_birthplace','$subj_birth_country','$subj_type_nation','$nr_extra','$subj_name','$subj_infix1','$subj_married_name','$subj_infix2','$subj_initials')";
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

    
    public function load_by_number()
    {
        if($this->_id_suspicion && $this->_number)
        {
            $this->oQuery->set_comment("load_by_number()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id_suspicion=$this->_id_suspicion");
            $this->oQuery->add_and("$this->_table_name.number=$this->_number");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        $this->row_assign($arRow);        
    }//load_by_number
    
    //===================
    //       GETS
    //===================
    public function get_number(){return $this->_number;}
    public function get_id_suspicion(){return $this->_id_suspicion;}
    public function get_suspicion()
    {
        $this->oSuspicion = new ModelSuspicion($this->_id_suspicion);
        $this->oSuspicion->load_by_id();
        return $this->oSuspicion;
    }
    public function get_nr_account(){return $this->_nr_account;}
    public function get_nr_creditcard(){return $this->_nr_creditcard;}
    public function get_nr_mtcn(){return $this->_nr_mtcn;}
    public function get_nr_police(){return $this->_nr_police;}
    public function get_nr_brief(){return $this->_nr_brief;}
    public function get_nr_client(){return $this->_nr_client;}
    public function get_address_zip(){return $this->_address_zip;}
    public function get_address_type_country(){return $this->_address_type_country;}
    public function get_phone(){return $this->_phone;}
    public function get_phone_type(){return $this->_phone_type;}
    public function get_phone_type_country(){return $this->_phone_type_country;}
    public function get_iddoc_issue_country(){return $this->_iddoc_issue_country;}
    public function get_iddoc_nationality(){return $this->_iddoc_nationality;}
    public function get_address(){return $this->_address;}
    public function get_address_nr(){return $this->_address_nr;}
    public function get_address_chars(){return $this->_address_chars;}
    public function get_address_place(){return $this->_address_place;}
    public function get_subj_type_profession(){return $this->_subj_type_profession;}
    public function get_iddoc(){return $this->_iddoc;}
    public function get_iddoc_type(){return $this->_iddoc_type;}
    public function get_iddoc_issue_date(){return $this->_iddoc_issue_date;}
    public function get_iddoc_expiry_date(){return $this->_iddoc_expiry_date;}
    public function get_iddoc_issue_place(){return $this->_iddoc_issue_place;}
    public function get_subj_full_name(){return $this->_subj_full_name;}
    public function get_subj_type_sex(){return $this->_subj_type_sex;}
    public function get_subj_birthdate(){return $this->_subj_birthdate;}
    public function get_subj_birthplace(){return $this->_subj_birthplace;}
    public function get_subj_birth_country(){return $this->_subj_birth_country;}
    public function get_subj_type_nation(){return $this->_subj_type_nation;}
    public function get_nr_extra(){return $this->_nr_extra;}
    public function get_subj_name(){return $this->_subj_name;}
    public function get_subj_infix1(){return $this->_subj_infix1;}
    public function get_subj_married_name(){return $this->_subj_married_name;}
    public function get_subj_infix2(){return $this->_subj_infix2;}
    public function get_subj_initials(){return $this->_subj_initials;}
    //===================
    //       SETS
    //===================
    public function set_number($value){$this->_number = $value;}
    public function set_id_suspicion($value){$this->_id_suspicion = $value;}
    public function set_suspicion($oValue){$this->oSuspicion = $oValue;}
    public function set_nr_account($value){$this->_nr_account = $value;}
    public function set_nr_creditcard($value){$this->_nr_creditcard = $value;}
    public function set_nr_mtcn($value){$this->_nr_mtcn = $value;}
    public function set_nr_police($value){$this->_nr_police = $value;}
    public function set_nr_brief($value){$this->_nr_brief = $value;}
    public function set_nr_client($value){$this->_nr_client = $value;}
    public function set_address_zip($value){$this->_address_zip = $value;}
    public function set_address_type_country($value){$this->_address_type_country = $value;}
    public function set_phone($value){$this->_phone = $value;}
    public function set_phone_type($value){$this->_phone_type = $value;}
    public function set_phone_type_country($value){$this->_phone_type_country = $value;}
    public function set_iddoc_issue_country($value){$this->_iddoc_issue_country = $value;}
    public function set_iddoc_nationality($value){$this->_iddoc_nationality = $value;}
    public function set_address($value){$this->_address = $value;}
    public function set_address_nr($value){$this->_address_nr = $value;}
    public function set_address_chars($value){$this->_address_chars = $value;}
    public function set_address_place($value){$this->_address_place = $value;}
    public function set_subj_type_profession($value){$this->_subj_type_profession = $value;}
    public function set_iddoc($value){$this->_iddoc = $value;}
    public function set_iddoc_type($value){$this->_iddoc_type = $value;}
    public function set_iddoc_issue_date($value){$this->_iddoc_issue_date = $value;}
    public function set_iddoc_expiry_date($value){$this->_iddoc_expiry_date = $value;}
    public function set_iddoc_issue_place($value){$this->_iddoc_issue_place = $value;}
    public function set_subj_full_name($value){$this->_subj_full_name = $value;}
    public function set_subj_type_sex($value){$this->_subj_type_sex = $value;}
    public function set_subj_birthdate($value){$this->_subj_birthdate = $value;}
    public function set_subj_birthplace($value){$this->_subj_birthplace = $value;}
    public function set_subj_birth_country($value){$this->_subj_birth_country = $value;}
    public function set_subj_type_nation($value){$this->_subj_type_nation = $value;}
    public function set_nr_extra($value){$this->_nr_extra = $value;}
    public function set_subj_name($value){$this->_subj_name = $value;}
    public function set_subj_infix1($value){$this->_subj_infix1 = $value;}
    public function set_subj_married_name($value){$this->_subj_married_name = $value;}
    public function set_subj_infix2($value){$this->_subj_infix2 = $value;}
    public function set_subj_initials($value){$this->_subj_initials = $value;}
}
