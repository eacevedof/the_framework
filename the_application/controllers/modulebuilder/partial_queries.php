<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.4
 * @name PartialQueries
 * @file partial_queries.php 
 * @date 24-10-2014 13:52 (SPAIN)
 * @observations:
 * @requires: 
 */
import_apptranslate("modulebuilder");
import_controller("modulebuilder","modulebuilder");
//bugif();
class PartialQueries extends ControllerModuleBuilder
{
    public function __construct()
    {
        $this->sModuleName = "modulebuilder";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        
        if($this->is_post("selTable")) 
        {    
            $this->sTableName = $this->get_post("selTable");
            $this->load_attribs();
        }
        
    }//__construct()

//<editor-fold defaultstate="collapsed" desc="PARSESQL">
    protected function clean_sql(&$sSQL)
    {
        $sSQL = trim($sSQL);
        $sSQL = str_replace("\n"," ",$sSQL);
        $sSQL = str_replace("\t"," ",$sSQL);
        $sSQL = str_replace("\r"," ",$sSQL);
        $sSQL = str_replace(", ",",",$sSQL);
        $sSQL = str_replace(" ,",",",$sSQL);
        
        //ESPACIOS
        for($i=0;$i<10;$i++)
            $sSQL = str_replace("  "," ",$sSQL);
        
        return $sSQL;
    }
    
    protected function parse_sql($sSQL)
    {
        //$sToParse = $sSQL;
        $this->clean_sql($sSQL);
        
        $arReplace = array
        (
            ","=>"\n,"
            ,"*/"=>"*/\n"
            ," UNION ALL "=>"\n\nUNION ALL\n\n"
            ," UNION "=>"\n\nUNION\n\n"
            ," SELECT "=>"\nSELECT\n"
            ,"SELECT "=>"SELECT\n"
            ,"DISTINCT "=>"DISTINCT "
            ," TOP "=>" TOP "
            ,"COUNT("=>"COUNT("
            ," ORDER BY "=>"\nORDER BY "
            ," HAVING "=>"\nHAVING "
            ," GROUP BY "=>"\nGROUP BY "
            ," INNER "=>"\nINNER "
            ," LEFT "=>"\nLEFT "
            ," OUTER "=>" OUTER "
            ," JOIN "=>" JOIN "
            ," CASE "=>" CASE "
            ," WHEN "=>" WHEN "
            ," THEN "=>" THEN "
            ," EXISTS "=>" EXSITS "
            ,"END "=>" END "
            ,"INSERT INTO "=>"INSERT INTO "
            ,"UPDATE "=>"UPDATE "
            ,"SET "=>"SET "
            ," VALUES "=>"\nVALUES\n"
            ," IN "=>" IN "
            ," AS "=>" AS "
            ," ON "=>"\nON "
            ," AND "=>"\nAND "
            ," OR "=>"\nOR "
            ," WHERE "=>"\nWHERE "
            ," IS NULL"=>" IS NULL"
            ," FROM "=>"\n\nFROM "
            ,"FROM ("=>"FROM \n("
            ,") AS "=>"\n) AS "
            ," ASC"=>" ASC"
            ," DESC"=>" DESC"
            
        );
        foreach($arReplace as $sString=>$sReplace)
            $sSQL = str_replace($sString,$sReplace,$sSQL);

        unset($arReplace[","]);unset($arReplace["*/"]);
        //minusculas
        foreach($arReplace as $sString=>$sReplace)
        {
            $sString = strtolower($sString);
            $sSQL = str_replace($sString,$sReplace,$sSQL);
        }
        return $sSQL;
   }
    
    protected function build_parsesql_opbuttons()
    {
        //pr("hi");
        $arButTabs = array();
        //$arButTabs["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_list);
        $arButTabs["exp"]=array("href"=>$this->build_url($this->sModuleName,"queries","exp"),"icon"=>"awe-search","innerhtml"=>exportar);
        $arButTabs["imp"]=array("href"=>$this->build_url($this->sModuleName,"queries","imp"),"icon"=>"awe-search","innerhtml"=>importar);
        return $arButTabs;
    }//build_parsesql_opbuttons()

