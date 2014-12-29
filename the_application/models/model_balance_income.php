<?php
/**
 * @author Module Builder 1.1.1
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name ModelBalanceIncome
 * @file model_balance_income.php
 * @date 05-10-2014 03:00 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
import_appmain("model");

class ModelBalanceIncome extends TheApplicationModel
{
    protected $_id_closed_by; //int(4)
    protected $oClosedBy; //Model Object
    protected $_id_type; //int(4)
    protected $oType; //Model Object
    protected $_subtotal_open; //numeric(9)
    protected $_subtotal_cash; //numeric(9)
    protected $_subtotal_creditcard; //numeric(9)
    protected $_subtotal_check; //numeric(9)
    protected $_subtotal; //numeric(9)
    protected $_subtotal_exclude; //numeric(9)    
    protected $_total_in; //numeric(9)
    protected $_receipt_nr; //varchar(25)
    protected $_receipt_date; //varchar(8)
    protected $_operation_date; //varchar(8)
    protected $_notes; //varchar(500)
    
    protected $sOperationDateStart;
    protected $sOperationDateEnd;
    protected $sReceiptDateStart;
    protected $sReceiptDateEnd;    

    public function __construct
    ($id=NULL,$id_closed_by=NULL,$id_type=NULL,$subtotal_open=NULL
    ,$subtotal_cash=NULL,$subtotal_creditcard=NULL,$subtotal_check=NULL,$subtotal=NULL
    ,$total=NULL,$receipt_nr=NULL,$receipt_date=NULL,$operation_date=NULL,$notes=NULL)
    {
        parent::__construct("app_balance_income");
        if($id!=NULL) $this->_id = $id;
        if($id_closed_by!=NULL) $this->_id_closed_by = $id_closed_by;
        if($id_type!=NULL) $this->_id_type = $id_type;
        if($subtotal_open!=NULL) $this->_subtotal_open = $subtotal_open;
        if($subtotal_cash!=NULL) $this->_subtotal_cash = $subtotal_cash;
        if($subtotal_creditcard!=NULL) $this->_subtotal_creditcard = $subtotal_creditcard;
        if($subtotal_check!=NULL) $this->_subtotal_check = $subtotal_check;
        if($subtotal!=NULL) $this->_subtotal = $subtotal;
        if($total!=NULL) $this->_total_in = $total;
        if($receipt_nr!=NULL) $this->_receipt_nr = $receipt_nr;
        if($receipt_date!=NULL) $this->_receipt_date = $receipt_date;
        if($operation_date!=NULL) $this->_operation_date = $operation_date;
        if($notes!=NULL) $this->_notes = $notes;
        
        //$this->arDescConfig = array("id","notes","separator"=>" - ");
        $this->sSELECTfields ="app_balance_income.processflag,app_balance_income.insert_platform,app_balance_income.insert_user,app_balance_income.insert_date
        ,app_balance_income.update_user,app_balance_income.update_date,app_balance_income.delete_user,app_balance_income.delete_date
        ,app_balance_income.delete_platform,app_balance_income.is_erpsent
        
        ,app_balance_income.is_enabled,app_balance_income.i,app_balance_income.id,app_balance_income.code_erp,app_balance_income.description
        ,app_balance_income.id_closed_by,app_balance_income.id_type,app_balance_income.id,app_balance_income.subtotal_open
        ,app_balance_income.subtotal_cash,app_balance_income.subtotal_creditcard,app_balance_income.subtotal_check,app_balance_income.subtotal
        ,app_balance_income.total_in,app_balance_income.subtotal_exclude
        
        ,app_balance_income.receipt_nr,app_balance_income.receipt_date
        ,app_balance_income.operation_date
        ,usr.description AS closedby
        ,blar.description AS type
        ";
        if($this->is_db_mssql())
            $this->sSELECTfields .= ",CONVERT(TEXT,app_balance_income.cru_csvnote) AS cru_csvnote,CONVERT(TEXT,app_balance_income.notes) AS notes";
        else
            $this->sSELECTfields .= ",app_balance_income.cru_csvnote,app_balance_income.notes";
        
        $this->oQuery->add_joins("LEFT JOIN base_user AS usr
        ON app_balance_income.id_closed_by = usr.id");
        
        if($this->is_table("app_balance_array_lang") && $this->_id_language)
            $this->oQuery->add_joins("LEFT JOIN app_balance_array_lang AS blar
                                      ON app_balance_income.id_type = blar.id_source
                                      AND blar.id_language='$this->_id_language'");
        else
            $this->oQuery->add_joins("LEFT JOIN app_balance_array AS blar
                                      ON app_balance_income.id_type = blar.id");
                
        $this->arFieldsMappingExtra = array
        (
            //"id"=>"app_order_head.id"
            //,"code_erp"=>"app_order_head.code_erp"
            "closedby" =>"usr.description"
            ,"type" =>"blar.description"
        );
    }//__construct()

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
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        $this->row_assign($arRow);
    }//load_by_id()

    public function get_select_all_ids()
    {
        $this->oQuery->set_comment("get_select_all_ids() overriden");
        $this->oQuery->set_fields("$this->_table_name.id");
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        //EXTRA AND
        $this->oQuery->add_and($this->build_sql_filters());
        
        if($this->sOperationDateStart)
            $this->oQuery->add_and("$this->_table_name.operation_date>='$this->sOperationDateStart'");
        if($this->sOperationDateEnd)
            $this->oQuery->add_and("$this->_table_name.operation_date<='$this->sOperationDateEnd'");
        
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
    
    //===================
    //       GETS
    //===================
    public function get_id_closed_by(){return $this->_id_closed_by;}
    public function get_closed_by()
    {
        $this->oClosedBy = new ModelUser($this->_id_closed_by);
        $this->oClosedBy->load_by_id();
        return $this->oClosedBy;
    }
    public function get_id_type(){return $this->_id_type;}
    public function get_type()
    {
        $this->oType = new ModelBalanceArray($this->_id_type);
        $this->oType->load_by_id();
        return $this->oType;
    }
    
    public function get_subtotal_open(){return $this->_subtotal_open;}
    public function get_subtotal_cash(){return $this->_subtotal_cash;}
    public function get_subtotal_creditcard(){return $this->_subtotal_creditcard;}
    public function get_subtotal_check(){return $this->_subtotal_check;}
    public function get_subtotal(){return $this->_subtotal;}
    public function get_subtotal_exclude(){return $this->_subtotal_exclude;}
    public function get_total_in(){return $this->_total_in;}
    public function get_receipt_nr(){return $this->_receipt_nr;}
    public function get_receipt_date(){return $this->_receipt_date;}
    public function get_operation_date(){return $this->_operation_date;}
    public function get_notes(){return $this->_notes;}
    //===================
    //       SETS
    //===================
    public function set_id_closed_by($value){$this->_id_closed_by = $value;}
    public function set_closed_by($oValue){$this->oClosedBy = $oValue;}
    public function set_id_type($value){$this->_id_type = $value;}
    public function set_type($oValue){$this->oType = $oValue;}
    public function set_subtotal_open($value){$this->_subtotal_open = $value;}
    public function set_subtotal_cash($value){$this->_subtotal_cash = $value;}
    public function set_subtotal_creditcard($value){$this->_subtotal_creditcard = $value;}
    public function set_subtotal_check($value){$this->_subtotal_check = $value;}
    public function set_subtotal($value){$this->_subtotal = $value;}
    public function set_subtotal_exclude($value){$this->_subtotal_exclude = $value;}
    public function set_total_in($value){$this->_total_in = $value;}
    public function set_receipt_nr($value){$this->_receipt_nr = $value;}
    public function set_receipt_date($value){$this->_receipt_date = $value;}
    public function set_operation_date($value){$this->_operation_date = $value;}
    public function set_notes($value){$this->_notes = $value;}
    
    public function set_receipt_date_start($value){$this->sReceiptDateStart = bodb_date($value);}
    public function set_receipt_date_end($value){$this->sReceiptDateEnd = bodb_date($value);}    
    public function set_operation_date_start($value){$this->sOperationDateStart = bodb_date($value);}
    public function set_operation_date_end($value){$this->sOperationDateEnd = bodb_date($value);}    
}
