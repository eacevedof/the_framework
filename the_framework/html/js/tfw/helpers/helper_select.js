//alert("helper_select.js");
function HelperSelect(id,name,size,multiple,style,cls)
{
    var sId= id||"";
    var sName= name||"";
    var iSize= size||1;
    var isMultiple = multiple||false;
    var sClass=cls||"";
    var sStyle=style||"";
   
    var oSelect = null;
    var __construct = function()
    {
        sId= id||"";
        sName= name||"";
        iSize= size||1;
        isMultiple = multiple||false;
        sClass=cls||"";
        sStyle=style||"";
        
        oSelect = document.createElement("select");
    }
    
    
    //solo para webkit
    document.addEventListener("DOMContentLoaded", __construct, false);

    //==============================
    // METODOS PRIVADOS
    //==============================
    //Al enviar los datos de un producto esta funcion recibe el formulario o un mensaje de tipo de error
    var data_received=function(sDataReceived)
    {
        var sMsgError = "";
        var objError = new Object();
        objError["error1"] = "El producto ya existe en el detalle";
        objError["error2"] = "Tipo de pedido no definido";
        objError["error3"] = "El producto seleccionado no existe o no est� activado";
        
        for(var sProperty in objError)
            if(sDataReceived==sProperty)
            {sMsgError=objError[sProperty];break;}
        
        if(sMsgError!="")
        {
            oGenericModal.close();
            alert(sMsgError);
        }
        //Si no ha habido errores en la recuperaci�n del producto
        //sDataReceived es un string con el formulario asi pues se incluye
        //dentro del div content
        else
        {
            //bug(sDataReceived,"data_from server");
            var oDivAux = oGenericModal.get_divstrip_as_object();
            oDivAux.style.visibility="hidden";
            oDivAux = oGenericModal.get_divcontent_as_object();
            oDivAux.innerHTML="";
            oDivAux.innerHTML=sDataReceived;
            oGenericModal.set_divcontent_middle();
        }
    }
    
    var after_datasaved = function(sDataReceived)
    {
        //alert(sDataReceived);
        //despues de hacer el insert por orderline_saveproduct.php se comprueba
        //los contadores globals y se devuelve este resultado. Asi pues alg�n valor
        //mayor a 0 es que ha ocurrido un error
        if(sDataReceived=="error1")
            alert("Ha ocurrido un error. No se ha podido agregar el producto");
        else if(sDataReceived=="error2")
            alert("Producto no agregado. Ya existe en el detalle");
        //sDataReceived=="0" o mensaje de llegada
        else
        {   
            oGenericModal.close();
            //Intenta recargar las lineas de pedido
            oUtils.parent_refresh();
            alert("Linea agregada correctamente");
            //alert(sDataReceived);
            var oStrongAlert = document.getElementById("sngAlert");
            if(sDataReceived!="0")
            {
                oStrongAlert.innerHTML = sDataReceived;
                //fuerzo la vista a esta zona
                location.hash="#divMainForm";
            }
            else
                oStrongAlert.innerHTML="";
        }
    }

    var after_dataupdated = function(sDataReceived)
    {
        if(sDataReceived=="error1")
            alert("Ha ocurrido un error. No se ha podido modificar la linea");
        else if(sDataReceived=="error2")
            alert("Linea no modificada. No existe en el detalle");
        else
        {   
            oGenericModal.close();
            alert("Linea modificada correctamente");
            
            var oStrongAlert = document.getElementById("sngAlert");
            if(sDataReceived!="0")
            {
                oStrongAlert.innerHTML = sDataReceived;
                location.hash="#divMainForm";
            }
            else
                oStrongAlert.innerHTML="";
            oUtils.refresh_window();
        }
    }
    
    //Esta funcion se utiliza antes de enviar los datos para guardar la linea
    var check_errors_before_save = function()
    {
        var arFieldNames = ["txtUnidades","txtBonificacion","txtDescuento","txtNeto"];
        var arLabels = ["Unid.","Bonif.","Dto.","Neto"];
        //Valores no numericos
        for(var i=0; i<arFieldNames.length; i++)
            //bug(isNaN(get_txtvalue(arFieldNames[i])),arFieldNames[i]);
            if(isNaN(get_txtvalue(arFieldNames[i])))
                return "El campo "+arLabels[i]+" no es n�merico!";
            
        var iUnidades = get_txtvalue("txtUnidades");
        iUnidades = parseInt(iUnidades);
        //si no llega al minimo
        if(iUnidades<=0) return "El campo Unid. debe tener un valor mayor a 0";
        
        if(get_txtvalue("txtBonificacion")>iUnidades)
            return "El valor de "+arLabels[1]+" no puede ser mayor a "+arLabels[0];
        
        var iUnidadesBulto = get_txtvalue("txtUnidadesBulto");
        var iResto = iUnidades%iUnidadesBulto;
        if(iResto!=0) return "El valor de Unid. no cumple con un multiplo v�lido";
        
        var fNeto = get_txtvalue("txtNeto");
        if(fNeto<0) return "El Neto no puede ser menor que 0";
        
        //Si es un pedido de tipo cambio debe seleecionar Entregado o Recogido
        var sOrderType=document.getElementById("Order_Type").value;
        if(sOrderType=="C")
        {
            sOrderType = document.getElementById("selTipoCambio");
            sOrderType = sOrderType.options[sOrderType.selectedIndex].value;
            if(sOrderType=="" ||sOrderType==null)
                return "Debe seleccionar un tipo de cambio"
        }
        
        return "noerrors";
    }
  
    var get_txtvalue=function(sElementId)
    {
        var fValue = 0.00;
        var oElement = document.getElementById(sElementId);
        if(oElement!=null)
        {
            fValue = oElement.value;
            if(fValue==0) return 0.00;
        }
        return fValue;
    }
    
    var set_txtvalue=function(sElementId,sValue)
    {
        var oElement = document.getElementById(sElementId);
        if(oElement!=null)oElement.value=sValue;
    }
    
    var in_array = function(arArray,sSearch)
    {
        if(arArray.length>0)
            for(var i=0; i<arArray.length; i++)
                if(arArray[i]==sSearch)
                    return true;
        return false;
    }
    
    //First occurence string position
    var fosubstr_pos = function(sString,sSearch)
    {
        return sString.indexOf(sSearch);
    }
    
    var show_rows = function(arRowIds)
    {
        for(var i=0; i<arRowIds.length; i++)
        {
            show_row(arRowIds[i]);
        }
    }
    
    var show_all_rows = function()
    {
        for(var i in arRowsIds)
        {
            show_row(arRowsIds[i]);
        }
    }
    
    var hide_all_rows = function()
    {
        for(var i in arRowsIds)
        {
            if(arRowsIds[i]!="trprod_head")
                hide_row(arRowsIds[i]);
        }
    }
    
    var hide_row = function(sRowId)
    {
        var oRow = document.getElementById(sRowId);
        if(oRow!=null)
        {
            oRow.style.display = "none";
            //oRow.style.visibility ="hidden";
        }
    }
    
    var show_row = function(sRowId)
    {
         var oRow = document.getElementById(sRowId);
         if(oRow!=null)
         {
             oRow.style.display = "";
             //oRow.style.visibility="visible";
         }
    }
    
    var is_text_found = function(sText,sSearch)
    {
        //var sText = sText.toLowerCase()||"";
        var sSearch = sSearch.toLowerCase()||"";
        
        if(sText.indexOf(sSearch)!=-1)
            return true;
        else
            return false;
    }
    
    var get_text_from_textnodes = function(oElement)
    {
        //http://www.w3schools.com/dom/prop_element_nodetype.asp
        var arNodes = oElement.childNodes;
        var oTmpNode = null;
        var sText = "";
        for(var i=0; i<arNodes.length; i++)
        {
            oTmpNode = arNodes[i];
            //3:textNode
            //bug(oTmpNode);
            if(is_textnode(oTmpNode))
                sText += oUtils.trim(oTmpNode.textContent);
        }
        return sText;
    };
    
    var is_textnode=function(oNode)
    {
        if(oNode!=null)
            if(oNode.nodeType==3)
                return true;
        return false;
    }
    
    var is_elementnode=function(oNode)
    {
        if(oNode!=null)
            if(oNode.nodeType==1)
                return true;
        return false;
    }

    //Recupera el producto desde la fila
    var get_product_from_list = function(sRowId)
    {
        var arProduct = arTableProducts[sRowId];
        //bug(arProduct,"desde tabla");
        arProduct.Order_Num = get_txtvalue("Order_Num");
        arProduct.Order_Type = get_txtvalue("Order_Type");
        arProduct.Code_Account = get_txtvalue("CodeAccount")
        //bug(arProduct,"despues de tabla");
        return arProduct;
        //bug(arTableProducts[sRowId],"table products","algo como trprod_0");
    }

    var get_form_normal = function(arProducto,isEditing)
    {
        var isEditing = isEditing || false;
        var sHtmlForm = "";
        //bug(arProducto);//Code, Code_Family, Description, Max_Tariff, Min_tariff, Unidades_Bulto
        var codOrderNum = arProducto["Order_Num"].toUpperCase() || "Num nulo";
        var sOrderType = arProducto["Order_Type"].toUpperCase() || "Tipo nulo";
        var codAccount = arProducto["Code_Account"].toUpperCase() || "Acc nulo";
        var codProduct = arProducto["Code"].toUpperCase() || "Prod nulo";//tabla
        var sProductDesc = arProducto["Description"].toUpperCase() || "Desc nulo"; //tabla
        var iUnidadesBulto = parseFloat(arProducto["Unidades_Bulto"]) || 0.00; //tabla
        var fMaxTariff = parseFloat(arProducto["Max_Tariff"]); //tabla
        var iBonficadas = parseInt(arProducto["Bonificadas"])|| 0;//get_bonificadas(codProduct,iUnidadesBulto);    
        var fDto = parseInt(arProducto["Dto"])||0.00; //get_product_discount(codProduct,iUnidadesBulto);

        var fNeto = (iUnidadesBulto * fMaxTariff)*(1-(fDto/100));
        fNeto = Math.round(fNeto*100)/100;
        if(isEditing)
        {
            var iUnidades = parseInt(arProducto["Unidades"])||0;
            fNeto = parseFloat(arProducto["Neto"])||0.00;
            var codLineNum = arProducto["Line_num"];
        }   

        //fMinTariff = arProducto["Min_Tariff"];
        sHtmlForm = "<!-- js_order_lines_product normal-->\n<br/>";
        //sHtmlForm += "<form id=\"frmLineaProducto\" name=\"frmLineaProducto\" action=\"\" method=\"post\" class=\"form-horizontal\" style=\"width:100%;margin:0;padding:0;\">"; 
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\"for=\"txtCodeProduct\">Codigo</label><div class=\"controls\">"; 
        if(isEditing)
            sHtmlForm += "<input type=\"hidden\" id=\"hidLineNum\" name=\"hidLinenum\" value=\""+codLineNum+"\" save=\"line\">";       
	sHtmlForm += "<input type=\"text\" id=\"txtCodeProduct\" name=\"txtCodeProduct\" value=\""+codProduct+"\" class=\"input-xxlarge\" readonly save=\"line\">";
	sHtmlForm += "<input type=\"hidden\" id=\"hidCodeAccount\" name=\"hidCodeAccount\" value=\""+codAccount+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidCodeProduct\" name=\"hidCodeProduct\" value=\""+codProduct+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Type\" name=\"hidOrder_Type\" value=\""+sOrderType+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Num\" name=\"hidOrder_Num\" value=\""+codOrderNum+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidMaxtariff\" name=\"hidMaxtariff\" value=\""+fMaxTariff+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidUnidadesBulto\" name=\"hidUnidadesBulto\" value=\""+iUnidadesBulto+"\" save=\"line\"></div></div>"; 
	sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtProducto\">Producto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtProducto\" name=\"txtProducto\" value=\""+sProductDesc+"\" class=\"input-xxlarge\" readonly save=\"line\"></div></div>"; 
        //Lote y PVPr:
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidadesBulto\">Lote.</label><div class=\"controls\">";
        sHtmlForm += "<input type=\"number\" id=\"txtUnidadesBulto\" name=\"txtUnidadesBulto\" value=\""+iUnidadesBulto+"\" class=\"input-small\" readonly save=\"line\">"; 
        sHtmlForm += "&nbsp;PVPr&nbsp;<input type=\"number\" id=\"txtMaxtariff\" name=\"txtMaxtariff\" value=\""+fMaxTariff+"\" class=\"input-small\" readonly save=\"line\"></div></div>";
	//Unid y Bonif
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidades\">Unid.</label><div class=\"controls\">";
        //sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidadesBulto+"\" class=\"input-small\" save=\"line\">";
        if(isEditing)
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidades+"\" class=\"input-small\" save=\"line\">";
        else //Agregando
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\"\" class=\"input-small\" save=\"line\">";        //sHtmlForm += "&nbsp;Bonif&nbsp;<input type=\"number\" id=\"txtBonificacion\" name=\"txtBonificacion\" onfocus=\"\" value=\""+iBonficadas+"\" class=\"input-small\" save=\"line\"></div></div>";
        sHtmlForm += "</div></div>";
        //DTO
        sHtmlForm += "<div class=\"control-group input-append\"><table><tr><td><label class=\"control-label\" for=\"txtDescuento\">Dto.</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtDescuento\" name=\"txtDescuento\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+fDto+"\" class=\"input-small\" save=\"line\"><span class=\"add-on\">%</span>"; 
        //BONIF
        sHtmlForm += "</td><td><label class=\"control-label\" style=\"width:60px;\" for=\"txtBonificacion\">Bonif&nbsp;</label><input type=\"number\" id=\"txtBonificacion\" name=\"txtBonificacion\" onfocus=\"\" value=\""+iBonficadas+"\" class=\"input-small\" save=\"line\">";
        sHtmlForm += "</td></tr></table></div></div>";
        //NETO
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtNeto\">Neto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"number\" id=\"txtNeto\" name=\"txtNeto\" value=\""+fNeto+"\" class=\"input-small\" readonly save=\"line\">"; 
        sHtmlForm += "</div></div>"; 
        sHtmlForm += "<div class=\"form-actions\" style=\"margin-bottom:0; border-bottom-left-radius:6px; border-bottom-right-radius:6px;\">";
        if(isEditing)
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save_update();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        else
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oGenericModal.close();\">&nbsp;Cancelar&nbsp;</button></div>";
        //sHtmlForm += "</form>\n";
        sHtmlForm += "<!--/js_order_lines_product -->";
        return sHtmlForm;
    }
    
    var get_form_transfer = function(arProducto,isEditing)
    {
        //alert("transfer");
        //bug(arProducto);
        var isEditing = isEditing || false;
        var sHtmlForm = "";
        //bug(arProducto);//Code, Code_Family, Description, Max_Tariff, Min_tariff, Unidades_Bulto
        var codOrderNum = arProducto["Order_Num"].toUpperCase() || "Num nulo";
        var sOrderType = arProducto["Order_Type"].toUpperCase() || "Tipo nulo";
        var codAccount = arProducto["Code_Account"].toUpperCase() || "Acc nulo";
        var codProduct = arProducto["Code"].toUpperCase() || "Prod nulo";//tabla
        var sProductDesc = arProducto["Description"].toUpperCase() || "Desc nulo"; //tabla
        var iUnidadesBulto = parseFloat(arProducto["Unidades_Bulto"]) || 0.00; //tabla
        var fMaxTariff = parseFloat(arProducto["Max_Tariff"]); //tabla
        var iBonficadas = parseInt(arProducto["Bonificadas"])|| 0;//get_bonificadas(codProduct,iUnidadesBulto);    
        var fDto = parseInt(arProducto["Dto"])||0.00; //get_product_discount(codProduct,iUnidadesBulto);

        var fNeto = (iUnidadesBulto * fMaxTariff)*(1-(fDto/100));
        fNeto = Math.round(fNeto*100)/100;
        if(isEditing)
        {
            var iUnidades = parseInt(arProducto["Unidades"])||0;
            fNeto = parseFloat(arProducto["Neto"])||0.00;
            var codLineNum = arProducto["Line_num"];
        }        

        sHtmlForm = "<!-- js_order_lines_product transfer -->\n<br/>";
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\"for=\"txtCodeProduct\">Codigo</label><div class=\"controls\">"; 
        if(isEditing)
            sHtmlForm += "<input type=\"hidden\" id=\"hidLineNum\" name=\"hidLinenum\" value=\""+codLineNum+"\" save=\"line\">";         
	sHtmlForm += "<input type=\"text\" id=\"txtCodeProduct\" name=\"txtCodeProduct\" value=\""+codProduct+"\" class=\"input-xxlarge\" readonly save=\"line\">";
	sHtmlForm += "<input type=\"hidden\" id=\"hidCodeAccount\" name=\"hidCodeAccount\" value=\""+codAccount+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidCodeProduct\" name=\"hidCodeProduct\" value=\""+codProduct+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Type\" name=\"hidOrder_Type\" value=\""+sOrderType+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Num\" name=\"hidOrder_Num\" value=\""+codOrderNum+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidMaxtariff\" name=\"hidMaxtariff\" value=\""+fMaxTariff+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidUnidadesBulto\" name=\"hidUnidadesBulto\" value=\""+iUnidadesBulto+"\" save=\"line\"></div></div>"; 
	sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtProducto\">Producto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtProducto\" name=\"txtProducto\" value=\""+sProductDesc+"\" class=\"input-xxlarge\" readonly save=\"line\"></div></div>"; 
        //Lote y PVPr:
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidadesBulto\">Lote.</label><div class=\"controls\">";
        sHtmlForm += "<input type=\"number\" id=\"txtUnidadesBulto\" name=\"txtUnidadesBulto\" value=\""+iUnidadesBulto+"\" class=\"input-small\" readonly save=\"line\">"; 
        sHtmlForm += "&nbsp;PVPr&nbsp;<input type=\"number\" id=\"txtMaxtariff\" name=\"txtMaxtariff\" value=\""+fMaxTariff+"\" class=\"input-small\" readonly save=\"line\"></div></div>";
	//Unid y Bonif
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidades\">Unid.</label><div class=\"controls\">";
        //sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidadesBulto+"\" class=\"input-small\" save=\"line\">";
        if(isEditing)
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidades+"\" class=\"input-small\" save=\"line\">";
        else //Agregando
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\"\" class=\"input-small\" save=\"line\">";
        //sHtmlForm += "&nbsp;Bonif&nbsp;<input type=\"number\" id=\"txtBonificacion\" name=\"txtBonificacion\" onfocus=\"\" value=\""+iBonficadas+"\" class=\"input-small\" save=\"line\"></div></div>";
        sHtmlForm += "</div></div>";
        //DTO
        sHtmlForm += "<div class=\"control-group input-append\"><table><tr><td><label class=\"control-label\" for=\"txtDescuento\">Dto.</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtDescuento\" name=\"txtDescuento\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+fDto+"\" class=\"input-small\" save=\"line\"><span class=\"add-on\">%</span>"; 
        //BONIF
        sHtmlForm += "</td><td><label class=\"control-label\" style=\"width:60px;\" for=\"txtBonificacion\">Bonif&nbsp;</label><input type=\"number\" id=\"txtBonificacion\" name=\"txtBonificacion\" onfocus=\"\" value=\""+iBonficadas+"\" class=\"input-small\" save=\"line\">";
        sHtmlForm += "</td></tr></table></div></div>";
        //NETO
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtNeto\">Neto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"number\" id=\"txtNeto\" name=\"txtNeto\" value=\""+fNeto+"\" class=\"input-small\" readonly save=\"line\"></div></div><div class=\"form-actions\" style=\"margin-bottom:0; border-bottom-left-radius:6px; border-bottom-right-radius:6px;\">";
        if(isEditing)
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save_update();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        else
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oGenericModal.close();\">&nbsp;Cancelar&nbsp;</button></div>";
        sHtmlForm += "<!--/js_order_lines_product -->";
        return sHtmlForm;
    }
    
    var get_form_grabados = function(arProducto, isEditing)
    {
        var isEditing = isEditing || false;
        var sHtmlForm = "";
        //bug(arProducto);//Code, Code_Family, Description, Max_Tariff, Min_tariff, Unidades_Bulto
        var codOrderNum = arProducto["Order_Num"].toUpperCase() || "Num nulo";
        var sOrderType = arProducto["Order_Type"].toUpperCase() || "Tipo nulo";
        var codAccount = arProducto["Code_Account"].toUpperCase() || "Acc nulo";
        var codProduct = arProducto["Code"].toUpperCase() || "Prod nulo";//tabla
        var sProductDesc = arProducto["Description"].toUpperCase() || "Desc nulo"; //tabla
        var iUnidadesBulto = parseFloat(arProducto["Unidades_Bulto"]) || 0.00; //tabla
        var fMaxTariff = parseFloat(arProducto["Max_Tariff"]); //tabla
        //var iBonficadas = parseInt(arProducto["Bonificadas"])|| 0;//get_bonificadas(codProduct,iUnidadesBulto);    
        var fDto = parseInt(arProducto["Dto"])||0.00; //get_product_discount(codProduct,iUnidadesBulto);

        var fNeto = (iUnidadesBulto * fMaxTariff)*(1-(fDto/100));
        fNeto = Math.round(fNeto*100)/100;
        if(isEditing)
        {
            var iUnidades = parseInt(arProducto["Unidades"])||0;
            fNeto = parseFloat(arProducto["Neto"])||0.00;
            var codLineNum = arProducto["Line_num"];
        }  
        
        sHtmlForm = "<!-- js_order_lines_product grabados -->\n<br/>";
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\"for=\"txtCodeProduct\">Codigo</label><div class=\"controls\">"; 
        if(isEditing)
            sHtmlForm += "<input type=\"hidden\" id=\"hidLineNum\" name=\"hidLinenum\" value=\""+codLineNum+"\" save=\"line\">";         
	sHtmlForm += "<input type=\"text\" id=\"txtCodeProduct\" name=\"txtCodeProduct\" value=\""+codProduct+"\" class=\"input-xxlarge\" readonly save=\"line\">";
	sHtmlForm += "<input type=\"hidden\" id=\"hidCodeAccount\" name=\"hidCodeAccount\" value=\""+codAccount+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidCodeProduct\" name=\"hidCodeProduct\" value=\""+codProduct+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Type\" name=\"hidOrder_Type\" value=\""+sOrderType+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Num\" name=\"hidOrder_Num\" value=\""+codOrderNum+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidMaxtariff\" name=\"hidMaxtariff\" value=\""+fMaxTariff+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidUnidadesBulto\" name=\"hidUnidadesBulto\" value=\""+iUnidadesBulto+"\" save=\"line\"></div></div>"; 
	sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtProducto\">Producto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtProducto\" name=\"txtProducto\" value=\""+sProductDesc+"\" class=\"input-xxlarge\" readonly save=\"line\"></div></div>"; 
        //Lote y PVPr:
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidadesBulto\">Lote.</label><div class=\"controls\">";
        sHtmlForm += "<input type=\"number\" id=\"txtUnidadesBulto\" name=\"txtUnidadesBulto\" value=\""+iUnidadesBulto+"\" class=\"input-small\" readonly save=\"line\">"; 
        sHtmlForm += "&nbsp;PVPr&nbsp;<input type=\"number\" id=\"txtMaxtariff\" name=\"txtMaxtariff\" value=\""+fMaxTariff+"\" class=\"input-small\" readonly save=\"line\"></div></div>";
	//Unid
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidades\">Unid.</label><div class=\"controls\">";
        //sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidadesBulto+"\" class=\"input-small\" save=\"line\">";
        if(isEditing)
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidades+"\" class=\"input-small\" save=\"line\">";
        else //Agregando
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\"\" class=\"input-small\" save=\"line\">";
        sHtmlForm += "</div></div>";
        
        sHtmlForm += "<div class=\"control-group input-append\"><label class=\"control-label\" for=\"txtDescuento\">Dto.</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtDescuento\" name=\"txtDescuento\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+fDto+"\" class=\"input-small\" save=\"line\"><span class=\"add-on\">%</span></div></div><div class=\"control-group\"><label class=\"control-label\" for=\"txtNeto\">Neto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"number\" id=\"txtNeto\" name=\"txtNeto\" value=\""+fNeto+"\" class=\"input-small\" readonly save=\"line\"></div></div><div class=\"form-actions\" style=\"margin-bottom:0; border-bottom-left-radius:6px; border-bottom-right-radius:6px;\">";
        if(isEditing)
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save_update();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        else
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oGenericModal.close();\">&nbsp;Cancelar&nbsp;</button></div>";
        sHtmlForm += "<!--/js_order_lines_product -->";
        return sHtmlForm;
    }
 
    var get_form_cambio = function(arProducto,isEditing)
    {
        var isEditing = isEditing || false;
        var sHtmlForm = "";
        //bug(arProducto);//Code, Code_Family, Description, Max_Tariff, Min_tariff, Unidades_Bulto
        var codOrderNum = arProducto["Order_Num"].toUpperCase() || "Num nulo";
        var sOrderType = arProducto["Order_Type"].toUpperCase() || "Tipo nulo";
        var codAccount = arProducto["Code_Account"].toUpperCase() || "Acc nulo";
        var codProduct = arProducto["Code"].toUpperCase() || "Prod nulo";//tabla
        var sProductDesc = arProducto["Description"].toUpperCase() || "Desc nulo"; //tabla
        var iUnidadesBulto = parseFloat(arProducto["Unidades_Bulto"]) || 0.00; //tabla
        var fMaxTariff = parseFloat(arProducto["Max_Tariff"]); //tabla
        //var iBonficadas = parseInt(arProducto["Bonificadas"])|| 0;//get_bonificadas(codProduct,iUnidadesBulto);    
        var fDto = parseInt(arProducto["Dto"])||0.00; //get_product_discount(codProduct,iUnidadesBulto);

        var fNeto = (iUnidadesBulto * fMaxTariff)*(1-(fDto/100));
        fNeto = Math.round(fNeto*100)/100;
        if(isEditing)
        {
            var iUnidades = parseInt(arProducto["Unidades"])||0;
            fNeto = parseFloat(arProducto["Neto"])||0.00;
            var codLineNum = arProducto["Line_num"];
        }  

        sHtmlForm = "<!-- js_order_lines_product cambio -->\n<br/>";
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\"for=\"txtCodeProduct\">Codigo</label><div class=\"controls\">"; 
        if(isEditing)
            sHtmlForm += "<input type=\"hidden\" id=\"hidLineNum\" name=\"hidLinenum\" value=\""+codLineNum+"\" save=\"line\">";         
	sHtmlForm += "<input type=\"text\" id=\"txtCodeProduct\" name=\"txtCodeProduct\" value=\""+codProduct+"\" class=\"input-xxlarge\" readonly save=\"line\">";
	sHtmlForm += "<input type=\"hidden\" id=\"hidCodeAccount\" name=\"hidCodeAccount\" value=\""+codAccount+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidCodeProduct\" name=\"hidCodeProduct\" value=\""+codProduct+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Type\" name=\"hidOrder_Type\" value=\""+sOrderType+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Num\" name=\"hidOrder_Num\" value=\""+codOrderNum+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidMaxtariff\" name=\"hidMaxtariff\" value=\""+fMaxTariff+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidUnidadesBulto\" name=\"hidUnidadesBulto\" value=\""+iUnidadesBulto+"\" save=\"line\"></div></div>"; 
	sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtProducto\">Producto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtProducto\" name=\"txtProducto\" value=\""+sProductDesc+"\" class=\"input-xxlarge\" readonly save=\"line\"></div></div>"; 
        //Lote y PVPr:
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidadesBulto\">Lote.</label><div class=\"controls\">";
        sHtmlForm += "<input type=\"number\" id=\"txtUnidadesBulto\" name=\"txtUnidadesBulto\" value=\""+iUnidadesBulto+"\" class=\"input-small\" readonly save=\"line\">"; 
        sHtmlForm += "&nbsp;PVPr&nbsp;<input type=\"number\" id=\"txtMaxtariff\" name=\"txtMaxtariff\" value=\""+fMaxTariff+"\" class=\"input-small\" readonly save=\"line\"></div></div>";

        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidades\">Unid.</label><div class=\"controls\">";
        //Unid
        //sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidadesBulto+"\" class=\"input-small\" save=\"line\">";
        if(isEditing)
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidades+"\" class=\"input-small\" save=\"line\">";
        else //Agregando
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\"\" class=\"input-small\" save=\"line\">";        
        sHtmlForm += "</div></div>";
	
        //Combo entregado o recogido
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"selTipoCambio\">Tipo</label><div class=\"controls\">";
        sHtmlForm += "<select id=\"selTipoCambio\" name=\"selTipoCambio\" save=\"line\"><option value=\"\"></option><option value=\"entregado\">Entregado</option><option value=\"recogido\">Recogido</option></select>";
        sHtmlForm += "</div></div>";
        
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtNeto\">Neto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"number\" id=\"txtNeto\" name=\"txtNeto\" value=\""+fNeto+"\" class=\"input-small\" readonly save=\"line\"></div></div><div class=\"form-actions\" style=\"margin-bottom:0; border-bottom-left-radius:6px; border-bottom-right-radius:6px;\">";
        if(isEditing)
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save_update();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        else
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        
        sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oGenericModal.close();\">&nbsp;Cancelar&nbsp;</button></div>";
        sHtmlForm += "<!--/js_order_lines_product -->";
        return sHtmlForm;
    }

    var get_form_entrega = function(arProducto,isEditing)
    {
        var isEditing = isEditing || false;
        var sHtmlForm = "";
        //bug(arProducto);//Code, Code_Family, Description, Max_Tariff, Min_tariff, Unidades_Bulto
        var codOrderNum = arProducto["Order_Num"].toUpperCase() || "Num nulo";
        var sOrderType = arProducto["Order_Type"].toUpperCase() || "Tipo nulo";
        var codAccount = arProducto["Code_Account"].toUpperCase() || "Acc nulo";
        var codProduct = arProducto["Code"].toUpperCase() || "Prod nulo";//tabla
        var sProductDesc = arProducto["Description"].toUpperCase() || "Desc nulo"; //tabla
        var iUnidadesBulto = parseFloat(arProducto["Unidades_Bulto"]) || 0.00; //tabla
        var fMaxTariff = parseFloat(arProducto["Max_Tariff"]); //tabla
        var iBonficadas = parseInt(arProducto["Bonificadas"])|| 0;//get_bonificadas(codProduct,iUnidadesBulto);    
        var fDto = parseInt(arProducto["Dto"])||0.00; //get_product_discount(codProduct,iUnidadesBulto);

        var fNeto = (iUnidadesBulto * fMaxTariff)*(1-(fDto/100));
        fNeto = Math.round(fNeto*100)/100;
        if(isEditing)
        {
            var iUnidades = parseInt(arProducto["Unidades"])||0;
            fNeto = parseFloat(arProducto["Neto"])||0.00;
            var codLineNum = arProducto["Line_num"];
        }  

        sHtmlForm = "<!-- js_order_lines_product entrega -->\n<br/>";
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\"for=\"txtCodeProduct\">Codigo</label><div class=\"controls\">"; 
        if(isEditing)
            sHtmlForm += "<input type=\"hidden\" id=\"hidLineNum\" name=\"hidLinenum\" value=\""+codLineNum+"\" save=\"line\">";         
	sHtmlForm += "<input type=\"text\" id=\"txtCodeProduct\" name=\"txtCodeProduct\" value=\""+codProduct+"\" class=\"input-xxlarge\" readonly save=\"line\">";
	sHtmlForm += "<input type=\"hidden\" id=\"hidCodeAccount\" name=\"hidCodeAccount\" value=\""+codAccount+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidCodeProduct\" name=\"hidCodeProduct\" value=\""+codProduct+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Type\" name=\"hidOrder_Type\" value=\""+sOrderType+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidOrder_Num\" name=\"hidOrder_Num\" value=\""+codOrderNum+"\" save=\"line\">";
        sHtmlForm += "<input type=\"hidden\" id=\"hidMaxtariff\" name=\"hidMaxtariff\" value=\""+fMaxTariff+"\" save=\"line\">"; 
        sHtmlForm += "<input type=\"hidden\" id=\"hidUnidadesBulto\" name=\"hidUnidadesBulto\" value=\""+iUnidadesBulto+"\" save=\"line\"></div></div>"; 
	sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtProducto\">Producto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtProducto\" name=\"txtProducto\" value=\""+sProductDesc+"\" class=\"input-xxlarge\" readonly save=\"line\"></div></div>"; 
        //Lote y PVPr:
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidadesBulto\">Lote.</label><div class=\"controls\">";
        sHtmlForm += "<input type=\"number\" id=\"txtUnidadesBulto\" name=\"txtUnidadesBulto\" value=\""+iUnidadesBulto+"\" class=\"input-small\" readonly save=\"line\">"; 
        sHtmlForm += "&nbsp;PVPr&nbsp;<input type=\"number\" id=\"txtMaxtariff\" name=\"txtMaxtariff\" value=\""+fMaxTariff+"\" class=\"input-small\" readonly save=\"line\"></div></div>";
	//Unid y Bonif
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtUnidades\">Unid.</label><div class=\"controls\">";
        //sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidadesBulto+"\" class=\"input-small\" save=\"line\">";
        if(isEditing)
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+iUnidades+"\" class=\"input-small\" save=\"line\">";
        else //Agregando
            sHtmlForm += "<input type=\"number\" id=\"txtUnidades\" name=\"txtUnidades\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\"\" class=\"input-small\" save=\"line\">";
        //sHtmlForm += "&nbsp;Bonif&nbsp;<input type=\"number\" id=\"txtBonificacion\" name=\"txtBonificacion\" onfocus=\"\" value=\""+iBonficadas+"\" class=\"input-small\" save=\"line\"></div></div>";
        sHtmlForm += "</div></div>";
        //DTO
        sHtmlForm += "<div class=\"control-group input-append\"><table><tr><td><label class=\"control-label\" for=\"txtDescuento\">Dto.</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"text\" id=\"txtDescuento\" name=\"txtDescuento\" onfocus=\"\" onkeyup=\"oProduct.update_neto();\" value=\""+fDto+"\" class=\"input-small\" save=\"line\"><span class=\"add-on\">%</span>"; 
        //BONIF
        sHtmlForm += "</td><td><label class=\"control-label\" style=\"width:60px;\" for=\"txtBonificacion\">Bonif&nbsp;</label><input type=\"number\" id=\"txtBonificacion\" name=\"txtBonificacion\" onfocus=\"\" value=\""+iBonficadas+"\" class=\"input-small\" save=\"line\">";
        sHtmlForm += "</td></tr></table></div></div>";
        //NETO
        sHtmlForm += "<div class=\"control-group\"><label class=\"control-label\" for=\"txtNeto\">Neto</label><div class=\"controls\">"; 
	sHtmlForm += "<input type=\"number\" id=\"txtNeto\" name=\"txtNeto\" value=\""+fNeto+"\" class=\"input-small\" readonly save=\"line\"></div></div><div class=\"form-actions\" style=\"margin-bottom:0; border-bottom-left-radius:6px; border-bottom-right-radius:6px;\">";
        if(isEditing)
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save_update();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        else
            sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oProduct.save();\">&nbsp;Guardar&nbsp;</button>&nbsp;&nbsp;&nbsp;&nbsp;";
        sHtmlForm += "<button type=\"button\" class=\"btn btn-primary\" onclick=\"oGenericModal.close();\">&nbsp;Cancelar&nbsp;</button></div>";
        sHtmlForm += "<!--/js_order_lines_product -->";
        return sHtmlForm;
    }
    
    var load_addform = function(sRowId)
    {
        var arProducto = new Object();
        arProducto = get_product_from_list(sRowId);
        var codOrderType = document.getElementById("Order_Type").value;
        
        var sHtmlForm = "";
        switch(codOrderType)
        {
            //Normal
            case "V":
                sHtmlForm = get_form_normal(arProducto);
            break;
            //Transfer
            case "T":
                sHtmlForm = get_form_transfer(arProducto);
            break;
            //Grabados
            case "G":
                sHtmlForm = get_form_grabados(arProducto);
            break;
            //Cambio
            case "C":
                sHtmlForm = get_form_cambio(arProducto);
            break;
            //Enetrega
            case "E":
                sHtmlForm = get_form_entrega(arProducto);
            break;

            default:
                //Tipo de pedido erroneo
                sHtmlForm = "Error: Tipo pedido no encontrado";
            break;
        }
        //sHtmlForm
        oGenericModal.add_html_to_divcontent(sHtmlForm);
        //carga todos los divs. La funcion data_received incluye el formulario en el div contenedor
        oGenericModal.show();
    }
    
    var load_editform = function(arProducto)
    {
        var codOrderType = document.getElementById("Order_Type").value;
        var sHtmlForm = "";
        //get_form_normal(arProducto,true); 
        //el segundo parametro indica que se usuar� el form para modificar
        switch(codOrderType)
        {
            //Normal
            case "V":
                sHtmlForm = get_form_normal(arProducto,true);
            break;
            //Transfer
            case "T":
                sHtmlForm = get_form_transfer(arProducto,true);
            break;
            //Grabados
            case "G":
                sHtmlForm = get_form_grabados(arProducto,true);
            break;
            //Cambio
            case "C":
                sHtmlForm = get_form_cambio(arProducto,true);
            break;
            //Enetrega
            case "E":
                sHtmlForm = get_form_entrega(arProducto,true);
            break;

            default:
                //Tipo de pedido erroneo
                sHtmlForm = "Error: Tipo pedido no encontrado";
            break;
        }
        
        //bug(arProducto);
        //sHtmlForm
        oGenericModal.add_html_to_divcontent(sHtmlForm);
        oGenericModal.show();
        
        if(codOrderType=="C")
        {
            var sTipoCambio = arProducto["Code_Returncause"].toLowerCase();
            var arOptions = document.getElementById("selTipoCambio").options;
            for (var i=0; i<arOptions.length; i++)
            {
                if(arOptions[i].value==sTipoCambio)
                    document.getElementById("selTipoCambio").options[i].selected=true;
                else
                    document.getElementById("selTipoCambio").options[i].selected=false;
            }
        }            
    }
    
    //==============================
    // METODOS PUBLICOS
    //==============================
    this.detail_modal = function(sRowId)
    {
        //bug(sRowId,"rowId");
        //var codProduct="";
        //incluye los nodos de texto \n 
        var arTd = document.getElementById(sRowId).childNodes;
        var sAuxString="";
        var sTdIdCode="";
        //bug(arTd);
        for(var i=0; i<arTd.length; i++)
        {
            if(is_elementnode(arTd[i]))
            {
                //solo si es nodo tipo element tiene el metodo getAttribute
                sAuxString = arTd[i].getAttribute("fieldname");
                //Celda con c�digo de producto
                if(sAuxString=="Code") 
                    sTdIdCode = arTd[i].id;
            }
        }

        if(sTdIdCode!="")
        {
            var iPos = fosubstr_pos(sTdIdCode,"_");
            //guarda las "coordenadas" de la celda en la tabla ejem: _2_1 Fila 2, columna 1
            var sPosition = sTdIdCode.substr(iPos);
            //bug(sPosition);
            //id campo hidden producto en grid. Ejemplo: hid_Code_3_1
            sAuxString = "hid_Code"+sPosition;
            //Campo que se enviar� por ajax. Tiene el atributo sendby="ajax"
            var oTxtCodeProduct = document.getElementById("CodeProduct");
            oTxtCodeProduct.value= document.getElementById(sAuxString).value;
            
            load_addform(sRowId);
            //this.send_request();
        }
    }//fin detail_modal_add
    
    this.detail_modal_edit = function(sRowNumber)
    {
        //alert(sRowId);
        var arProducto = new Object();
        var arTd = document.getElementById("td_r"+sRowNumber+"_c1").parentNode.cells;
        
        //arTd = document.queryString("")
        //bug(arTd);
        if(arTd.length>0)
        {
            arProducto.Order_Num = get_txtvalue("Order_Num");
            arProducto.Order_Type = get_txtvalue("Order_Type");
            arProducto.Code_Account = get_txtvalue("CodeAccount");
            var sFieldName = "";
            
            for(var i=0; i<arTd.length; i++)
            {
                sFieldName = arTd[i].getAttribute("fieldname");
                //Compruebo el fieldname por casos para crear un objeto producto parecido al
                //utilizando en el alta.
                switch(sFieldName)
                {
                    case "Code_Product":
                        arProducto["Description"] = get_text_from_textnodes(arTd[i]);
                        var sIdHidden = arTd[i].id;
                        //bug(sIdHidden,"idHidden");
                        sIdHidden = sIdHidden.replace("td",sFieldName);
                        arProducto["Code"] = document.getElementById(sIdHidden).value;
                    break;
                    case "Price":
                        arProducto["Max_Tariff"] = get_text_from_textnodes(arTd[i]);
                    break;
                    case "Dis1":
                        arProducto["Bonificadas"] = get_text_from_textnodes(arTd[i]);
                    break;
                    case "Dis3":
                        arProducto["Dto"] = get_text_from_textnodes(arTd[i]);
                    break;
                    case "Quantity":
                        arProducto["Unidades"] = get_text_from_textnodes(arTd[i]);
                    break;
                    case "Net_Amount":
                        arProducto["Neto"] = get_text_from_textnodes(arTd[i]);
                    break;
                    default:
                       arProducto[sFieldName] = get_text_from_textnodes(arTd[i]); 
                }
            }
            
            load_editform(arProducto);
        }
         
    }//fin detail_modal_edit
    
    this.busqueda_general=function()
    {
        var arColumnasBuscar = ["Code_Family","Code","Description"];
        var sValueToSearch = txtBusquedaGeneral.value;

        if(sValueToSearch!="")
        {
            arRowsFound=[];
            if(iNumRows-1>0)
            {
                var oRow = null;
                for(var sRowId in arTableProducts)
                {
                    if(sRowId!="trprod_head")
                    {
                        oRow = arTableProducts[sRowId];
                        for(var sFieldName in oRow)
                        {
                            var sTextValue = "";
                            if(in_array(arColumnasBuscar,sFieldName))
                            {
                                sTextValue = oRow[sFieldName];
                                if(is_text_found(sTextValue,sValueToSearch))
                                {
                                    //bug(sRowId);
                                    arRowsFound.push(sRowId);
                                }
                            }
                        }//Recorre columnas
                    }//sRowId!=trprod_head
                }//Recorre filas
            }//Hay m�s de una fila  
            hide_all_rows();
            show_rows(arRowsFound);
        }
        //valuetosearch==""
        else
        {
            show_all_rows();
        }
    }
        
    this.busqueda_description = function()
    {
        var arColumnasBuscar = ["Description"];
        var sValueToSearch = txtBusquedaGeneral.value;
    }
    
    //Evita que se envie el formulario al presionar enter
    this.on_enterpress=function(event)
    {
        //bug(event);
        var ikeyNumber=-1;
        if(window.event)
            ikeyNumber = window.event.keyCode; //IE
        else
            ikeyNumber = event.which;     //firefox

        if(ikeyNumber == 13)
        {    
            this.busqueda_general();
            return false;
        }
        else
            return true;
    }
    
    //Hace la peticion del formulario con el producto
    this.send_request = function()
    {
        //oButlerAjax.sUrl="./ajax/ajx_order_lines_product.php";
        var sFormId="#frmMainTss [sendby=\"ajax\"]";
        var sSerialize = jQuery(sFormId).serialize();
        oButlerAjax.sUrl="./ajax/orderline_getproduct.php";
        oButlerAjax.mxData = sSerialize; //producto
        oButlerAjax.on_success = data_received; //la funcion que controlara la recepcion de los datos
        //oButlerAjax.is_asincrono = true;
        oButlerAjax.send_data();//ejecuta el envio al servidor
        oGenericModal.show();//carga todos los divs. La funcion data_received incluye el formulario en el div contenedor
    }
    
    this.busqueda_code = function(){}

    this.busqueda_family = function(){}
    
    this.save = function()
    {
        var sError = check_errors_before_save();
        //bug(sError,"error");
        if(sError!="noerrors")
            alert(sError);
        else
        {
            var sSerialize = jQuery("[save=line]").serialize();
            //bug(sSerialize);
            oButlerAjax.sUrl="./ajax/orderline_saveproduct.php";
            oButlerAjax.mxData = sSerialize; //producto
            oButlerAjax.on_success = after_datasaved; //la funcion que controlara la reepcion de los datos
            //oButlerAjax.is_asincrono = true;
            oButlerAjax.send_data();//ejecuta el envio al servidor
            //oGenericModal.show();
        }
    }
    
    this.save_update = function()
    {
        var sError = check_errors_before_save();
        //bug(sError,"error");
        if(sError!="noerrors")
            alert(sError);
        else
        {
            var sSerialize = jQuery("[save=line]").serialize();
            //bug(sSerialize);
            oButlerAjax.sUrl="./ajax/orderline_updateproduct.php";
            oButlerAjax.mxData = sSerialize; //producto
            oButlerAjax.on_success = after_dataupdated; //la funcion que controlara la reepcion de los datos
            //oButlerAjax.is_asincrono = true;
            oButlerAjax.send_data();//ejecuta el envio al servidor
            //oGenericModal.show();
        }
    }
    
    this.update = function()
    {
        var sError = check_errors_before_save();
        //bug(sError,"error");
        if(sError!="noerrors")
            alert(sError);
        else
        {
            var sSerialize = jQuery("[save=line]").serialize();
            //bug(sSerialize);
            oButlerAjax.sUrl="./ajax/orderline_updateproduct.php";
            oButlerAjax.mxData = sSerialize; //producto
            oButlerAjax.on_success = after_datasaved; //la funcion que controlara la reepcion de los datos
            //oButlerAjax.is_asincrono = true;
            oButlerAjax.send_data();//ejecuta el envio al servidor
            //oGenericModal.show();
        }
    }
      
    this.update_neto = function()
    {
        var arFieldNames = ["txtUnidades","txtDescuento","hidMaxtariff","txtNeto"];
        // $fNeto = ($iUnidadesBulto * $fMaxTariff)*(1-($fDto/100));
        var fUnid = parseFloat(get_txtvalue(arFieldNames[0]));
        var fDto = parseFloat(get_txtvalue(arFieldNames[1]));
        var fMaxTariff = parseFloat(get_txtvalue(arFieldNames[2]));
        //bug(fDto,"dto");bug(fUnid,"unid");bug(fMaxTariff,"maxt");
        var fNeto = (fUnid*fMaxTariff)*(1-(fDto/100));
        fNeto = Math.round(fNeto*100)/100;
        //bug(fNeto);
        set_txtvalue(arFieldNames[3],fNeto);
    }
}

//Iniciacion del objeto producto
var oProduct = new Product();
