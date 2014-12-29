/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_ajax.js
 * @Name: TfwAjax  
 * @Version: 1.0.1;
 * @date: 22-07-2013 22:52 (SPAIN)
 * @DEPENDENCIES:
 */
var TfwAjax =
{
    //CONSTANTES CON LOS ESTADOS
    READYSTATE_UNINTIALIZED : 0,
    READYSTATE_LOADING : 1,
    READYSTATE_LOADED : 2,
    READYSTATE_INTERACTIVE : 3,
    READYSTATE_COMPLETE : 4,

    //Esta funcion devuelve el objeto creado si no ha habido problemas en su intento
    //sino devuelve un booleano false
    get_object_xmlhttprequest : function()
    {
        var oXmlRequest = false;
        //Si el navegador soporta XMLHttpRequest entonces es un navegador estandar
        if(window.XMLHttpRequest)
        {
            oXmlRequest = new XMLHttpRequest();
        }
        else if(window.ActiveXObject)//sino comprobamos que sea IE
        {
            //Como MICROSOFT maneja dos ActiveX para trabajar con TfwAjax, pruebo los 2
            try
            {
                oXmlRequest = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(sExeption)
            {
                alert("Excepcion: "+sExeption);
                oXmlRequest = new ActiveXObject("Msxml2.XMLHTTP");
            }

        }
        return oXmlRequest;
    },
   
    send_input_by_post : function(sPostUrl, arInput, oFunction)
    {
        var oDivMessage = document.getElementById("divMessage");
        var oXmlRequest = TfwAjax.get_object_xmlhttprequest();
        
        if(oXmlRequest)
        {
            var sUrlParams = TfwAjax.get_input_url(arInput);
            //var sParametros = TfwAjax.get_json_in_url(oJson);
            
            oXmlRequest.open("POST",sPostUrl,true);
            oXmlRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //oXmlRequest.setRequestHeader("Content-length", sUrlParams.length);
            oXmlRequest.setRequestHeader("Connection", "close");

            oXmlRequest.onreadystatechange = function()
            {
                if(oXmlRequest.readyState == TfwAjax.READYSTATE_COMPLETE)
                {
                    var sResponse = oXmlRequest.responseText;
                    //bug(sResponse);
                    //if(oDivMessage) oDivMessage.innerHTML = sResponse;
                    oFunction(sResponse);
                }
            }
            
            oXmlRequest.send(sUrlParams);
            //console.debug(oXmlRequest);
        }
        else
        {
            alert("no se pudo crear el objeto XMLHTTPRequest");
        }
    },
    
    get_input_url: function(arInput)
    {
        //http://localhost/aplicacion?parametro1=valor1&parametro2=valor2&parametro3=valor3
        var iLen = arInput.length;
        var sFieldName = "";
        var sValue = "";
        var sUrl = "";
        var arPairs = [];
        
        if(iLen)
        {
            for(var i=0; i<iLen; i++)
            {
                sFieldName = arInput[i];
                sValue = TfwControl.get_value_by_id(sFieldName);
                sValue = encodeURIComponent(sValue);
                arPairs.push(sFieldName+"="+sValue);
            }
            
            sUrl = arPairs.join("&");
        }
        return sUrl;
    },
    
    
    get_input_in_json: function(arInput)
    {
        
        
    },
    
    /*
     * For check, select (and radio ) controls
     **/
    get_multi_input_in_json: function(sInputId)
    {
        var oCore = TfwCore;
        var arSelectedValues = [];
        var oJson = null;
        
        var eMultiInput = document.getElementById(sInputId);
        if(oCore.is_checkbox(eMultiInput))
        {
            //console.debug(eMultiInput);
        }
        else if(oCore.is_select_multiple(eMultiInput))
        {
            arSelectedValues = TfwControl.get_selected_options_values(eMultiInput);
            oJson = {};
            oJson[sInputId] = arSelectedValues;
        }
        //console.debug(oJson);
        return oJson;
    },
    
    get_json_in_url: function(oJson)
    {
        var sUrl = '';
        for(var sProperty in oJson)
        {
            sUrl += sProperty+'='+oJson[sProperty];
            sUrl += '&';
        }
        return sUrl;
    },
    
    get_json_array_in_url: function(oJson)
    {
        var sUrl = '';
        for(var sProperty in oJson)
        {
            var arValores = oJson[sProperty];
            for(var i in arValores)
            {
                sUrl += sProperty+'[]='+arValores[i];
                sUrl += '&';
            }
        }
        return sUrl;
    }
}