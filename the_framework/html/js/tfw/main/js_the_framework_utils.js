/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_utils.js  
 * @Name: TfwUtils
 * @Version: 1.0.0;
 * @Date: 17-03-2013 13:58 (SPAIN)
 * @DEPENDENCIES: js_the_framework_core.js
 */
var TfwUtils =
{

    form_submit : function(sFormId)
    {
        var sFormId =  sFormId || "frmInsert";
        var oForm = document.getElementById(sFormId);
        oForm.submit();
    },
    
    form_submit_action : function(sAction, sFormId, sMethod)
    {
        var sAction = sAction || "index.php";
        var sFormId = sFormId || "frmInsert";
        var sMethod = sMethod || "post";
        var oForm = document.getElementById(sFormId);
        
        oForm.attr("action",sAction); 
        oForm.attr("method",sMethod); 
        oForm.submit();
    },
    
    //Al seleccionar el registro desde un grid
    set_values_in_parent : function(sInputId, mxValues)
    {
        //La ventana padre del popup
        var eParentWindow = top.opener;
        //opener: padre //top: popup  //top.opener el padre del popup
        if(eParentWindow)
        {
            var eInput = eParentWindow.document.getElementById(sInputId);
            var mxValues = mxValues || "";
            
            if(TfwControl.is_input_text(eInput)
                ||TfwControl.is_input_textarea(eInput))
            {
                if(TfwCore.is_string(mxValues))
                    eInput.value=mxValues;
                else if(TfwCore.is_array(mxValues))
                    eInput.value=TfwFunction.implode(mxValues,",");
            }
            else if(TfwControl.is_select(eInput))
            {
                var sValue = ""; 
                if(TfwCore.is_string(mxValues))
                    sValue = mxValues;
                else if(TfwCore.is_array(mxValues))
                    sValue = mxValues[0];               
                TfwControl.sel_option(eInput,sValue);
            }
            else if(TfwControl.is_select_multiple(eInput))
            {
                var arValues = [];
                if(TfwCore.is_string(mxValues))
                    arValues.push(mxValues);
                else if(TfwCore.is_array(mxValues))
                    arValues = mxValues;
                TfwControl.sel_multi(eInput, arValues);
            }
            return true;
        }
        else
        {
            bug("no hay ventana de destino");
            return false;
        }
    },
    
    close_window : function(sType)
    {
        //parent or popup or self
        var sType = sType || "popup";
        //top.Opener padre del popup
        var eWindow = top;
        if(sType=="parent") eWindow = top.opener;
        else if(sType=="me" || sType=="self" || sType=="this") eWindow = self;
        
        if(eWindow) eWindow.close();
        else bug("no hay ventana para cerrar");
    },
    
    parent_reload: function()
    {
        var eParentWindow = top.opener;
        eParentWindow.location.replace(eParentWindow.location);
    },
    
    parent_submit: function(sFormId)
    {
        var sFormId = sFormId || "frmInsert";
        var eParentWindow = top.opener;
        var eForm = eParentWindow.document.getElementById(sFormId);
        eForm.submit();
    },
    
    go_to_url: function(sUrl){document.location = sUrl;},
    
    go_to_mvc: function(sController,sPartial,sMethod,arExtra)
    {
        var sUrl="";
        var arUrl = [];
        var sController = sController||"";
        var sPartial = sPartial||"";
        var sMethod = sMethod||"";
        var arExtra = arExtra||[];
        
        if(sController) arUrl.push(sController);
        if(sPartial) arUrl.push(sPartial);
        if(sMethod) arUrl.push(sMethod);
        var sExtra = TfwFunction.implode(arExtra,"&");
        sUrl = TfwFunction.implode(arUrl,"&");
        sUrl += sExtra;
        if(sUrl!="") sUrl = "?"+sUrl;
        this.go_to_url(sUrl);
    }
    
}//Fin TfwUtil
