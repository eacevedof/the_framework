<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.8
 * @name ComponentQuery
 * @file component_query.php
 * @date 03-10-2014 20:54 (SPAIN)
 * @observations: Kind of helper
 */
class ComponentQuery //extends TheFrameworkComponent 
{
    //To highlight a query u need ComponentDebug (component_debug.php 1.2.2)
    protected $isHighlight;
    protected $sComment;
    protected $isDistinct;
    protected $iTop;
    protected $mxFields;
    protected $mxFromTables;
    protected $mxJoins;
    protected $mxWhere;
    protected $mxAnds;
    protected $mxOrs;
    protected $mxGroupBys;
    protected $mxHavings;
    protected $mxOrderBys;
    protected $iLimitStart;
    protected $iLimitTotal;
    
    //Insert, Update, Delete
    protected $sWriteTable;
    //INSERT
    protected $mxFieldNames;
    protected $mxFieldValues;
    //UPDATE
    protected $mxFieldAndValue;

    protected $sSQL;
    protected $sSQLExplode;
    protected $sDBType;

    /**
     * 
     * @param string $sTableName
     * @param string $sDbType mssql|mysql|sqlite|posgre (is case sensitive)
     */
    public function __construct($sTableName=NULL,$sDbType=NULL)
    {  
        if(!$sDbType) 
        {
            if(defined("TFW_DB_TYPE")) 
                $sDbType = TFW_DB_TYPE;
            else
                $sDbType = "mysql";
        }
            
        $this->sDBType = $sDbType;
        if($sTableName) 
        {    
            $this->mxFromTables[] = $sTableName;
            $this->sWriteTable = $sTableName;
        }
    }
    
    public function get_select()
    {
        $this->sSQL = "";
        if($this->isHighlight) $this->sComment = "%highlight% ".$this->sComment;
        if($this->sComment) $this->sSQL .= "/*$this->sComment*/ ";
        $this->sSQL .= "SELECT ";
        if($this->isDistinct) $this->sSQL .= "DISTINCT ";
        if(is_numeric($this->iTop) && $this->is_mssql()) $this->sSQL .= "TOP $this->iTop ";
        if($this->mxFields) $this->sSQL .= $this->build_select_fields()." ";
        if($this->mxFromTables) $this->sSQL .= "FROM ".$this->build_fromtables()." ";
        if($this->mxJoins) $this->sSQL .= $this->build_joins()." ";
        $this->sSQL .= "WHERE ".$this->build_where()." ";
        if($this->mxAnds) $this->sSQL .= "AND ".$this->build_ands()." ";
        if($this->mxOrs) $this->sSQL .= "OR ".$this->build_ors()." ";
        if($this->mxGroupBys) $this->sSQL .= "GROUP BY ".$this->build_groupbys()." ";
        if($this->mxHavings) $this->sSQL .= "HAVING ".$this->build_havings()." ";
        if($this->mxOrderBys) $this->sSQL .= "ORDER BY ".$this->build_orderbys()." ";
        if($this->iLimitTotal && $this->is_mysql()) $this->sSQL .= "LIMIT ".$this->build_mysqllimit();
        return $this->sSQL;
    }
    
    public function get_insert()
    {
        $this->sSQL = "";
        if($this->isHighlight) $this->sComment = "%highlight% ".$this->sComment;
        if($this->sComment) $this->sSQL .= "/*$this->sComment*/ ";
        if($this->sWriteTable) $this->sSQL .= "INSERT INTO $this->sWriteTable ";
        if($this->mxFieldNames) $this->sSQL .= "\n(".$this->build_insertfields().") ";
        if($this->mxFieldNames && $this->mxFieldValues) $this->sSQL .= "\nVALUES\n(".$this->build_insertvalues().") ";
        return $this->sSQL;
    }
    
