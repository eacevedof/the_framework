<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.3.3
 * @name HelperTableBasic
 * @date 01-11-2014 16:38 (SPAIN)
 * @file helper_table_basic.php
 * @requires
 */

import_helper("select,form,fieldset,input_hidden,checkbox,table");
class HelperTableBasic extends HelperTable
{
    //Campos a crear antes del listado
    protected $arObjFields;
    protected $arKeyFields;
    //Colmnas (Cabecera)
    protected $arColumns;    
    //Filas de datos
    protected $arDataRows;
    //protected $arKeysDelete;
    //protected $arKeysDetail;
    //protected $arKeysPick;
    protected $arOrderBy;
    protected $sIdForm;
    protected $sUrlDelete;
    protected $sUrlUpdate;
    protected $sUrlQuarantine;
    protected $sUrlPickSingle;
    protected $sUrlPickMultiple;
    protected $sUrlPaginate;
    protected $sModule;
    
    protected $arOrderWay;//asc|desc
    protected $isOrdenable;
    protected $isToDetail;//crea columna ir a detalle vista update
    
    protected $isPickMultiple; //crea columna con checks
    protected $isPickSingle; //de momento no tiene efectos
    protected $doMergePkeys;
    protected $isMergeKeyfields;
    protected $sMergeGlue;
    protected $arSingleAssign; //crea funcion js que permite lanzar popup con filas a seleccionar
    protected $arMultiAssign; // crea funcion js "
    protected $arMultiAdd; //funcion que ejecuta el post para realizar el proceso de asignacion
    protected $arSingleAdd; //funcion que ejecuta traspaso de valores por js a la ventana padre
    protected $arExtraColumns;
    protected $arHiddenColumns;
    protected $arExtraHidden;

    protected $arConfigColTypes;//los distintos formatos de las columnas. Fecha, hora4 hora6,fechahora4, fechahora6, decim
    protected $isDeleteSingle;//crea columna con boton eliminacion single
    protected $isQuarantineSingle;
    //arColumns=array(fieldname=>label);
    
    //INFO PAGINATE
    protected $iInfoNumRegs;
    protected $iInfoCurrentPage;
    protected $iInfoNumPages;
    protected $iInfoNextPage;
    protected $iInfoPreviousPage;
    protected $iInfoFirstPage;
    protected $iInfoLastPage;
    protected $iItemsPerPage;
    
    //rowcheckclick
    protected $isCheckOnRowclick;
    protected $isPaginateBar;
    
    protected $arTmpJs;
    
    public function __construct($arRows,$arColumns=array(),$sIdForm="frmList",$sModule="")
    {
        //table,innert,classes,extra,style, parent=HelperTable
        //Mientras que helper table trabaja con $arObjTr HelperTableBasic Lo hace solo con array de datos
        //con estos array de datos posteriormente reutiliza arobjtr
        parent::__construct();
        $this->isPaginateBar = TRUE;
        $this->lower_fieldnames($arRows);
        $this->arDataRows = $arRows;
        $this->iNumRows = count($arRows);
        $this->arColumns = $arColumns;
        $this->iNumCols = count($arColumns);
        $this->sIdForm = $sIdForm;
        $this->_idprefix = "tbl";
        $this->_id = $sModule;
        $this->sModule = $sModule;
        //crea las urls que se guardaran en campos hidden para quarantine, delete, update
        $this->load_get_urls();
        $this->sMergeGlue=",";
        //bug($this->isPermaLink,"ispermalink en contruct"); die;
    }

    public function get_html()
    {
        $this->useThead = true;
        $this->useTfoot =true;
        
        $sHtmlToReturn = "";
        $oFieldset = new HelperFieldset();
        $oForm = new HelperForm($this->sIdForm);
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin:0;padding:0;border:0;");
        
        $sHtmlToReturn .= $oForm->get_opentag();
        $sHtmlToReturn .= $oFieldset->get_opentag();
        //"FILTROS"
        //Los campos que se mostrarán antes del listado. Suele ser para filtrs
        $sHtmlToReturn .= $this->get_fields_as_string();
        $sHtmlToReturn .= $oFieldset->get_closetag();
        //Barra de navegacion por paginas
        if($this->isPaginateBar)
            $sHtmlToReturn .= $this->build_paginate_bar();
        //crea la etiqueta table
        $sHtmlToReturn .= $this->get_opentag();
        //Filas.  Se carga en la propiedad arObjTrs de tfw_helper se utiliza el metodo tr_as_string
        //para imprimir en formato cadena el array de objetos de tipo Tr
        $this->load_array_object_tr();
        $sHtmlToReturn .= $this->get_html_rows();
        //Fin Filas
        $sHtmlToReturn .= $this->get_closetag();
        $sHtmlToReturn .= $this->build_hidden_fields();
        $sHtmlToReturn .= $oForm->get_closetag();
        //Fin formulario
        $sHtmlToReturn .= $this->build_js();
        return $sHtmlToReturn;
    }   
    
    protected function get_fields_as_string()
    {
        $sHtmlString = "";
        foreach($this->arObjFields as $arObjField)
            $sHtmlString .= $arObjField->get_html();
        return $sHtmlString;
    }

    protected function build_paginate_bar()
    {
        //errorson();
        $arPages = array();
        for($i=1; $i<=$this->iInfoNumPages; $i++)
            $arPages[$i] = "pag $i";
        
        //bug($this->iInfoCurrentPage);
        $oSelPages = new HelperSelect($arPages,"selPage");
        $oSelPages->set_value_to_select($this->iInfoCurrentPage);
        //$oSelPages->add_class("span2");
        $oSelPages->add_style("margin:0;padding:0;width:85px;");
        $oSelPages->set_name("selPage");
        $oSelPages->set_js_onchange("table_frmsubmit();");
        
        $sHtmlSelect = $oSelPages->get_html();
        $sHtmlNavPages = 
        "
        <table id=\"tblNavPages\" style=\"width:100%; padding:0; margin-bottom:3px; margin-top:3px;\">
        <tr>
        <td style=\"background:#40444D; padding:0; color:white; border-radius: 4px 4px 4px 4px;\">
        <div class=\"pagination pagination-left\" style=\"padding:0;margin:0; margin-left:3px;\">";
        $sHtmlNavPages.= $this->build_navigation_buttons($sHtmlSelect);
        $sHtmlNavPages.="</div>    
        </td>
        </tr>
        </table>
        ";
        return $sHtmlNavPages;
    }//build_paginate_bar
    
    protected function get_parent_url()
    {
        $arUrl = array();
        if($this->isPermaLink)
        {
            $sParam = $this->get_current_module();
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_current_section();
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_current_view();
            if($sParam)$arUrl[]=$sParam;
            return implode("/",$arUrl);            
        }
        else
        {
            $sParam = $this->get_current_module();
            if($sParam)$arUrl[]="parentmodule=$sParam";
            $sParam = $this->get_current_section();
            if($sParam)$arUrl[]="parentsection=$sParam";
            $sParam = $this->get_current_view();
            if($sParam)$arUrl[]="parentview=$sParam";
            return implode("&",$arUrl);           
        }
        
    }//get_parent_url()
    
    /*
     * Si se ha parametrizado con multi asign en ventana origen se ha generado
     * la funcion js con url get en las que hay parametros return. Que es el destino
     * donde el popup enviará los datos
     */
    protected function get_return_multiurl()
    {
        $arUrl = array();
        if($this->isPermaLink)
        {
            $sParam = $this->get_get("returnmodule");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("returnsection");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("returnview");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("parentmodule");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("parentsection");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("parentview");
            if($sParam)$arUrl[]=$sParam;
            $sParam = (int)$this->get_get("close");
            if($sParam)$arUrl[]=$sParam;
            $sUrl = implode("/",$arUrl);
        }
        else
        {
            $sParam = $this->get_get("returnmodule");
            if($sParam)$arUrl[]="module=$sParam";
            $sParam = $this->get_get("returnsection");
            if($sParam)$arUrl[]="section=$sParam";
            $sParam = $this->get_get("returnview");
            if($sParam)$arUrl[]="view=$sParam";
            $sParam = $this->get_get("parentmodule");
            if($sParam)$arUrl[]="parentmodule=$sParam";
            $sParam = $this->get_get("parentsection");
            if($sParam)$arUrl[]="parentsection=$sParam";
            $sParam = $this->get_get("parentview");
            if($sParam)$arUrl[]="parentview=$sParam";
            $sParam = (int)$this->get_get("close");
            $arUrl[]="close=$sParam";
            $sUrl = implode("&",$arUrl);            
        }
        return $sUrl;
    }
    /*
     * Para single return solo se envia el dato por js.
     * Necesito recuperar
     */
    protected function get_return_single()
    {
        //if(sIdNameKey) sUrlAction += \"&returnkey=\"+sIdNameKey;
        //if(sIdNameDesc) sUrlAction += \"&returndesc=\"+sIdNameDesc;
        $arJs = array();
        $sIdReturnKey = $this->get_get("returnkey");
        $sIdReturnDesc = $this->get_get("returndesc");
        $doClose = (int)$this->get_get("close");
        $arJs[] = "var sIdReturnKey=\"$sIdReturnKey\";";
        $arJs[] = "var sIdReturnDesc=\"$sIdReturnDesc\";";
        $arJs[] = "var doClose=$doClose;";
        return implode("\n",$arJs);        
    }
    
