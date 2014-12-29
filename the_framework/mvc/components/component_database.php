<?php
/**
@author: Eduardo Acevedo Farje
@email: eacevedof@yahoo.es
@web: www.eduardoaf.com
@name: ComponentDatabase
@file: component_database.php
@version: 1.3.0
@date: 24-06-2014 23:43 (SPAIN)
@requires: 
    optional: component_database.php
    component_file.php
*/

//ini_set("mssql.charset","UTF-8");
//Permite recuperación de más de 255 chars
//putenv("TDSVER=70"); no funciona :s 24/05/2014
class ComponentDatabase //Singleton
{
    private $sServer;
    private $sDBName;
    private $sUserName;
    private $sPassword;
    private $sDBType;
   
    private $oLinkId;
    private $arMessages;
    private $isError;
   
    private $iAffectedRows;
    /**
     * @var ComponentFile;
     */
    private $oLog;
    /**
    * @var ComponentDatabase
    */
    private static $oSelf = null;
   
    private $isTimeCapture = FALSE;
    private $fStartTime;
    private $fEndTime;
    private $fQueryTime;
    /**
     * @param string $sServer IP or DNS: ie: 192.168.1.22 or srv_001
     * @param string $sDbName ie: db_payments
     * @param string $sDbUser ie: imroot
     * @param string $sDbPassw ie: ItIsMyAccess
     * @param string $sDbType ie: mssql | mysql
     */
    public function __construct($sServer="",$sDbName="",$sDbUser="",$sDbPassw="",$sDbType="mysql")
    {
        $this->sServer = $sServer;
        $this->sDBName = $sDbName;
        $this->sUserName = $sDbUser;
        $this->sPassword = $sDbPassw;
        if(!$sServer){$this->sServer = TFW_DB_SERVER;}
        if(!$sDbName){$this->sDBName = TFW_DB_NAME;}
        if(!$sDbUser){$this->sUserName = TFW_DB_USER;}
        if(!$sDbPassw){$this->sPassword = TFW_DB_PASSWORD;}
        $this->sDBType = strtolower(trim($sDbType));
        $this->oLinkId = null; //objeto Id de Conexión
        $this->arMessages = array();
        $this->isError = false;
        //TODO Component file hacerlo multisistema
        if(class_exists("ComponentFile"))
        {
            //Solo para logs de errores
            $this->oLog = new ComponentFile("windows");
            $this->oLog->set_path_folder_target(TFW_PATH_FOLDER_LOGDS."errors");
            $sFileName = "db_$sDbName";
            if($_SESSION["tfw_user_identificator"]) $sFileName .= $_SESSION["tfw_user_identificator"]."_";
            $sFileName .= date("Ymd").".log";
            $this->oLog->set_filename_target($sFileName);
        }
    }
   
    /**
    * Este es el pseudo constructor singleton
    * Comprueba si la variable privada $_oSelf tiene un objeto
    * de esta misma clase, sino lo tiene lo crea y lo guarda
    * @return ComponentDatabase
    */
    public static function get_instance($sServer="",$sDbName="",$sDbUser="",$sDbPassw="",$sDbType="mysql")
    {
        //bug(self::$oSelf);die;
        if(!self::$oSelf instanceof self)
        {    
            self::$oSelf = new self($sServer,$sDbName,$sDbUser,$sDbPassw,$sDbType);
            //bug(self::$oSelf,"self database");
            //self::$oSelf->querytimer_on();
        }    
        return self::$oSelf;
    }

    //=================== CONECTAR ===========================
    private function connect_mysql()
    {
        $sDbName = $this->sDBName;
        if($this->oLinkId==0)
        {
            $oConnect = mysql_connect
            (
                $this->sServer,
                $this->sUserName,
                $this->sPassword,
                    true
            );
            if(!is_resource($oConnect))
            {
                $sMessage = "ERROR 0001: No se pudo conectar con la base de datos \"$sDbName\"";
                $this->set_error($sMessage);
                return FALSE;
            }
           
            $isExisteBD = mysql_select_db($this->sDBName, $oConnect);
            //si no se pudo encontrar esa BD lanza un error
            if(!$isExisteBD)
            {
                $sMessage = "ERROR 0002: La base de datos \"$sDbName\" ";
                $this->set_error($sMessage);
                return FALSE;
            }
            //Hay base de datos y se conectó
            else
            {
                //Guardo el id de conexión
                $this->oLinkId = $oConnect;
                $this->arMessages[] = "Conexión realizada con la bd: \"$sDbName\"";  
                mysql_set_charset("utf8",$this->oLinkId);
            }
        }
        //Ya existe recurso abierto, oLinkId!=0
        else
        {}
        //bug($this,"this database");
        return TRUE;
    }
   