    public function get_update()
    {
        $this->sSQL = "";
        if($this->isHighlight) $this->sComment = "%highlight% ".$this->sComment;
        if($this->sComment) $this->sSQL .= "/*$this->sComment*/ ";
        if($this->sWriteTable) $this->sSQL .= "UPDATE $this->sWriteTable ";
        if($this->mxFieldAndValue) $this->sSQL .= "\nSET ".$this->build_updatefields()." ";
        if($this->mxFromTables) $this->sSQL .= "\nFROM ".$this->build_fromtables()." ";
        if($this->mxJoins) $this->sSQL .= $this->build_joins()." ";
        $this->sSQL .= "\nWHERE ".$this->build_where()." ";
        if($this->mxAnds) $this->sSQL .= "\nAND ".$this->build_ands()." ";
        return $this->sSQL;
    }
    
    public function get_delete()
    {
        $this->sSQL = "";
        if($this->isHighlight) $this->sComment = "%highlight% ".$this->sComment;
        if($this->sComment) $this->sSQL .= "/*$this->sComment*/ ";
        if($this->sWriteTable) $this->sSQL .= "DELETE FROM $this->sWriteTable ";
        if($this->mxJoins) $this->sSQL .= $this->build_joins()." ";
        $this->sSQL .= "\nWHERE ".$this->build_where()." ";
        if($this->mxAnds) $this->sSQL .= "\nAND ".$this->build_ands()." ";
        return $this->sSQL;        
    }
    
    private function build_select_fields()
    {
        if(is_array($this->mxFields))
        {           
            return implode(",",$this->mxFields);
        }
        return $this->mxFields;
    }    

    private function build_fromtables()
    {
        if(is_array($this->mxFromTables)) 
            return implode(",",$this->mxFromTables);
        return $this->mxFromTables;
    }
    
    private function build_joins()
    {
        if(is_array($this->mxJoins)) 
            return implode("\n",$this->mxJoins);
        return $this->mxJoins;
    }
    
    private function build_where()
    {
        if(is_array($this->mxWhere)) 
            return implode(" AND ",$this->mxWhere);
        if(!$this->mxWhere) $this->mxWhere = "1=1";
        return $this->mxWhere;
    }
    
    private function build_ands()
    {
        if(is_array($this->mxAnds)) 
            return implode(" AND ",$this->mxAnds);
        return $this->mxAnds;
    }    
    
    private function build_ors()
    {
        if(is_array($this->mxOrs)) 
            return implode(" OR ",$this->mxOrs);
        return $this->mxOrs;
    }  
    
    private function build_groupbys()
    {
        if(is_array($this->mxGroupBys)) 
            return implode(",",$this->mxGroupBys);
        //str_replace("GROUP BY","",$this->mxOrderBys);
        return $this->mxGroupBys;
    } 
    
    private function build_havings()
    {
        if(is_array($this->mxHavings)) 
            return implode(" AND ",$this->mxHavings);
        return $this->mxHavings;
    }
    
    private function build_orderbys()
    {
        if(is_array($this->mxOrderBys)) 
            return implode(",",$this->mxOrderBys);
        //str_replace("ORDER BY","",$this->mxOrderBys);
        return $this->mxOrderBys;
    }    
    
    public function build_updatefields()
    {
        if(is_array($this->mxFieldAndValue)) 
            return implode(",",$this->mxFieldAndValue);
        return $this->mxFieldAndValue;        
    }
    
    public function build_insertfields()
    {
        if(is_array($this->mxFieldNames)) 
            return implode(",",$this->mxFieldNames);
        return $this->mxFieldNames;        
    }

    public function build_insertvalues()
    {
        $arTemp = array();
        $arTempValues = array();
        if(is_array($this->mxFieldValues))
        {
            $useNames = TRUE;
            $arTemp = array_keys($this->mxFieldValues);
            if(is_numeric($arTemp[0])) $useNames = FALSE; 
            
            if(is_array($this->mxFieldNames)) 
            {   
                foreach($this->mxFieldNames as $i=>$sFieldName)
                    if($useNames)
                        $arTempValues[] = $this->mxFieldValues[$sFieldName];
                    else
                        $arTempValues[] = $this->mxFieldValues[$i];
            }
            elseif(is_string($this->mxFieldNames))
            {
                $arTemp = explode_select(",",$this->mxFieldNames);
                $arTemp = $this->array_trim($arTemp);
                foreach($arTemp as $sFieldName)
                    $arTempValues[] = $this->mxFieldValues[$sFieldName];

            }
            return implode(",",$arTempValues);
        }            
        return $this->mxFieldValues;        
    }
    
