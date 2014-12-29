<?php
/**
* @author Module Builder 1.0.22
* @link www.eduardoaf.com
* @version 1.0.1
* @name ModelSuspicionHead
* @file model_suspicion_head.php
* @date 19-04-2014 13:03 (SPAIN)
* @observations: 
* @requires: theapplication_model.php
*/
include_once("theapplication_model.php");

class ModelSuspicionHead extends TheApplicationModel
{
    protected $_id_transfer; //int(4)
    protected $oSuspicion; //Model Object
    protected $_id_isr; //int(4)
    protected $oIsr; //Model Object
    protected $_path_logo; //varchar(250)
    protected $_office_name; //varchar(100)
    protected $_number; //varchar(25)
    protected $_observations; //varchar(1000)
    protected $_user_creation; //varchar(100)
    protected $_date_creation; //varchar(8)
    protected $_notes; //varchar(150)
    protected $_hour_creation; //varchar(6)
    protected $_status; //varchar(50)
    protected $_type_char; //varchar(15)
    protected $_amount; //varchar(200)
    protected $_amount_cash; //varchar(200)
    protected $_filial_name; //varchar(150)

    public function __construct
    ($id=NULL,$id_transfer=NULL,$id_isr=NULL,$path_logo=NULL,$office_name=NULL,$number=NULL,$observations=NULL,$user_creation=NULL,$date_creation=NULL,$notes=NULL,$hour_creation=NULL,$status=NULL,$type_char=NULL,$amount=NULL,$amount_cash=NULL,$filial_name=NULL)
    {
        parent::__construct("app_suspicion_head");
        if($id!=NULL) $this->_id = $id;
        if($id_transfer!=NULL) $this->_id_transfer = $id_transfer;
        if($id_isr!=NULL) $this->_id_isr = $id_isr;
        if($path_logo!=NULL) $this->_path_logo = $path_logo;
        if($office_name!=NULL) $this->_office_name = $office_name;
        if($number!=NULL) $this->_number = $number;
        if($observations!=NULL) $this->_observations = $observations;
        if($user_creation!=NULL) $this->_user_creation = $user_creation;
        if($date_creation!=NULL) $this->_date_creation = $date_creation;
        if($notes!=NULL) $this->_notes = $notes;
        if($hour_creation!=NULL) $this->_hour_creation = $hour_creation;
        if($status!=NULL) $this->_status = $status;
        if($type_char!=NULL) $this->_type_char = $type_char;
        if($amount!=NULL) $this->_amount = $amount;
        if($amount_cash!=NULL) $this->_amount_cash = $amount_cash;
        if($filial_name!=NULL) $this->_filial_name = $filial_name;
        //$this->arDescConfig = array("id","filial_name","separator"=>" - ");
    }//__construct()

    public function insert()
    {
        $id_transfer = mssqlclean($this->_id_transfer,1);
        $id_isr = mssqlclean($this->_id_isr,1);
        $path_logo = mssqlclean($this->_path_logo);
        $office_name = mssqlclean($this->_office_name);
        $number = mssqlclean($this->_number);
        $observations = mssqlclean($this->_observations);
        $user_creation = mssqlclean($this->_user_creation);
        $date_creation = mssqlclean($this->_date_creation);
        $notes = mssqlclean($this->_notes);
        $hour_creation = mssqlclean($this->_hour_creation);
        $status = mssqlclean($this->_status);
        $type_char = mssqlclean($this->_type_char);
        $amount = mssqlclean($this->_amount);
        $amount_cash = mssqlclean($this->_amount_cash);
        $filial_name = mssqlclean($this->_filial_name);

        $sSQL = "INSERT INTO $this->_table_name
        (id_transfer,id_isr,path_logo,office_name,number,observations,user_creation,date_creation,notes,hour_creation,status,type_char,amount,amount_cash,filial_name)
        VALUES
        ($id_transfer,$id_isr,'$path_logo','$office_name','$number','$observations','$user_creation','$date_creation','$notes','$hour_creation','$status','$type_char','$amount','$amount_cash','$filial_name')";
        $this->execute($sSQL);
    }//insert()
            
