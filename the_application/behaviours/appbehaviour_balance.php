<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name AppBehaviourBalance
 * @file appbehaviour_balance.php   
 * @date 12-08-2014 13:51 (SPAIN)
 * @observations: 
 * @requires:
 */
class AppBehaviourBalance extends TheApplicationBehaviour
{
    protected $sDateStart;
    protected $sDateEnd;
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function get_totals()
    {
        $sSQL = 
        "/*get_totals()*/
            SELECT ROUND(SUM(in_total),2) AS sum_in
            , ROUND(SUM(out_total),2) AS sum_out
            , ROUND(SUM(total),2) AS sum_total
            FROM vapp_balance 
            WHERE 1=1
        ";
        
        if($this->sDateStart) $sSQL .= "AND in_date>='$this->sDateStart' ";
        if($this->sDateEnd) $sSQL .= "AND in_date<='$this->sDateEnd' ";
        
        $arRow = $this->query($sSQL,1);
        
        //bug($arRow);
        $arRow["sum_in"] = dbbo_numeric2($arRow["sum_in"]);
        $arRow["sum_out"] = dbbo_numeric2($arRow["sum_out"]);
        $arRow["sum_total"] = dbbo_numeric2($arRow["sum_total"]);
        return $arRow;        
    }

    public function get_sum_per_month()
    {
        $sSQLAnd = "1=1 ";
        if($this->sDateStart) $sSQLAnd .= "AND in_date>='$this->sDateStart' ";
        if($this->sDateEnd) $sSQLAnd .= "AND in_date<='$this->sDateEnd' ";        
        
        $sSQL = 
        "/*get_sum_per_month()*/
        SELECT 
            yy
            ,mm
            ,ROUND(SUM(in_total),2) AS sum_in
            ,ROUND(SUM(out_total),2) AS sum_out
            ,ROUND(SUM(total),2) AS sum_total
            ,mm+'/'+SUBSTRING(yy,3,2) AS period
        FROM
        (
            SELECT
            in_total
            ,out_total
            ,total
            ,SUBSTRING(in_date,0,5) AS yy
            ,SUBSTRING(in_date,5,2) AS mm
            ,SUBSTRING(in_date,7,2) AS dd
            FROM vapp_balance
            WHERE $sSQLAnd
        ) AS sub
        GROUP BY yy,mm
        ORDER BY yy,mm ASC 
        ";
        //bug($sSQL);
        $arRows = $this->query($sSQL);
        //muestro solo 2 decimales
        
        foreach($arRows as $iRow=>$arRow)
        {    
            $arRows[$iRow]["sum_in"] = dbbo_numeric2($arRow["sum_in"]);
            $arRows[$iRow]["sum_out"] = dbbo_numeric2($arRow["sum_out"]);
            $arRows[$iRow]["sum_total"] = dbbo_numeric2($arRow["sum_total"]);        
        }
        return $arRows;
    }//get_sum_per_month    
        
    //**********************************
    //             SETS
    //**********************************
    public function set_date_start($sValue){$this->sDateStart = bodb_date($sValue);}
    public function set_date_end($sValue){$this->sDateEnd = bodb_date($sValue);}
    
    //**********************************
    //             GETS
    //**********************************
    
    //**********************************
    //           MAKE PUBLIC
    //**********************************
}
