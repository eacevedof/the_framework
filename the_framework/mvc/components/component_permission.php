<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.5
 * @name ComponentPermission
 * @file component_permission.php
 * @date 07-11-2013 09:58
 */
class ComponentPermission extends TheFrameworkComponent
{
    protected $arPermissions;//dbpermissions. Pueden ser de varios módulos
    protected $sModule;//módulo a comprobar en el array anterior
    protected $arModulePermission; //modulo
    protected $arTypes;
    
    
    public function __construct($arPermissions,$sModule=NULL)
    {  
        if(!$sModule && $_GET["module"]) $sModule = $_GET["module"];
        $this->sModule = $sModule;        
        $this->arPermissions = $arPermissions;
        $this->arTypes = array("is_select_menu","is_insert_menu","is_insert","is_select"
                                ,"is_update","is_delete","is_pick","is_quarantine","is_read","is_excelexport","is_print");
        $this->load();
    }
    
    public function load()
    {
        $arKeys = array_keys($this->arPermissions);
        //si es numerico quiere decir que es un conjunto de filas
        if(is_numeric($arKeys[0]))
        {  
            foreach($this->arPermissions as $arPermission)
                if($arPermission["module"]==$this->sModule)
                    $this->arModulePermission = $arPermission;
        }
        else//es una fila en version corta
            if($this->arPermissions["module"]==$this->sModule)
                $this->arModulePermission = $this->arPermissions;
    }
    
    public function grant_all()
    {
        foreach($this->arTypes as $sType)
            $this->arModulePermission[$sType] = 1;
    }
    
    public function deny_all()
    {
         foreach($this->arTypes as $sType)
            $this->arModulePermission[$sType] = 0;       
    }
    
    /**
     * @param string $sType "is_select_menu","is_insert_menu","is_insert","is_select","is_update","is_delete","is_pick","is_quarantine","is_read","is_excelexport","is_print";
     */
    public function grant($sType){ $this->arModulePermission[$sType] = 0;}    
    /**
     * @param string $sType "is_select_menu","is_insert_menu","is_insert","is_select","is_update","is_delete","is_pick","is_quarantine","is_read","is_excelexport","is_print";
     */    
    public function deny($sType){ $this->arModulePermission[$sType] = 1;}
    
    //=======================
    //        GETS
    //=======================        
    public function is_select_menu(){return $this->arModulePermission["is_select_menu"];}
    public function is_insert_menu(){return $this->arModulePermission["is_insert_menu"];}
    public function is_insert(){return $this->arModulePermission["is_insert"];}
    public function is_select(){return $this->arModulePermission["is_select"];}
    public function is_update(){return $this->arModulePermission["is_update"];}
    public function is_delete(){return $this->arModulePermission["is_delete"];}
    public function is_pick(){return $this->arModulePermission["is_pick"];}
    public function is_quarantine(){return $this->arModulePermission["is_quarantine"];}
    public function is_read(){return $this->arModulePermission["is_read"];}
    public function is_excelexport(){return $this->arModulePermission["is_excelexport"];}
    public function is_print(){return $this->arModulePermission["is_print"];}
    
    //NOT 
    public function is_not_select_menu(){return !$this->arModulePermission["is_select_menu"];}
    public function is_not_insert_menu(){return !$this->arModulePermission["is_insert_menu"];}
    public function is_not_insert(){return !$this->arModulePermission["is_insert"];}
    public function is_not_select(){return !$this->arModulePermission["is_select"];}
    public function is_not_update(){return !$this->arModulePermission["is_update"];}
    public function is_not_delete(){return !$this->arModulePermission["is_delete"];}
    public function is_not_pick(){return !$this->arModulePermission["is_pick"];}
    public function is_not_quarantine(){return !$this->arModulePermission["is_quarantine"];}
    public function is_not_read(){return !$this->arModulePermission["is_read"];}
    public function is_not_excelexport(){return !$this->arModulePermission["is_excelexport"];}
    public function is_not_print(){return !$this->arModulePermission["is_print"];}
    
    //=======================
    //        SETS
    //=======================
    public function set_select_menu($isOn=1){$this->arModulePermission["is_select_menu"] = $isOn;}
    public function set_insert_menu($isOn=1){$this->arModulePermission["is_insert_menu"] = $isOn;}
    public function set_insert($isOn=1){$this->arModulePermission["is_insert"] = $isOn;}
    public function set_select($isOn=1){$this->arModulePermission["is_select"] = $isOn;}
    public function set_update($isOn=1){$this->arModulePermission["is_update"] = $isOn;}
    public function set_delete($isOn=1){$this->arModulePermission["is_delete"] = $isOn;}
    public function set_pick($isOn=1){$this->arModulePermission["is_pick"] = $isOn;}
    public function set_quarantine($isOn=1){$this->arModulePermission["is_quarantine"] = $isOn;}
    public function set_read($isOn=1){$this->arModulePermission["is_read"] = $isOn;}    
    public function set_module($sModule){$this->sModule = $sModule;}
    public function set_permissions($arPermissions){$this->arPermissions = $arPermissions;}
    public function set_excelexport($isOn=1){$this->arModulePermission["is_excelexport"] = $isOn;}
    public function set_print($isOn=1){$this->arModulePermission["is_print"] = $isOn;}    
}