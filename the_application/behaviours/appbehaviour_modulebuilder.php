<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.8
 * @name AppBehaviourModuleBuilder
 * @file appbehaviour_modulebuilder.php 
 * @date 02-10-2014 15:54 (SPAIN)
 * @observations: 
 */
class AppBehaviourModuleBuilder extends TheApplicationBehaviour
{
    
    public function __construct($sTableName=NULL) 
    {
        //crea el objeto db
        parent::__construct();
        $this->sTableName = $sTableName;
    }
    
    public function get_fields_and_types_for_controller()
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
            'delete_date','delete_user'
            ,'is_erpsent','i','cru_csvnote'
            ,'insert_platform','processflag'
        )
        ";
        
        if(TFW_DB_TYPE=="mysql")
        {
            $sSQL = 
            "SELECT LOWER(column_name) AS field_name
            ,DATA_TYPE AS field_type
            ,character_maximum_length AS field_length
            FROM information_schema.columns 
            WHERE table_name='$this->_table_name'
            AND column_name NOT IN
            (
                'delete_date','delete_user'
                ,'is_erpsent','i','cru_csvnote'
                ,'insert_platform','processflag'            
            )
            ORDER BY ordinal_position ASC";           
        }
        
        if($this->sTableName)
        {            
            $arRows = $this->query($sSQL);
            foreach($arRows as $arRow)
                $arField[$arRow["field_name"]]=array($arRow["field_type"]=>$arRow["field_length"]);
        }        
        $arField = $this->order_keys($arField);
        return $arField;
    }
    
    public function get_db_tables()
    {
        $sSQL = 
        "SELECT sqltable.name AS tablename
        , sqlcolumn.name AS columnname
        FROM sysobjects sqltable
        INNER JOIN syscolumns sqlcolumn
        ON sqltable.id = sqlcolumn.id
        WHERE 1=1
        AND sqltable.xtype = 'U' --Tablas
        AND sqltable.name 
        NOT IN ('_template','_template_array')
        AND sqltable.name NOT LIKE 'base_%'
        ORDER BY sqltable.name, sqlcolumn.name
        ";
        
        $sSQL =" /*behaviour modulebuilder->get_db_tables() */
        SELECT LOWER(sqltable.name) AS tablekey
        ,sqltable.name AS tablename
        ,sqltable.xtype AS otype
        FROM sysobjects sqltable
        WHERE 1=1
        AND (sqltable.xtype = 'U' OR sqltable.xtype = 'V') --Tablas o vistas
        AND sqltable.name 
        NOT IN ('_template','_template_array')
        -- AND sqltable.name NOT LIKE 'base_%'
        ORDER BY sqltable.name
        ";
        $sDb = TFW_DB_NAME;
        if(TFW_DB_TYPE=="mysql")
            //information_schema.tables aqui se encuentra el tipo
            $sSQL = 
            "/*behaviour modulebuilder->get_db_tables() */
            SELECT LOWER(table_name) AS tablekey
            ,table_name AS tablename
            ,'TODO where is type' AS otype
            FROM information_schema.columns 
            WHERE 1=1
            AND table_schema='$sDb'
            AND table_name NOT IN ('_template','_template_array')
            ORDER BY table_name ASC";
        $arRows = $this->query($sSQL);
        $arRows = $this->array_for_picklist($arRows,"tablekey","tablename");
        return $arRows;
    }
    
    public function get_physic_tables()
    {
        $sSQL =" /*behaviour modulebuilder->get_physic_tables() */
        SELECT LOWER(sqltable.name) AS tablekey
        ,sqltable.name AS tablename
        ,sqltable.xtype AS otype
        FROM sysobjects sqltable
        WHERE 1=1
        AND (sqltable.xtype = 'U' OR sqltable.xtype = 'V') --Tablas o vistas
        AND sqltable.name 
        NOT IN ('_template','_template_array')
        ORDER BY sqltable.name
        ";
        if(TFW_DB_TYPE=="mysql")
        {
           $sSQL = 
            " /*behaviour modulebuilder->get_physic_tables() */
            SELECT LOWER(table_name) AS tablekey
            ,table_name AS tablename
            ,'TODO where is type' AS otype
            FROM information_schema.columns 
            WHERE 1=1
            -- AND otype falta tablas o vistas 
            AND table_schema='$sDb'
            AND table_name NOT IN ('_template','_template_array')
            ORDER BY table_name ASC";
        }
               
        $arRows = $this->query($sSQL);
        $arRows = $this->array_for_picklist($arRows,"tablekey","tablename");
        return $arRows;
    }
    
    private function order_keys($arFields)
    {
        $arOrdered = array();
        //load pk id
        foreach($arFields as $sFieldName=>$arField)
            if($sFieldName=="id")
                $arOrdered[$sFieldName] = $arField;

        //load pk code_erp
        foreach($arFields as $sFieldName=>$arField)
            if($sFieldName=="code_erp")
                $arOrdered[$sFieldName] = $arField;
            
        //load only keys
        foreach($arFields as $sFieldName=>$arField)
            if($this->is_keyforeign($sFieldName))
                $arOrdered[$sFieldName] = $arField;
        //not keys
        foreach($arFields as $sFieldName=>$arField)
            if(!$this->is_keyforeign($sFieldName)||!($sFieldName=="id"&&$sFieldName=="code_erp"))
                $arOrdered[$sFieldName] = $arField;
        return $arOrdered;
    }
    
    private function is_keyforeign($sFieldName)
    {
        if(strstr($sFieldName,"id_")) return true;
        return false;
    }
    
    public function set_table_name($value){$this->sTableName = $value;}

    public function db_backup($sPathBackupDS,$sDb="theframework")
    {
        $sSQL = "EXEC prc_backup '$sDb','$sPathBackupDS'";
        $arRow = $this->query($sSQL);
        //pr($arRow);
        return $arRow;
    }
}