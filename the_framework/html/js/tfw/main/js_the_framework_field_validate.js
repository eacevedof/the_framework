/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_field_validate.js  
 * @Name: TfwFieldValidate  
 * @Version: 1.0.1;
 * @Date: 14-08-2013 19:25 (SPAIN)
 * @DEPENDENCIES: js_the_framework_core.js
 *                js_the_framework_control.js 
 */
var TfwFieldValidate =
{    
    is_email: function(sValue)
    {
        var sExpresionRegular = /^[0-9a-z_\-\.]+@[0-9a-z\-\.]+\.[a-z]{2,4}$/i;
        return sExpresionRegular.test(sValue);
    },
    is_length : function(sElementId,iLength)
    {
        var sValue = "";
        if(TfwControl.is_input_text_by_id(sElementId)
           ||TfwControl.is_input_textarea_by_id(sElementId))
        {
            sValue = TfwControl.get_value_by_id(sElementId);
            //bug(sValue.length,"length "+sElementId);
            if(sValue.length>iLength)
                return false;
        }
        else if(TfwControl.is_checkbox_by_id(sElementId)
           ||TfwControl.is_select_by_id(sElementId))
        {
            var arValues = [];
            if(TfwControl.is_checkbox_by_id(sElementId))
            {
                var eCheckbox = TfwControl.get_checkbox_by_id(sElementId);
                arValues = TfwControl.get_checked_values(eCheckbox.name,1);
                
                for(var i=0; i<arValues.length; i++)
                    if(arValues[i].length>iLength)
                        return false;
                //return true;
            }
            else
            {
                var eSelect = document.getElementById(sElementId);
                arValues = TfwControl.get_selected_options_values(eSelect);
                
                for(var i=0; i<arValues.length; i++)
                    if(arValues[i].length>iLength)
                        return false;
                //return true;                
            }
        }
        return true;
    },
    
    is_required: function(sElementId)
    {
        var sValue = "";
        if(TfwControl.is_input_text_by_id(sElementId)
           ||TfwControl.is_input_textarea_by_id(sElementId))
        {
            sValue = TfwControl.get_value_by_id(sElementId);
            sValue = TfwFunction.trim(sValue);
            if(sValue=="")
                return false;
        }
        //seleccion multiple
        else if(TfwControl.is_checkbox_by_id(sElementId)
           ||TfwControl.is_select_by_id(sElementId)
           ||TfwControl.is_radio_by_id(sElementId))
        {
            var arValues = [];
            
            if(TfwControl.is_checkbox_by_id(sElementId)
                ||TfwControl.is_radio_by_id(sElementId))
            {
                var eCheckOrRadio = TfwControl.get_checkbox_by_id(sElementId);
                if(TfwCore.is_null(eCheckOrRadio))
                    eCheckOrRadio = TfwControl.get_radio_by_id(sElementId);
                arValues = TfwControl.get_checked_values(eCheckOrRadio.name,1);
                if(arValues.length<1)
                    return false;
            }
            else
            {
                var eSelect = document.getElementById(sElementId);
                arValues = TfwControl.get_selected_options_values(eSelect);
                for(var i=0; i<arValues.length; i++)
                {
                    sValue = arValues[i];
                    if(sValue=="")
                        return false;
                }
            }
        }

        return true;
    },
    
    is_integer: function(sElementId)
    {
        var sValue = "";
        if(TfwControl.is_input_text_by_id(sElementId)
           ||TfwControl.is_input_textarea_by_id(sElementId))
        {
            sValue = TfwControl.get_value_by_id(sElementId);
            if(TfwFunction.is_numeric(sValue))
            {
                if(TfwFunction.strstr(sValue,","))
                    return false;
                else if(TfwFunction.strstr(sValue,"."))
                    return false;
            }
            //Not numeric
            else return false;
        }
        else if(TfwControl.is_checkbox_by_id(sElementId)
           ||TfwControl.is_select_by_id(sElementId))
        {
            var arValues = [];
            //var sTempValue = "";
            if(TfwControl.is_checkbox_by_id(sElementId))
            {
                var eCheckbox = TfwControl.get_checkbox_by_id(sElementId);
                arValues = TfwControl.get_checked_values(eCheckbox.name,1);
                for(var i=0; i<arValues.length; i++)
                {
                    sValue = arValues[i];
                    if(TfwFunction.is_numeric(sValue))
                    {
                        if(TfwFunction.strstr(sValue,","))
                        {    return false;}
                        else if(TfwFunction.strstr(sValue,"."))
                        {return false;}
                    }
                    //Not numeric
                    else return false;
                }
            }
            else
            {
                var eSelect = document.getElementById(sElementId);
                arValues = TfwControl.get_selected_options_values(eSelect);
                for(var i=0; i<arValues.length; i++)
                {
                    sValue = arValues[i];
                    if(TfwFunction.is_numeric(sValue))
                    {
                        if(TfwFunction.strstr(sValue,","))
                        {    
                            return false;
                        }
                        else if(TfwFunction.strstr(sValue,"."))
                        {
                            return false;
                        }
                    }
                    //Not numeric
                    else return false;
                }
            }
        }
        return true;        
    },
    
    is_numeric: function(sElementId)
    {
        var sValue = "";
        if(TfwControl.is_input_text_by_id(sElementId)
           ||TfwControl.is_input_textarea_by_id(sElementId))
        {
            sValue = TfwControl.get_value_by_id(sElementId);
            if(!TfwFunction.is_numeric(sValue))
                return false;
        }
        else if(TfwControl.is_checkbox_by_id(sElementId)
           ||TfwControl.is_select_by_id(sElementId))
        {
            var arValues = [];
            if(TfwControl.is_checkbox_by_id(sElementId))
            {
                var eCheckbox = TfwControl.get_checkbox_by_id(sElementId);
                arValues = TfwControl.get_checked_values(eCheckbox.name,1);
                for(var i=0; i<arValues.length; i++)
                {
                    sValue = arValues[i];
                    if(!TfwFunction.is_numeric(sValue))
                        return false;
                }
            }
            else
            {
                var eSelect = document.getElementById(sElementId);
                arValues = TfwControl.get_selected_options_values(eSelect);
                for(var i=0; i<arValues.length; i++)
                {
                    sValue = arValues[i];
                    if(!TfwFunction.is_numeric(sValue))
                        return false;
                }
            }
        }
        return true;
    },
    
    checkfields: function(arObjFields)
    {
        var objError = {message:""};
        //Objeto de la clase js_the_framework_field
        var oTmpField = null;//TfwField(sId,iLen,sLabel,arValidate)
        var arValTypes = [];
        
        //bug(arFields,"arFields");
        for(var i=0; i<arObjFields.length; i++)
        {
            oTmpField = arObjFields[i];
            //alert(oTmpField.get_id());
            
            arValTypes = oTmpField.get_validate_types();
            //bug(arValTypes,"valtypes");
            if(oTmpField.exists())
                for(var j=0; j<arValTypes.length; j++)
                {
                    //bug(j,"j");
                    if(arValTypes[j]=="length")
                    {
                        if(!TfwFieldValidate.is_length(oTmpField.get_id(),oTmpField.get_length()))
                        {
                            objError["message"] = "Field "+oTmpField.get_label()+" has exeeded its length!";
                            break;
                        }
                    }

                    if(arValTypes[j]=="integer")
                    {
                        if(!TfwFieldValidate.is_integer(oTmpField.get_id()))
                        {
                            objError["message"] = "Field "+oTmpField.get_label()+" is not integer!";
                            break;
                        }
                    }

                    if(arValTypes[j]=="numeric")
                    {
                        if(!TfwFieldValidate.is_numeric(oTmpField.get_id()))
                        {
                            objError["message"] = "Field "+oTmpField.get_label()+" is not numeric!";
                            break;
                        }
                    }

                    if(arValTypes[j]=="required")
                    {
                        if(!TfwFieldValidate.is_required(oTmpField.get_id()))
                        {
                            objError["message"] = "Field "+oTmpField.get_label()+" is empty!";
                            break;
                        }
                    }
                }//fin for arValTypes
                
            if(objError.message!="")
            {  
                objError["id"] = oTmpField.get_id();
                objError["label"] = oTmpField.get_label();
                var oFieldFocus = document.getElementById(objError.id);
                var sValue = ""; 
                if(oFieldFocus)
                { 
                    //tengo q guardar el valor aqui pq si aplico select() se borra el texto
                    sValue = oFieldFocus.value;
                    oFieldFocus.focus(); 
                }
                if(TfwControl.is_input_text(oFieldFocus) || TfwControl.is_input_textarea(oFieldFocus))
                {
                    oFieldFocus.value = sValue;
                    //oFieldFocus.select(); 
                }
                break;
            }
        }//fin for arObjFields
        return objError;
    },//fin function checkfields
    
    reset: function(arFieldsId)
    {
        var oTmpElement = null;
        
        //bug(arFields,"arFields");
        for(var i=0; i<arFieldsId.length; i++)
        {
            oTmpElement = document.getElementById(arFieldsId[i]);
            if(oTmpElement)
            {
                if(TfwControl.is_checkbox(oTmpElement))
                   TfwControl.set_checkbox_check(oTmpElement,0);
                else if(TfwControl.is_select(oTmpElement))
                   TfwControl.sel_option(oTmpElement,"");
                else if(TfwControl.is_input_text(oTmpElement)
                        ||TfwControl.is_input_hidden(oTmpElement) || TfwControl.is_input_textarea(oTmpElement))
                    oTmpElement.value="";
            }
            else
                continue;
        }//fin for arObjFields
    }//fin function checkfields
}//TfwFieldValidate    