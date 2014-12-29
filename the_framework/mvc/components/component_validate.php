<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.5
 * @name ComponentValidate
 * @file component_validate.php
 * @date 08-08-2014 13:21
 */
class ComponentValidate
{
    private $arFieldsConfig;
    private $arFieldsValues;
    private $arErrorMessages=array();
    
    private $arError = null;
    private $isError = false;
    private $iCharsExeeded = 0;
    
    public function __construct($arFieldsConfig=array(),$arFieldsValues=array())
    {
        $this->arFieldsConfig = $arFieldsConfig;
        $this->arFieldsValues = $arFieldsValues;
        $this->arErrorMessages["length"] = "length exeeded in ";
        $this->arErrorMessages["required"] = "is empty";
        $this->arErrorMessages["integer"] = "is not an integer";
        $this->arErrorMessages["numeric"] = "is not numeric";
        $this->arErrorMessages["email"] = "wrong email format";
    }
    
    
    public function get_error_field()
    {
        foreach($this->arFieldsConfig as $sFieldName=>$arValidation)
        {
            //Solo si existe en el post se valida
            if($this->is_inpost($sFieldName))
            {    
                $sFieldValue = $this->arFieldsValues[$sFieldName];
                if($arValidation["length"])
                {
                    $this->iCharsExeeded = $this->length($sFieldValue,(int)$arValidation["length"]);
                    if($this->iCharsExeeded)
                    {
                        $this->arErrorMessages["length"] .= $this->iCharsExeeded . " chars (T: ".strlen($sFieldValue).")";
                        $this->set_error($sFieldName,$arValidation["label"],"length",$sFieldValue,$this->arErrorMessages["length"]);
                        return $this->arError;
                    }
                }
                if(in_array("required",$arValidation["type"]))
                {
                    if($this->required($sFieldValue))
                    {
                        $this->set_error($sFieldName,$arValidation["label"],"required",$sFieldValue,$this->arErrorMessages["required"]);
                        return $this->arError;
                    }
                }
                if(in_array("numeric",$arValidation["type"]))
                {
                    if(!$this->numeric($sFieldValue))
                    {
                        //pr($sFieldValue);
                        $this->set_error($sFieldName,$arValidation["label"],"numeric",$sFieldValue,$this->arErrorMessages["numeric"]);
                        return $this->arError;
                    }
                }
                if(in_array("email",$arValidation["type"]))
                {
                    if(!$this->email($sFieldValue))
                    {
                        $this->set_error($sFieldName,$arValidation["label"],"email",$sFieldValue,$this->arErrorMessages["email"]);
                        return $this->arError;
                    }
                }
            }//if is_inpost($sFieldName)
        }//Fin foreach fieldconfig
        return FALSE;
    }//get_error_field

    public function length($sFieldValue,$iLength)
    {
        $iExeed = strlen($sFieldValue)-$iLength;//si $iExeed>0
        //bug($iExeed,"exeed");
        if($iExeed<=0) return false; 
        return $iExeed;
    }
    
    public function email($sValue)
    {
        $sRegExp="/(^[0-9a-zA-Z]+(?:[._][0-9a-zA-Z]+)*)@([0-9a-zA-Z]+(?:[._-][0-9a-zA-Z]+)*\.[0-9a-zA-Z]{2,3})$/";
        $sValue = $this->clean_string($sValue);
        if(preg_match($sRegExp,$sValue))
            return true;
        return false;;
    }
    public function name($sValue)
    {
        $sRegExp="/^[a-züA-ZÜ]+[\s]?[a-züA-ZÜ]+$/";
        $sValue = $this->clean_string($sValue);
        if(preg_match($sRegExp,$sValue))
            return true;
        return false;
    }

    public function phone($sValue)
    {
        $sRegExp="/^[0-9\s\+\-]+$/";
        //$sRegExp="/^([0-9]+)([-]?|[0-9]+)([0-9]+$)/";
        $sValue = $this->clean_string($sValue);
        if(preg_match($sRegExp,$sValue))
            return true;
        return false;
    }

    public function date($sValue)
    {
        $sRegExp="/^[0-9\s\+\-]+$/";
        ///^\d{1,2}\/\d{1,2}\/\d{4}$/'
        $sValue = $this->clean_string($sValue);
        if(preg_match($sRegExp,$sValue))
            return true;
        return false;
    }
    
    public function numeric($sValue)
    {
        $sValue = str_replace(",","",$sValue);
        $sValue = str_replace(".","",$sValue);
        if(is_numeric($sValue))
            return true;
        return false;
    }
    
    public function integer($sValue)
    {
        if(is_integer($sValue))
            return true;
        return false;
    }    
    
    public function required($sValue){return $this->clean_string($sValue)==="";}
    
    protected function clean_string($sValue){return strtolower(trim($sValue));}
    
    private function is_inpost($sKey){return in_array(array_keys($_POST),$sKey);}
    
    public function set_fields_config($arFields){$this->arFieldsConfig=$arFields;}
    public function set_fields_values($arFields){$this->arFieldsValues=$arFields;}
    protected function set_error($sFieldName,$sLabel,$sType,$sValue,$sMessage="")
    {
        $this->isError = TRUE;
        $this->arError = array("field"=>$sFieldName,"value"=>$sValue,"type"=>$sType,"message"=>$sMessage,"label"=>$sLabel);
    }
    
    public function set_error_messages($arMessages=array("length"=>"","required"=>"")){$this->arErrorMessages=$arMessages;}
}