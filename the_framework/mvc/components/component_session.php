<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.0
 * @name ComponentSession
 * @file component_session.php
 * @date 12-06-2014 20:27
 * @observations
 */
class ComponentSession extends TheFrameworkComponent
{
    
    public function __construct()
    {   ;}
    
    public function close()
    {
        if(!headers_sent())
        {
            $_SESSION=NULL; //$_GET=NULL; $_POST=NULL;
            session_unset();
            session_start();
            /*CLOSE PREVIOUS SESSION*/
            //session_unlink();
            session_destroy();
            /*NOW GENERATING LINK FOR SESSION DATA */
            session_id();
            session_start();
            //session_regenerate_id();//Regenerating SID for sending
        }
    }    

    public function set($sKey,$mxValue){$_SESSION[$sKey]=$mxValue;}
    public function set_filter($sController,$sPartial,$sMethod,$mxValue)
    {
        $sKey = "$sController$sPartial$sMethod";
        $_SESSION[$sKey] = $mxValue;
    }
    
    public function clear($sKey){unset($_SESSION[$sKey]);}
    public function clearall(){unset($_SESSION);}
    /**
     * Escribe en $_SESSION["tfw_user_identificator"] = $iId
     * @param int $iId Puede ser negativo 
     */
    public function set_user_id($iId){$_SESSION["tfw_user_identificator"] = $iId;}
    public function set_user_language($sLanguage){$_SESSION["tfw_user_language"] = $sLanguage;}
    public function set_user_language_id($IdLanguage){$_SESSION["tfw_user_idlanguage"] = $IdLanguage;}
    
    public function get($sKey){return $_SESSION[$sKey];}
    public function get_filter($sController,$sPartial,$sMethod)
    {
        $sKey = "$sController$sPartial$sMethod";
        return $this->get($sKey);
    }
    
    public function exists($sKey){return ($_SESSION[$sKey]!=null);}
    
    public function get_user_id(){return $_SESSION["tfw_user_identificator"];}
    public function get_user_language(){return $_SESSION["tfw_user_language"];}
    public function get_user_id_language(){return $_SESSION["tfw_user_idlanguage"];}
}