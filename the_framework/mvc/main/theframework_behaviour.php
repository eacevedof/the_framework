<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.2.2
 * @name TheFrameworkBehaviour
 * @file theframework_behaviour.php 
 * @date 04-10-2014 18:17 (SPAIN)
 * @observations:
 * @requires: 
 */
class TheFrameworkBehaviour extends TheFramework
{
    protected static $arDbs = array();    
    /**
     * @var ComponentDatabase
     */
    protected $oDB;

    protected $sTableName;

    //ERROR CAPTURE
    protected $isDebug;
    protected $isError;
    protected $arErrorMessage = array();
    protected $arMessage = array();
    
    //LOGS
    protected $_log_insert = false;
    protected $_log_select = false;
    protected $_log_update = false;
    protected $_log_delete = false;
    
    protected $_path_log_insert;
    protected $_path_log_select;
    protected $_path_log_update;
    protected $_path_log_delete;
    
    protected $_id_language;
    protected $isLanguage;
     /**
     * @var ComponentQuery 
     */
    protected $oQuery;
    
    public function __construct($iDb=0)
    {
        $this->oDB = self::$arDbs[$iDb];
        $this->_path_log_insert = TFW_PATH_FOLDER_LOGDS."queries/insert";
        $this->_path_log_select = TFW_PATH_FOLDER_LOGDS."queries/select";
        $this->_path_log_update = TFW_PATH_FOLDER_LOGDS."queries/update";
        $this->_path_log_delete = TFW_PATH_FOLDER_LOGDS."queries/delete";
    }
    
    protected function array_for_picklist($arRows,$sPk="id",$sDescription="description",$useBlank=TRUE)
    {
        $arForSelect = array();
        if($useBlank) $arForSelect[""]="NONE";
        foreach($arRows as $arRow)
            $arForSelect[$arRow[$sPk]] = $arRow[$sDescription];    
        return $arForSelect;
    }
    
    public function get_languages()
    {
        $arPicklist[""]=tr_none;
        $arPicklist["english"]=tr_english;
        $arPicklist["spanish"]=tr_spanish;
        return $arPicklist;
    }
    
    /**
     * Se puede activar la propiedad isDebug se mostrará por pantalla la consulta
     * Se puede activar la _log_*=TRUE para guardar la consulta en logs
     * @param string $sSQL Consulta de lectura
     * @param int $iRow Fila Numero de fila a recuperar 1-n
     * @param int $iColumn Columna Numero de columna a recuperar 1-m
     * @return array Tipo array[0]=>(fieldname1=>value1,fieldname2=>value2...)
     */
    protected function query($sSQL,$iRow=0,$iColumn=0)
    {
        if($this->isDebug)  pr($sSQL);
        $arRows = $this->oDB->query($sSQL);
        if($iRow)
            if(count($arRows)>0)
            {    
                $arRows = $arRows[$iRow-1];
                if($iColumn)
                {
                    $iColumn = $iColumn-1;
                    $arKeys = array_keys($arRows);
                    $arRows = $arRows[$arKeys[$iColumn]];
                }
            }
            
        if($this->oDB->is_error())
            $this->add_error($this->oDB->get_error_message());
        
        $this->save_query_in_log($sSQL,$this->oDB->get_affected_rows(),$this->oDB->get_query_time());
        return $arRows;
    }
        
    protected function execute($sSQL)
    {
        //ejecuta la sentencia
        $this->oDB->query($sSQL);
        //si ha ocurrido algun error se recupera y se propaga a las variables
        //de control de error del modelo. arErrorMessage y isError
        if($this->oDB->is_error())
            $this->add_error($this->oDB->get_error_message());
        
        $this->save_query_in_log($sSQL,$this->oDB->get_affected_rows());
        return $this->oDB->get_affected_rows();
    }
    
