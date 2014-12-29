function get_toplist(sUrl,doClose,iW,iH)
{
    var iW = iW||800;
    var iH = iH||600;
    var sUrlAction = "module=orders&view=get_toplist";
    if(sUrl) sUrlAction += "&"+sUrl;
    if(doClose) sUrlAction += "&close=1";
    else sUrlAction += "&close=0";
    
    var sIdCustomer = TfwControl.get_value_by_id("selIdCustomer");
    if(sIdCustomer)
    {
        sUrlAction += "&id_customer="+sIdCustomer;
        window.open("index.php?"+sUrlAction,"top10","width="+iW+",height="+iH,status=0,scrollbars=0,resizable=0,left=0,top=0);
    }
    else
        alert("No customer supplied");
}

function get_history(sUrl,doClose,iW,iH)
{
    var iW = iW||800;
    var iH = iH||600;
    var sUrlAction = "module=customernotes&view=get_history";
    if(sUrl) sUrlAction += "&"+sUrl;
    if(doClose) sUrlAction += "&close=1";
    else sUrlAction += "&close=0";
    
    var sIdCustomer = TfwControl.get_value_by_id("selIdCustomer");
    if(sIdCustomer)
    {
        sUrlAction += "&id_customer="+sIdCustomer;
        window.open("index.php?"+sUrlAction,"top10","width="+iW+",height="+iH,status=0,scrollbars=0,resizable=0,left=0,top=0);
    }
    else
        alert("No customer supplied");
}
