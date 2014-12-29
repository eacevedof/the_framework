function InputText(id,name,value,isreadonly,maxlength)
{
    var sId="";
    var sName="";
    var sValue="";
    var isReadonly=false;
    var iMaxLength=0;
    
    var oThis = null;
    
    function __construct()
    {
        sId=id;
        sName=name;
        sValue=value;
        isReadonly=isreadonly;
        iMaxLength=maxlength;
        
        oThis = document.createElement("input");
        oThis.type="text";
        if(sId!="") oThis.id=sId;
        if(sName!="") oThis.name=sName;
        if(sValue!="") oThis.value=sValue;
        if(isReadonly) oThis.readonly="readonly";
        if(iMaxLength!=0) oThis.maxlength=iMaxLength;
    }
    
    this.set_value = function(sValue){oThis.value = sValue;}
    this.set_id = function(sValue){oThis.id = sValue;}
    this.set_name = function(sValue){oThis.name = sValue;}
    this.readonly = function(sValue)
    {
        if(sValue)oThis.readonly = "readonly";
        else oThis.removeAttribute("readonly");
    }
    this.maxlenth = function(sValue){oThis.maxlenth=value;}
    this.disabled = function(sValue){if(sValue)oThis.disabled = "disabled";
        else oThis.removeAttribute("disabled");}
    
}