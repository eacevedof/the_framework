<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.2.2
 * @name TheApplicationModel
 * @file theapplication_model.php 
 * @date 23-06-2014 21:43 (SPAIN)
 * @observations: application library.
 */
class TheApplicationModel extends TheFrameworkModel
{
    public function __construct($sTableName,$iDb=0) 
    {
        //si no hay bd se crea una
        //BD theframework
        if(!self::$arDbs[0])
        {    
            self::$arDbs[0] = new ComponentDatabase(TFW_DB_SERVER,TFW_DB_NAME,TFW_DB_USER,TFW_DB_PASSWORD,TFW_DB_TYPE);
            //self::$arDbs[0] = ComponentDatabase::get_instance(TFW_DB_SERVER,TFW_DB_NAME,TFW_DB_USER,TFW_DB_PASSWORD,TFW_DB_TYPE);
            //self::$arDbs[0]->connect();
        }
        //BD origen
        if(!self::$arDbs[1])
        {    
            self::$arDbs[1] = new ComponentDatabase(APP_DB_SERVER,APP_DB_NAME,APP_DB_USER,APP_DB_PASSWORD,APP_DB_TYPE);
            //self::$arDbs[1] = ComponentDatabase::get_instance(APP_DB_SERVER,APP_DB_NAME,APP_DB_USER,APP_DB_PASSWORD,APP_DB_TYPE);
            //self::$arDbs[1]->connect();
        }
        $this->oLog = new ComponentFile("windows");
        
        //Disparadores para guardar logs de consultas
        $this->_log_insert = true;
        $this->_log_update = true;
        /**
        $this->_log_insert = true;
        $this->_log_select = true;
        $this->_log_update = true;
        $this->_log_delete = true;
        /**/
        
        //crea _path_log_all y _fields_definition
        parent::__construct($sTableName,$iDb);
    }    
}