    /**
     * 
     * @param type $sSQL
     * @param type $iAffectedRows
     */
    private function save_query_in_log($sSQL,$iAffectedRows)
    {
        $doSaveLog = false;
        $sAuxPathFolder = $this->oLog->get_path_folder_target();
        $sAuxFilename = $this->oLog->get_target_file_name();
        
        //Aplico directorio de destino de traza
        if($this->_log_delete && ereg("DELETE FROM ([^ ]+)",$sSQL))
        {$this->oLog->set_path_folder_target($this->_path_log_delete); $doSaveLog=true;$sPrefix="del";}
        elseif($this->_log_insert && ereg("INSERT INTO ([^ ]+)",$sSQL))        
        {$this->oLog->set_path_folder_target($this->_path_log_insert);$doSaveLog=true;$sPrefix="ins";}
        elseif($this->_log_update && ereg("UPDATE ([^ ]+)",$sSQL))        
        {$this->oLog->set_path_folder_target($this->_path_log_update);$doSaveLog=true;$sPrefix="upd";}
        elseif($this->_log_select && ereg("SELECT ([^ ]+)",$sSQL))        
        {$this->oLog->set_path_folder_target($this->_path_log_select);$doSaveLog=true;$sPrefix="sel";}

        //bug($arMatches,"arMatches");
        if($doSaveLog)
        {
            $sFileName = $sPrefix."_";
            if($_SESSION["tfw_user_identificator"]) $sFileName .= $_SESSION["tfw_user_identificator"]."_";
            $sFileName .= date("Ymd").".log";
            $sContent = "[".date("H:i:s")."]\n$sSQL($iAffectedRows)";
            
            $this->oLog->set_filename_target($sFileName);
            $this->oLog->add_content($sContent);
            //Restauro la carpeta y archivo configurado
            $this->oLog->set_path_folder_target($sAuxPathFolder);
            $this->oLog->set_filename_target($sAuxFilename);
        }
    }
    
    public function get_fields_and_types_for_model()
    {
        $arField = array();
        $sSQL = "
        SELECT LOWER(cols.name) AS field_name
        ,types.name AS field_type
        ,cols.Length AS field_length
        FROM syscolumns AS cols
        INNER JOIN systypes AS types
        ON cols.xtype=types.xtype
        INNER JOIN sysobjects AS tables
        ON tables.id=cols.id
        AND tables.name = '$this->sTableName'
        AND cols.name NOT IN 
        (
            'code_erp','insert_platform','insert_date','insert_user','delete_platform','delete_date','delete_user'
            ,'description','is_enabled','id','is_erpsent'
            ,'update_platform','update_date','update_user','insert_platform','processflag'
            ,'i','cru_csvnote','order_by','id_source','id_language'
        )
        ";
        
        if($this->sTableName)
        {            
            $arRows = $this->query($sSQL);
            foreach($arRows as $arRow)
                $arField[$arRow["field_name"]]=array($arRow["field_type"]=>$arRow["field_length"]);
        }        
        return $arField;
    }
    
    public function get_fields_definition($sTableName)
    {
        $sSQL = "
        SELECT LOWER(cols.name) AS field_name
        ,types.name AS field_type
        ,cols.Length AS field_length
        FROM syscolumns AS cols
        INNER JOIN systypes AS types
        ON cols.xtype=types.xtype
        INNER JOIN sysobjects AS tables
        ON tables.id=cols.id
        AND tables.name = '$sTableName'
        ";
        $arRows = $this->query($sSQL);
        
        $arDefinition = array();
        foreach($arRows as $arRow)
            $arDefinition[$arRow["field_name"]]=array($arRow["field_type"]=>$arRow["field_length"]);
        
        return $arDefinition;
    }
    
    public function get_select_all()
    {
        $sSQL = "SELECT * FROM $this->sTableName";
        $arRows = $this->query($sSQL);
        return $arRows;
    }
    //**********************************
    //             GETS
    //**********************************    
    public function get_id_language(){return $this->_id_language;}
    public function is_language(){return $this->isLanguage;}
    
    //**********************************
    //             SETS
    //**********************************    
    public function set_debugon($isOn=TRUE){$this->isDebug=$isOn;}
    public function log_save_insert($isOn=TRUE){$this->_log_insert = $isOn;}
    public function log_save_select($isOn=TRUE){$this->_log_select = $isOn;}
    public function log_save_update($isOn=TRUE){$this->_log_update = $isOn;}
    public function log_save_delete($isOn=TRUE){$this->_log_delete = $isOn;}
    /**
     * Consultar la tabla: base_languages. english:1,spanish:2,dutch:3,papiaments:4
     * @param int $idLanguage
     */
    public function set_id_language($idLanguage){$this->_id_language = $idLanguage;}
    //public function set_order_by($value){$this->_order_by = $value;}
    public function use_language($isOn=TRUE){$this->isLanguage = $isOn;}
    public function use_dbobject($i=0){$this->oDB = self::$arDbs[$i];}
    
    protected function is_db_mysql(){return $this->oDB->get_type()=="mysql";}
    protected function is_db_mssql(){return $this->oDB->get_type()=="mssql";}
    protected function get_db_type(){return $this->oDB->get_type();}    
}