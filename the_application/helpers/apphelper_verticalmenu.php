<?php
/**
 * @author Eduardo Acevedo Farje
 * @link www.eduardoaf.com
 * @version 1.1.0
 * @name ApphelperVerticalmenu
 * @file apphelper_verticalmenu.php
 * @date 14-06-2013 14:45 (SPAIN)
 * @observations: 
 */
import_helper("anchor,ul,ul_li,span");
class ApphelperVerticalmenu extends TheFrameworkHelper
{
    private $arMenu = array();
    private $sCurrentUlId;
    private $isSubmenuDisplayed;
    private $arOptionsVisibility;
    //Indica si despues de leer los permisos hay subitems a mostrar
    private $isSubmenuBuilt;
    
    /**
     * 
     * @param array $arMenu
     * @param string $id
     * @param string $sCurrentUlId
     * @param boolean $isSubmenuDisplayed
     */
    public function __construct($arMenu=array(),$id="",$arVisibility=array(),$sCurrentUlId="",$isSubmenuDisplayed=false)
    {
        $this->_id = $id;
        $this->arMenu = $arMenu;
        //bug($this->arMenu);die;
        $this->arOptionsVisibility = $arVisibility;
        $this->sCurrentUlId = $sCurrentUlId;
        $this->isSubmenuDisplayed = $isSubmenuDisplayed;
    }

    private function get_ar()
    {
        $arLi[0] = array
        (
            
            "li"=>array("id"=>"module","class"=>"current")
            ,"a"=>array("href"=>"#","class"=>"no-submenu","text"=>"texto","icon"=>"","onclick"=>"js")
            ,"ul"=>array
            (
                "style"=>"display:none,display:block;"
                ,"li"=>array
                (
                    array("href"=>"url","text"=>"texto","class"=>"cls","onclick"=>"js")
                    ,array("href"=>"url","text"=>"texto","class"=>"cls","onclick"=>"js")
                )
            )
        );    
    }
    
    private function extract_submenu_array($arMainLi)
    {
        $arSubmenu = array();
        $arKeys = array_keys($arMainLi);
        if(in_array("ul",$arKeys)) $arSubmenu = $arMainLi["ul"];
        return $arSubmenu;
    }
    
    private function build_nested_ul($arSubmenu,$sId,$sModule)
    {
        $sHtmlSubmenu = "";
        $sStyle = $arSubmenu["style"];
        //Extraigo los links (botones) a listado e insert
        $arLi = $arSubmenu["li"];
        //bug($arSubmenu);
        $arObjLi = array();
        foreach($arLi as $iOrder => $arLiData)
        {
            //bug($sId,"id");
            $sSubLiId = $sId."_$iOrder";
            $sPermissionType = $arLiData["permission"];
            //bug($sPermissionType,"perm type nested");
            if($sPermissionType)
            {
                //bug($this->arOptionsVisibility,"permtype:$sPermissionType");
                //get_list
                if($sPermissionType=="select")
                {   
                    //bug($this->is_select($sModule),"$sModule.is_select");
                    if(!$this->is_select($sModule))
                        continue;
                }
                //insert
                else
                    if(!$this->is_insert($sModule))
                        continue;
            }
            $oSpan = new HelperSpan();
            $oSpan->add_class($arLiData["class"]);
            $oAnchor = new HelperAnchor();
            $oAnchor->set_href($arLiData["href"]);
            $oAnchor->set_js_onclick($arLiData["onclick"]);
            $oAnchor->set_innerhtml($oSpan->get_html().$arLiData["text"]);
            $oLi = new HelperUlLi($sSubLiId);
            $oLi->add_inner_object($oAnchor);
            $arObjLi[] = $oLi;
        }
        $oSpan = NULL;
        //Si se ha añadido al menos un item en el submenu
        if($arObjLi)
        {
            $oUl = new HelperUl($sId);
            $oUl->set_array_li($arObjLi); $arObjLi=NULL;
            $oUl->add_style($sStyle);
            $sHtmlSubmenu = $oUl->get_html();        
        }
        return $sHtmlSubmenu;
    }
    
    /**
     * Elimino todos los módulos que no tienen ningún tipo de permiso
     */
    private function remove_not_present()
    {
        $arPermModule = array();
        foreach($this->arOptionsVisibility as $arPermission)
            $arPermModule[$arPermission["id_module"]] = $arPermission["module"];
        
        foreach($this->arMenu as $i=>$arItem)
            if(!in_array($arItem["li"]["id"],$arPermModule))
                unset($this->arMenu[$i]);
    }
    