    private function connect_mssql()
    {
        if(!$this->oLinkId)
        {
            $this->oLinkId = mssql_connect
            (
                $this->sServer
                ,$this->sUserName
                ,$this->sPassword
                ,TRUE
            );
            
            if(!is_resource($this->oLinkId))
            {
                $sMessage = "ERROR 0003: Db not found! db: $this->sDBName in server: $this->sServer";
                $this->set_error($sMessage);
                return FALSE;
            }

            $isExisteBD  = mssql_select_db($this->sDBName,$this->oLinkId);
            if(!$isExisteBD)
            {
                $sMessage = "ERROR 0004: Db \"$this->sDBName\" does not exist! ";
                $this->set_error($sMessage);
                return FALSE;
            }
            //Hay base de datos y se conectó
            else
            {
                $this->arMessages[] = "Conexion realizada con la bd: \"$this->sDBName linkid: $this->oLinkId \" ";//.var_export($oConnect,TRUE);  
            }
        }
        //Existe recurso abierto
        else
        {}
        return TRUE;
    }
   
    public function connect()
    {
        $isConnected = false;
        switch ($this->sDBType)
        {
            case "mysql":
                //bug("mysql");die;
                $isConnected = $this->connect_mysql();
            break;
        
            case "mssql":
                $isConnected = $this->connect_mssql();
            break;
        
            default:
            break;
        }
        return $isConnected;
    }
    //=================== FIN CONECTAR ===========================
   
    //==================== QUERY==================================
    private function query_mysql($sSQL)
    {
        //Cuando se recupera un objeto desde sesion no cuenta con un linkid
        if(!$this->oLinkId)
            $this->connect();
        try
        {
            $arRows = array();
            $oQuery = mysql_query($sSQL,$this->oLinkId);
            if($oQuery!=false)
            {  
                $this->iAffectedRows = mysql_affected_rows($this->oLinkId); 
                while($arRow_i = mysql_fetch_array($oQuery, MYSQL_ASSOC))
                    $arRows[] = $arRow_i;
            }
            else
            {
                $sMessage = "ERROR IN SQL: $sSQL";
                $this->set_error($sMessage);
                return -1;
            }
            return $arRows;
        }
        catch(Exception $e)
        {
            $sMessage = "ERROR 0005 SQL: $sSQL, $e ";
            $this->set_error($sMessage);
            return -1;
        }
    }
    
    private function query_mssql($sSQL)
    {
        //Cuando se recupera un objeto desde sesion no cuenta con linkid
        if(!$this->oLinkId) 
            $this->connect();
        //bug($this->oLinkId,"linkid");
        if($this->oLinkId)
        {
            $arRows = array();
            try
            {
                //errorson();
                $oQuery = mssql_query($sSQL,$this->oLinkId);
                if($oQuery!=false)
                {
                    $this->iAffectedRows = mssql_rows_affected($this->oLinkId); 
                    while($arRow_i = mssql_fetch_array($oQuery, MSSQL_ASSOC))
                    {   
                        //$this->log_error($arRow_i);
                        //codificacion bd: Modern_Spanish_CI_AS  SELECT DATABASEPROPERTYEX('theframework', 'collation') 
                        //var_dump(mb_detect_encoding($arRow_i["field_name"]),$arRow_i["field_name"]);//die; //esto devuelve ASCII                        
                        foreach($arRow_i as $sFieldName=>$sValue)
                            $arRow_i[$sFieldName] = $this->get_utf8_encoded($sValue);
                        
                        $arRows[] = $arRow_i;
                    }
                }
                else
                {
                    $sMessage  = "SQL instruction with errors! ".mssql_get_last_message()."\n";
                    $sMessage .= "SQL = $sSQL";
                    $this->set_error($sMessage);
                    return FALSE;
                }            
                return $arRows;
            }
            catch (Exception $e)
            {
                $sMessage = "ERROR 0006 SQL: $sSQL, $e ";
                $this->set_error($sMessage);
                return -1;
            }
        }
        else
            exit("linkid: $this->oLinkId db conection failed!");
    }
   
