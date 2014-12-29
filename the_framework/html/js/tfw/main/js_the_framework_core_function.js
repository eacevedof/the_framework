/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_core_function.js  
 * @Name: TfwFunction  
 * @Version: 1.0.1
 * @Date: 28-07-2013 17:28 (SPAIN)
 * @DEPENDENCIES: js_the_framework_core.js
 */
var bug = function(value,title)
{
    if(window.console != undefined)
    {    
        if(title!=null) console.debug(title);
        console.debug(value);
    }
}

var TfwFunction =
{
//IMPORT
//http://www.daniweb.com/web-development/javascript-dhtml-ajax/threads/78817
    empty : function(oAnyObject)
    {
        var isEmpty = false;
        
        isEmpty = 
        (
            oAnyObject==null || oAnyObject==undefined ||
            oAnyObject===null || oAnyObject===undefined ||   
            oAnyObject==false || oAnyObject=="undefined" ||
            oAnyObject===false || oAnyObject==="undefined" ||
            oAnyObject==0 || oAnyObject=="0" || oAnyObject=="" ||
            oAnyObject===0 || oAnyObject==="0" || oAnyObject===""
        );
        return isEmpty;
    },
    
    count: function(oArray){return oArray.length;},
    
    is_numeric : function(sValue)
    {
        sValue = sValue.replace(",","");
        if(sValue=="" || sValue==null || sValue==undefined)
            return false;
        else
            return !isNaN(sValue);
    },
    
    strstr : function(sString,sValue){return (sString.indexOf(sValue)!=-1);},
    
    trim : function(sValue)
    {
        if((typeof sValue)=="string")
        {
            var sPatern = /^\s*|\s*$/g;
            var sTrimmed = sValue.replace(sPatern,"");
            return sTrimmed;
        }
        bug("Error 15: "+ sValue.type +" is not a string");
        return "";
    },

    in_array : function(arValues,mxSearch)
    {
        for(var i=0; i<arValues.length; i++)
            if(arValues[i]==mxSearch)
                return true;
        return false;
    },
    
    implode : function(arValues,sSeparator)
    {
        var sString = "";
        if(TfwCore.is_array(arValues))
            sString = arValues.join(sSeparator);
        return sString;
    },
    
    explode : function(arValues,sSeparator)
    {
        var sString = "";
        if(TfwCore.is_array(arValues))
            sString = arValues.split(sSeparator);
        return sString;
    }    
}
//==============================  END TfwFunction =================================
