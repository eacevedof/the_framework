<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name ApphelperHeadertabs 
 * @date 14-07-2013 18:18 (SPAIN)
 * @file apphelper_headertabs.php
 * @observations: common tabs
 */
//include_once("helper_ul.php");
//include_once("helper_ul_li.php");
//include_once("helper_anchor.php");
include_once("theapplication_helper.php");

class AppHelperHeadertabs extends TheApplicationHelper
{
    private $arTabs;
    private $sActiveTab;
    private $sAnchorClass;

    public function __construct($arTabs=array(),$sActive="")
    {
        $this->arTabs = $arTabs;
        $this->sActiveTab = $sActive;
        $this->sAnchorClass = "tab-href-fix";
    }
    
    public function get_html()
    {
        //TODO: Para las pestañas laterales hace falta encerrar esto entre divs
        $sHtmlToReturn = "";
        $sHtmlToReturn .= $this->build_ul_tabs();
        return $sHtmlToReturn;
    }
    
    protected function build_ul_tabs()
    {
        $sHtmlTabs = "<ul class=\"nav nav-tabs nav-tabs-fix\">\n";
        foreach($this->arTabs as $sTab=>$arData)
        {
            $sHtmlTabs .= "<li";
            if($sTab==$this->sActiveTab) $sHtmlTabs.=" class=\"active\"";
            $sHtmlTabs .= ">";
            $sHref = $arData["href"]; $sIcon = $arData["icon"]; $sTarget = $arData["target"];
            $sInnerHtml = $arData["innerhtml"];
            $sHtmlTabs .= "<a class=\"$this->sAnchorClass\" href=\"$sHref\"";
            if($sTarget) $sHtmlTabs .= " target=\"_$sTarget\"";
            $sHtmlTabs .= ">";
            if($sIcon) $sHtmlTabs .= "<span class=\"$sIcon\"></span> ";
            $sHtmlTabs .= "$sInnerHtml</a>";
            $sHtmlTabs .="</li>\n";
        }
        $sHtmlTabs .= "</ul>\n";
        return $sHtmlTabs;
    }
    
    public function set_anchor_class($sValue){$this->sAnchorClass=$sValue;}
    public function set_active_tab($sValue){$this->sActiveTab=$sValue;}
    public function set_h2_icon($sValue){$this->sH2IconClass=$sValue;}
    /**
     * 
     * @param array $arTabs array(href=>url,icon=>class icon, target=>, innerhtml=>texttoshow)
     */
    public function set_tabs($arTabs){$this->arTabs=$arTabs;}
}