    public function query($sSQL)
    {
        if($this->isTimeCapture) $this->querytimer_on();
        $arRows = array();
        switch( $this->sDBType)
        {
            case "mysql":
                $arRows = $this->query_mysql($sSQL);
            break;
            case "mssql":
                $arRows = $this->query_mssql($sSQL);
            default:
            break;
        }        
        if($this->isTimeCapture) $this->querytimer_off();
        //bug($this->fQueryTime,"$sSQL");
        if((TFW_DEBUG_ISON || TFW_DEBUG_ISREMOTE) && class_exists("ComponentDebug"))
        {
            $sSQL = str_replace("\n"," ",$sSQL);
            if(strstr($sSQL,"UPDATE ")||strstr($sSQL,"DELETE FROM ")||strstr($sSQL,"INSERT INTO "))
            {
                if(!is_array($_SESSION["componentdebug"]))$_SESSION["componentdebug"] = array();
                $_SESSION["componentdebug"][]["sql"] = $sSQL;
                $iLastKey = end(array_keys($_SESSION["componentdebug"]));
                $_SESSION["componentdebug"][$iLastKey]["count"] = $this->iAffectedRows;
                $_SESSION["componentdebug"][$iLastKey]["time"] = $this->fQueryTime;
            }
            ComponentDebug::set_sql($sSQL,$this->iAffectedRows,$this->fQueryTime);            
        }//if TFW_DEBUG_x && classexists componentdebug
        return $arRows;
    }
    //==================== FIN QUERY ================================
   
    public function disconect()
    {
        if($this->oLinkId)
        {
            if($this->sDBType=="mssql")
                mssql_close($this->oLinkId);
            elseif($this->sDBType=="mysql")
                mysql_close($this->oLinkId);
        }
    }
   
// <editor-fold defaultstate="collapsed" desc="QUERY OBJECT">
    private function query_object_mysql($sSQL)
    {
        //bug($sSQL,"query_object_mysql");
        try
        {
            $arRows = array();
            $oQuery = mysql_query($sSQL, $this->oLinkId);
            while($arRow_i = mysql_fetch_object($oQuery))
                $arRows[] = $arRow_i;

            return $arRows;
        }
        catch(Exception $e)
        {
            $this->set_error("ERROR 0011 SQL: $sSQL, $e ");
            return -1;
        }
    }  
    private function query_object_mssql($sSQL)
    {
        try
        {
            $arRows = array();
            //TODO comprobar lo que devuelve _query
            $oQuery = mssql_query($sSQL, $this->oLinkId);
            while($arRow_i = mssql_fetch_object($oQuery))
                $arRows[] = $arRow_i;

            return $arRows;
        }
        catch(Exception $e)
        {
            $this->set_error("ERROR 0012 SQL: $sSQL, $e ");
            return -1;
        }
    }  
    public function query_object($sSQL)
    {
        $arRows = array();
        switch ($this->sDBType)
        {
            case "mysql":
                $arRows = $this->query_object_mysql($sSQL);
            break;
            case "mssql":
                $arRows = $this->query_object_mssql($sSQL);
            default:
            break;
        }
        return $arRows;    
    }    
// </editor-fold>    

    private function querytimer_on()
    {
        list($fMiliSec, $fSec) = explode(" ", microtime());
        $this->fStartTime = ((float)$fSec + (float)$fMiliSec);
    }
    
    private function querytimer_off()
    {
        list($fMiliSec, $fSec) = explode(" ", microtime());
        $this->fEndTime = ((float)$fSec + (float)$fMiliSec);
        $this->fQueryTime = $this->fEndTime-$this->fStartTime;
    }
    