    public function load_by_id()
    {
        if($this->_id)
        {
            $this->oQuery->set_comment("load_by_id()");
            //$this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins(NULL);
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id=$this->_id");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery,$sSQL);die;
            $arRow = $this->query($sSQL,1);
        }

        $this->row_assign($arRow);
    }//load_by_id()

    public function load_by_transfer()
    {
        if($this->_id_transfer)
        {
            $this->oQuery->set_comment("lodad_by_suspicion()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins(NULL);
            //$this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            //$this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id_transfer=$this->_id_transfer");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        //bug($arRow,"moldel_suspicion_head");
        $this->row_assign($arRow);
    }//load_by_suspicion()
    
    public function has_suspicion()
    {
        $hasSuspicion = false;
        return $hasSuspicion;
    }
    
    public function get_counter_number()
    {
        //bug($this->_date_creation,"date creation");
        if($this->_date_creation)
        {
            //occodcli,occonsec,ocobserv,ocfecing,ocfecmod,
            $this->oQuery = new ComponentQuery();
            $this->oQuery->set_comment("get_counter_number()");
            $this->oQuery->add_fields("COUNT($this->_table_name.id) AS maxday");
            $this->oQuery->set_fromtables($this->_table_name);
            //ya no uso date_creation pq es la fecha del giro y no la creacion del melding
            //$this->oQuery->add_and("$this->_table_name.date_creation='$this->_date_creation'");
            $this->oQuery->add_and("$this->_table_name.insert_date LIKE '$this->_date_creation%'");
            $sSQL = $this->oQuery->get_select();
            //bug($sSQL);
            $arRow = $this->query($sSQL,1,1);
            //bug($arRow);
            $arRow++;
            if(!$arRow) $arRow = "1";
            return sprintf("%03d",$arRow);
        }
        return "001";
    }
    
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
    public function get_id_transfer(){return $this->_id_transfer;}
    public function get_suspicion()
    {
        $this->oSuspicion = new ModelSuspicion($this->_id_transfer);
        $this->oSuspicion->load_by_id();
        return $this->oSuspicion;
    }
    public function get_id_isr(){return $this->_id_isr;}
//    public function get_isr()
//    {
//        $this->oIsr = new ModelIsr($this->_id_isr);
//        $this->oIsr->load_by_id();
//        return $this->oIsr;
//    }
    public function get_path_logo(){return $this->_path_logo;}
    public function get_office_name(){return $this->_office_name;}
    public function get_number(){return $this->_number;}
    public function get_observations(){return $this->_observations;}
    public function get_user_creation(){return $this->_user_creation;}
    public function get_date_creation(){return $this->_date_creation;}
    public function get_notes(){return $this->_notes;}
    public function get_hour_creation(){return $this->_hour_creation;}
    public function get_status(){return $this->_status;}
    public function get_type_char(){return $this->_type_char;}
    public function get_amount(){return $this->_amount;}
    public function get_amount_cash(){return $this->_amount_cash;}
    public function get_filial_name(){return $this->_filial_name;}
    //===================
    //       SETS
    //===================
    public function set_id_transfer($value){$this->_id_transfer = $value;}
    public function set_suspicion($oValue){$this->oSuspicion = $oValue;}
    public function set_id_isr($value){$this->_id_isr = $value;}
    public function set_isr($oValue){$this->oIsr = $oValue;}
    public function set_path_logo($value){$this->_path_logo = $value;}
    public function set_office_name($value){$this->_office_name = $value;}
    public function set_number($value){$this->_number = $value;}
    public function set_observations($value){$this->_observations = $value;}
    public function set_user_creation($value){$this->_user_creation = $value;}
    public function set_date_creation($value){$this->_date_creation = $value;}
    public function set_notes($value){$this->_notes = $value;}
    public function set_hour_creation($value){$this->_hour_creation = $value;}
    public function set_status($value){$this->_status = $value;}
    public function set_type_char($value){$this->_type_char = $value;}
    public function set_amount($value){$this->_amount = $value;}
    public function set_amount_cash($value){$this->_amount_cash = $value;}
    public function set_filial_name($value){$this->_filial_name = $value;}
}
