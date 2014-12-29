/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_core.js  
 * @Name: TfwCore
 * @Version: 1.0.2
 * @Date: 27-07-2013 20:22 (SPAIN)
 * @DEPENDENCIES: js_the_framework_core_function.js
 */


var TfwCore =
{   
    bug : function(oAnyObject, iAlertType, sNombre, showAlert)
    {
        var eDivMensaje = document.getElementById("divMensaje");
        if(iAlertType==1)
        {
            if(sNombre != null)
                alert(oAnyObject);
            else
                alert(sNombre + " : " + oAnyObject);
        }
        //div_print
        else if(iAlertType==2)
        {
            if(eDivMensaje!=null)
                eDivMensaje.innerHTML = oAnyObject; 
            else
                eDivMensaje.innerHTML = sNombre +":"+ oAnyObject;             
        }
        else
        {
            if((window.console.firebug !== undefined) && (window.console.firebug!==null))
                console.debug(oAnyObject);
            else
            {
                if(showAlert==true)
                    alert("console.debug not installed");
            }
        }
    },
    
    show_in_console : function (sMensaje)
    {
        if((window.console !== undefined) && 
            (window.console!==null))
            console.debug(sMensaje);
    },
    
    //sType: http://www.crockford.com/javascript/remedial.html
    is_of_type: function(eHtml,sType)
    {
        var sElementType = this.get_type(eHtml);
        if(sType == sElementType)
            return true;
        return false;
    },

    is_object: function(oAnyObject){return this.is_of_type(oAnyObject,"object")},

    is_null: function (oAnyObject){return (oAnyObject == null);},

    //Indica si existe una propiedad en el objeto
    //oAnyProperty = oAnyObject.oProperty
    is_undefined: function(oAnyProperty){return (oAnyProperty==undefined);},
    
    exists_by_id: function(sElmentId)
    {
        if(this.is_string(sElmentId))
        {
            var eTemp = document.getElementById(sElmentId);
            return !(this.is_null(eTemp) || this.is_undefined(eTemp));
        }
        this.show_in_console("Error 1: "+sElmentId + ": is not a string");
        return false;
    },

    exists_by_name: function(sElementName)
    {
        if(this.is_string(sElementName))
        {
            var eTemp = document.getElementsByName(sElementName)[0];
            return !this.is_null(eTemp);
        }
        this.show_in_console("Error 2: "+ sElementName + ": is not a string");
        return false;
    },

    is_string: function (sAnyString)
    {
        if(this.is_null(sAnyString))
        {
            this.show_in_console("Error 3: "+sAnyString.toString() +"is not a string");
            return false;
        }
        return this.get_type(sAnyString)=="string";
    },

    //string : devuelve el tipo
    get_type: function (oAnyObject)
    {
        //typeof is an operator not a method, so dont use ()
        var sElementType = typeof oAnyObject;
        return sElementType;
    },

    //string
    get_type_by_id: function(sElmentId)
    {
        if(this.exists_by_id(sElmentId))
        {
            var oTemp = document.getElementById(sElmentId);
            return this.get_type(oTemp);
        }
        this.show_in_console("Error 4: Element with id="+sElmentId+ ": does not exist");
        return "";
    },

    is_array: function(oAnyObject)
    {
        if(oAnyObject.length != undefined)
            return true;
        return false;
    },
    
    is_inarray: function(sItem,arSearch)
    {
       
        if(arSearch.indexOf(sItem) != -1)
            return true;
        return false;
    },

    is_ie: function()
    {
        //element.attachEvent("onclick",startDragDrop)
        var sBrowserName = navigator.appName;
        this.show_in_console("browser: "+sBrowserName);
        return (sBrowserName=="Microsoft Internet Explorer");
    },
    
    get_current_browser: function(){return navigator.appName;},
    
    set_event: function(oElement,sEvent,oFunction,doBubble)
    {
        if(!this.is_null(oElement))
        {
            if(this.is_null(oFunction))
                this.show_in_console("Error 13: "+oFunction.toString()+" is null");

            if(this.is_ie())
            {
                //document.attachEvent("onreadystatechange", callback);
                //window.attachEvent("onLoad",callback);
                oElement.attachEvent("on"+sEvent, oFunction);
            }
            //Good browsers :)
            else
            {
                if(doBubble!=true)
                    oElement.addEventListener(sEvent, oFunction, false);
                else
                    oElement.addEventListener(sEvent, oFunction, true);
            }
        }
        else
            this.show_in_console("Error 14: Event not set. oElement is null")
    },

    remove_event: function(oElement, sEvent, oFunction, doBubble)
    {
        if(!this.is_null(oElement))
        {
            if(this.is_null(oFunction))
                this.show_in_console("Error 19: oFunction is null");

            if(this.is_ie())
                oElement.detachEvent("on"+sEvent, oFunction);
            //Good browsers :)
            else
            {
                if(doBubble!=true)
                    oElement.removeEventListener(sEvent, oFunction, false);
                else
                    oElement.removeEventListener(sEvent, oFunction, true);
            }
        }
        else
            this.show_in_console("Error 20: Event not removed. oElement is null")
    },

    //object
    get_element_by_id: function(sElementId)
    {
        var oElement = null;
        if(this.is_string(sElementId))
        {
            oElement = document.getElementById(sElementId);
            if(this.is_null(oElement))
                this.show_in_console("Error 18: Element with id="+sElementId+" does not exist");                
        }
        else
            this.show_in_console("Error 18: Element id must be a string");
        return oElement;
    },
    //object
    get_element_by_name: function(sElementName)
    {
        var oElement = null;
        if(this.exists_by_name(sElementName))
            oElement = document.getElementsByName(sElementName)[0];
        else
            this.show_in_console("Error 18: Element with name="+sElementName+" does not exist");
        return oElement;
    },        
    get_elements_by_attribute:function(sAttribute,sValue)
    {
        var arElements = document.getElementsByTagName('*');
        for(var i=0; i<arElements.length; i++)
            if(arElements[i].getAttribute(sAttribute) == sValue)
                return arElements[i];        
    },
    get_key_from_value: function(arValues, sValueToSearch)
    {
        var sTmpValue = null;
        var iPosicion = 0;
        var iNumItems = 0;
        if(this.is_array(arValues))
        {
            iNumItems = arValues.length;
            for(iPosicion=0; iPosicion<iNumItems; iPosicion++)
            {
                sTmpValue = arValues[iPosicion];
                if(sTmpValue == sValueToSearch)
                    return iPosicion;
            }
            //this.show_in_console("");
        }
        else
            this.show_in_console("ERROR: object "+arValues.toString()+" is not an array");
        return -1;
    },
    
    set_innerhtml: function(oElement,sInnerHtml)
    {
        if(oElement)
        {
            if(oElement.innerHTML!=undefined)
                oElement.innerHTML = sInnerHtml;
        }
        else
            bug("set_innerhtml failed!. element is null");
    }
    
}

//==============================  END TfwCore =================================