    private function log_error($sContent)
    {
        if(is_object($this->oLog))
        {
            if(!is_string($sContent))
                $sContent = var_export($sContent,1);
            $sContent = "[".date("H:i:s")."] $sContent";
            //$sContent .= var_export($this,TRUE);
            $this->oLog->add_content($sContent);
        }
    }
    
    //=======================================================
    //http://www.joelonsoftware.com/articles/Unicode.html
    //  19/04/2014
    //Resumen: 
    //  1 - ASCII codificación 2^7  [0 - 127] válido para idioma ingles. Tiene más caracteres que ANSI. Los puntos de codigo (ejemplo:10FFF(hex)) son fijos
    //  2 - ANSI codificacion 2^8 mantiene la codificación como ASCII de [0-127] de [128-255] Se definen páginas de códigos ejemplo Arabe,Griego. Los puntos de código varian
    //  3 - DBCS parche para ANSI para idiomas con mas caracteres que los 255, ej: chino Hace que algunos caracteres se hagan con dos bits
    //  4 - UTF-16 2^16 65536 caracteres
    //  5 - UTF-8 Es una codificacion estandard reducida que ahorra en procesador y cubre casi todos los idiomas
    //      -variantes: UTF-7, UCS-4
    //      -variantes menores por internacionalizacion: windows-1252,iso-8859-1=Latin-1. 
    //          En estas no podras guardar letras hebreas o rusas por ejemplo
    //      
    //  El problema surge si se han guardado caracteres que existén en UTF y no tienen equivalente en una codificación de menor juego. Entonces ahí aparece
    //  la marca ?
    //  Ejemplo actual: 
    //      Tengo la codificacion de la bd en Modern_Spanish_CI_AS (ASCII) si he guardado caracteres no ingleses ñ,ó..
    //      Cuando sube a php espero una codificación UTF8. Para los caracteres anglos no hay problema porque el "code point U+n1n2n3n4" es el mismo en las
    //      dos codificaciones. 
    //      Los castellanos hay que traducirlos a su equivalente UTF8 este es el motivo de los metodos abajo expuestos
    //=======================================================
    
    protected function get_utf8_encoded($string) 
    {
        $sCodingType = mb_detect_encoding($string);
        //$sCodingType = mb_detect_encoding($string,"UTF-8,ISO-8859-1,ISO-8859-15,ASCII",TRUE);
        if($sCodingType!="UTF-8")
            $string = utf8_encode($string);
            //mb_convert_encoding($string,"UTF-8",$sCodingType);
        //pr($sCodingType);
        //mb_convert_encoding($string,"UTF-8",$sCodingType);
        return $string; 
    }

    protected function get_iso_encoded($string) 
    {
        //$sCodingType = mb_detect_encoding($string,"UTF-8,ISO-8859-1,ISO-8859-15",TRUE);
        $sCodingType = mb_detect_encoding($string,"UTF-8,ISO-8859,ISO-8859-15,ASCII",TRUE);
        return mb_convert_encoding($string,"ISO-8859-1",$sCodingType);
    }    

    //==================================
    //             GETS
    //==================================
    //private function get_server_name(){return $this->sServer;}
    public function get_user_name(){return $this->sUserName;}
    //private function get_password(){return $this->sPassword;}
    public function get_error_message(){return implode(". ",$this->arMessages);}
    public function get_dbname(){return $this->sDBName;}
    //private function get_link_id(){return $this->oLinkId;}        
    public function get_type(){return $this->sDBType;}
    public function is_error(){return $this->isError;}
    public function get_affected_rows(){ return $this->iAffectedRows;}    
    public function get_query_time(){return $this->fQueryTime;}
    
    //==================================
    //             SETS
    //==================================
    private function set_error($sMessage)
    {
        $this->iAffectedRows = -1;
        $this->isError = TRUE;
        $this->arMessages[] = $sMessage;
        $sMessage = implode(";",$this->arMessages);
        $sMessage = str_replace("\t","",$sMessage);
        $this->log_error($sMessage);
    }
    public function set_timecapture($isOn=TRUE){$this->isTimeCapture=$isOn;}
}