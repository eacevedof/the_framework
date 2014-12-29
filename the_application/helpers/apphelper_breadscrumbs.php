<?php
/**
 * @author Eduardo Acevedo Farje
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name AppHelperBreadscrumbs
 * @file apphelper_breadscrumbs.php    
 * @date 21-07-2013 16:02 (SPAIN)
 * @observations: 
 */
class AppHelperBreadscrumbs extends TheFrameworkHelper
{
    private $arTabs;
    private $isLastNoAnchor;

    public function __construct($arTabs=array())
    {
        $this->arTabs = $arTabs;
    }
    
    public function get_html()
    {
        $sHtmlToReturn = "";
        $sHtmlToReturn .= $this->build_ul_tabs();
        return $sHtmlToReturn;
    }
    
    protected function build_ul_tabs()
    {
        $sHtmlTabs = "<ul class=\"breadcrumb breadcrumb-fix\">\n";
        $sLastTab = array_keys($this->arTabs);
        $sLastTab = end($sLastTab);
        
        foreach($this->arTabs as $sTab=>$arData)
        {
            $sHtmlTabs .= "<li";
            if($sTab==$sLastTab) $sHtmlTabs.=" class=\"active\"";
            $sHtmlTabs .= ">";
            
            $sInnerHtml = $arData["innerhtml"];
            $sHref = $arData["href"]; //$sIcon = $arData["icon"]; 
            $sTarget = $arData["target"];
            
            //$sHtmlTabs .= "<a class=\"$this->sAnchorClass\" href=\"$sHref\"";
            if($sTab!=$sLastTab)
            {
                $sHtmlTabs .= "<a href=\"$sHref\"";
                if($sTarget) $sHtmlTabs .= " target=\"_$sTarget\"";
                $sHtmlTabs .= ">";
                
                $sHtmlTabs .= "$sInnerHtml</a>";
                $sHtmlTabs .= "<span class=\"divider\">/</span> ";
            }
            else 
            {   
                if($this->isLastNoAnchor) $sHtmlTabs .= "$sInnerHtml";
                else
                {
                    //TODO Improve this "else"
                    $sHtmlTabs .= "<a href=\"$sHref\"";
                    if($sTarget) $sHtmlTabs .= " target=\"_$sTarget\"";
                    $sHtmlTabs .= ">";
                    $sHtmlTabs .= "$sInnerHtml</a>";
                }
            }
            $sHtmlTabs .="</li>\n";
        }
        $sHtmlTabs .= "</ul>\n";
        return $sHtmlTabs;
    }    
    public function set_tabs($arTabs){$this->arTabs=$arTabs;}
    public function set_last_noanchor($isOn=TRUE){$this->isLastNoAnchor=$isOn;}
}
