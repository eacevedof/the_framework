<?php
/**
* @author Eduardo Acevedo Farje.
* @link www.eduardoaf.com
* @version 1.0.1
* @name AppBehaviourPicklist
* @file appbehaviour_picklist.php   
* @date 26-10-2013 08:25 (SPAIN)
* @observations: 
* @requires:
*/
class AppBehaviourPicklist extends TheApplicationBehaviour
{
    public function __construct() 
    {;
    }
    
    public function get_languages()
    {
        $arPicklist[""]=tr_none;
        $arPicklist["english"]=tr_english;
        $arPicklist["spanish"]=tr_spanish;
        return $arPicklist;
    }
    
    public function get_boolean()
    {
        $arPicklist[""] = tr_none;
        $arPicklist[1] = tr_yes;
        $arPicklist[0] = tr_not;
        return $arPicklist;      
    }
}
