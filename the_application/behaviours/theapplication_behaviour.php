<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.4
 * @name TheApplicationBehaviour
 * @file theapplication_behaviour.php   
 * @date 01-08-2014 17:04 (SPAIN)
 * @observations: 
 * @requires:
 */
class TheApplicationBehaviour extends TheFrameworkBehaviour
{
    public function __construct($iDb=0) 
    {
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
        //Crea la ruta folder_log = custom
        $this->oLog = new ComponentFile("windows");
        //Indica el objeto de conexión y configura ruta de logs
        parent::__construct($iDb);
        $this->oQuery = new ComponentQuery(NULL,TFW_DB_TYPE);
        
    }
    
    public function query($sSQL,$iRow=0,$iColumn=0) 
    {
        return parent::query($sSQL,$iRow,$iColumn);
    }
}
