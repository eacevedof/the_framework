<?php
/**
* @author Module Builder 1.1.1
* @link www.eduardoaf.com
* @version 1.0.2
* @name ModelBalanceOutcome
* @file model_balance_outcome.php
* @date 05-10-2014 03:00 (SPAIN)
* @observations: 
* @requires: theapplication_model.php
*/
import_appmain("model");

class ModelBalanceOutcome extends TheApplicationModel
{
    protected $_id_provider; //int(4)
    protected $oProvider; //Model Object
    protected $_id_type; //int(4)
    protected $oType; //Model Object
    protected $_is_paid; //int(4)
    protected $_subtotal_business; //numeric(9)
    protected $_subtotal_own; //numeric(9)
    protected $_subtotal; //numeric(9)
    protected $_total_out; //numeric(9)
    protected $_total_pending; //numeric(9)
    protected $_receipt_nr; //varchar(25)
    protected $_receipt_date; //varchar(8)
    protected $_operation_date; //varchar(8)
    protected $_notes; //varchar(500)

    protected $sOperationDateStart;
    protected $sOperationDateEnd;
    protected $sReceiptDateStart;
    protected $sReceiptDateEnd;    

    public function __construct
    ($id=NULL,$id_provider=NULL,$id_type=NULL,$is_paid=NULL,$subtotal_business=NULL
     ,$subtotal_own=NULL,$subtotal=NULL,$total_out=NULL,$receipt_nr=NULL,$receipt_date=NULL,$operation_date=NULL,$notes=NULL)
    {
        parent::__construct("app_balance_outcome");
        if($id!=NULL) $this->_id = $id;
        if($id_provider!=NULL) $this->_id_provider = $id_provider;
        if($id_type!=NULL) $this->_id_type = $id_type;
        if($is_paid!=NULL) $this->_is_paid = $is_paid;
        if($subtotal_business!=NULL) $this->_subtotal_business = $subtotal_business;
        if($subtotal_own!=NULL) $this->_subtotal_own = $subtotal_own;
        if($subtotal!=NULL) $this->_total = $subtotal;
        if($total_out!=NULL) $this->_total_out = $total_out;
        if($receipt_nr!=NULL) $this->_receipt_nr = $receipt_nr;
        if($receipt_date!=NULL) $this->_receipt_date = $receipt_date;
        if($operation_date!=NULL) $this->_operation_date = $operation_date;
        if($notes!=NULL) $this->_notes = $notes;
        //$this->arDescConfig = array("id","notes","separator"=>" - ");
        $this->sSELECTfields ="app_balance_outcome.i,app_balance_outcome.id_provider,app_balance_outcome.id_type,app_balance_outcome.is_paid
        ,app_balance_outcome.id,app_balance_outcome.subtotal_business,app_balance_outcome.subtotal_own,app_balance_outcome.subtotal
        ,app_balance_outcome.total_out,app_balance_outcome.total_pending,app_balance_outcome.processflag,app_balance_outcome.insert_platform,app_balance_outcome.insert_user
        ,app_balance_outcome.insert_date,app_balance_outcome.update_platform,app_balance_outcome.update_user
        ,app_balance_outcome.is_enabled,app_balance_outcome.code_erp,app_balance_outcome.description
        ,app_balance_outcome.receipt_nr,app_balance_outcome.receipt_date,app_balance_outcome.operation_date,app_balance_outcome.update_date
        ,app_balance_outcome.delete_platform,app_balance_outcome.delete_user,app_balance_outcome.delete_date
        ,app_balance_outcome.is_erpsent
        ,prov.description AS provider
        ,blar.description AS type
        ";
        
        if($this->is_db_mssql())
            $this->sSELECTfields .= ",CONVERT(TEXT,app_balance_outcome.notes) AS notes,CONVERT(TEXT,app_balance_outcome.cru_csvnote) AS cru_csvnote";
        else
            $this->sSELECTfields .= ",app_balance_outcome.cru_csvnote,app_balance_outcome.notes";        
        
        $this->oQuery->add_joins("LEFT JOIN app_provider AS prov
        ON prov.id = app_balance_outcome.id_provider");
        
        if($this->is_table("app_balance_array_lang") && $this->_id_language)
            $this->oQuery->add_joins("LEFT JOIN app_balance_array_lang AS blar
                                      ON app_balance_outcome.id_type = blar.id_source
                                      AND blar.id_language='$this->_id_language'");
        else
            $this->oQuery->add_joins("LEFT JOIN app_balance_array AS blar
                                      ON app_balance_outcome.id_type = blar.id");
        
        $this->arFieldsMappingExtra = array
        (
            //"id"=>"app_order_head.id"
            //,"code_erp"=>"app_order_head.code_erp"
            "provider" =>"prov.description"
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
    
    //===================
    //       GETS
    //===================
    public function get_id_provider(){return $this->_id_provider;}
    public function get_provider()
    {
        $this->oProvider = new ModelProvider($this->_id_provider);
        $this->oProvider->load_by_id();
        return $this->oProvider;
    }
    public function get_id_type(){return $this->_id_type;}
    public function get_type()
    {
        $this->oType = new ModelBalanceArray($this->_id_type);
        $this->oType->load_by_id();
        return $this->oType;
    }
    public function get_is_paid(){return $this->_is_paid;}
    public function get_subtotal_business(){return $this->_subtotal_business;}
    public function get_subtotal_own(){return $this->_subtotal_own;}
    public function get_subtotal(){return $this->_subtotal;}
    public function get_total_out(){return $this->_total_out;}
    public function get_total_pending(){return $this->_total_pending;}
    public function get_receipt_nr(){return $this->_receipt_nr;}
    public function get_receipt_date(){return $this->_receipt_date;}
    public function get_operation_date(){return $this->_operation_date;}
    public function get_notes(){return $this->_notes;}
    //===================
    //       SETS
    //===================
    public function set_id_provider($value){$this->_id_provider = $value;}
    public function set_provider($oValue){$this->oProvider = $oValue;}
    public function set_id_type($value){$this->_id_type = $value;}
    public function set_type($oValue){$this->oType = $oValue;}
    public function set_is_paid($value){$this->_is_paid = $value;}
    public function set_subtotal_business($value){$this->_subtotal_business = $value;}
    public function set_subtotal_own($value){$this->_subtotal_own = $value;}
    public function set_subtotal($value){$this->_subtotal = $value;}
    public function set_total_out($value){$this->_total_out = $value;}
    public function set_total_pending($value){$this->_total_pending = $value;}
    public function set_receipt_nr($value){$this->_receipt_nr = $value;}
    public function set_receipt_date($value){$this->_receipt_date = $value;}
    public function set_operation_date($value){$this->_operation_date = $value;}
    public function set_notes($value){$this->_notes = $value;}
    
    public function set_receipt_date_start($value){$this->sReceiptDateStart = bodb_date($value);}
    public function set_receipt_date_end($value){$this->sReceiptDateEnd = bodb_date($value);}    
    public function set_operation_date_start($value){$this->sOperationDateStart = bodb_date($value);}
    public function set_operation_date_end($value){$this->sOperationDateEnd = bodb_date($value);}    
}
