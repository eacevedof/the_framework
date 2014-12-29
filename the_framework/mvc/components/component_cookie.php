<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ComponentCookie 
 * @date 02-03-2013 20:35 (SPAIN)
 * @observations: Usa las funciones básicas de php para tratar cookies
 */
class ComponentCookie //extends TheFrameworkComponent 
{
    private $_name;
    private $_value;
    private $_expire;
    private $_valid_path;
    private $_domain;
    private $isSecured;
    private $isHttpOnly;

    //Variables de control
    private $_message = array();
    private $_is_error = false;

    public function __construct($name="",$value="",$valid_path="/",$expire_time=0
            ,$domain="",$issecured=false,$httponly=false)
    {  
        $this->_name = $name;
        $this->_value = $value;
        $this->_valid_path = $valid_path;
        $this->_expire = time()+60*60*24*30;
        if($expire_time) $this->_expire = $expire_time;
        $this->_domain = $domain;
        $this->isSecured = $issecured;
        $this->isHttpOnly = $httponly;
    }
    
    public function kill($name=null)
    {
        if($name) $this->_name = $name;
        $this->_is_error=setcookie($this->_name,"",time()-3600,$this->_valid_path,$this->_domain,$this->isSecured,$this->isHttpOnly);//
        unset($_COOKIE[$this->_name]);
    }
            
    public function write($name=null,$value=null)
    {
        if($name) $this->_name = $name;
        if($value) $this->_value = $value;
        $this->_is_error=setcookie($this->_name,$this->_value,$this->_expire,$this->_valid_path,$this->_domain,$this->isSecured,$this->isHttpOnly);//
    }
    
    private function load_value($name=null){if($name) $this->_name = $name; $this->_value=$_COOKIE[$this->_name];}
        
    public function set_name($value){$this->_name=$value;}
    public function set_value($value)
    {
        $this->_value = $value;
        $this->write();    
    }
    public function set_expire($value){$this->_expire = $value;}
    public function set_valid_path($value){$this->_valid_path = $value;}
    public function set_domain($value){$this->_domain = $value;}
    public function set_secured($isOn=true){$this->isSecured = $isOn;}
    public function set_httponly($isOn=true){$this->isHttpOnly = $isOn;}
    
    public function get_name(){return $this->_name;}
    public function get_value($name=null){$this->load_value($name); return $this->_value;}
    public function get_expire(){return $this->_expire;}
    public function get_valid_path(){return $this->_valid_path;}
    public function get_domain(){return $this->_domain;}
    public function get_secured(){return $this->isSecured;}
    public function get_httponly(){return $this->isHttpOnly;}
    public function get_all(){return $_COOKIE;}
    public function exists($name=null){if($name)$this->_name=$name;return key_exists($this->_name,$_COOKIE);}
    public function get_array($sName)
    {
        $arCookie=array();
        foreach($_COOKIE[$sName] as $sKey => $sValue)
            $arCookie[$sKey] = $sValue;
        return $arCookie;
    }
}