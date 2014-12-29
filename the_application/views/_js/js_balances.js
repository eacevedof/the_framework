//
//INCOMES
//
function update_subtotal()
{
    var oTxtSubtCash = document.getElementById("txtSubtotalCash");
    var oTxtSubtCCard = document.getElementById("txtSubtotalCreditcard");
    var oTxtSubtCheck = document.getElementById("txtSubtotalCheck");//Paid
    var oTxtSubtotal = document.getElementById("txtSubtotal");
    var isFields = (oTxtSubtCash && oTxtSubtCCard && oTxtSubtCheck && oTxtSubtotal);
    
    if(isFields)
    {
        var fCash = parseFloat(oTxtSubtCash.value);
        var fCCard = parseFloat(oTxtSubtCCard.value);
        var fCheck = parseFloat(oTxtSubtCheck.value);
        
        if(isNaN(fCash)) fCash = 0.00;
        if(isNaN(fCCard)) fCCard = 0.00;
        if(isNaN(fCheck)) fCheck = 0.00;
        
        var fSubtotal = fCash + fCCard + fCheck;
        fSubtotal = fSubtotal.toFixed(2);
        oTxtSubtotal.value = fSubtotal.toString();
    }    
}


//
// OUTCOMES
//
function update_total_out()
{
    //txtSubtotalBusiness,txtSubtotalOwn,txtTotalOut,txtTotalPending
    var oTxtBusiness = document.getElementById("txtSubtotalBusiness");
    var oTxtOwn = document.getElementById("txtSubtotalOwn");
    var oTxtSubtotal = document.getElementById("txtSubtotal");
    var oTxtTotalOut = document.getElementById("txtTotalOut");//Paid
    var isFields = (oTxtBusiness && oTxtOwn && oTxtSubtotal && oTxtTotalOut);
    
    if(isFields)
    {
        var fSubtotBusiness = parseFloat(oTxtBusiness.value);
        var fOwn = parseFloat(oTxtOwn.value);
        
        if(isNaN(fSubtotBusiness)) fSubtotBusiness = 0.00;
        if(isNaN(fOwn)) fOwn = 0.00;
        
        var fTotalOut = fSubtotBusiness + fOwn;
        fTotalOut = fTotalOut.toFixed(2);
        oTxtSubtotal.value = fTotalOut.toString();
        oTxtTotalOut.value = fTotalOut.toString();
    }
}//update_total_out

function update_total_pending()
{
    //txtSubtotalBusiness,txtSubtotalOwn,txtTotalOut,txtTotalPending
    var oTxtBusiness = document.getElementById("txtSubtotalBusiness");
    var oTxtOwn = document.getElementById("txtSubtotalOwn");
    var oTxtTotalOut = document.getElementById("txtTotalOut");//Paid
    var oTxtTotalPending = document.getElementById("txtTotalPending");
    var isFields = (oTxtBusiness && oTxtOwn && oTxtTotalOut && oTxtTotalPending);
    
    if(isFields)
    {
        var fSubtotBusiness = parseFloat(oTxtBusiness.value);
        var fOwn = parseFloat(oTxtOwn.value);
        
        if(isNaN(fSubtotBusiness)) fSubtotBusiness = 0.00;
        if(isNaN(fOwn)) fOwn = 0.00;
        
        var fTotal = fSubtotBusiness + fOwn;
        var fTotalOut = parseFloat(oTxtTotalOut.value);
        var fTotalPending = fTotal - fTotalOut;
        fTotalPending = fTotalPending.toFixed(2);
        oTxtTotalPending.value = fTotalPending.toString();
    }
}//update_total_pending