    protected function build_mysqllimit()
    {
        $sLimit = "";
        if($this->iLimitStart)
        {  
            $sLimit = $this->iLimitStart;
            if($this->iLimitTotal)
                $sLimit .= ",".$this->iLimitTotal;
        }
        elseif($this->iLimitTotal)
            $sLimit = $this->iLimitTotal;
        return $sLimit;        
    }


    private function extract_top($sSQL)
    {
        //puede ser select top o select distinct top 
        $sTopPatern = "/select[\s]+top[\s]+[\d]+[\s]/";
        preg_match($sTopPatern,$sSQL,$arMatch);
        //si no hay coincidencias es probable que haya un distinct asi que se extrae con distinct
        if(!$arMatch[0])
        {
            $sTopPatern = "/select[\s]+distinct[\s]+top[\s]+[\d]+[\s]/";
            preg_match($sTopPatern,$sSQL,$arMatch);
        }

        if($arMatch[0])
        {
            $sTop = explode_select("top",$arMatch[0]);
            $sTop = trim($sTop[1]);
        }
        return $sTop;
    }

    private function has_part($sPart,$sSQL)
    {   
        if($sPart=="distinct")//ok
            return strstr($sSQL,"select distinct ");
        elseif($sPart=="top")
            return strstr($sSQL," top ");
        elseif($sPart=="where")
            return strstr($sSQL," where ");
        elseif($sPart=="groupby")
            return strstr($sSQL," group by ");
        elseif($sPart=="having")
            return strstr($sSQL," having ");
        elseif($sPart=="orderby")
            return strstr($sSQL," order by ");
        return FALSE;
    }
    
    private function unify_sql($sSQL)
    {
        $sSQL = trim($sSQL);
        $sSQL = strtolower($sSQL);
        $sSQL = str_replace("\n"," ",$sSQL);
        $sSQL = str_replace("\r"," ",$sSQL);
        $sSQL = str_replace("   "," ",$sSQL);
        $sSQL = str_replace("  "," ",$sSQL);
        return $sSQL;
    }

    private function array_trim($arTotrim)
    {
        if(!is_array($arTotrim))
            return $arTotrim;
        
        $arTrimmed = array();
        foreach($arTotrim as $sKey=>$mxValue)
            if(is_string($mxValue))
                $arTrimmed[$sKey] = trim($mxValue);
            else
                $arTrimmed[$sKey] = $mxValue;
        return $arTrimmed;
    }
    