    protected function convert_to_urlparams($arForParams)
    {
        $arUrl = array();
        $cGlue = "&";
        if($this->isPermaLink)
        {
            $cGlue = "/";
            foreach($arForParams as $arParams)
                foreach($arParams as $sFieldName=>$sValue)
                    $arUrl[] = $sValue;
            
        }
        else
        {
            foreach($arForParams as $arParams)
                foreach($arParams as $sFieldName=>$sValue)
                    $arUrl[] = "$sFieldName=$sValue";

        }    
        $sUrl = implode($cGlue,$arUrl);
        return $sUrl;
    }
    
    protected function js_fn_multiassign_window()
    {
        $arParentUrl = array();
        $arParentUrl[] = $this->get_parent_url();
        //Parametros extras como claves primarias y/o nuevos
        $arParentUrl[] = $this->convert_to_urlparams($this->arMultiAssign);

        $this->set_tmpjs();
        
        if($this->isPermaLink)
        {
            $arParentUrl = implode("/",$arParentUrl);
            $this->add_tmpjs("if(sUrl) sUrlAction += \"/\"+sUrl;");
            $this->add_tmpjs("if(doClose) sUrlAction += \"/1\";");
            $this->add_tmpjs("else sUrlAction += \"/0\";");
            $this->add_tmpjs("window.open(\"/\"+sUrlAction,\"multipick\",\"width=\"+iW+\",height=\"+iH,status=0,scrollbars=0,resizable=0,left=0,top=0);");
            
        }
        else
        {
            $arParentUrl = implode("&",$arParentUrl);
            $this->add_tmpjs("if(sUrl) sUrlAction += \"&\"+sUrl;");
            $this->add_tmpjs("if(doClose) sUrlAction += \"&close=1\";");
            $this->add_tmpjs("else sUrlAction += \"&close=0\";");
            $this->add_tmpjs("window.open(\"index.php?\"+sUrlAction,\"multipick\",\"width=\"+iW+\",height=\"+iH,status=0,scrollbars=0,resizable=0,left=0,top=0);");
        }
        
        $sTmpJs = $this->get_tmpjs();
        $sJs = "
        //parentparams se crean automaticamente
        //url: module,section,view,returnmodule,returnsection,returnview,extra1,extran..
        function multiassign_window(sUrl,iW,iH,doClose)
        {
            var iW = iW || 800;
            var iH = iH || 600;
            var sUrlAction = \"$arParentUrl\";
            $sTmpJs
        }
        ";
        return $sJs;
    }
    
    protected function js_fn_singleassign_window()
    {
        $arParentUrl = array();
        $arParentUrl[] = $this->get_parent_url();
        $arParentUrl[] = $this->convert_to_urlparams($this->arSingleAssign);
        
        $this->set_tmpjs();
        if($this->isPermaLink)
        {
            $arParentUrl = implode("/",$arParentUrl);
            $this->add_tmpjs("if(sUrl) sUrlAction += \"/\"+sUrl;");
            $this->add_tmpjs("if(doClose) sUrlAction += \"/1\";");
            $this->add_tmpjs("else sUrlAction += \"/0\";");
            $this->add_tmpjs("window.open(\"/\"+sUrlAction,\"singlepick\",\"width=\"+iW+\",height=\"+iH,status=0,scrollbars=0,resizable=0,left=0,top=0);");
            
        }
        else
        {
            $arParentUrl = implode("&",$arParentUrl);
            $this->add_tmpjs("if(sUrl) sUrlAction += \"&\"+sUrl;");
            $this->add_tmpjs("if(doClose) sUrlAction += \"&close=1\";");
            $this->add_tmpjs("else sUrlAction += \"&close=0\";");
            $this->add_tmpjs("window.open(\"index.php?\"+sUrlAction,\"singlepick\",\"width=\"+iW+\",height=\"+iH,status=0,scrollbars=0,resizable=0,left=0,top=0);");
        }
        
        $sTmpJs = $this->get_tmpjs();
        $sJs .= "
        //parentparams se crean automaticamente
        //url: module,section,view,returnmodule,returnsection,returnview,extra1,extran..
        function singleassign_window(sUrl,sIdNameKey,sIdNameDesc,iW,iH,doClose)
        {
            var iW = iW||800;
            var iH = iH||600;
            var sUrlAction = \"$arParentUrl\";
            $sTmpJs
        }
        ";
        return $sJs;
    }
    
