/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_control.js  
 * @Name: TfwControl  
 * @Version: 1.0.0;
 * @Date: 17-03-2013 13:58 (SPAIN)
 * @DEPENDENCIES: js_the_framework_core.js
 *               js_the_framework_function.js
 */
var TfwControl =
{
    oCore : TfwCore,
    oFunction : TfwFunction,
    
    //object
    get_input_text_by_id: function(sTextId)
    {
        if(this.oCore.exists_by_id(sTextId))
        {
            var oText = document.getElementById(sTextId);
            return oText;
        }
        this.oCore.show_in_console("Error 8: input-text with id="+sTextId+" does not exist");
        return null;
    },

    //string
    get_text_value : function(eText)
    {
        if(this.is_input_text(eText))
            return eText.value;
        this.oCore.show_in_console("Error 9: "+eText+" is not an input text");
        return "";
    },

    //string
    get_text_value_by_id: function(sTextId)
    {
        //before assign it checks existence
        var eText = this.get_input_text_by_id(sTextId);
        //before return it checks if type is text
        return this.get_text_value(eText);
    },

    get_selected_option_value : function(eSelect)
    {
        var iNumOpciones = eSelect.length;
        for (var i=0; i<iNumOpciones; i++) 
            if(eSelect[i].selected == true) 
                return eSelect[i].value;
        return null;
    },

    get_selected_options_values : function(eSelect)
    {
        //http://www.javascriptkit.com/jsref/select.shtml
        var arOpciones = eSelect.options;
        var iNumOpciones = arOpciones.length;
        var arValues = [];

        for (var i=0; i<iNumOpciones; i++) 
            if(arOpciones[i].selected == true) 
                arValues.push(arOpciones[i].value);
        return arValues;
    },
    
    get_input_in_json: function(sIdInput)
    {
        var oJson = null;
        var eInput = document.getElementById(sIdInput);
        if(eInput!=null)
        {
            oJson = {};
            oJson[sIdInput] = eInput.value;
        }
        return oJson;
    },
    
    is_input_textarea_element: function(oAnyElement)
    {
        var sTextType = "[object HTMLTextAreaElement]";
        if(!this.oCore.is_null(oAnyElement))
            return (oAnyElement.toString() == sTextType);

        this.oCore.show_in_console("Error 5: "+oAnyElement.toString()+" is a null object");
        return false;        
    },

    //boolean
    is_input_element: function(oAnyElement)
    {
        //[object HTMLInputElement]
        var sTextType = "[object HTMLInputElement]";
        if(!this.oCore.is_null(oAnyElement))
            return (oAnyElement.toString() == sTextType);

        this.oCore.show_in_console("Error 5: "+oAnyElement+" is a null object");
        return false;
    },

    is_input_element_by_id : function (sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_input_element(eTemp);
    },
    
    //boolean input-text
    is_input_text: function(eElement)
    {
        if(this.is_input_element(eElement))
            return (eElement.type == "text");

        this.oCore.show_in_console("Error 7: "+ eElement.toString() + ": is not an HTMLInputElement. Not a text");
        return false;
    },

    is_input_text_by_id: function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_input_text(eTemp);
    },
    
    //boolean input-text
    is_input_hidden: function(eElement)
    {
        if(this.is_input_element(eElement))
            return (eElement.type == "hidden");
        
        this.oCore.show_in_console("Error 7: "+ eElement.toString() + ": is not an HTMLInputElement. Not a hidden");
        return false;
    },

    is_input_hidden_by_id: function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_input_hidden(eTemp);
    },
    
    //boolean input-text
    is_password: function(eElement)
    {
        if(this.is_input_element(eElement))
            return (eElement.type == "password");
        
        this.oCore.show_in_console("Error 7: "+ eElement.toString() + ": is not an HTMLInputElement. Not a password");
        return false;
    },

    is_pasword_by_id: function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.oCore.is_password(eTemp);
    },    

    is_file: function(eElement)
    {
        if(this.is_input_element(eElement))
            return (eElement.type == "file");

        this.oCore.show_in_console("Error 7: "+ eElement.toString() + ": is not an HTMLInputElement. Not a file");
        return false;
    },

    is_file_by_id: function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_file(eTemp);
    },    

    is_input_textarea: function(eElement)
    {
        if(this.is_input_textarea_element(eElement))
            return true;
        
        this.oCore.show_in_console("Error 7: "+ eElement.toString() + ": is not an HTMLTextAreaElement. Not a textarea");
        return false;        
    },
    
    is_input_textarea_by_id: function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_input_textarea(eTemp);
    },
    
    //boolean inpu-text
    is_radio: function(eElement)
    {
        //bug(eElement,"is radio");
        if(this.is_input_element(eElement))
            return (eElement.type == "radio");
        
        this.oCore.show_in_console("Error 7: "+ eElement + ": is not an HTMLInputElement");
        return false;
    },
    
    is_radio_by_id: function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_radio(eTemp);
    },
    
    //select-one  select-multiple
    is_select: function(eElement)
    {
        //bug(eElement,"is_select");
        var sTextType = "[object HTMLSelectElement]";
        if(!this.oCore.is_null(eElement))
            return (eElement.toString() == sTextType);
        else
            this.oCore.show_in_console("Error 5: "+eElement+" is a null object");
        
        return false;            
    },
    
    is_select_by_id : function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        //bug(this.is_select(eTemp),"is_select");
        return this.is_select(eTemp);
    },

    is_select_one : function(eElement)
    {
        if(this.is_select(eElement))
            return eElement.type == "select-one";
        
        this.oCore.show_in_console("Error 7: "+ eElement.toString() + ": is not a Select control");
        return false;                   
    },

    is_select_one_by_id : function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_select_one(eTemp);
    },
    
    is_select_multiple : function(eElement)
    {
        if(this.is_select(eElement))
            return eElement.type == "select-multiple";
        
        this.oCore.show_in_console("Error 7: "+ eElement.toString() + ": is not a Select control");
        return false;                   
    },

    is_select_multiple_by_id : function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_select_multiple(eTemp);
    },
  
    //Hay que comprobar que sea tipo radio y checkbox antes
    //de llamar a esta funcion
    is_checked : function(eElement)
    {
        var IsChecked = IsChecked || 0;
        if(eElement.checked!=undefined)
        {
            if(eElement.checked==1)
                return true;
            return false;
        }
        else
            this.oCore.show_in_console("ERROR: "+eElement.toString()+"is not checkkeable");
        return false;
    },
  
    //Check o radio
    is_unchecked: function(eElement){return !this.is_checked(eElement);},
    //boolean
    is_checkbox: function(eElement)
    {
        if(this.is_input_element(eElement))
            return (eElement.type == "checkbox");
        
        this.oCore.show_in_console("Error 6: "+ eElement.toString() + ": is not an HTMLInputElement. Not a checkbox");
        return false;           
    },
    
    is_checkbox_by_id: function(sElementId)
    {
        var eTemp = document.getElementById(sElementId);
        return this.is_checkbox(eTemp);
    },
    
    //object
    get_checkbox_by_id: function(sCheckBoxId)
    {
        var eCheckBox = document.getElementById(sCheckBoxId);
        if(this.is_checkbox(eCheckBox))
            return eCheckBox;
        
        this.oCore.show_in_console("Error 10: input-checkbox with id="+sCheckBoxId+" does not exist");
        return null;
    },

    //object
    get_radio_by_id: function(sRadioId)
    {
        var eRadio = document.getElementById(sRadioId);
        if(this.is_radio(eRadio))
            return eRadio;
        
        this.oCore.show_in_console("Error 10: input-radio with id="+sRadioId+" does not exist");
        return null;
    },

    get_first_checkbox_by_name: function(sCheckBoxName)
    {
        var arCheckBox = document.getElementsByName(sCheckBoxName);
        if(this.oCore.is_array(arCheckBox))
            return arCheckBox[0];
        return null;
    },
    
    //IsChecked 0:uncheck, null or 1: check
    set_checkbox_check: function(eCheckBox, IsChecked)
    {
        if(this.is_checkbox(eCheckBox))
        {
            if(IsChecked==null) IsChecked = 1;
            eCheckBox.checked = IsChecked;
        }
        else
            this.oCore.show_in_console("Error 11: "+eCheckBox.type +" is not a checkbox");
    },

    //IsChecked 0:uncheck, null or 1: check
    set_checkboxes_in_form: function(sFormId, IsChecked)
    {
        var IsChecked = IsChecked || 0;
        var oForm = null;
        var eTemp = null;
        var i = 0;
        //Comprobamos si existe el formulario
        if(this.oCore.exists_by_id(sFormId))
        {
            oForm = document.getElementById(sFormId);
            //Control de segundo parametro vacio
            for (i=0; i<oForm.elements.length; i++)
            {
                eTemp = oForm.elements[i];
                //Comprueba si es checkbox antes de asignar
                this.set_checkbox_check(eTemp,IsChecked);
            }
        }
        else
            this.oCore.show_in_console("Form with id="+sFormId + " does not exist");
    },
    
    //Funciona con: "chkTest[]". iChecked (null=0),1
    //solo para checkboxes, pq los radios no se activn en grupo
    set_checked_by_name: function(sCheckBoxName, IsChecked)
    {
        var IsChecked = IsChecked || 0;
        var eTemp = null;
        var arElements = document.getElementsByName(sCheckBoxName);
        var i = 0;
        var iTamano = 0;

        bug(arElements,"arElements");
        if(this.oCore.is_array(arElements))
        {
            iTamano = this.oFunction.count(arElements);
            //if(IsChecked==null) IsChecked=0;
            for(i=0; i<iTamano; i++)
            {
                eTemp = arElements[i];
                bug(eTemp,"element to check");bug(IsChecked,"is checked");
                this.set_checkbox_check(eTemp, IsChecked);
            }
        }
        else
            this.oCore.show_in_console("Checkboxes with name="+sCheckBoxName + " is not a checkbox array");
    },
    
    //For checkboxes and radios
    set_checked_by_value : function(eElement, sValueToCheck, IsChecked)
    {
        var IsChecked = IsChecked || 0;
        if(sValueToCheck != "" && sValueToCheck!=null && sValueToCheck !=undefined)
        {
            if(this.is_checkbox(eElement)||this.is_radio(eElement))
                if(eElement.value == sValueToCheck)
                    eElement.checked = IsChecked;
            else
                this.oCore.show_in_console("ERROR: "+eElement.toString()+" is not checkeable");
        }
        else
            this.oCore.show_in_console("ERROR: You have to provide a sValueToCheck");
    },  
    
    //For checkboxes. Using a chechbox name it search in its group whether exists
    //the value passed and following isChecked
    set_checked_value_by_name: function(sCheckBoxName, sValueToCheck, IsChecked)
    {
        var IsChecked = IsChecked || 0;
        var iPosicion = -1;
        //TODO falta get_unchecked_values
        var arCheckBoxes = document.getElementsByName(sCheckBoxName);
        var eCheckBox = null;
        var sTmpValue = "";
        
        if(this.oCore.is_array(arCheckBoxes))
        {
            for(iPosicion in arCheckBoxes)
            {
                eCheckBox = arCheckBoxes[iPosicion];
                sTmpValue = eCheckBox.value;
                if(sTmpValue == sValueToCheck)
                    eCheckBox.checked = IsChecked;
            }
        }
    },

    //Search all the values pased in a group of checkboxes and sets their
    //values to isChecked
    set_checked_values_by_name: function(sCheckBoxName, arValues, IsChecked)
    {
        //TODO: que sirva incluso para un string
        var arValues = arValues || [];
        var IsChecked = IsChecked || 0;
        var iPosicion = null;
        var sValue = null;
        
        //isArray
        if(this.oCore.is_array(arValues))
        {
            for(iPosicion in arValues)
            {
                sValue = arValues[iPosicion];
                this.set_checked_value_by_name(sCheckBoxName, sValue, IsChecked);
            }
        }
        
    },
    
    //autoselecciona
    sel_option: function(eSelect,sOptionValue)
    {
        if(this.is_select(eSelect))
        {
            for(var i=0; i<eSelect.options.length; i++)
                if(eSelect.options[i].value == sOptionValue)
                {
                    eSelect.selectedIndex = i;
                    return;
                }
        }
        else
             this.oCore.show_in_console("ERROR element is not a select control");
    },
    //autoselecciona
    sel_option_by_id: function(sElementId,sOptionValue)
    {
        var eSelect = document.getElementById(sElementId);
        this.sel_option(eSelect, sOptionValue);
    },    
    
    //autoselecciona
    sel_multi: function(eSelect,arValues,sOptionGroup)
    {
        if(this.is_select(eSelect))
        {
            for(var i=0; i<eSelect.options.length; i++)
                for(var j=0; j<arValues.length; j++)
                    if(eSelect.options[i].value == arValues[j])
                         eSelect.options[i].selected = true;
                        //eSelect.selectedIndex = i;
        }
        else
             this.oCore.show_in_console("ERROR element is not a select control");        
    },
    
    sel_multi_by_id: function(sElementId,arValues,sOptionGroup)
    {
        var eSelect = document.getElementById(sElementId);
        this.sel_multi(eSelect, arValues, sOptionGroup);
    },    
    //Devuelve los valores de un grupo de checkboxes
    //o radios que cumplan con ischecked
    get_checked_values: function(sElementName, IsChecked)
    {
        var IsChecked = IsChecked || 0;
        var arValues = [];
        var arCheckBoxes = document.getElementsByName(sElementName);
        var i=0;
        var iTamano = 0;
        var eTempCheckBox = null;
        var sTempValue = "";

        if(this.oCore.is_array(arCheckBoxes))
        {
            iTamano = this.oFunction.count(arCheckBoxes);
            for(i=0; i<iTamano; i++)
            {
                eTempCheckBox = arCheckBoxes[i];
                if(this.is_checkbox(eTempCheckBox)||this.is_radio(eTempCheckBox))
                {
                    if(eTempCheckBox.checked == IsChecked)
                        arValues.push(sTempValue);    
                }
                else
                {
                    this.oCore.show_in_console("ERROR trying to recover values that were checked");
                    return null;
                }
            }
        }
        else
            this.oCore.show_in_console("Error getting checked values from "+sElementName+". No es un array");
        return arValues;
     },
    
    //Checks and Radio devuelve el valor del control si cumple con 
    //isChecked
    get_checked_value: function(eElement, IsChecked)
    {
        var IsChecked = IsChecked || 0;
        var sValue = null;
        if(this.is_checkbox(eElement) || this.is_radio(eElement))
        {
            if(eElement.checked == IsChecked)
                sValue = eElement.value;
        }
        else
            this.oCore.show_in_console("Error getting checkbox value. "+eElement.toString()+"is not a checkbox");
        return sValue;
    },
    
    //devuelve el valor del control indicado por el id.
    //no tiene mucho sentido puesto que habria que asignar un unico id
    //a cada checkbox que se crea
    get_checked_value_by_id: function(sCheckBoxId)
    {
        var eTemp = document.getElementById(sCheckBoxId);
        return this.get_checked_value(eTemp);
    },
    
    //boolean
    is_checkbox_checked: function(eCheckBox)
    {
        //bug(eCheckBox,"eCheckbox");
        //alert(eCheckBox);
        if(this.is_checkbox(eCheckBox)) 
            //alert("is checkbox")    
            return (eCheckBox.checked);
        
        this.oCore.show_in_console("Error 12:"+ eCheckBox.type +" is not a checkbox");
        return false;
    },

    //boolean
    is_checkbox_checked_by_id: function(sCheckBoxId)
    {
        //oCheckBox is null or an instace of an existing checkbox
        var oCheckBox = this.get_checkbox_by_id(sCheckBoxId);
        return this.is_checkbox_checked(oCheckBox);
    },
    
    get_value_by_id: function(sElementId)
    {
        //this = TfwControl
        //bug(this,"this en get_value_by_id");
        var sValue="";
        var eElement = this.oCore.get_element_by_id(sElementId);
        
        if(!eElement) return sValue;
        if(this.is_input_text(eElement)
           ||this.is_input_textarea(eElement)||this.is_input_hidden(eElement))
            sValue=eElement.value;        
        else if(this.is_radio(eElement))
            sValue = this.get_checked_value(eElement,1);
        //Esto podria haber sido un multiselect
        else if(this.is_select(eElement))
            sValue = this.get_selected_option_value(eElement);        
        //Se podría haber seleccionado varis checks
        else if(this.is_checkbox(eElement))
            sValue = this.get_checked_value(eElement,1);
        else
            bug("get_value_by_id: element not found :"+sElementId);
        return sValue;
    },
    
    form_submit: function(sFormId,sUrlAction)
    {
        var sFormId = sFormId || "frmList";
        var sUrlAction = sUrlAction || null;
        var eForm = this.oCore.get_element_by_id(sFormId);
        if(sUrlAction) eForm.action = sUrlAction;
        eForm.submit();
    }
}