    protected function build_parsesql_form($usePost=0)
    {
        $oForm = new HelperForm("frmParseSql");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_parsesql_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_parsesql_form()
    
    protected function build_parsesql_fields()
    {   //bugpg();
        //bug($arFields);die;
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_mdbqry_entity.tr_mdbqry_parse_tool);
        //txaText
        $oAuxField = new HelperTextarea("txaText","txaText");
        $oAuxField->add_class("input-large");
        $oAuxField->set_innerhtml($this->get_post("txaText"));
        //$oAuxField->set_js_keypress("postback(this);");
        $oAuxField->on_entersubmit();
        $oAuxLabel = new HelperLabel("txaText",tr_mdbqry_parse_text,"lblText");
        $oAuxField->add_style("width:800px; height:200px;");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //txaParsed
        $oAuxField = new HelperTextarea("txaParsed","txaParsed");
        $oAuxField->add_style("width:800px; height:200px; background-color:#ddd;");
        $oAuxField->readonly();
        $oAuxField->set_innerhtml($this->get_post("txaParsed"));
        $oAuxLabel = new HelperLabel("txaParsed",tr_mdbqry_parsed_text,"lblParsed");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        $oAuxField = new HelperButtonBasic("butSave",tr_mdbqry_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("postback(this);");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
        
        //Accion
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        //Postback
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;
       
        return $arFields;
    }//build_parsesql_fields()
    
    public function parsesql()
    {
        $sTextToParse = $this->get_post("txaText");
        $sParsedText = $this->parse_sql($sTextToParse);
        //bug($sParsedText);
        $this->set_post("txaParsed",$sParsedText);
        $oForm = $this->build_parsesql_form();       
        $oJavascript = new HelperJavascript();
        $oJavascript->set_formid("frmParseSql");

        //bug($oForm); die;
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($this->build_parsesql_opbuttons());

        //bug($oForm); die;
        $this->oView->add_var($oOpButtons,"oOpButtons");        
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->set_path_view("modulebuilder/view_parsesql");
        $this->oView->show_page();
    }//parsesql()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="QUERY BUILDER">
    protected function build_query_opbuttons()
    {
        $arButTabs = array();
        $arButTabs["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_list);
        return $arButTabs;
    }//build_query_opbuttons()

    protected function build_query_form($usePost=0)
    {
        $oForm = new HelperForm("frmInsert");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_query_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_query_form()
    
    protected function build_query_fields($usePost=0)
    {   //bugpg();
        //bug($arFields);die;
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead("Build Query");
               
        $oBehavBuilder = new AppBehaviourModuleBuilder();
        $arTables = $oBehavBuilder->get_physic_tables();

        
        $oAuxField = new HelperSelect($arTables,"selTable","selTable");
        $oAuxField->add_class("input-large");
        $oAuxField->is_primarykey();
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selTable"));
        $oAuxLabel = new HelperLabel("selTable","Table/View","lblModel");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
      
        $arQueries = array("insert"=>"Insert","select"=>"Select");
        $oAuxField = new HelperSelect($arQueries,"selTypeQuery","selTypeQuery");
        $oAuxField->add_class("input-large");
        $oAuxField->is_primarykey();
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selTypeQuery"));
        $oAuxLabel = new HelperLabel("selTypeQuery",tr_type_query,"lblTypeQuery");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
       
        if($this->get_post("txaText")!="")
        {    
            $oAuxField = new HelperTextarea("txaText","txaText");
            $oAuxField->add_class("input-large");
            $oAuxField->set_innerhtml($this->get_post("txaText"));
            $oAuxLabel = new HelperLabel("txaText",tr_mdbqry_parse_text,"lblText");
            $oAuxField->add_style("width:1000px; height:1000px;");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        }
        
        $oAuxField = new HelperButtonBasic("butSave",tr_ins_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("insert();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
               
        //Accion
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;
       
        return $arFields;
    }//build_query_fields()
    
    
    protected function build_query()
    {
        //$sTableName = $this->get_post("selTable");
        
        $oBehavBuilder = new AppBehaviourModuleBuilder($this->sTableName);
        $this->arFields = $oBehavBuilder->get_fields_and_types_for_model();
        //separa los tipos campos y longitudes en los atributos tipo array creados para este fin
        $this->load_fieldtype($this->arFields);
        //bug($this->sTableName);
        $arRows = $oBehavBuilder->get_select_all();
        
        $arLines = array();
        foreach($arRows as $arRow)
        {
            $sStringSQL = "INSERT INTO [$this->sTableName]";
            $sStringSQL .= "(";
            $sStringSQL .= implode(",",$this->arFields);
            $sStringSQL .= ")";
            $sStringSQL .= "VALUES(";
            $arValues = array();
            foreach($arRow as $sFieldName=>$sFieldValue)
            {
                $arValues[] = "'$sFieldValue'";
            }
            $sStringSQL .= implode(",",$arValues);
            $sStringSQL .= ");";
            $arLines[] = $sStringSQL;
        }
        $sStringSQL = implode("\n",$arLines);
        $this->set_post("txaText",$sStringSQL);
        //bugp();
        //bug($this->arFields);
    }//build_query()
    
    public function query()
    {
        $arFieldsConfig = array();
        $arFieldsConfig["selTable"] = array("id"=>"selTable","label"=>tr_table,"length"=>100,"type"=>array("required"));
        //$arFieldsConfig[] = array("id"=>"selTypeQuery","label"=>tr_type_query,"length"=>100,"type"=>array("required"));

        if($this->is_inserting())
        {
            //array de configuracion length=>i,type=>array("numeric","required")
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
           
            $arFieldsValues = $this->get_fields_from_post();
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            //$arErrData = $oValidate->get_error_field();
            //bug($arErrData); die;
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_module_not_built);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            //no error
            else
            {
                $this->build_query();
                $arErrData = 1;
            }//no error
        }//fin if post action=save

        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_query_form(1);
        else $oForm = $this->build_query_form();
        //bug($oForm); die;
       
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_focusid("id_all");
       
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($this->build_query_opbuttons());

        //bug($oForm); die;
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//insert()
    
//</editor-fold>
    
    public function get_list() 
    {
        echo "get_list";
    }
    
    public function exp()
    {
        //para ejecutar exes: configurar en php.ini
        //safe_mode = On
        //safe_mode_exec_dir = C:\inetpub\wwwroot\proy_storck\scripts
        //$oJavascript = new HelperJavascript();
        //errorson();
        //C:\xampp\htdocs\proy_storck\scripts\export_ppc.cmd
        $sPathCmd = "C:\\inetpub\\wwwroot\\proy_storck\\scripts\\export_ppc.cmd";
        
        //bugfile($sPathCmd);
        $ar1 = array();
        $s = "";
        //exec("echo hola",$ar1,$s);
        //exec("echo hola");
        //$output = shell_exec($sPathCmd);
        //pr($ar1); pr("s: ".$s);
        //pr($output);
        $this->add_alert("Exp lanzada");
        $this->go_to_module($this->get_current_module(),"queries","parsesql");
    }
    
    public function imp()
    {
        errorson();
        //phpinfo();
        //ini_set($varname, $newvalue)
        //$oJavascript = new HelperJavascript();
        //errorson();
        //C:\xampp\htdocs\proy_storck\scripts\export_ppc.cmd
        $sPathCmd = "cmd /c C:\\Program Files (x86)\\PHP\\safedir\\import_ppc.cmd";
        //$sPathCmd = "\"C:\Program Files (x86)\PHP\safedir\import_ppc.bat\"";
        //$sPathCmd = "C:\\Program Files (x86)\\PHP\\safedir\\import_ppc.bat";
        
        $cmd = "C:/Program Files (x86)/PHP/safedir/hi.bat";
        $cmd = "C:/\"Program Files (x86)\"/PHP/safedir/hi.bat";
        $cmd = "C:\\\"Program Files (x86)\"\\PHP\\safedir\\hi.bat";
        $cmd = "C:\Program Files (x86)\PHP\safedir\hi.bat";
        $cmd = "cmd /C C:\\Program Files (x86)\\PHP\\safedir\\hi.bat";
        $cmd = "C:Program Files (x86)\\PHP\\safedir\\hi.bat";
        
        $cmd = "cmd /C dir /S %windir%";
        
        $cmd = "C:\windows\system32\cmd.exe /c C:\Program Files (x86)\PHP\safedir\hi.bat";
        
        $cmd = "hi.bat";
        
        $cmd = "start /B C:/Program Files (x86)/PHP/safedir/hi.bat";
        $cmd = "cmd /C C:/Program Files (x86)/PHP/safedir/hi.bat";
        $cmd = "cd C:/Program Files (x86)/PHP/safedir/ hi.bat";
        
        $cmd = "C:\\Program Files (x86)\\PHP\\safedir\\hi.bat";
        //system($cmd);
        echo get_current_user();
        $cmd = 'hi.bat';
        $abs = realpath($cmd);
        pr($abs);
        exec($abs, $output2);
        echo var_dump($output2);die;
        
  //      $WshShell = new COM("WScript.Shell");
//$oExec = $WshShell->Run($cmd, 0, false);
        //$cmd = "dir *.*";
        //$cmd = "calc.exe";
        //$cmd = "ping google.com";
        //exec("ping google.com", $oExec, $return);
        //$oExec = shell_exec("ping google.com");
        //$oExec = shell_exec($cmd);
        $oExec = exec($cmd);
        //$oExec = system($cmd,$oExec);
        //exec("import_ppc.bat",$oExec,$return);
        //exec($sPathCmd,$oExec,$return);
        $oExec = var_export($oExec,1);
        //$output = shell_exec("cmd.exe /c ipconfig -all");
        //pr($ar1); pr($s);
        //bug($oExec);die;
          //$disabled = explode(', ', ini_get('disable_functions'));
          //$disabled = ini_get('disable_functions');
  
        $this->add_alert("Import lanzada [output:$oExec] ");
        //$this->go_to_module($this->get_current_module(),  $this->get_current_section(),"parsesql");
        
    }    
}