    protected function js_fn_multiadd()
    {
        $arParentUrl = array();
        $arParentUrl[] = $this->get_return_multiurl();
        //Recupera los datos
        $arParentUrl[] = $this->convert_to_urlparams($this->arMultiAdd);
        
        $this->set_tmpjs();
        if($this->isPermaLink)
        {
            $arParentUrl = implode("/",$arParentUrl);
            $this->add_tmpjs("var sUrlAction = \"/$arParentUrl\";");
        }
        else
        {
            $arParentUrl = implode("&",$arParentUrl);
            $this->add_tmpjs("var sUrlAction = \"?$arParentUrl\";");
        }
        
        $sTmpJs = $this->get_tmpjs();        
        $sJs = "
        //helper_table_basic
        function multiadd(sIdForm)
        {
            var sIdForm = sIdForm || \"frmList\"; 
            $sTmpJs
            var oForm = document.getElementById(sIdForm);
            if(oForm)
            {
                if(is_checked(\"pkeys[]\"))
                {    
                    if(confirm(\"Are you sure to commit this operation?\"))
                    {
                        oForm.action=sUrlAction;
                        oForm.submit();
                    }
                }
                else
                    alert(\"No row selected\");
            }
        }
        ";
        return $sJs;        
    }

    protected function js_fn_singleadd()
    {
        $sJs = "
        //helper_table_basic
        function singleadd(iRow,sIdReturnKey,sIdReturnDesc,doClose)
        {
            var sHidKey = \"hidKeySingle_\"+iRow;
            var sHidDesc = \"hidDescSingle_\"+iRow;
            var eParentWindow = top.opener;
            //opener: padre //top: popup  //top.opener el padre del popup
            var sKeyValue = document.getElementById(sHidKey).value;
            var sDescValue = document.getElementById(sHidDesc).value;
            if(eParentWindow)
            {
                var eInput = eParentWindow.document.getElementById(sIdReturnKey);
                if(!eInput) eInput = eParentWindow.document.getElementsByName(sIdReturnKey)[0];
                eInput.value = sKeyValue;
                eInput = eParentWindow.document.getElementById(sIdReturnDesc);
                if(!eInput) eInput = eParentWindow.document.getElementsByName(sIdReturnDesc)[0];
                eInput.value = sDescValue;
                if(doClose) self.close();
            }
        }
        ";
        return $sJs;        
    }
    
    protected function js_fn_form_submit()
    {
        //OJO CON ESTO es un parche. Tengo que ver pq ya no es util concatenar en la url la página
        //sin permalnk no se pasa page en la url ya que se envia selpage por post y en los even_postandget se genera 
        //el $_GET[page]. En permalink no me vale ya que tengo que pasar la pagina en la url para que pueda ser interpretado
        //por el router
        $sTmpJs = "";
        if($this->isPermaLink)
        {
            $this->set_tmpjs();
            $this->add_tmpjs("var iPage = TfwControl.get_value_by_id(\"selPage\");");
            $this->add_tmpjs("iPage = iPage || 1;");
            $this->add_tmpjs("sUrlAction += iPage + \"/\";");
            $sTmpJs = $this->get_tmpjs();
        }
        
        $sJs = "    
        //helper_table_basic
        function table_frmsubmit()
         {            
            var sUrlAction = TfwControl.get_value_by_id(\"hidUrlPaginate\");
            $sTmpJs
            TfwControl.form_submit(\"$this->sIdForm\",sUrlAction);
         }
        ";
        return $sJs;        
    }    
    
    protected function js_fn_rowcheck()
    {
        $sJs = "
        //helper_table_basic
        function rowcheck(iRow,id)
        {
            var id = id||\"pkeys\";
            id = id+\"_\"+iRow;
            //alert(id);return;
            var eCheckBox = document.getElementById(id);
            //alert(eCheckBox);
            if(eCheckBox)
            {
                if(TfwControl.is_checkbox_checked(eCheckBox))
                    TfwControl.set_checkbox_check(eCheckBox,0);
                else
                    TfwControl.set_checkbox_check(eCheckBox,1);
                rowchange(iRow);
            }
        }
        ";
        return $sJs;          
    }
    
    protected function js_fn_check_all()
    {
        $sJs = "
        //helper_table_basic
        function check_all(id,name)
        {
            var id = id||\"pkeys_all\";
            var name = name||\"pkeys[]\";
            var eCheckBox = document.getElementById(id);
            //alert(eCheckBox);
            if(TfwControl.is_checkbox_checked(eCheckBox))
                TfwControl.set_checked_by_name(name,true);
            else
                TfwControl.set_checked_by_name(name,false);
        }
        ";
        return $sJs;        
    }
    
    protected function js_fn_nav_click()
    {
        $this->set_tmpjs();
        if($this->isPermaLink)
        {
            $this->add_tmpjs("sUrlAction += iPage+\"/\";");
        }
        else
        {
            $this->add_tmpjs("sUrlAction += \"&page=\"+iPage;");
        }
        
        $sTmpJs = $this->get_tmpjs();   
        
        $sJs = "
        //helper_table_basic
        function nav_click(iPage)
        {
            var iPage = iPage || 1;
            var sUrlAction = TfwControl.get_value_by_id(\"hidUrlPaginate\");
            $sTmpJs
            TfwControl.sel_option_by_id(\"selPage\",iPage);
            TfwControl.form_submit(\"$this->sIdForm\",sUrlAction);
        }
        ";
        return $sJs;        
    }
    
    protected function js_fn_multi_delete()
    {
        $sCheckPostKey = $this->build_postkey();
        
        $this->set_tmpjs();
        if($this->isPermaLink)
        {
            $this->add_tmpjs("var sUrlAction = document.location+\"/delete\";");
        }
        else
        {
            $this->add_tmpjs("var sUrlAction = document.location+\"&view=delete\";");
        }
        
        $sTmpJs = $this->get_tmpjs();
        
        $sJs = "    
        //helper_table_basic
        function multi_delete()
        {
            if(is_checked(\"$sCheckPostKey"."[]\"))
            {
                var oHidAction = document.getElementById(\"hidAction\");
                oHidAction.value = \"multidelete\";
                $sTmpJs
                TfwControl.form_submit(\"$this->sIdForm\",sUrlAction);
            }
            else
                alert(\"No row selected\");
        }
        ";
        return $sJs;        
    }
    
    protected function js_fn_multi_quarantine()
    {
        $sCheckPostKey = $this->build_postkey();
        
        $this->set_tmpjs();
        if($this->isPermaLink)
        {
            $this->add_tmpjs("var sUrlAction = document.location+\"quarantine/\";");
        }
        else
        {
            $this->add_tmpjs("var sUrlAction = document.location+\"&view=quarantine\";");
        }
        
        $sTmpJs = $this->get_tmpjs();
        
        $sJs = "
        //helper_table_basic
        function multi_quarantine()
        {
            if(is_checked(\"$sCheckPostKey"."[]\"))
            {
                var oHidAction = document.getElementById(\"hidAction\");
                oHidAction.value = \"multiquarantine\";
                $sTmpJs
                TfwControl.form_submit(\"$this->sIdForm\",sUrlAction);
            }
            else
                alert(\"No row selected\");
        }
        ";
        return $sJs;        
    }
    
    protected function js_fn_is_checked()
    {
        $sJs = "
        //helper_table_basic
        function is_checked(sCheckName)
        {
            var arObjChecks = document.getElementsByName(sCheckName);
            //bug(arObjChecks);
            if(arObjChecks.length!=undefined)
            {
                for(var i=0; i<arObjChecks.length; i++)
                    if(arObjChecks[i].checked==1)
                        return true;
            }
            return false;
        }
        ";
        return $sJs;        
    }
    
    protected function js_fn_rowchange()
    {
        $sJs = "
        //helper_table_basic
        function rowchange(iRow)
        {
            var sId = \"hidRowChanged_\"+iRow+\"_0\";
            var oHidRowChanged = document.getElementById(sId);
            //alert(oHidRowChanged);
            if(oHidRowChanged) oHidRowChanged.value=1;
        }
        ";
        return $sJs;        
    }
    
    protected function build_js()
    {
        $sHtmlJs .= "<script helper=\"tablebasic\" type=\"text/javascript\">\n";
        $sHtmlJs .= $this->js_fn_rowchange();
        $sHtmlJs .= $this->js_fn_form_submit();
        $sHtmlJs .= $this->js_fn_check_all();
        $sHtmlJs .= $this->js_fn_nav_click();
        $sHtmlJs .= $this->js_fn_multi_delete();
        $sHtmlJs .= $this->js_fn_multi_quarantine();
        $sHtmlJs .= $this->js_fn_is_checked();
        if($this->isCheckOnRowclick)
            $sHtmlJs .= $this->js_fn_rowcheck();
        
        if($this->isOrdenable)
        {
            $sHtmlJs .= 
        "var sThBackground=\"\";
         var sThColor=\"\";
            
         function order_by(eTh)
         {
            var sFieldName = eTh.getAttribute(\"dbfield\");
            var oHidOrderBy = document.getElementById(\"hidOrderBy\");
            var oHidOrderType = document.getElementById(\"hidOrderType\");
            
            var sOrderBy = oHidOrderBy.value;
            var sOrderType = oHidOrderType.value;
            
            if(sOrderBy)
            {
                if(sFieldName==sOrderBy)
                {
                    if(sOrderType.toUpperCase()==\"ASC\")
                        oHidOrderType.value=\"DESC\";
                    else oHidOrderType.value=\"ASC\";
                }
                else
                {
                    oHidOrderBy.value = sFieldName;
                    oHidOrderType.value = \"ASC\";
                }
            }
            else
            {
                oHidOrderBy.value = sFieldName;
                oHidOrderType.value = \"ASC\";                
            }
            //bug('antes de submit');
            table_frmsubmit();
         }
         
         function on_thover(eTh,sColor,sBackColor)
         {
            sThBackground = eTh.style.backgroundColor;
            sThColor = eTh.style.color;
            eTh.style.color=sColor;
            eTh.style.backgroundColor=sBackColor;
         }
         
         function on_thout(eTh){eTh.style.backgroundColor=sThBackground;eTh.style.color=sThColor;}
        ";
        }
        
//        //Campos filtros
//        if($this->arObjFields)
//        {
//            $sFieldIds = $this->build_js_field_ids();
//            $sHtmlJs .="
//         function reset_filters()
//         {
//            var arFields = [$sFieldIds];
//            TfwFieldValidate.reset(arFields);
//         }
//        ";
//        }//hay arObjFields 
        
        //funcion js para popup
        if($this->arMultiAssign) $sHtmlJs .= $this->js_fn_multiassign_window();
        if($this->arMultiAdd) $sHtmlJs .= $this->js_fn_multiadd();
        if($this->arSingleAssign) $sHtmlJs .= $this->js_fn_singleassign_window();
        if($this->arSingleAdd) $sHtmlJs .= $this->js_fn_singleadd();
        $sHtmlJs .= "</script>\n";
        return $sHtmlJs;
    }
    
    protected function build_js_field_ids()
    {
        $arIds = array_keys($this->arColumns);
        $sFieldIds = implode("\",\"",$arIds);
        $sFieldIds = "\"$sFieldIds\"";
        return $sFieldIds;
    }
    
    protected function build_hidden_fields()
    {
        $sHtmlHidden = "";
        $oHidden = new HelperInputHidden();
        
        $oHidden->set_id("hidOrderBy");
        $oHidden->set_name("hidOrderBy");
        $oHidden->set_value(implode(",",$this->arOrderBy));
        $sHtmlHidden .= $oHidden->get_html();
        
        $oHidden->set_id("hidOrderType");
        $oHidden->set_name("hidOrderType");
        $oHidden->set_value(implode(",",$this->arOrderWay));
        $sHtmlHidden .= $oHidden->get_html();
        
        $oHidden->set_id("hidKeyFields");
        $oHidden->set_name("hidKeyFields");
        $oHidden->set_value(implode(",",$this->arKeyFields));
        $sHtmlHidden .= $oHidden->get_html();
        
        //URLS: Solo para js
        $oHidden->set_name(NULL);
        $oHidden->set_id("hidUrlPickMultiple");
        $oHidden->set_value($this->sUrlPickMultiple);
        $sHtmlHidden .= $oHidden->get_html();        
        
        $oHidden->set_id("hidUrlDelete");
        $oHidden->set_value($this->sUrlDelete);
        $sHtmlHidden .= $oHidden->get_html();

        $oHidden->set_id("hidUrlUpdate");
        $oHidden->set_value($this->sUrlUpdate);
        $sHtmlHidden .= $oHidden->get_html();

        $oHidden->set_id("hidUrlPaginate");
        //bug($this->sUrlPaginate);
        $oHidden->set_value($this->sUrlPaginate);
        $sHtmlHidden .= $oHidden->get_html();
        
        //Necesario para saber si solo se está refrescando, filtrando o eliminando
        $oHidden->set_id("hidAction");
        $oHidden->set_name("hidAction");
        $oHidden->set_value("");
        $sHtmlHidden .= $oHidden->get_html();
        
        $oHidden->set_id("hidPostback");
        $oHidden->set_name("hidPostback");
        $oHidden->set_value("");
        $sHtmlHidden .= $oHidden->get_html();    

        $oHidden->set_id("selItemsPerPage");
        $oHidden->set_name("selItemsPerPage");
        $oHidden->set_value($this->iItemsPerPage);
        $sHtmlHidden .= $oHidden->get_html();        
        //Si se ha indicado que la tabla es de asignacion
        //se guarda los datos actuales de la url
        if($this->arMultiAdd)
        {
            $oHidden->set_id("hidDataModule");
            $oHidden->set_name("hidDataModule");
            $oHidden->set_value($this->get_current_module());
            $sHtmlHidden .= $oHidden->get_html();

            $oHidden->set_id("hidDataSection");
            $oHidden->set_name("hidDataSection");
            $oHidden->set_value($this->get_current_section());
            $sHtmlHidden .= $oHidden->get_html();

            $oHidden->set_id("hidDataView");
            $oHidden->set_name("hidDataView");
            $oHidden->set_value($this->get_current_view());
            $sHtmlHidden .= $oHidden->get_html();
        }
        return $sHtmlHidden;
    }
    
    protected function build_hidden_row($iNumRow)
    {
        $oHidden = new HelperInputHidden();
        $sIdName = "hidRow_$iNumRow"."_0";
        $oHidden->set_id($sIdName);
        $oHidden->set_name($sIdName);
        $oHidden->set_value($iNumRow);
        return $oHidden->get_html();
    }
    
    protected function build_hidden_rowchange($iNumRow)
    {
        $oHidden = new HelperInputHidden();
        $sIdName = "hidRowChanged_$iNumRow"."_0";
        $oHidden->set_id($sIdName);
        $oHidden->set_name($sIdName);
        $oHidden->set_value("0");
        return $oHidden->get_html();
    }
    
    protected function build_hidden_keys($arRow,$iNumRow)
    {
        $sHtmlHidden = "";
        $oHidden = new HelperInputHidden();
        foreach($this->arKeyFields as $sFieldName)
        {
            $sIdName = "hid$sFieldName"."_$iNumRow";
            $oHidden->set_id($sIdName);
            $oHidden->set_name($sIdName);
            $oHidden->set_value($this->get_fieldvalue_by_name($arRow,$sFieldName));
            $sHtmlHidden .= $oHidden->get_html();            
        }
        return $sHtmlHidden;
    }

    protected function build_hidden_columns($arRow,$iNumRow)
    {
        $sHtmlHidden = "";
        $oHidden = new HelperInputHidden();
        foreach($this->arHiddenColumns as $sFieldName)
        {
            $sIdName = "hid$sFieldName"."_$iNumRow"."_0";
            $oHidden->set_id($sIdName);
            $oHidden->set_name($sIdName);
            $oHidden->add_extras("cellpos",$iNumRow."_0");
            //bug($sFieldName);
            $oHidden->set_value($this->get_fieldvalue_by_name($arRow,$sFieldName));
            $sHtmlHidden .= $oHidden->get_html();            
        }
        return $sHtmlHidden;
    }

    protected function build_extra_hidden($iNumRow)
    {
        $sHtmlHidden = "";
        $oHidden = new HelperInputHidden();
        foreach($this->arExtraHidden as $sFieldName=>$mxValue)
        {
            $sIdName = "hid$sFieldName"."_$iNumRow"."_0";
            $oHidden->set_id($sIdName);
            $oHidden->set_name($sIdName);
            $oHidden->add_extras("cellpos",$iNumRow."_0");
            
            if(is_array($mxValue)) $mxValue = implode(",",$mxValue);
            $oHidden->set_value($mxValue);
            $sHtmlHidden .= $oHidden->get_html();            
        }
        return $sHtmlHidden;
    }
    
    /**
     * Crea el array de columnas incluyendo operaciones si las hubiese
     * Segun estas columnas crea la fila de cabecera
     * Segun las columnas crea las filas de datos 
     * @return array
     */
    protected function load_array_object_tr() 
    {
        //Devuelve las columnas de operaciones  Upd,Delete,Multiple
        $arColumns = $this->get_operation_columns();
        //bug($arColumns,"arcolumns antes");
        //Fila Cabecera. Añade la cabecera según las posiciones de las columnas
        $this->load_head_row($arColumns,$this->arObjTrs);
        //Filas Cuerpo. Añade las filas restantes según las posiciones de las columnas
        $this->load_body_rows($arColumns,$this->arObjTrs);
    }//load_array_object_tr
    
    protected function get_operation_columns()
    {
        $arColumns = array();
        if($this->isToDetail) $arColumns["detail"] = "Upd";
        if($this->isPickMultiple) $arColumns["multipick"] = "Multi";
        if($this->isPickSingle) $arColumns["singlepick"] = "Single";
        
        if(!empty($arColumns)) $arColumns = array_merge($arColumns,$this->arColumns);
        else $arColumns = $this->arColumns;
        
        if($this->isDeleteSingle) $arColumns["delete"] = "Del";
        if($this->isQuarantineSingle) $arColumns["quarantine"] = "Del";
        //bug($arColumns,"operation_columns");
        if($this->arExtraColumns) $arColumns = $this->reorder_columns($arColumns,$this->arExtraColumns);
        return $arColumns;
    }
    
    protected function reorder_columns($arColumns,$arVirtualColumns)
    {
        $arColsReordered = array();
        $arOccupied = array();
        $arTemp = array();

        foreach($arColumns as $sFieldName=>$sLabel)
        {
            $iPosition = array_key_position($sFieldName,$arColumns);
            $arOccupied[] = array("fieldname"=>$sFieldName,"label"=>$sLabel,"position"=>$iPosition);
        }

        foreach($arOccupied as $i=>$arColumnOccupied)
        {
            $sFieldName = $arColumnOccupied["fieldname"];
            $sLabel = $arColumnOccupied["label"];
            $iPosition = $arColumnOccupied["position"];

            $arNewInPosition = $this->get_columns_by_position($iPosition,$arVirtualColumns);
            foreach($arNewInPosition as $i=>$arNewCol)
                $arTemp[] = $arNewCol;

            $arTemp[] = $arColumnOccupied;
            $this->unset_by_position($iPosition,$arVirtualColumns);
        }    

        //Fuera del rango de los que existian
        foreach($arVirtualColumns as $i=>$arData)
        {
            $arData["fieldname"] = "virtual_$i";
            $arTemp[] = $arData;
        }

        foreach($arTemp as $arData)
            $arColsReordered[$arData["fieldname"]]=$arData["label"];  
        //bug($arColsReordered); die;
        return $arColsReordered;
    }

    protected function get_columns_by_position($iPosition,$arColumns)
    {
        $arColsInPosition = array();
        foreach($arColumns as $i=>$arColData)
            if($arColData["position"]==$iPosition)
            {
                $arColData["fieldname"] = "virtual_$i";
                $arColsInPosition[] = $arColData;
            }
        return $arColsInPosition;
    }
    
    protected function unset_by_position($iPosition,&$arColums)
    {
        foreach($arColums as $key=>$arColData)
            if($arColData["position"]==$iPosition)
                unset($arColums[$key]);
    }

    protected function load_head_row($arColumns,&$arObjRows)
    {
        $oTrHead = new HelperTableTr();
        $oTrHead->set_as_rowhead();
        $oTrHead->set_attr_rownumber(-1);
        //bug($arColumns,"arColumns");die;
        //Cabecera
        foreach($arColumns as $sFieldName=>$sLabel)
        {
            $iColumnPosition = array_key_position($sFieldName,$arColumns);
            //bug($sFieldName,$iNumColumn);
            $oTh = new HelperTableTd();
            $oTh->set_as_header();
            $oTh->set_attr_dbfield($sFieldName);
            $oTh->set_attr_colnumber($iColumnPosition);
            $oTh->set_attr_rownumber(-1);
            switch($sFieldName)
            {
                case "multipick":
                    //Al ser multipick es una columna checkbox sin label
                    $oTh->set_innerhtml($this->build_multiple_button_head());
                break;
                default:
                    $sColLabel = $this->build_column_label($sFieldName,$sLabel);
                    $oTh->set_innerhtml($sColLabel);
                    //bug($oTh,$sFieldName);
                    //si la tabla permite ordenar se aplica la func js para ejecutar el submit
                    $arNoOrder=array("delete","detail","singlepick","quarantine");
                    if($this->isOrdenable && !in_array($sFieldName,$arNoOrder) && !strstr($sFieldName,"virtual_"))
                    {    
                        $oTh->set_js_onclick("order_by(this);");
                        $oTh->set_js_onmouseover("on_thover(this,'#000','#B2B2B2');");
                        $oTh->set_js_onmouseout("on_thout(this);");
                    }
                break;
            }//fin switch fieldname
            $arObjThs[] = $oTh;
        }
        $oTrHead->set_objtds($arObjThs);
        //array con celdas cabecera
        $arObjRows[] = $oTrHead;
        //Fin cabecera        
    }//fin load_head_row
    
    protected function build_column_label($sFieldName,$sLabel)
    {
        //bug($this->arOrderBy,"arOrdeBy");bug($this->arOrderWay,"arOrderWay");
        $sColumnHeader = $sLabel;
        if($this->isOrdenable)
        {
            //bug($this->arOrderBy,$sFieldName);bug($this->arOrderWay);
            $iPosition = array_search($sFieldName,array_values($this->arOrderBy));
            //bug($iPosition,"position $sFieldName");
            if($iPosition || $iPosition===0) $sOrderWay = $this->arOrderWay[$iPosition];
            //bug($sOrderWay,"orderway");
            if($sOrderWay=="ASC")
                $sColumnHeader = "$sColumnHeader <span class=\"awe-caret-up\"></span>";
            elseif($sOrderWay=="DESC")
                $sColumnHeader = "$sColumnHeader <span class=\"awe-caret-down\"></span>";
            //else  Aqui deberia dar errores
        }  
        //bug($sColumnHeader,"columnHeader: $sFieldName, $sLabel");
        return $sColumnHeader;
    }//build_column_label

    protected function load_body_rows($arColums,&$arObjRows)
    {
        //Recorre las filas de datos
        foreach($this->arDataRows as $iNumRow=>$arRow)
        {
            //Celdas de la fila
            $arObjTds = array();
            $oTr = new HelperTableTr();
            
            if($this->isCheckOnRowclick)  
            {    
                $sPostKey = $this->build_postkey();
                $oTr->set_js_onclick("rowcheck('$iNumRow','$sPostKey');");
                //$oTr->set_js_onchange("alert('changed')");//no escucha este evento
            }
            
            $oTr->set_attr_rownumber($iNumRow);
            //Recorre las columnas segun el orden pasado en las cabeceras
            foreach($arColums as $sFieldName=>$sLabel)
            {
                $iNumColumn = (int)array_key_position($sFieldName,$arColums);
                
                $oTd = new HelperTableTd();
                $oTd->set_attr_dbfield($sFieldName);
                $oTd->set_attr_colnumber($iNumColumn);
                $oTd->set_attr_rownumber($iNumRow);
                $oTd->set_attr_position($iNumRow,$iNumColumn);
                //CONTENIDO DE CELDA
                $sTdInner = $this->build_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn);
                $oTd->set_innerhtml($sTdInner);
                $arObjTds[] = $oTd;
            }//fin for arColumns
            $oTr->set_objtds($arObjTds);
            $arObjRows[] = $oTr;
        }//fin for arDataRows
    }//load_body_rows

    /**
     * ___MAIN_CELL__
     * Segun el nombre de la columna, ya sea relacionada con un campo en bd y/o con una operacion agregada
     * se genera su contenido.
     * @param array $arRow Array de pares columna=>valor
     * @param string $sFieldName Nombre de la columna a la que corresponde la celda
     * @param integer $iNumRow Numero de la fila. Se utiliza para crear el atributo de posicion de celda
     * @param integer $iNumColumn Numero de la columna. Se utiliza para crear los hiddenkeys y la posicion de la celda
     * @return string Html con contenido de la celda
     */
    protected function build_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn)
    {
        $sTdInner = "";
        if($iNumColumn===0)
        {   
            $sTdInner .= $this->build_hidden_rowchange($iNumRow);
            $sTdInner .= $this->build_hidden_row($iNumRow);
            $sTdInner .= $this->build_hidden_keys($arRow,$iNumRow);
            $sTdInner .= $this->build_hidden_columns($arRow,$iNumRow);
            if($this->arHiddenColumns)
                $sTdInner .= $this->build_extra_hidden($iNumRow);
        }

        switch($sFieldName)
        {
            //columna delete single
            case "delete":
                $sTdInner .= $this->build_delete_button($arRow);
            break;
            case "quarantine":
                $sTdInner .= $this->build_quarantine_button($arRow);
            break;        
            case "detail":
                $sTdInner .= $this->build_detail_button($arRow);
            break;
            case "multipick":
                //bug($oChekbox);
                $sTdInner .= $this->build_multiple_button($arRow,$iNumRow);
            break;
            case "singlepick":
                $sTdInner .= $this->build_single_button($arRow,$iNumRow);
                //bug($sTdInner,"tdinner");
            break;         
            default:
                $sTdInner .= $this->get_fieldvalue_by_name($arRow,$sFieldName);
            break;
        }//fin switch fieldname
        return $sTdInner;
    }

    protected function build_url_button($sUrlMethod,$arRow,$sExclude=NULL)
    {
        $sReturnUrl = $sUrlMethod;
        $sKeys = $this->get_keys_as_url($arRow,$sExclude);
        
        if($this->isPermaLink)
        {
            //si no acaba en / le añado esta
            if(!$this->is_lastchar_slash($sReturnUrl))
                $sReturnUrl = $sUrlMethod."/";

            if($sKeys) $sReturnUrl.=$sKeys."/";
            
        }
        elseif($sKeys)
            $sReturnUrl.="&".$sKeys; 
        
        return $sReturnUrl;
    }
    
    protected function build_delete_button($arRow)
    {
        $oAnchor = new HelperAnchor();
        $oAnchor->add_class("btn btn-danger");
        //icon-remove-sign
        $oAnchor->set_innerhtml("\n<span class=\"awe-remove-sign\"></span> delete");
        $oAnchor->set_target("self");
        $sUrlButton = $this->build_url_button($this->sUrlDelete,$arRow);
        $oAnchor->set_href($sUrlButton);
        $sHtmlAnchor .= $oAnchor->get_html();
        return $sHtmlAnchor;
    }

    protected function build_quarantine_button($arRow)
    {
        $oAnchor = new HelperAnchor();
        $oAnchor->add_class("btn btn-danger");
        //icon-remove-sign
        $oAnchor->set_innerhtml("\n<span class=\"awe-remove-sign\"></span> delete");
        $oAnchor->set_target("self");
        $sUrlButton = $this->build_url_button($this->sUrlQuarantine,$arRow);
        $oAnchor->set_href($sUrlButton);
        $sHtmlAnchor .= $oAnchor->get_html();
        return $sHtmlAnchor;
    }
    
    protected function build_detail_button($arRow)
    {
        $oAnchor = new HelperAnchor();
        $oAnchor->add_class("btn btn-info");
        $oAnchor->set_innerhtml("\n<span class=\"awe-info-sign\"></span> info");
        $oAnchor->set_target("self");
        $sUrlButton = $this->build_url_button($this->sUrlUpdate,$arRow,"page");
        $oAnchor->set_href($sUrlButton);
        $sHtmlAnchor .= $oAnchor->get_html();
        return $sHtmlAnchor;
    }

    protected function build_multiple_button($arRow,$iNumRow="")
    {
        $oChekbox = new HelperCheckbox();
        $oChekbox->set_unlabeled();
        $sHtmlCheck = "";
        
        if($this->doMergePkeys)
        {
            foreach($this->arKeyFields as $sFldKeyName)
                $arValues[] = $sFldKeyName."=".$this->get_fieldvalue_by_name($arRow,$sFldKeyName);
            
            $sMerged = implode($this->sMergeGlue,$arValues);
            $oChekbox->set_options(array($sMerged=>NULL));
            $oChekbox->set_id("pkeys_$iNumRow");
            $oChekbox->set_name("pkeys");
            $oChekbox->set_attr_dbfield("pkeys");
            //$oChekbox->set_js_onchange("alert('hola')");
            $sHtmlCheck .= $oChekbox->get_html();            
        }    
        else
        //TODO: Esto habría que mejorarlo. Se debe añadir el atributo
        //name por js cuando se active el check y quitarlo cuando se desactiva
        //para evitar dos check en una celda Así pues cuando se envie el formulario
        //llegaran por post solo los hidden con nombre
        foreach($this->arKeyFields as $sFldKeyName)
        {
            $sFieldValue = $this->get_fieldvalue_by_name($arRow,$sFldKeyName);
            $oChekbox->set_options(array($sFieldValue=>""));
            $id = $sFldKeyName;
            if($iNumRow!=="") $id .= "_$iNumRow";
            $oChekbox->set_id($id);
            //Cambio en el helper Checkbox. No hace falta [] ya que se activa o desactiva según la variable isGrouped. Por defecto true
            $oChekbox->set_name($sFldKeyName);
            $oChekbox->set_attr_dbfield($sFldKeyName);
            //$oChekbox->set_js_onchange("alert('hola')");
            $sHtmlCheck .= $oChekbox->get_html();
        }
        return $sHtmlCheck;
    }//build_multiple_button

    protected function build_single_button($arRow,$iNumRow="")
    {
        $sIdDestKey = $this->arSingleAdd["destkey"];
        $sIdDestDesc = $this->arSingleAdd["destdesc"];
        $arColumnsKeys = explode(",",$this->arSingleAdd["keys"]);
        $arColumnsDesc = explode(",",$this->arSingleAdd["descs"]);
        $doClose = (int)$this->arSingleAdd["close"];
        
        $oButton = new HelperButtonBasic();
        $oButton->add_class("btn btn-success");
        $oButton->set_innerhtml("Pick");
        $oHidKey = new HelperInputHidden("hidKeySingle_$iNumRow");        
        $oHidDesc = new HelperInputHidden("hidDescSingle_$iNumRow");

        $oButton->set_js_onclick("singleadd($iNumRow,'$sIdDestKey','$sIdDestDesc',$doClose);");

        $sHtmlButton = "";
        
        $iNumFields = count($arColumnsKeys);
        foreach($arColumnsKeys as $sFieldName)
            if($iNumFields>1)
                $arValues[] = $sFieldName."=".$this->get_fieldvalue_by_name($arRow,$sFieldName);
            else
                $arValues[] = $this->get_fieldvalue_by_name($arRow,$sFieldName);

        $sMerged = implode($this->sMergeGlue,$arValues);
        $oHidKey->set_value($sMerged);

        $arValues = array();
        //$iNumFields = count($arColumnsDesc);
        foreach($arColumnsDesc as $sFieldName)
            $arValues[] = $this->get_fieldvalue_by_name($arRow,$sFieldName);
        
        $sMerged = implode($this->sMergeGlue,$arValues);
        $oHidDesc->set_value($sMerged);

        $sHtmlButton .= $oButton->get_html();
        $sHtmlButton .= $oHidKey->get_html();
        $sHtmlButton .= $oHidDesc->get_html();        
        return $sHtmlButton;
    }//build_single_button
    
    protected function build_multiple_button_head()
    {
        $oChekbox = new HelperCheckbox();
        $oChekbox->set_unlabeled();
        $sHtmlCheck = "";
        if($this->doMergePkeys)
        {
            $sFldKeyNames = implode($this->sMergeGlue,array_values($this->arKeyFields));
            $oChekbox->set_options(array($sFldKeyNames=>NULL));
            $oChekbox->set_id("pkeys_all");
            $oChekbox->set_name("pkeys_all");
            $oChekbox->set_js_onclick("check_all();");
            $sHtmlCheck .= $oChekbox->get_html();
        }    
        else//No es un keymerge entonces se crea una caja por key
            foreach($this->arKeyFields as $sFldKeyName)
            {
                $oChekbox->set_options(array($sFldKeyName=>NULL));
                $oChekbox->set_id($sFldKeyName."_all");
                $oChekbox->set_name($sFldKeyName."_all");
                $oChekbox->set_js_onclick("check_all('$sFldKeyName"."_all','$sFldKeyName"."[]');");
                $sHtmlCheck .= $oChekbox->get_html();
            }
            return $sHtmlCheck;
    }
    
    protected function get_keys_as_url($arRow,$mxExclude=NULL)
    {
        //Necesario para poder excluir parámetros que no se usan con .htcacces en los links
        //y que dificultan el enrutamiento ej. module=xxx&page=3&update=65.  "page" sobra
        $arExclude = $this->mixed_to_array($mxExclude);
        $arRowKeys = array();
        $cGlue = "&";
        
        if($this->isPermaLink)
        {
            foreach($this->arKeyFields as $sFldKeyName)
            {
                if(in_array($sFldKeyName,$arExclude))
                    continue;
                $sFieldValue = $this->get_fieldvalue_by_name($arRow,$sFldKeyName);
                if($sFieldValue) 
                    $arRowKeys[] = $sFieldValue;
            }            
        }
        else
        {
            foreach($this->arKeyFields as $sFldKeyName)
            {
                if(in_array($sFldKeyName,$arExclude))
                    continue;                
                $sFieldValue = $this->get_fieldvalue_by_name($arRow,$sFldKeyName);
                if($sFieldValue) 
                    $arRowKeys[] = "$sFldKeyName=$sFieldValue";
            }
        }
        $sUrl = implode($cGlue,$arRowKeys);
        return $sUrl;
    }
    
    protected function get_fieldvalue_by_name($arRow,$sName)
    {
        foreach($arRow as $sFieldName=>$sFieldValue)
        {    
            if($sFieldName==$sName)
            {    
                $sFieldValue = $this->format_value($sFieldName, $sFieldValue);
                return $sFieldValue;
            }
        }
        return NULL;
    }    
    
    protected function get_colformat($sFieldName)
    {
        foreach($this->arConfigColTypes as $sField=>$sFormat)
            if($sFieldName==$sField)
                return $sFormat;
        return NULL;
    }
    
    /**
     * Este metodo se llama igual que en theframework con la diferencia que este transforma un valor de bd a bo
     * de modo que se muestre en un formato entendido por el usuario
     * @param string $sFieldName
     * @param string $sFieldValue
     * @return formated value
     */
    protected function format_value($sFieldName,$sFieldValue)
    {
        $sValue = "";
        $sFormat = $this->get_colformat($sFieldName);
        switch($sFormat) 
        {
            case "date":
                $sValue = dbbo_date($sFieldValue);
            break;
        
            case "datetime4":
                $sValue = dbbo_datetime4($sFieldValue);
            break;

            case "datetime6":
                $sValue = dbbo_datetime6($sFieldValue);
            break;

            case "time4":
                $sValue = dbbo_time4($sFieldValue);
            break;

            case "time6":
                $sValue = dbbo_time6($sFieldValue);
            break;

            case "int":
                $sValue = dbbo_int($sFieldValue);
            break;
        
            case "numeric2":
                $sValue = dbbo_numeric2($sFieldValue);
            break;
        
            default:
                $sValue = $sFieldValue;
            break;
        }
        //bug($sValue,$sFieldName);
        return $sValue;
    }        
    
    protected function build_navigation_buttons($sHtmlSelect)
    {
        $sHtmlUlButtons = "";
        $sHtmlUlButtons .= "<ul style=\"margin:0\">";
        if($this->iInfoCurrentPage>1)
        {            
            $sHtmlUlButtons .= "<li><a href=\"javascript:nav_click($this->iInfoFirstPage);\">&nbsp;&nbsp;<span class=\"awe-arrow-left\"></span>&nbsp;&nbsp;</a></li>";
            $sHtmlUlButtons .= "<li><a href=\"javascript:nav_click($this->iInfoPreviousPage);\">&nbsp;&nbsp;«&nbsp;&nbsp;</a></li>";
        }        
        $sHtmlUlButtons .= "<li>&nbsp;Total: $this->iInfoNumRegs - ($this->iInfoCurrentPage/$this->iInfoNumPages)&nbsp;</li>";
        if($this->iInfoCurrentPage<$this->iInfoLastPage)        
        {
            $sHtmlUlButtons .= "<li><a href=\"javascript:nav_click($this->iInfoNextPage);\">&nbsp;&nbsp;»&nbsp;&nbsp;</a></li>";
            $sHtmlUlButtons .= "<li><a href=\"javascript:nav_click($this->iInfoLastPage);\">&nbsp;&nbsp;<span class=\"awe-arrow-right\"></span>&nbsp;&nbsp;</a></li>";
        }       

        if($this->iInfoNumPages>1) $sHtmlUlButtons .= "<li>&nbsp;&nbsp;Go to:&nbsp;&nbsp;$sHtmlSelect</li>";
        $sHtmlUlButtons .= "</ul>";
        return $sHtmlUlButtons;
    }

    private function get_fixed_params($isByMvc=0)
    {
        $arFixedKeys = array("module","section","view");
        if($isByMvc)
            $arFixedKeys = array("controller","partial","method");
        
        $arFixedParams = array();
        foreach($_GET as $key=>$mxValue)
            if(in_array($key,$arFixedKeys))
                $arFixedParams[$key] = "$key=$mxValue";
        return $arFixedParams;
    }
    
    private function get_unfixed_params()
    {
        $arFixedKeys = array("module","section","view","controller","partial","method");
        
        $arFixedParams = array();
        foreach($_GET as $key=>$mxValue)
            if(!in_array($key,$arFixedKeys))
                $arFixedParams[$key] = "$key=$mxValue";        
       
        return $arFixedParams;
    }

    
    private function build_url_nomethod($isByMvc=0)
    {
        if($this->isPermaLink)
        {
            if($isByMvc)
            {
                if($_GET["controller"]) $arUrlNoMethod[0] = $_GET["controller"];
                if($_GET["partial"]) $arUrlNoMethod[1] = $_GET["partial"];
                return implode("/",$arUrlNoMethod);                
            }
            //isByMvc=0
            else
            {
                if($_GET["module"]) $arUrlNoMethod[0] = $_GET["module"];
                if($_GET["section"]) $arUrlNoMethod[1] = $_GET["section"];
                return implode("/",$arUrlNoMethod);
            }
        }
        //no permalink
        else
        {
            if($isByMvc)
            {
                if($_GET["controller"]) $arUrlNoMethod[0] = "controller=".$_GET["controller"];
                if($_GET["partial"]) $arUrlNoMethod[1] = "partial=".$_GET["partial"];
                return implode("&",$arUrlNoMethod);
            }
            //isByMvc=0
            else
            {
                if($_GET["module"]) $arUrlNoMethod[0] = "module=".$_GET["module"];
                if($_GET["section"]) $arUrlNoMethod[1] = "section=".$_GET["section"];
                return implode("&",$arUrlNoMethod);
            }
        }
    }//build_url_nomethod
    
    private function build_url_return($isByMvc=0)
    {
        if($this->isPermaLink)
        {
            if($isByMvc)
            {
                if($_GET["returncontroller"]) $arUrl[0] = $_GET["returncontroller"];
                if($_GET["returnpartial"]) $arUrl[1] = $_GET["returnpartial"];
                if($_GET["returnmethod"]) $arUrl[2] = $_GET["returnmethod"];
            }
            //isByMvc=0
            else
            {
                if($_GET["returnmodule"]) $arUrl[0] = $_GET["returnmodule"];
                if($_GET["returnsection"]) $arUrl[1] = $_GET["returnsection"];
                if($_GET["returnmethod"]) $arUrl[2] = $_GET["returnmethod"];
            }
            return implode("/",$arUrl);
        }
        //no permalink
        else
        {
            if($isByMvc)
            {
                if($_GET["returncontroller"]) $arUrl[0] = "returncontroller=".$_GET["returncontroller"];
                if($_GET["returnpartial"]) $arUrl[1] = "returnpartial=".$_GET["returnpartial"];
                if($_GET["returnmethod"]) $arUrl[2] = "returnmethod=".$_GET["returnmethod"];
            }
            //isByMvc=0
            else
            {
                if($_GET["returnmodule"]) $arUrl[0] = "returnmodule=".$_GET["returnmodule"];
                if($_GET["returnsection"]) $arUrl[1] = "returnsection=".$_GET["returnsection"];
                if($_GET["returnview"]) $arUrl[2] = "returnview=".$_GET["returnview"];
            }
            return implode("&",$arUrl);
        }
    }//build_url_return
    
    private function build_url_extras($isByMvc=0)
    {
        $arGet = $_GET;
        //bugg();
        $arRemove = array("module","section","view","controller","partial","method"
                        ,"returnmodule","returnsection","returnview"
                        ,"returncontroller","returnpartial","returnmethod","page","selPage");
        //limpio los get
        foreach($arGet as $sParam=>$sValue)
            if(in_array($sParam,$arRemove))
                unset($arGet[$sParam]);
            
        if($this->isPermaLink)
        {
            return implode("/",$arGet);
        }
        //no permalink
        else
        {
            foreach($arGet as $sParam=>$sValue)
                $arUrl[] = "$sParam=$sValue";            
            return implode("&",$arUrl);
        }
    }//build_url_extras
    
    protected function load_get_urls($isByMvc=0)
    {
        $sUrlNoMethod = $this->build_url_nomethod($isByMvc);
        $sUrlReturn = $this->build_url_return($isByMvc);
        $sUrlExtras = $this->build_url_extras($isByMvc);
        
        //bug("urlnomethod:$sUrlNoMethod,urlreturn:$sUrlReturn,urlextras:$sUrlExtras");
        if($this->isPermaLink)
        {           
            $this->sUrlDelete = "/".$sUrlNoMethod."/delete/";
            $this->sUrlUpdate = "/".$sUrlNoMethod."/update/";
            $this->sUrlQuarantine = "/".$sUrlNoMethod."/quarantine/";
            $this->sUrlPaginate = "/".$sUrlNoMethod."/";
            
            if($sUrlReturn)
            {
                $this->sUrlDelete .= "$sUrlReturn/";
                $this->sUrlUpdate .= "$sUrlReturn/";
                $this->sUrlQuarantine .= "$sUrlReturn/";
                $this->sUrlPaginate .= "$sUrlReturn/";                
            }
            
            if($sUrlExtras)
            {
                $this->sUrlDelete .= "$sUrlExtras/";
                $this->sUrlUpdate .= "$sUrlExtras/";
                $this->sUrlQuarantine .= "$sUrlExtras/";
                $this->sUrlPaginate .= "$sUrlExtras/";                
            }
        }
        else
        {
            $this->sUrlDelete = "?".$sUrlNoMethod."&view=delete";
            $this->sUrlUpdate = "?".$sUrlNoMethod."&view=update";
            $this->sUrlQuarantine = "?".$sUrlNoMethod."&view=quarantine";
            $this->sUrlPaginate = "?".$sUrlNoMethod."&view=get_list";
            
            if($sUrlReturn)
            {
                $this->sUrlDelete .= "&$sUrlReturn";
                $this->sUrlUpdate .= "&$sUrlReturn";
                $this->sUrlQuarantine .= "&$sUrlReturn";
                $this->sUrlPaginate .= "&$sUrlReturn";                
            }
            
            if($sUrlExtras)
            {
                $this->sUrlDelete .= "&$sUrlExtras";
                $this->sUrlUpdate .= "&$sUrlExtras";
                $this->sUrlQuarantine .= "&$sUrlExtras";
                $this->sUrlPaginate .= "&$sUrlExtras";                
            }
        }
       //bug($this->sUrlPaginate,"urlpaginate");
    }//load_get_urls()
    
    /**
     * Si se está usando mergekeys devolvera pkeys ya que los controles check
     * se crearán con con ids pkeys_i, si no se usa mergekeys entonces se crean los 
     * controles con nombre tipo key1key2key3_0, key1key2key3_1 ... key1key2key3_n
     * @return string
     */
    protected function build_postkey()
    {
        $sCheckName = "";
        if($this->doMergePkeys)
            $sCheckName = "pkeys";
        else
            foreach($this->arKeyFields as $sFldKeyName)
               $sCheckName .=$sFldKeyName;
        return $sCheckName;
    }
    
    protected function lower_fieldnames(&$arRows)
    {
        $arLowered = array();
        foreach($arRows as $i=>$arRow)
        {
            $arTmpRow = array();
            foreach($arRow as $sFieldName=>$sValue)
            {    
                $sFieldName = strtolower($sFieldName);
                $arTmpRow[$sFieldName] = $sValue; 
            }
            $arLowered[$i] = $arTmpRow;
        }
        $arRows=$arLowered;
    }
    
    //**********************************
    //             SETS
    //**********************************
    protected function set_tmpjs($mxValues=NULL)
    {
        $this->arTmpJs=array();
        if(is_array($mxValues))
            $this->arTmpJs = $mxValues;
        elseif($mxValues)
            $this->arTmpJs[] = $mxValues;
    }
    
    protected function add_tmpjs($sJsString)
    {
        if($sJsString!==NULL)
            $this->arTmpJs[] = $sJsString;
    }
    
    public function set_column_detail($isOn=true){$this->isToDetail=$isOn;}
    public function set_column_delete($isOn=true){$this->isDeleteSingle=$isOn;}
    public function set_column_quarantine($isOn=true){$this->isQuarantineSingle=$isOn;}
    public function set_column_pickmultiple($isOn=true){$this->isPickMultiple=$isOn;}
    public function merge_pks($isOn=true,$sGlue=","){$this->doMergePkeys=$isOn; $this->sMergeGlue=$sGlue;}
    public function set_column_picksingle($isOn=true){$this->isPickSingle=$isOn;}    
    public function set_keyfields($arKeyFields){$this->arKeyFields=$arKeyFields;}
    public function set_orderby($arFieldNames){$this->arOrderBy=$arFieldNames;}
    public function set_orderby_type($arOrderWay){$this->arOrderWay=$arOrderWay;}
    public function set_url_delete($sUrl){$this->sUrlDelete=$sUrl;}
    public function set_url_quarantine($sUrl){$this->sUrlQuarantine=$sUrl;}
    public function set_url_update($sUrl){$this->sUrlUpdate=$sUrl;}
    public function set_url_paginate($sUrl){$this->sUrlPaginate=$sUrl;}
    public function set_url_picksingle($sUrl){$this->sUrlPickSingle=$sUrl;}
    
    /**
     * Asigna el módulo y aplica las urls
     * @param string $sModule Modulo que gestiona el listado
     * cuando se usa esto??? deprecated por inclusion de load_get_urls()
     * Esto me rompe la paginación en las pestañas foreign
     */
    public function set_module($sModule)
    {
        $this->sModule=$sModule;
        if($this->sModule)
        {
            if($this->isPermaLink)
            {
                if(!$this->sUrlDelete) $this->sUrlDelete="/$this->sModule/delete";
                if(!$this->sUrlQuarantine) $this->sUrlQuarantine="/$this->sModule/quarantine";
                if(!$this->sUrlUpdate) $this->sUrlUpdate="/$this->sModule/update";   
                $this->sUrlPaginate="/$this->sModule/";                  
            }
            else
            {
                if(!$this->sUrlDelete) $this->sUrlDelete="?module=$this->sModule&view=delete";
                if(!$this->sUrlQuarantine) $this->sUrlQuarantine="?module=$this->sModule&view=quarantine";
                if(!$this->sUrlUpdate) $this->sUrlUpdate="?module=$this->sModule&view=update";   
                $this->sUrlPaginate="?module=$this->sModule&view=get_list";        
            }
        }
    }//set_module
    
    public function set_current_page($iNumPage){$this->iInfoCurrentPage=$iNumPage;}
    public function set_next_page($iNumPage){$this->iInfoNextPage=$iNumPage;}
    public function set_previous_page($iNumPage){$this->iInfoPreviousPage=$iNumPage;}
    public function set_total_regs($iNumRegs){$this->iInfoNumRegs=$iNumRegs;}
    public function set_total_pages($iNumPages){$this->iInfoNumPages=$iNumPages;}
    public function set_first_page($iNumFirstPage){$this->iInfoFirstPage=$iNumFirstPage;}
    public function set_last_page($iNumLastPage){$this->iInfoLastPage=$iNumLastPage;}
    public function set_fields($arObjFields){$this->arObjFields=$arObjFields;}
    public function is_ordenable($isOn=true){$this->isOrdenable=$isOn;}
    public function set_check_onrowclick($isOn=true){$this->isCheckOnRowclick=$isOn;}
    
    /**
     * Datos que se desean pasar al popup por la url.
     * @param array $arData = array("keys"=>array("fieldname"=>value...),"extras"=>array("key"=>value..));
     */
    public function set_multiassign($arData){$this->arMultiAssign=$arData;}  
    public function set_singleassign($arData){$this->arSingleAssign=$arData;}
    
    public function set_multiadd($arData){$this->arMultiAdd=$arData;}  
    /**
     * @param array $arData array("destkey"=>"","destdesc"=>"","keys"=>",","descs"=>",");
     */
    public function set_singleadd($arData){$this->arSingleAdd=$arData;}
    /**
     * format: ("date","datetime4","datetime6","time4","time6","int","numeric2")
     * @param array $arFormat array("fieldname"=>"format"...)
     */
    public function set_format_columns($arFormat){$this->arConfigColTypes = $arFormat;}
    
    /**
     * @param array $arData array(0=>array("position"=>1,"label"=>"xxx","type"=>"anchor","href"=>"localhost/","hrefparams"=>array("col1","col2",)))
     */
    public function add_extra_colums($arData){$this->arExtraColumns = $arData;}

    /**
     * @param array $arColumns array("fieldname1","fieldname2"..)
     */
    public function set_column_hidden($arColumns){$this->arHiddenColumns = $arColumns;}
    
    /**
     * 
     * @param array $arColumns array("fieldname"=>"value","f.."=>"val2"..))
     */
    public function set_extra_hidden($arColumns){$this->arExtraHidden = $arColumns;}
    
    public function set_items_per_page($iItemsPerPage){$this->iItemsPerPage = $iItemsPerPage;}
    
    /**
     * Disable pagination controls
     * @param type $isOn
     */
    public function set_no_paginatebar($isOn=FALSE){$this->isPaginateBar=$isOn;}
    //**********************************
    //             GETS
    //**********************************
    protected function get_tmpjs($cGlue="\n"){return implode($cGlue,$this->arTmpJs);}
    
}