    public function explode_select()
    {
        //Limpia la consulta de saltos de linea, mayusculas y retornos de carro.
        $sSQL = $this->unify_sql($this->sSQLExplode);

        $sDistinct = $this->has_part("distinct",$sSQL);
        $sTop = $this->has_part("top",$sSQL);
        if($sTop) $sTop = $this->extract_top($sSQL);
        
        $sWhere =  $this->has_part("where",$sSQL);
        $sGroupBy = $this->has_part("groupby",$sSQL);
        $sHaving = $this->has_part("having",$sSQL); 
        $sOrderBy = $this->has_part("orderby",$sSQL);

        //SELECT y FROM siempre existen en una sentencia SQL
        $sFields = explode_select(" from ",$sSQL);
        //var_dump($sFields);
        $sFields = explode_select("select ",$sFields[0]);
        //quito la sentencia top
        $sFields = str_replace("top $sTop ","",$sFields[1]);
        //si existiera distinct se elimina
        $sFields = str_replace("distinct","",$sFields);
        $sFields = trim($sFields);

        //Recuperacion de relaciones INNER LEFT ...
        $sHierarchy = explode_select("from ",$sSQL);
        if($sWhere)
            $sHierarchy = explode_select("where ",$sHierarchy[1]);
        //No hay clausula where busco proxima marca "groupby"
        else
        {
            if($sGroupBy)
                $sHierarchy = explode_select("group by ",$sHierarchy[1]);
            else//no hay group by
            {
                if($sOrderBy) 
                    $sHierarchy = explode_select("order by ",$sHierarchy[1]);
            }
        }
        $sHierarchy = $sHierarchy[0];

        //Recuperacion de condiciones AND , OR despues de WHERE y antes de GROUP BY Y ORDER BY
        if($sWhere) 
        {    
            $sWhere = explode_select("where ",$sSQL);
            //debo limpiar todo lo que está entre '' y que tenga contenido group by, having, order by
            if($sGroupBy)
                $sWhere = explode_select("group by ",$sWhere[1]);
            elseif($sOrderBy)//no hay group by
                $sWhere = explode_select("order by ",$sWhere[1]);
            else
                $sWhere[0]=$sWhere[1];
        }

        $sWhere = $sWhere[0];

        //Recuperacion de agrupaciones
        if($sGroupBy)
        {
            $sGroupBy = explode_select("group by ",$sSQL);
            if($sOrderBy) 
                $sGroupBy = explode_select("order by ",$sGroupBy[1]);
        }
        $sGroupBy = $sGroupBy[0];

        //Recuperacion de ordenacion
        if($sOrderBy)
            $sOrderBy = explode_select("order by ",$sSQL);
        $sOrderBy = $sOrderBy[1];

        if($sDistinct) $this->isDistinct = TRUE;
        if($sTop) $this->iTop = $sTop;
        $this->mxFields = explode_select(",",$sFields);
        $this->mxFields = $this->array_trim($this->mxFields);
        $this->mxJoins = $sHierarchy;
        $this->mxWhere = explode_select(" AND ",$sWhere);
        $this->mxWhere = $this->array_trim($this->mxWhere);
        $this->mxGroupBys = explode_select(",",$sGroupBy);
        $this->mxGroupBys = $this->array_trim($this->mxGroupBys);
        $this->mxOrderBys = explode_select(",",$sOrderBy);
        $this->mxOrderBys = $this->array_trim($this->mxOrderBys);
    }

    /*
     * TODO
     */
    public function explode_insert()
    {}
    
    /*
     * TODO
     */
    public function explode_update()
    {}
    
    /**
     * TODO
     */
    public function explode_delete()
    {}
    
    /**/
    private function replace_fields($arReplace,$sFields)
    {
        foreach($arReplace as $sSearch=>$sReplace)
            $sFields = str_replace($sSearch,$sReplace,$sFields);
        return $sFields;
    }    

    private function is_mssql(){ return $this->sDBType=="mssql";}
    private function is_mysql(){ return $this->sDBType=="mysql";}
    private function is_postgre(){ return $this->sDBType=="postgre";}
    private function is_sqlite(){ return $this->sDBType=="sqlite";}
    
    /**
     * Resetea $mxStrArray y si procede ($mxValue tiene valor) se asigna $mxValue como primer elemento
     * @param array $mxStrArray
     * @param value $mxValue
     */
    private function set_mixed(&$mxStrArray,$mxValue)
    {
        $mxStrArray = array(); 
        if($mxValue) 
            if(is_array($mxValue)) 
                $mxStrArray = $mxValue;
            else//if($mxValue!==NULL) no puede ser null por el primer if 
                $mxStrArray[] = $mxValue; 
    }    
    //=======================
    //         SETS
    //=======================
    public function set_comment($sComment){$this->sComment=$sComment;}
    public function set_distinct($isOn=TRUE){$this->isDistinct = $isOn;}
    public function set_top($iTop){$this->iTop = $iTop; if($this->is_mysql())$this->iLimitTotal=$iTop;}
    public function set_fields($mxFields){$this->set_mixed($this->mxFields,$mxFields);}
    public function set_fromtables($mxTables){$this->set_mixed($this->mxFromTables,$mxTables);}
    public function set_joins($mxJoins){$this->set_mixed($this->mxJoins,$mxJoins);}
    public function set_where($mxWhere){$this->set_mixed($this->mxWhere,$mxWhere);}
    public function set_and($mxAnd){$this->set_mixed($this->mxAnds,$mxAnd);}
    public function set_or($mxOr){$this->set_mixed($this->mxOrs,$mxOr);}
    public function set_groupby($mxGroupBy){$this->set_mixed($this->mxGroupBys,$mxGroupBy);}
    public function set_having($mxHaving){$this->set_mixed($this->mxHavings,$mxHaving);}
    public function set_orderby($mxOrderBy){$this->set_mixed($this->mxOrderBys,$mxOrderBy);}
    public function set_sqltoexplode_select($sSQL){$this->sSQLExplode = $sSQL;}

