/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_field.js  
 * @Name: TfwField
 * @Version: 1.0.0;
 * @Date: 17-03-2013 13:58 (SPAIN)
 * @DEPENDENCIES: js_the_framework_core.js
 */
function TfwField(sId,iLen,sLabel,arValidate)
{
    var sId = sId || "";
    var iLength = iLen || 10;
    var arValidate = arValidate || [];
    var sLabel = sLabel || "";
    
    this.get_id = function(){return sId;}
    this.get_length = function(){return iLength;}
    this.get_validate_types = function(){return arValidate;}
    this.get_label = function(){return sLabel;}
    this.exists = function()
    {
        var oElement = document.getElementById(sId);
        if(oElement) return true;
        return false;
    }
    this.get_value = function()
    {
        var sValue="";
        sValue = TfwControl.get_value_by_id(sId);
        return sValue;
    }
    
}
//var oField = new TfwField("txtId",5,["length","required","integer",""]);
//bug(oField.get_value(),"field value");