    public function get_html()
    {  
        $sHtmlToReturn = "";
        $arObjLi = array();
        //Quito los módulos sobre los que no se tiene ningún tipo de permiso
        $this->remove_not_present();
        //bug($this->arOptionsVisibility,"visib");
        //bug($this->arMenu,"armenu");die;
        foreach($this->arMenu as $iOrder=>$arMainItem)
        {
            //$addToArray = TRUE;
            //id del módulo
            $sModule = $arMainItem["li"]["id"];
            //if($this->is_insert($sIdLi)&&$this->is_select($sModule))
            //bug($sModule,"module");
            if($sModule) $sLiId = "Menu".ucfirst($sModule);
            $sClass = $arMainItem["li"]["class"];
            
            $oLiMain = new HelperUlLi($sLiId);
            $oLiMain->add_class($sClass);
            if($this->sCurrentUlId==$sModule) 
            {
                $oLiMain->add_class("current");
                //if($this->isSubmenuDisplayed) $oLi->add_class();
            }
            $arSubmenu = $this->extract_submenu_array($arMainItem);
            //bug($arSubmenu,"ar submenu");
            $oSpan = new HelperSpan();
            $oAnchor = new HelperAnchor();
            //si tiene submenu
            if($arSubmenu)
            {
                //build_nested_ul comprueba permisos
                $sHtmlSubmenu = $this->build_nested_ul($arSubmenu,"ulMain_$iOrder",$sModule);                
                if($sHtmlSubmenu)
                {
                    $oSpan->add_class($arMainItem["a"]["icon"]);
                    $oAnchor->set_href("#");
                    $oAnchor->set_innerhtml($oSpan->get_html().$arMainItem["a"]["text"]);
                    $oLiMain->set_innerhtml($oAnchor->get_html().$sHtmlSubmenu);
                }
                //como lleva submenu y no se ha devuelto ningun item (string vacio)
                //no se añade $oLiMain al array del menu principal
                else 
                    continue;
            }
            //No submenu
            else
            {
                $sPermissionType = $arMainItem["li"]["permission"];
                if($sPermissionType)
                {
                   if($sPermissionType=="select")
                   {    
                       if(!$this->is_select($sModule))
                           continue;
                   }
                   //no se daría casi nunca puesto que el menu principal tiene
                   //new and list
                   else
                       if(!$this->is_insert($sModule))
                           continue;
                }
                $oSpan->add_class($arMainItem["a"]["icon"]);
                $oAnchor->add_class("no-submenu");
                $oAnchor->set_innerhtml($oSpan->get_html().$arMainItem["a"]["text"]);
                $oAnchor->set_href($arMainItem["a"]["href"]);
                $oAnchor->set_js_onclick($arMainItem["a"]["onclick"]);
                $oLiMain->set_innerhtml($oAnchor->get_html());
            }
            
            $arObjLi[] = $oLiMain;
        }//Fin foreach arMenu
        
        //El objeto lista principal del menu
        $oUl = new HelperUl("ul".$this->_id);
        $oUl->set_array_li($arObjLi);
        $sHtmlToReturn = $oUl->get_html(); 
        return $sHtmlToReturn;
    }

    private function is_insert($sModule)
    {
        foreach($this->arOptionsVisibility as $arPermission)
        {
            //bug($arPermission,"arpermiision insert");
            $sPerModule = $arPermission["module"];
            $isInsert = $arPermission["is_insert"];
            if($sPerModule==$sModule && $isInsert)
                return true;
        }
        return false;
    }
    
    private function is_select($sModule)
    {
        //bug($sModule);
        //bug($this->arOptionsVisibility,"options visibility");
        foreach($this->arOptionsVisibility as $arPermission)
        {
            //bug($arPermission,"arpermission select");
            $sPerModule = $arPermission["module"];
            $isSelect = $arPermission["is_select"];
            if($sPerModule==$sModule && $isSelect)
                return true;
        }
        return false;
    }
    
    //=======================
    //         SETS
    //=======================    
    public function set_menu(array $arMenu){$this->arMenu = $arMenu;}
    public function set_current_id($sValue){$this->sCurrentUlId = $sValue;}
    /**
     * Array de permisos sobre módulos
     * @param array $arPermission Tipo array(0=array(id_module=>"id","module"=>module,is_select_menu=>1,...,is_print=>0)
     */
    public function set_user_permissions(array $arPermission){$this->arOptionsVisibility=$arPermission;}
}