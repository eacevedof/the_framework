<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name AppBehaviourReport
 * @file appbehaviour_report.php   
 * @date 04-10-2014 18:58 (SPAIN)
 * @observations: 
 * @requires:
 */
class AppBehaviourReport extends TheApplicationBehaviour
{
    protected $sDateStart;
    protected $sDateEnd;
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * Devuelve el top 10 de clientes con más pedidos realizados en el periodo
     * @return array
     */
    public function get_top_10_customers()
    {
        $oQuery = new ComponentQuery("app_order_head");
        $oQuery->set_db_type($this->get_db_type());
        $oQuery->set_top("10");
        $oQuery->add_fields("id_customer");
        $oQuery->add_fields("COUNT(id) AS num_ordersh");
        $oQuery->add_fields("SUM(amount_total) AS totalsale");
        $oQuery->add_and("delete_date IS NULL");
        $oQuery->add_and("id_type_validate='3'");
        $oQuery->add_and("(date>='$this->sDateStart' AND date<='$this->sDateEnd')");
        $oQuery->add_groupby("id_customer");
        $oQuery->add_orderby("2,3 DESC");
        
        $sSQL = $oQuery->get_select();
        
        $sSQL = 
        "/*get_top_10_customers()*/
            SELECT cus.id
            -- ,cus.first_name
            -- ,cus.last_name
            ,cus.company
            ,topten.num_ordersh
            ,topten.totalsale
            FROM app_customer AS cus
            INNER JOIN 
            (
                $sSQL
            ) AS topten
            ON topten.id_customer = cus.id
            WHERE cus.delete_date IS NULL
            ORDER BY num_ordersh,totalsale DESC, company ASC
        ";
        //bug($sSQL);
        $arRows = $this->query($sSQL);
        return $arRows;
    }
    
    public function get_top_10_products()
    {   
        $oQuery = new ComponentQuery();
        $oQuery->set_fromtables
        (" (
                SELECT ol.id_product
                , SUM(ol.num_items) AS sumsale
                FROM app_order_head AS oh
                INNER JOIN app_order_line AS ol
                ON oh.id = ol.id_order_head
                WHERE oh.delete_date IS NULL
                AND ol.delete_date IS NULL
                AND ol.is_free='NO'
                AND oh.id_type_validate='3'
                AND (oh.date>='$this->sDateStart' AND oh.date<='$this->sDateEnd')
                GROUP BY id_product
            ) AS prodcount
        ");
        $oQuery->set_db_type($this->get_db_type());
        $oQuery->set_top("10");
        $oQuery->set_fields("*");
        $oQuery->add_orderby("sumsale DESC");
        $sSQLAux1 = $oQuery->get_select();
        
        $oQuery = new ComponentQuery("app_picture");
        $oQuery->add_fields("id_entity");
        $oQuery->add_fields("'blank' AS target");
        
        if($this->is_db_mssql())
        {
            $oQuery->add_fields("uri_public+'/'+folder+'/'+folder+'_'+CONVERT(VARCHAR,id)+'_th1.'+extension AS uri_thumb");
            $oQuery->add_fields("uri_public+'/'+folder+'/'+folder+'_'+CONVERT(VARCHAR,id)+'.'+extension AS uri_href");
        }
        else
        {
            $oQuery->add_fields("CONCAT(uri_public,'/',folder,'/',folder,'_',CAST(id AS CHAR),'_th1.',extension) AS uri_thumb");
            $oQuery->add_fields("CONCAT(uri_public,'/',folder+'/',folder,'_',CAST(id AS CHAR),'.',extension) AS uri_href");            
        }
        $oQuery->add_and("delete_date IS NULL");
        $oQuery->add_and("is_enabled=1");
        $oQuery->add_and("id_type_entity=4 ");
        $oQuery->add_and("is_bydefault=1");
        $sSQLAux2 = $oQuery->get_select();
        
        $sSQL = 
        "/*get_top_10_products()*/
        SELECT pt.id
        ,pt.description
        ,topten.sumsale
        ,COALESCE(picture.uri_thumb,'images/custom/no_image_small.png') AS uri_thumb
        ,COALESCE(picture.uri_href,'javascript:;') AS uri_href
        ,COALESCE(picture.target,'self') AS target        
        FROM app_product AS pt
        INNER JOIN ($sSQLAux1) AS topten
        ON topten.id_product = pt.id
        LEFT JOIN ($sSQLAux2) AS picture 
        ON picture.id_entity=pt.id
        WHERE pt.delete_date IS NULL
        ORDER BY sumsale DESC,description ASC
        ";
        //bug($sSQL);
        $arRows = $this->query($sSQL);
        return $arRows;
    }
    
    public function get_total_sold()
    {
        $sSQL = 
        "/*get_total_sold()*/
            SELECT SUM(amount_total) AS totalsale
            FROM app_order_head
            WHERE delete_date IS NULL
            AND id_type_validate='3'
            AND (date>='$this->sDateStart' AND date<='$this->sDateEnd')
        ";
        //bug($sSQL);
        $arRows = (float)$this->query($sSQL,1,1);
        $arRows = float_roundstr($arRows);
        return $arRows;        
    }

    public function get_sale_per_month()
    {
        $oQuery = new ComponentQuery();
        $oQuery->set_comment("get_sale_per_month()");
        $oQuery->add_fields("yy,mm");
        $oQuery->add_fields("ROUND(SUM(amount_total),2) AS rounded_total");
        
        if($this->is_db_mssql())
        {
           $oQuery->set_fromtables
           ("(
               SELECT 
               amount_total
               ,SUBSTRING(date,0,5) AS yy
               ,SUBSTRING(date,5,2) AS mm
               ,SUBSTRING(date,7,2) AS dd
               FROM app_order_head
               WHERE delete_date IS NULL
               AND id_type_validate='3'
               AND (date>='$this->sDateStart' AND date<='$this->sDateEnd')            
           ) AS sub");            
            $oQuery->add_fields("mm+'/'+yy AS period");
        }
        else
        {    
            $oQuery->set_fromtables
           ("(
               SELECT 
               amount_total
               ,SUBSTRING(`date`,1,5) AS yy
               ,SUBSTRING(`date`,5,2) AS mm
               ,SUBSTRING(`date`,7,2) AS dd
               FROM app_order_head
               WHERE delete_date IS NULL
               AND id_type_validate='3'
               AND (date>='$this->sDateStart' AND date<='$this->sDateEnd')            
           ) AS sub");              
            $oQuery->add_fields("CONCAT(mm,'/',yy) AS period");
        }
        
        $oQuery->add_groupby("yy,mm");
        $oQuery->add_orderby("yy,mm ASC");
        $sSQL = $oQuery->get_select();
        
        //bug($sSQL);
        $arRows = $this->query($sSQL);
        return $arRows;        
    }    
        
    //**********************************
    //             SETS
    //**********************************
    public function set_date_start($sValue){$this->sDateStart = $sValue;}
    public function set_date_end($sValue){$this->sDateEnd = $sValue;}
    
    //**********************************
    //             GETS
    //**********************************
    
    //**********************************
    //           MAKE PUBLIC
    //**********************************
}