    public function set_writetable($sTable){$this->sWriteTable=$sTable;}
    public function set_insertfields($mxFields){$this->set_mixed($this->mxFieldNames,$mxFields);}
    public function set_insertvalues($mxValues){$this->set_mixed($this->mxFieldValues,$mxValues);}
    public function set_limit($iTotal=NULL,$iStart=NULL){$this->iLimitTotal=$iTotal; $this->iLimitStart=$iStart; if($this->is_mssql())$this->iTop=$iTotal;}
    /**
     * FOR UPDATE
     * @param string|array $mxFieldAndValues 
     */
    public function set_fieldandvalues($mxFieldAndValues){$this->mxFieldAndValue = $mxFieldAndValues;}
    /**
     * mssql: Microsoft SQL Server
     * mysql: MYSQL
     * postgre: 
     * sqli: SQL LITE
     * @param string $sDbType: mssql,mysql,postgre,sqli
     */
    public function set_db_type($sDbType){$this->sDBType = $sDbType;}
    
    public function add_fields($mxFields){if(!is_array($mxFields)&&$mxFields&&!is_string($this->mxFields)) $this->mxFields[] = $mxFields;}
    public function add_fromtables($mxTables){if(!is_array($mxTables)&&$mxTables&&!is_string($this->mxFromTables)) $this->mxFromTables[] = $mxTables;}
    public function add_joins($mxJoins){if(!is_array($mxJoins)&&$mxJoins&&!is_string($this->mxJoins)) $this->mxJoins[] = $mxJoins;}
    public function add_where($mxWhere){if(!is_array($mxWhere)&&$mxWhere&&!is_string($this->mxWhere)) $this->mxWhere[] = $mxWhere;}
    public function add_and($mxAnd){if(!is_array($mxAnd)&&$mxAnd&&!is_string($this->mxAnds)) $this->mxAnds[] = $mxAnd;}
    public function add_or($mxOr){if(!is_array($mxOr)&&$mxOr&&!is_string($this->mxOrs))$this->mxOrs[] = $mxOr;}
    public function add_groupby($mxGroupBy){if(!is_array($mxGroupBy)&&$mxGroupBy&&!is_string($this->mxGroupBys))$this->mxGroupBys[] = $mxGroupBy;}
    public function add_having($mxHaving){if(!is_array($mxHaving)&&$mxHaving&&!is_string($this->mxHavings))$this->mxHavings[] = $mxHaving;}
    public function add_orderby($mxOrderBy){if(!is_array($mxOrderBy)&&$mxOrderBy&&!is_string($this->mxOrderBys))$this->mxOrderBys[] = $mxOrderBy;}
    
    public function add_insertfield($sFieldName){$this->mxFields[] = $sFieldName;}
    public function add_insertvalue($sFieldName,$sFieldValue){$this->mxFieldNames[$sFieldName] = $sFieldValue;}
    public function add_fieldandvalue($sFieldAndValue){$this->mxFieldAndValue[] = $sFieldAndValue;}
    public function highlight($isOn=TRUE){$this->isHighlight = $isOn;}
    
    //=======================
    //        GETS
    //=======================
    public function get_comment(){return $this->sComment;}
    public function get_distinct(){return $this->isDistinct;}
    public function get_top(){return $this->iTop;}
    public function get_fields(){return $this->mxFields;}
    public function get_fromtables(){return $this->mxFromTables;}
    public function get_joins(){return $this->mxJoins;}
    public function get_where(){return $this->mxWhere;}
    public function get_and(){return $this->mxAnds;}
    public function get_or(){return $this->mxOrs;}
    public function get_groupby(){return $this->mxGroupBys;}
    public function get_having(){return $this->mxHavings;}
    public function get_orderby(){return $this->mxOrderBys;}
    public function get_sqltoexplode_select(){return $this->sSQLExplode;}
    
    public function get_insertfields(){return $this->mxFieldNames;}
    public function get_insertvalues(){return $this->mxFieldValues;}
}