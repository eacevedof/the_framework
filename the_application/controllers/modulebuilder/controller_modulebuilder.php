<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.4
 * @name ControllerModuleBuilder
 * @file controller_modulebuilder.php 
 * @date 25-10-2014 12:52 (SPAIN)
 * @observations:
 * @requires: 
 */
import_component("page,validate,filter");
import_helper("select,form,form_fieldset,form_legend,input_text,textarea,
    label,anchor,table,table_typed,input_password,button_basic,raw,div,javascript,image,imagelist");
import_appmain("controller,view,behaviour");
//import_model("user");//Hay un conflicto usernodb
import_model("usernodb");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");
import_appbehaviour("picklist,modulebuilder");
//include_once 'theapplication_controller.php';
class ControllerModuleBuilder extends TheApplicationController
{
    protected $sTableName;
    protected $sTableAcronim;//as translation prefix
    protected $arNumeric = array("int","real","money","float","decimal","numeric");
    protected $arNotInsert = "insert_platform,insert_user,insert_date,update_platform,update_user,update_date,delete_platform,delete_user,delete_date,is_enabled,cru_csvnote,i";
    protected $arNotUpdate = "insert_platform,insert_user,insert_date,update_platform,update_user,update_date,delete_platform,delete_user,delete_date,is_enabled,cru_csvnote,i";
    protected $arNotFilter = "insert_platform,insert_user,insert_date,update_platform,update_user,update_date,delete_platform,delete_user,delete_date,is_enabled,cru_csvnote,i";
    protected $arNotColumn = "insert_platform,insert_user,insert_date,update_platform,update_user,update_date,delete_platform,delete_user,delete_date,is_enabled,cru_csvnote,i";
    protected $arFields = array();
    protected $arDecimalTypes = array("real","float","numeric");//"int"
    protected $arFieldsComment = array("code_erp","description");
    protected $arType = array();
    protected $arLength = array();
    protected $arPks = array("id");
    protected $arTranslation;
    
    protected $oFile;
    protected $sTimeNow;//$this->sTimeNow = date("d-m-Y H:i")." (SPAIN)";

    protected $sModelClassName;
    protected $sModelFileName;
    protected $sModelName;
    protected $sModelObjectName;
    
    protected $sControllerName;
    protected $sControllerClassName;
    protected $sControllerFileName;
    
    protected $sTranslatePrefix;
    
    public function __construct()
    {
        //errorson();
        $this->sModuleName = "modulebuilder";
        $this->sTrLabelPrefix = "tr_mdb_";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->load_pagetitle();
        $this->oFile = new ComponentFile("windows");
        $this->arNotColumn = explode(",",$this->arNotColumn);
        $this->arNotInsert = explode(",",$this->arNotInsert);
        $this->arNotFilter = explode(",",$this->arNotFilter);
        $this->arNotUpdate = explode(",",$this->arNotUpdate);
        if($this->is_post("selTable")) 
        {    
            $this->sTableName = $this->get_post("selTable");
            $this->load_attribs();
        }        
    }//__construct()

    protected function load_attribs()
    {
        $this->sTimeNow = date("d-m-Y H:i")." (SPAIN)";
        
        $this->sModelClassName = $this->table_to_classname();
        $this->sModelFileName = $this->table_to_filename();
       
        $this->sModelName = str_replace("Model","",$this->sModelClassName);
        $this->sModelObjectName = "o$this->sModelName";
        
        $this->sControllerClassName = $this->table_to_classname("Controller","c");
        $this->sControllerName = $this->table_to_controllername();
        $this->sControllerFileName = $this->table_to_filename("controller","c");
        
        //var_dump($this->sTimeNow,$this->sModelClassName,$this->sModelFileName,$this->sControllerClassName,$this->sControllerName,$this->sControllerFileName);
    }
    
    protected function load_fieldtype($arFields)
    {
        $this->arFields = array();
        $this->arType = array();
        $this->arLength = array();
        
        foreach($arFields as $sFieldName=>$arData)
        {    
            $sType = array_keys($arData);
            $sType = $sType[0];
            $sLength = array_values($arData);
            $sLength = $sLength[0];
            $this->arFields[] = $sFieldName;
            $this->arType[] = $sType;
            $this->arLength[] = $sLength;
        }
    }//load_field_type($arFields)
    
    protected function table_to_filename($sFileName="model",$sType="m")
    {
        $arTablePrefix = array("base","app","");
        $arNamePieces = explode("_",$this->sTableName);
        //si hay prefijo, hay que quitarlo
        if(in_array($arNamePieces[0],$arTablePrefix))
            unset($arNamePieces[0]);

        if($sType=="c")//controller: controller_part1part2partn
            $sFileName = $sFileName."_".implode("",$arNamePieces);
        else//model: model_part1_part2_partn
            $sFileName = $sFileName."_".implode("_",$arNamePieces);
        $sFileName = strtolower($sFileName);
        
        //Para el nombre del controlador se añade una s al final
        if($sType=="c")
            if(!is_lastchar($sFileName,"s")) 
                $sFileName.="s";
            
        return $sFileName.".php";  
    }//table_to_filename($sFileName="model")

    protected function table_to_foldername()
    {
        $arTablePrefix = array("base","app","");
        $sFileName = $this->clean_prefix($this->sTableName,$arTablePrefix);
        $sFileName = str_replace("_","",$sFileName);
        if(!is_lastchar($sFileName,"s")) $sFileName.="s";
        return strtolower($sFileName);  
    }//table_to_foldername()

    protected function table_to_controllername()
    {
        $arTablePrefix = array("base","app","");
        $sModuleName = $this->clean_prefix($this->sTableName,$arTablePrefix);
        $sModuleName = str_replace("_","",$sModuleName);
        if(!is_lastchar($sModuleName,"s")) $sModuleName.="s";
        return strtolower($sModuleName);        
    }//table_to_controllername()
    
    /**
     * A partir de $this->sTableName se crea un nombre de clase. Puede ser Modelo o Controlador
     * @param string $sPrefix Lo que ira como parte inicial del nombre
     * @return string Nombre de la clase tipo: PrefixTablenamecamelcased
     */
    protected function table_to_classname($sPrefix="Model",$sType="m")
    {
        $arTablePrefix = array("base","app","");
        $arNamePieces = explode("_",$this->sTableName);

        $sClassName = "";
        if($sPrefix) $sClassName = $sPrefix;
        //si hay prefijo, hay que quitarlo
        if(in_array($arNamePieces[0],$arTablePrefix))
            unset($arNamePieces[0]);

        foreach($arNamePieces as $sPiece)
        {
            $sPiece = strtolower($sPiece);
            $sClassName .= ucfirst($sPiece);
        }
        if($sType=="c")
            if(!is_lastchar($sClassName,"s"))
                    $sClassName .= "s";
            
        return $sClassName;
    }//table_to_classname($sPrefix="Model",$sType="m")
    
    protected function remove_prefix($sFileName,$sPrefix="controller",$sSeparator="_")
    {
        $arNamePieces = explode($sSeparator,$sFileName);
        if($arNamePieces[0]==$sPrefix) unset($arNamePieces[0]);
        return implode("_",$arNamePieces);
    }//remove_prefix($sFileName,$sPrefix="controller",$sSeparator="_")
    
    protected function clean_prefix($sText,$mxPrefix="",$sDelimiter="_")
    {
        $arTextPieces = explode($sDelimiter,$sText);
        $arNoPrefix = array();
        //array
        if(is_array($mxPrefix))
        {
            foreach($arTextPieces as $sPiece)
                if(!in_array($sPiece,$mxPrefix))
                    $arNoPrefix[] = $sPiece;
        }           
        //string
        else
        {
            //si es un string separado por comas
            if(strstr($mxPrefix,","))
                $mxPrefix = explode(",",$mxPrefix);
            if(is_array($mxPrefix))
            {
                foreach($arTextPieces as $sPiece)
                    if(!in_array($sPiece,$mxPrefix))
                        $arNoPrefix[] = $sPiece;
            }
            //simple string. 1 prefijo
            else
            {
                foreach($arTextPieces as $sPiece)
                    if($sPiece!=$mxPrefix)
                        $arNoPrefix[] = $sPiece;
            }
        }
        return implode($sDelimiter,$arNoPrefix);        
    }//clean_prefix($sText,$mxPrefix="",$sDelimiter="_")
    
    protected function fieldname_to_objectname($sFieldName)
    {
        $sObjectName = $sFieldName;
        if($this->is_foreignkey($sFieldName))//tiene id_
           $sObjectName = str_replace("id_","",$sFieldName);
        $sObjectName = sep_to_camel($sObjectName);
        $sObjectName = "o".$sObjectName;
        return $sObjectName;
    }
    
    protected function fieldname_to_modelname($sFieldName)
    {
        $sModelName = "";
        if($this->is_foreignkey($sFieldName))//tiene id_
        {   
            if(strstr($sFieldName,"id_type_"))
            {
                $sModelName = $this->sModelClassName."Array";
            }
            else
            {
                $sModelName = str_replace("id_","","$sFieldName");
                $sModelName = "Model".sep_to_camel($sModelName);
            }
        }
        return $sModelName;
    }
    
    protected function fieldname_to_entityname($sFieldName)
    {
        $sEntityName = "";
        if($this->is_foreignkey($sFieldName))//tiene id_
        {   
            if(strstr($sFieldName,"id_type_"))
            {
                $sEntityName = str_replace("id_type_","","$sFieldName");
            }
            else
            {
                $sEntityName = str_replace("id_","",$sFieldName);
                $sEntityName = str_replace("_","",$sEntityName);
            }
        }
        return $sEntityName;
    }
    
    protected function load_table_acronym()
    {
        $this->sTableAcronim = "";
        $arTableParts = explode("_",$this->sTableName);
        if(in_array($arTableParts[0],array("app","base")))
            unset($arTableParts[0]);
        
        $arTableParts = implode("_",$arTableParts);
        $arTableParts = explode("_",$arTableParts);
        
        $this->sTableAcronim = get_firstchar($arTableParts[0]).get_lastchar($arTableParts[0]).get_firstchar($arTableParts[1]);
    }
     
    protected function get_import_models()
    {
        $arImport = array();
        $sImport = str_replace("model_","",$this->sModelFileName);
        $sImport = str_replace(".php","",$sImport); 
        $arImport[] = $sImport;
        foreach($this->arFields as $sFieldName)
            if($this->is_foreignkey($sFieldName) && !strstr($sFieldName,"user"))
            {
                $sImport = str_replace("id_","",$sFieldName);
                $arImport[] = $sImport;
            }
        return implode(",",$arImport);
    }
//<editor-fold defaultstate="collapsed" desc="BUILD TRANSLATION">
    protected function load_translate_prefix()
    {
        $this->sTranslatePrefix = "tr_mdb_".$this->sTableAcronim."_";
    }
    
    protected function load_extra_translate()
    {
        $arExtra = array
        (
            "entity_controller","entity_module","entities","entity","entity_new","entity_of","entities_of"
        );
        
        foreach ($arExtra as $sExtra)
            $this->arTranslation[] = $this->sTranslatePrefix.$sExtra;
    }
    
    protected function translate_add_by_type(&$arLines,$sType="entity")
    {
        $arTrToUnset = array();
        if($sType=="entity")
        {
            $arLines[] = "//==================";
            $arLines[] = "//    ENTITY";
            $arLines[] = "//==================";
            foreach($this->arTranslation as $i=>$sTransConst)
                if(strstr($sTransConst,$this->sTranslatePrefix."enti"))
                {    
                    //$sNoPrefix = str_replace("_col","_",$sTransConst);//insert
                    $arLines[] = "define(\"$sTransConst\",\"\");";
                    $arTrToUnset[] = $i;
                }            
        }
        elseif($sType=="insert")
        {    
            $arLines[] = "//==================";
            $arLines[] = "//    INSERT";
            $arLines[] = "//==================";
            foreach($this->arTranslation as $i=>$sTransConst)
                if(strstr($sTransConst,$this->sTranslatePrefix."ins"))
                {    
                    $sNoPrefix = str_replace("_ins","_",$sTransConst);//insert
                    $sNoPrefix = str_replace("__","_",$sNoPrefix);
                    $arLines[] = "define(\"$sTransConst\",\"$sNoPrefix\");";  
                    $arTrToUnset[] = $i;
                }                        
        }
        elseif($sType=="update") 
        {
            $arLines[] = "//==================";
            $arLines[] = "//    UPDATE";
            $arLines[] = "//==================";
            foreach($this->arTranslation as $i=>$sTransConst)
                if(strstr($sTransConst,$this->sTranslatePrefix."upd"))
                {    
                    $sNoPrefix = str_replace("_upd","_",$sTransConst);//insert
                    $sNoPrefix = str_replace("__","_",$sNoPrefix);
                    $arLines[] = "define(\"$sTransConst\",\"$sNoPrefix\");";
                    $arTrToUnset[] = $i;
                }           
        }
        elseif($sType=="list") 
        {
            $arLines[] = "//==================";
            $arLines[] = "//      LIST";
            $arLines[] = "//==================";
            foreach($this->arTranslation as $i=>$sTransConst)
                if(strstr($sTransConst,$this->sTranslatePrefix."list"))
                {    
                    $sNoPrefix = str_replace("_list","_",$sTransConst);//insert
                    $sNoPrefix = str_replace("__","_",$sNoPrefix);
                    $arLines[] = "define(\"$sTransConst\",\"$sNoPrefix\");";
                    $arTrToUnset[] = $i;
                }           
        }        
        elseif($sType=="filters") 
        {
            $arLines[] = "//==================";
            $arLines[] = "//    FILTERS";
            $arLines[] = "//==================";
            foreach($this->arTranslation as $i=>$sTransConst)
                if(strstr($sTransConst,$this->sTranslatePrefix."fil"))
                {    
                    $sNoPrefix = str_replace("_fil","_",$sTransConst);//insert
                    $sNoPrefix = str_replace("__","_",$sNoPrefix);
                    $arLines[] = "define(\"$sTransConst\",\"$sNoPrefix\");";
                    $arTrToUnset[] = $i;
                }           
        }
        elseif($sType=="columns") 
        {
            $arLines[] = "//==================";
            $arLines[] = "//    COLUMNS";
            $arLines[] = "//==================";
            foreach($this->arTranslation as $i=>$sTransConst)
                if(strstr($sTransConst,$this->sTranslatePrefix."col"))
                {    
                    $sNoPrefix = str_replace("_col","_",$sTransConst);//insert
                    $sNoPrefix = str_replace("__","_",$sNoPrefix);
                    $arLines[] = "define(\"$sTransConst\",\"$sNoPrefix\");";
                    $arTrToUnset[] = $i;
                }           
        }
        else
        {
            $arLines[] = "//==================";
            $arLines[] = "//    EXTRA";
            $arLines[] = "//==================";
            foreach($this->arTranslation as $i=>$sTransConst)
                $arLines[] = "define(\"$sTransConst\",\"\");";  
              
        }
        
        foreach($arTrToUnset as $iPos)
            unset($this->arTranslation[$iPos]);
        
    }
    
    protected function get_translation_content()
    {
        $arLines = array();
        $arLines[] = "<?php";
        $arLines[] = "/**
 * @author Module Builder 1.1.4
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name Module $this->sControllerName - English Translation
 * @file translate_$this->sControllerName.php    
 * @date $this->sTimeNow
 * @observations: 
 * @requires:
 */";
        $this->translate_add_by_type($arLines,"entity");
        $this->translate_add_by_type($arLines,"insert");
        $this->translate_add_by_type($arLines,"update");
        $this->translate_add_by_type($arLines,"list");
        $this->translate_add_by_type($arLines,"filters");
        $this->translate_add_by_type($arLines,"columns");
        $this->translate_add_by_type($arLines,"rest");
        $sContent = implode("\n",$arLines);
        return $sContent;
    }//get_translation_content($sModuleName)
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="BUILD MODEL">
    protected function model_add_extra_fields(&$arLines)
    {
        foreach($this->arFields as $sFieldName)
        {
            if(!$this->is_foreignkey($sFieldName))
            {        
                $sFieldName = $this->remove_prefix($sFieldName,"id");
                $arLines[] = "\t\t//\$this->arFieldsMappingExtra[\"$sFieldName\"] = \"$sFieldName.description\";";
            }
        }        
    }

    protected function model_add_attribs(&$arLines)
    {
        $arAttribsCreated = array();
        //pr($this->arFields);
        foreach($this->arFields as $i=>$sFieldName)
        {
            $sFieldType = $this->arType[$i];
            $sFieldLen = $this->arLength[$i];
            //atributos simples
            $arLines[] = "\tprotected $"."_$sFieldName; //$sFieldType($sFieldLen)";

            if($this->is_foreignkey($sFieldName))
            {
                if(strstr($sFieldName,"id_type_"))
                    $sNoPrefix = $this->sModelObjectName."Array";
                else 
                {
                    $sNoPrefix = $this->remove_prefix($sFieldName,"id");
                    $sNoPrefix = "o".sep_to_camel($sNoPrefix);                  
                }
                if(!in_array($sNoPrefix,$arAttribsCreated))
                {
                    $arLines[] = "\tprotected \$$sNoPrefix; //Model Object";    
                    $arAttribsCreated[] = $sNoPrefix;
                }
            }
        }        
    }
    
    protected function model_add_construct_params(&$arLines)
    {
        //$iVarsPerLine=5;
        //$iVars=0; TODO
        //para cada linea if((iTempLine+1)%$iVarsPerLine)==1) salta otra linea 
        
        $arTmpLine = array();
        foreach($this->arPks as $sFieldName)
            $arTmpLine[] = "$"."$sFieldName=NULL";
        
        foreach($this->arFields as $sFieldName)
            $arTmpLine[] = "$"."$sFieldName=NULL";
        
        $arLines[] = "(".implode(",",$arTmpLine).")";
    }
    
    protected function model_add_construct_assign(&$arLines)
    {
        $arLines[] = "\t\tparent::__construct(\"$this->sTableName\");";
        
        foreach($this->arPks as $sFieldName)
            $arLines[] = "\t\tif(\$$sFieldName!=NULL) \$this->_$sFieldName = \$$sFieldName;";
        
        foreach($this->arFields as $sFieldName)
            $arLines[] = "\t\tif($".$sFieldName."!=NULL) \$this->_$sFieldName = \$$sFieldName;";
        
        $arLines[] = "\t\t//\$this->arDescConfig = array(\"id\",\"$sFieldName\",\"separator\"=>\" - \");";        
    }
    
    protected function model_add_func_insert(&$arLines)
    {
        foreach($this->arFields as $sFieldName)
        {
            $sFieldType = $this->get_field_type($sFieldName);
            if(in_array($sFieldType,$this->arNumeric))
                $arLines[] = "\t\t\$$sFieldName = mssqlclean(\$this->_$sFieldName,1);";
            else
                $arLines[] = "\t\t\$$sFieldName = mssqlclean(\$this->_$sFieldName);";
        }
        $arLines[] = "";
        $arLines[] = "\t\t\$sSQL = \"INSERT INTO \$this->_table_name";
        $arLines[] = "\t\t(".implode(",",$this->arFields).")";
        $arLines[] = "\t\tVALUES";
        
        $arTmpLine = array();
        foreach($this->arFields as $sFieldName)
        {
            $sFieldType = $this->get_field_type($sFieldName);
            if(in_array($sFieldType,$this->arNumeric))
                $arTmpLine[] = "\$$sFieldName";
            else
                $arTmpLine[] = "'\$$sFieldName'";
        }
        
        $arLines[] = "\t\t(".implode(",",$arTmpLine).")\";";
        $arLines[] = "\t\t\$this->execute(\$sSQL);";    
    }
    
    protected function model_add_func_loadbyid(&$arLines)
    {
        $arExtras = array
        (
            "id","insert_platform","insert_user","insert_date","update_platform","update_user","update_date","code_erp","description"
            ,"delete_platform","delete_date","delete_user","is_enabled","is_erpsent","processflag"
        );
        
        $arLines[] = "\t\tif(\$this->_id)";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$this->oQuery->set_comment(\"load_by_id()\");";
        $arLines[] = "\t\t\t\$this->oQuery->set_fields(\$this->get_all_fields());";
        $arLines[] = "\t\t\t\$this->oQuery->set_fromtables(\$this->_table_name);";
        $arLines[] = "\t\t\t\$this->oQuery->set_joins();";
        $arLines[] = "\t\t\t\$this->oQuery->add_where(\"\$this->_table_name.delete_date IS NULL\");";
        $arLines[] = "\t\t\t\$this->oQuery->add_where(\"\$this->_table_name.is_enabled=1\");";
        $arLines[] = "\t\t\t\$this->oQuery->set_and(\"\$this->_table_name.id=\$this->_id\");";
        $arLines[] = "\t\t\t\$sSQL = \$this->oQuery->get_select();";
        $arLines[] = "\t\t\t//bug(\$this->oQuery);";
        $arLines[] = "\t\t\t\$arRow = \$this->query(\$sSQL,1);";       
        $arLines[] = "\t\t}";
        $arLines[] = "\t\t\$this->row_assign(\$arRow);";

//        foreach($this->arFields as $sFieldName)
//            $arLines[] = "\t\t\$this->_$sFieldName = \$arRow[\"$sFieldName\"];";
//        $arLines[] = "\t\t//BASE FIELDS";
//        foreach($arExtras as $sFieldName)
//            $arLines[] = "\t\t\$this->_$sFieldName = \$arRow[\"$sFieldName\"];";        
    }

    protected function model_add_func_getselectallids(&$arLines)
    {
        $arLines[] = "\t\t//\$this->oQuery->set_comment(\"get_select_all_ids() overriden\");";
        $arLines[] = "\t\t//\$this->oQuery->set_fields(\"\$this->_table_name.id\");";
        $arLines[] = "\t\t////si está definido \$this->_select_user";
        $arLines[] = "\t\t//\$this->oQuery->add_joins(\$this->build_userhierarchy_join(\$this->_select_user,\"customer\",\"id_customer\"));";
        $arLines[] = "\t\t//\$this->oQuery->add_where(\"\$this->_table_name.delete_date IS NULL\");";
        $arLines[] = "\t\t//\$this->oQuery->add_where(\"\$this->_table_name.is_enabled=1\");";
        $arLines[] = "\t\t////EXTRA AND";
        $arLines[] = "\t\t//\$this->oQuery->add_and(\$this->build_sql_filters());";
        $arLines[] = "\t\t////ORDERBY ";
        $arLines[] = "\t\t////default orderby";
        $arLines[] = "\t\t//\$this->oQuery->set_orderby(\"\$this->_table_name.id DESC\");";
        $arLines[] = "\t\t//\$sOrderByAuto = \$this->build_sql_orderby();";
        $arLines[] = "\t\t//if(\$sOrderByAuto) \$this->oQuery->set_orderby(\$sOrderByAuto);";
        $arLines[] = "\t\t//\$sSQL = \$this->oQuery->get_select();";
        $arLines[] = "\t\t//\$this->oQuery->set_fields(\$this->sSELECTfields);";
        $arLines[] = "\t\t////bug(\$sSQL);";
        $arLines[] = "\t\t//return \$this->query(\$sSQL);";
    }
    
    protected function model_add_func_getters(&$arLines)
    {
        foreach($this->arFields as $sFieldName)
        {            
            $arLines[] = "\tpublic function get_$sFieldName(){return \$this->_$sFieldName;}";
            if($this->is_foreignkey($sFieldName))
            {
                $sNoPrefix = $this->remove_prefix($sFieldName,"id");
                $arLines[] = "\tpublic function get_$sNoPrefix()";
                $arLines[] = "\t{";
                if(strstr($sFieldName,"id_type_"))
                    $sNoPrefix = $this->sModelObjectName."Array";
                else 
                {
                    $sNoPrefix = $this->remove_prefix($sFieldName,"id");
                    $sModelName = sep_to_camel($sNoPrefix);
                    $sNoPrefix = "o".$sModelName;                  
                }
                $arLines[] = "\t\t\$this->$sNoPrefix = new Model$sModelName(\$this->_$sFieldName);";
                $arLines[] = "\t\t\$this->$sNoPrefix"."->load_by_id();";
                $arLines[] = "\t\treturn \$this->$sNoPrefix;";
                $arLines[] = "\t}";
            }    
        }        
    }

    protected function model_add_func_setters(&$arLines)
    {
        foreach($this->arFields as $sFieldName)
        {
            $arLines[] = "\tpublic function set_$sFieldName(\$value){\$this->_$sFieldName = \$value;}";
            if($this->is_foreignkey($sFieldName))
            {
                $sNoPrefix = $this->remove_prefix($sFieldName,"id");
                $sTmpLine = "\tpublic function set_$sNoPrefix(\$oValue){";
                if(strstr($sFieldName,"id_type_"))
                    $sNoPrefix = $this->sModelObjectName."Array";
                else 
                {
                    $sNoPrefix = $this->remove_prefix($sFieldName,"id");
                    $sModelName = sep_to_camel($sNoPrefix);
                    $sNoPrefix = "o".$sModelName;                  
                }
                $sTmpLine .= "\$this->$sNoPrefix = \$oValue;}";
                $arLines[] = $sTmpLine;
            }                
        }            
    }
    
    protected function get_model_content()
    {
        $arLines = array();
                
        $arLines[] = "<?php";
        $arLines[] = "/**
 * @author Module Builder 1.1.4
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name $this->sModelClassName
 * @file $this->sModelFileName
 * @date $this->sTimeNow
 * @observations: 
 * @requires: theapplication_model.php
 */";
        $arLines[] = "include_once(\"theapplication_model.php\");";
        $arLines[] = "";
        $arLines[] = "class $this->sModelClassName extends TheApplicationModel";
        $arLines[] = "{";        
        $this->model_add_attribs($arLines);
        $arLines[] = "";
        //FUNCION CONSTRUCT
        $arLines[] = "\tpublic function __construct";
        $this->model_add_construct_params($arLines);
        $arLines[] = "\t{";
        $this->model_add_construct_assign($arLines);
        $arLines[] = "\t}//__construct()";
        $arLines[] = "";
        
        //FUNCION INSERT
//        $arLines[] = "\tpublic function insert()";
//        $arLines[] = "\t{";
//        $this->model_add_func_insert($arLines);
//        $arLines[] = "\t}//insert()";
//        $arLines[] = "";
        
        //FUNCION LOADABYID
        $arLines[] = "\tpublic function load_by_id()";
        $arLines[] = "\t{";
        $this->model_add_func_loadbyid($arLines);
        $arLines[] = "\t}//load_by_id()";
        $arLines[] = "";

        $arLines[] = "\t//public function get_select_all_ids()";
        $arLines[] = "\t//{";
        $this->model_add_func_getselectallids($arLines);
        $arLines[] = "\t//}//get_select_all_ids overriden";        
        $arLines[] = "";
        //FUNCIONES GETS
        $arLines[] = "\t//===================";
        $arLines[] = "\t//       GETS";
        $arLines[] = "\t//===================";
        $this->model_add_func_getters($arLines);
        //FUNCIONES SETS
        $arLines[] = "\t//===================";
        $arLines[] = "\t//       SETS";
        $arLines[] = "\t//===================";
        $this->model_add_func_setters($arLines);
        $arLines[] = "}";//fin clase
        $sContent = implode("\n",$arLines);
        return $sContent;
    }//get_model_content($this->sControllerName)
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="BUILD CONTROLLER">
    
    //func_1
    protected function controlleradd_func_construct(&$arLines)
    {
        $arLines[] = "\t\t\$this->sModuleName = \"$this->sControllerName\";";
        $arLines[] = "\t\t\$this->sTrLabelPrefix = \"$this->sTranslatePrefix\";";
        $arLines[] = "\t\t//console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view";
        $arLines[] = "\t\tparent::__construct(\$this->sModuleName);";
        $arLines[] = "\t\t\$this->load_pagetitle();";
        //$arLines[] = "\t\t\$this->oSessionUser = \$this->oSession->get(\"oSessionUser\");";
        //$arLines[] = "\t\t\$this->oView->set_session_user(\$this->oSessionUser);";
        //$arLines[] = "\t\t\$this->oView->set_layout(\"twocolumn\");";
        $arLines[] = "\t\t\$this->$this->sModelObjectName = new $this->sModelClassName();";
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_platform(\$this->oSessionUser->get_platform());";        
        $arLines[] = "\t\tif(\$this->is_inget(\"id\"))";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->set_id(\$this->get_get(\"id\"));";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->load_by_id();";
        $arLines[] = "\t\t}";
        $arLines[] = "\t\t//\$this->oSessionUser->set_dataowner_table(\$this->$this->sModelObjectName"."->get_table_name());";
        $arLines[] = "\t\t//\$this->oSessionUser->set_dataowner_tablefield(\"id_customer\");";
        $arLines[] = "\t\t//\$this->oSessionUser->set_dataowner_keys(array(\"id\"=>\$this->$this->sModelObjectName"."->get_id()));";         
    }//controlleradd_func_construct
    
    //func_2
    protected function controlleradd_lst_buildlistscrumbs(&$arLines)
    {
        $arLines[] = "\t\t\$arLinks = array();";
        $arLines[] ="\t\t\$sUrlLink = \"\$this->build_url();";
        $arLines[] = "\t\t\$arLinks[\"list\"]=array(\"href\"=>\$sUrlLink,\"innerhtml\"=>$this->sTranslatePrefix"."entities);"; 
	$arLines[] = "\t\t\$oScrumbs = new AppHelperBreadscrumbs(\$arLinks);";
	$arLines[] = "\t\treturn \$oScrumbs;";        
    }//controlleradd_lst_buildlistscrumbs
    
    //func_3 -> func_18
    protected function controlleradd_lst_buildlisttabs(&$arLines)
    {
        $this->arTranslation[] = $this->sTranslatePrefix."listtabs_1";
        $this->arTranslation[] = $this->sTranslatePrefix."listtabs_2";
        
        $arLines[] = "\t\t\$arTabs = array();";
        $arLines[] =  "\t\t//\$sUrlTab = \"\$this->build_url(\$this->sModuleName,NULL,\"get_list\",\"id=\".\$this->get_get(\"id_parent_foreign\"));";
        $arLines[] = "\t\t//\$arTabs[\"list\"]=array(\"href\"=>\$sUrlTab,\"innerhtml\"=>$this->sTranslatePrefix"."listtabs_1);";
        $arLines[] = "\t\t//\$sUrlTab = \$this->build_url(\$this->sModuleName,NULL,\"get_list_by_foreign\",\"id_foreign=\".\$this->get_get(\"id_parent_foreign\"));";
        $arLines[] = "\t\t//\$arTabs[\"listbyforeign\"]=array(\"href\"=>\$sUrlTab,\"innerhtml\"=>$this->sTranslatePrefix"."listtabs_2);";
        $arLines[] = "\t\t\$oTabs = new AppHelperHeadertabs(\$arTabs,\"list\");";
        $arLines[] = "\t\treturn \$oTabs;";     
    }//controlleradd_lst_buildlisttabs
    
    //func_4 -> func_19
    protected function controlleradd_lst_buildlistoperationbuttons(&$arLines)
    {
        $this->arTranslation[] = $this->sTranslatePrefix."listopbutton_filters";
        $this->arTranslation[] = $this->sTranslatePrefix."listopbutton_reload";
        $this->arTranslation[] = $this->sTranslatePrefix."listopbutton_insert";
        
        $this->arTranslation[] = $this->sTranslatePrefix."listopbutton_multiquarantine";
        $this->arTranslation[] = $this->sTranslatePrefix."listopbutton_multidelete";
        $this->arTranslation[] = $this->sTranslatePrefix."listopbutton_multiassign";
        $this->arTranslation[] = $this->sTranslatePrefix."listopbutton_singleassign";
        
        $arLines[] = "\t\t\$arOpButtons = array();";
        $arLines[] = "\t\t\$arOpButtons[\"filters\"]=array(\"href\"=>\"javascript:reset_filters();\",\"icon\"=>\"awe-magic\",\"innerhtml\"=>$this->sTranslatePrefix"."listopbutton_filters);";
        $arLines[] = "\t\t\$arOpButtons[\"reload\"]=array(\"href\"=>\"javascript:TfwControl.form_submit();\",\"icon\"=>\"awe-refresh\",\"innerhtml\"=>$this->sTranslatePrefix"."listopbutton_reload);";
        $arLines[] = "\t\tif(\$this->oPermission->is_insert())";
        $arLines[] = "\t\t\t\$arOpButtons[\"insert\"]=array(\"href\"=>\$this->build_url(\$this->sModuleName,NULL,\"insert\"),\"icon\"=>\"awe-plus\",\"innerhtml\"=>$this->sTranslatePrefix"."listopbutton_insert);";
        $arLines[] = "\t\tif(\$this->oPermission->is_quarantine())";
        $arLines[] = "\t\t\t\$arOpButtons[\"multiquarantine\"]=array(\"href\"=>\"javascript:multi_quarantine();\",\"icon\"=>\"awe-remove\",\"innerhtml\"=>$this->sTranslatePrefix"."listopbutton_multiquarantine);";        
        $arLines[] = "\t\t//if(\$this->oPermission->is_delete())";
        $arLines[] = "\t\t\t//\$arOpButtons[\"multidelete\"]=array(\"href\"=>\"javascript:multi_delete();\",\"icon\"=>\"awe-remove\",\"innerhtml\"=>$this->sTranslatePrefix"."listopbutton_multidelete);";
        $arLines[] = "\t\t//PICK WINDOWS";
        $arLines[] = "\t\t//\$arOpButtons[\"multiassign\"]=array(\"href\"=>\"javascript:multiassign_window('\$this->sModuleName',null,'multiassign','\$this->sModuleName','addexternaldata');\",\"icon\"=>\"awe-external-link\",\"innerhtml\"=>$this->sTranslatePrefix"."listopbutton_multiassign);";
        $arLines[] = "\t\t//\$arOpButtons[\"singleassign\"]=array(\"href\"=>\"javascript:single_pick('\$this->sModuleName','singleassign','txtI','txtI');\",\"icon\"=>\"awe-external-link\",\"innerhtml\"=>$this->sTranslatePrefix"."listopbutton_singleassign);";
        $arLines[] = "\t\t\$oOpButtons = new AppHelperButtontabs($this->sTranslatePrefix"."entities);";
        $arLines[] = "\t\t\$oOpButtons->set_tabs(\$arOpButtons);";
        $arLines[] = "\t\treturn \$oOpButtons;";    
    }//controlleradd_lst_buildlistoperationbuttons    
    
    //func_5
    protected function controlleradd_lst_loadconfiglistfilters(&$arLines)
    {
        //LIST - FILTERS
        foreach($this->arFields as $sFieldName)
        {
            if(!in_array($sFieldName,$this->arNotFilter))
            {        
                $sComment = "";
                if($this->do_comment($sFieldName)) $sComment="//";                
                $isIdForeign = $this->is_foreignkey($sFieldName);
                $sCamelName = sep_to_camel($sFieldName);
                $arLines[] = "\t\t//$sFieldName";
                if($isIdForeign)
                    $arLines[] = "\t\t$sComment\$this->set_filter(\"$sFieldName\",\"sel$sCamelName\");";
                else
                    $arLines[] = "\t\t$sComment\$this->set_filter(\"$sFieldName\",\"txt$sCamelName\",array(\"operator\"=>\"like\"));";
            }
        }    
    }//controlleradd_lst_loadconfiglistfilters
        
    //func_6 
    protected function controlleradd_lst_setlistfiltersfrompost(&$arLines)
    {
        //LIST - FILTERS
        foreach($this->arFields as $sFieldName)
        {
            if(!in_array($sFieldName,$this->arNotFilter))
            {        
                $sComment = "";
                if($this->do_comment($sFieldName)) $sComment="//";                
                $isIdForeign = $this->is_foreignkey($sFieldName);
                $sCamelName = sep_to_camel($sFieldName);
                $arLines[] = "\t\t//$sFieldName";
                if($isIdForeign)
                    $arLines[] = "\t\t$sComment\$this->set_filter_value(\"$sFieldName\",\$this->get_post(\"sel$sCamelName\"));";
                else
                    $arLines[] = "\t\t$sComment\$this->set_filter_value(\"$sFieldName\",\$this->get_post(\"txt$sCamelName\"));";
            }
        }        
    }//controlleradd_lst_setlistfiltersfrompost
    
    //func_7
    protected function controlleradd_lst_getlistfilters(&$arLines,$addToTranslation=1)
    {
        $arLines[] = "\t\t$sComment\$arFields = array();";
        foreach($this->arFields as $sFieldName)
        {
            if(!in_array($sFieldName,$this->arNotFilter))
            {        
                $sComment = "";
                if($this->do_comment($sFieldName)) $sComment="//";
            
                $isIdForeign = $this->is_foreignkey($sFieldName);
                $sLabel = $this->sTranslatePrefix."fil_".$this->get_translation_label($sFieldName);
                if($addToTranslation)
                {    
                    $this->arTranslation[] = $sLabel;
                    if($isIdForeign)
                    {                    
                        if(strstr($sLabel,"id_type_")) $this->arTranslation[] = str_replace("id_type_","",$sLabel);
                        else $this->arTranslation[] = str_replace("id_","",$sLabel);
                    }
                }
                $sCamelName = sep_to_camel($sFieldName);
                $arLines[] = "\t\t//$sFieldName";
                if($isIdForeign)
                {    
                    $sObjectName = $this->fieldname_to_objectname($sFieldName);
                    $sModelName = $this->fieldname_to_modelname($sFieldName);
                    $arLines[] = "\t\t$sComment\$$sObjectName = new $sModelName();";
                    if(!$this->is_foreignkey_array($sFieldName))
                        $arLines[] = "\t\t$sComment\$arOptions = \$$sObjectName"."->get_picklist();";
                    else 
                    {
                        $sType = str_replace("id_type_","",$sFieldName);
                        $arLines[] = "\t\t$sComment\$arOptions = \$$sObjectName"."->get_picklist_by_type(\"$sType\");";
                    }
                    $arLines[] = "\t\t$sComment\$oAuxField = new HelperSelect(\$arOptions,\"sel$sCamelName\",\"sel$sCamelName\");";
                    $arLines[] = "\t\t$sComment\$oAuxField->set_value_to_select(\$this->get_post(\"sel$sCamelName\"));";
                    $arLines[] = "\t\t$sComment\$oAuxField->set_postback();";
                    $arLines[] = "\t\t$sComment\$oAuxWrapper = new ApphelperControlGroup(\$oAuxField,new HelperLabel(\"sel$sCamelName\",$sLabel));";
                }
                else
                {
                    $arLines[] = "\t\t$sComment\$oAuxField = new HelperInputText(\"txt$sCamelName\",\"txt$sCamelName\");";
                    $arLines[] = "\t\t$sComment\$oAuxField->set_value(\$this->get_post(\"txt$sCamelName\"));";
                    $arLines[] = "\t\t$sComment\$oAuxField->on_entersubmit();";
                    $arLines[] = "\t\t$sComment\$oAuxWrapper = new ApphelperControlGroup(\$oAuxField,new HelperLabel(\"txt$sCamelName\",$sLabel));";                    
                }
                $arLines[] = "\t\t$sComment\$arFields[] = \$oAuxWrapper;";
            }//!in_array($sFieldName,$this->arNotFilter)
        }//foreach $this->arFilters        
    }//controlleradd_lst_getlistfilters(&$arLines,$addToTranslation=1)

    //func_8
    protected function controlleradd_lst_getlistcolumns(&$arLines,$addToTranslation=1)
    {
        foreach($this->arFields as $sFieldName)
        {
            if(!in_array($sFieldName,$this->arNotColumn))
            {        
                $sComment = "";
                if($this->do_comment($sFieldName)) $sComment="//";
                
                $sLabel = $this->sTranslatePrefix."col_".$this->get_translation_label($sFieldName);
                if($addToTranslation)
                {    
                    $this->arTranslation[] = $sLabel; 
                    if($this->is_foreignkey($sFieldName))
                    {   
                        if(strstr($sLabel,"id_type_")) $this->arTranslation[] = str_replace("id_type_","",$sLabel);
                        else $this->arTranslation[] = str_replace("id_","",$sLabel);
                    }
                }
                
                if($this->is_foreignkey($sFieldName))
                {
                    $arLines[] = "\t\t//\$arColumns[\"$sFieldName\"] = $sLabel;";
                    $sEntityName = $this->fieldname_to_entityname($sFieldName);
                    $arLines[] = "\t\t$sComment\$arColumns[\"$sEntityName\"] = $sLabel;";
                }
                else
                    $arLines[] = "\t\t$sComment\$arColumns[\"$sFieldName\"] = $sLabel;";
            }
        }
    }//controller_columns()
    
    //func_9
    protected function controlleradd_lst_getlist(&$arLines)
    {
        $arLines[] = "\t\t\$this->go_to_401(\$this->oPermission->is_not_select());";        
        $arLines[] = "\t\t\$oAlert = new AppHelperAlertdiv();";
        $arLines[] = "\t\t\$oAlert->use_close_button();";
        $arLines[] = "\t\t\$sMessage = \$this->get_session_message(\$sMessage);";
        $arLines[] = "\t\tif(\$sMessage)";
        $arLines[] = "\t\t\t\$oAlert->set_title(\$sMessage);";
        $arLines[] = "\t\t\$sMessage = \$this->get_session_message(\$sMessage,\"e\");";
        $arLines[] = "\t\tif(\$sMessage)";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$oAlert->set_type();";
        $arLines[] = "\t\t\t\$oAlert->set_title(\$sMessage);";
        $arLines[] = "\t\t}";
        $arLines[] = "\t\t\$arColumns = \$this->get_list_columns(); ";
        $arLines[] = "";
        //FILTROS
        $arLines[] = "\t\t//Carga en la variable global la configuración de los campos que se utilizarán";
        $arLines[] = "\t\t//FILTERS";
        $arLines[] = "\t\t\$this->load_config_list_filters();";
        $arLines[] = "\t\t\$oFilter = new ComponentFilter();";
        $arLines[] = "\t\t\$oFilter->set_fieldnames(\$this->get_filter_fieldnames());";
        $arLines[] = "\t\t//Indica que no se guardara en sesion por nombre de campo sino por nombre de control";
        $arLines[] = "\t\t//para esto es necesario respetar el estricto camelcase";
        $arLines[] = "\t\t\$oFilter->use_field_prefix();";
        $arLines[] = "\t\t//Guarda en sesion y post los campos enviados, los de orden y página";
        $arLines[] = "\t\t\$oFilter->refresh();";
        $arLines[] = "\t\t\$this->set_listfilters_from_post();";
        $arLines[] = "\t\t";
        $arLines[] = "\t\t\$arObjFilter = \$this->get_list_filters();";
        $arLines[] = "";
        $arLines[] = "\t\t//RECOVER DATALIST";
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_orderby(\$this->get_orderby());";
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_ordertype(\$this->get_ordertype());";
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_filters(\$this->get_filter_searchconfig());";
        $arLines[] = "\t\t//hierarchy recover";
        $arLines[] = "\t\t//\$this->$this->sModelObjectName"."->set_select_user(\$this->oSessionUser->get_id());";
        $arLines[] = "\t\t\$arList = \$this->$this->sModelObjectName"."->get_select_all_ids();";
        $arLines[] = "\t\t\$iRequestPage = \$this->get_post(\"selPage\");";
        $arLines[] = "\t\t\$oPage = new ComponentPage(\$arList,\$iRequestPage);";
        $arLines[] = "\t\t\$arList = \$oPage->get_items_to_show();";
        $arLines[] = "\t\t\$arList = \$this->$this->sModelObjectName"."->get_select_all_by_ids(\$arList);";
        
        $arLines[] = "\t\t//TABLE";
        $arLines[] = "\t\t//This method adds objects controls to search list form";
        $arLines[] = "\t\t\$oTableList = new HelperTableTyped(\$arList,\$arColumns);";
        $arLines[] = "\t\t\$oTableList->set_fields(\$arObjFilter);";
        $arLines[] = "\t\t\$oTableList->set_module(\$this->get_current_module());";
        $arLines[] = "\t\t\$oTableList->add_class(\"table table-striped table-bordered table-condensed\");";
        $arLines[] = "\t\t\$oTableList->set_keyfields(array(\"id\"));";
        $arLines[] = "\t\t\$oTableList->is_ordenable();";
        $arLines[] = "\t\t\$oTableList->set_orderby(\$this->get_orderby());";
        $arLines[] = "\t\t\$oTableList->set_orderby_type(\$this->get_ordertype());";
        $arLines[] = "\t\t//COLUMNS CONFIGURATION";
        $arLines[] = "\t\tif(\$this->oPermission->is_quarantine()||\$this->oPermission->is_delete())";
        $arLines[] = "\t\t\t\$oTableList->set_column_pickmultiple();//checks column";
        $arLines[] = "\t\tif(\$this->oPermission->is_read())";
        $arLines[] = "\t\t\t\$oTableList->set_column_detail();";
        $arLines[] = "\t\tif(\$this->oPermission->is_quarantine())";
        $arLines[] = "\t\t\t\$oTableList->set_column_quarantine();";
        $arLines[] = "\t\t//if(\$this->oPermission->is_delete())";
        $arLines[] = "\t\t\t//\$oTableList->set_column_delete();";        
        $arLines[] = "\t\t//\$arExtra[] = array(\"position\"=>1,\"label\"=>\"Lines\");";
        $arLines[] = "\t\t//\$oTableList->add_extra_colums(\$arExtra);";
        $arLines[] = "\t\t//\$oTableList->set_column_anchor(array(\"virtual_0\"=>array";
        $arLines[] = "\t\t//(\"href\"=>\"url_lines\",\"innerhtml\"=>$this->sTranslatePrefix"."order_lines,\"class\"=>\"btn btn-info\",\"icon\"=>\"awe-info-sign\")));";
        $arLines[] = "\t\t//\$arFormat = array(\"amount_total\"=>\"numeric2\",\"date\"=>\"date\",\"delivery_date\"=>\"date\");";
        $arLines[] = "\t\t//\$oTableList->set_format_columns(\$arFormat);";        

        $arLines[] = "\t\t//parametros a pasar al popup";
        $arLines[] = "\t\t//\$oTableList->set_multiassign(array(\"keys\"=>array(\"k\"=>1,\"k2\"=>2)));";
        $arLines[] = "\t\t\$oTableList->set_current_page(\$oPage->get_current());";
        $arLines[] = "\t\t\$oTableList->set_next_page(\$oPage->get_next());";
        $arLines[] = "\t\t\$oTableList->set_first_page(\$oPage->get_first());";
        $arLines[] = "\t\t\$oTableList->set_last_page(\$oPage->get_last());";
        $arLines[] = "\t\t\$oTableList->set_total_regs(\$oPage->get_total_regs());";
        $arLines[] = "\t\t\$oTableList->set_total_pages(\$oPage->get_total());";
        $arLines[] = "\t\t//SCRUMBS";
        $arLines[] = "\t\t\$oScrumbs = \$this->build_list_scrumbs();";
        $arLines[] = "\t\t//TABS";
        $arLines[] = "\t\t\$oTabs = \$this->build_list_tabs();";
        $arLines[] = "\t\t//OPER BUTTONS";
        $arLines[] = "\t\t\$oOpButtons = \$this->build_listoperation_buttons();";
        $arLines[] = "\t\t//JAVASCRIPT";
        $arLines[] = "\t\t\$oJavascript = new HelperJavascript();";
        $arLines[] = "\t\t\$oJavascript->set_filters(\$this->get_filter_controls_id());";
        $arLines[] = "\t\t\$oJavascript->set_focusid(\"id_all\");";
        $arLines[] = "\t\t//VIEW SET";
        $arLines[] = "\t\t\$this->oView->add_var(\$oScrumbs,\"oScrumbs\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oTabs,\"oTabs\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oOpButtons,\"oOpButtons\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oAlert,\"oAlert\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oJavascript,\"oJavascript\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oTableList,\"oTableList\");";
        $arLines[] = "\t\t\$this->oView->show_page();";        
    }//controlleradd_lst_getlist
        
    //func_10
    protected function controlleradd_ins_buildinsertscrumbs(&$arLines)
    {
        //$this->arTranslation[] = $this->sTranslatePrefix."entities";
        $this->arTranslation[] = $this->sTranslatePrefix."entity_insert";
        
        //$this->arTranslation[] = "";
        $arLines[] = "\t\t\$arLinks = array();";
        $arLines[] ="\t\t\$sUrlLink = \"\$this->build_url();";
        $arLines[] = "\t\t\$arLinks[\"list\"]=array(\"href\"=>\$sUrlLink,\"innerhtml\"=>$this->sTranslatePrefix"."entities);";
        $arLines[] = "\t\t\$sUrlLink = \$this->build_url(\$this->sModuleName,NULL,\"insert\");";
        $arLines[] = "\t\t\$arLinks[\"insert\"]=array(\"href\"=>\$sUrlLink,\"innerhtml\"=>$this->sTranslatePrefix"."entity_insert);";
        $arLines[] = "\t\t\$oScrumbs = new AppHelperBreadscrumbs(\$arLinks);";
        $arLines[] = "\t\treturn \$oScrumbs;";              
    }//controlleradd_ins_buildinsertscrumbs
    
    //func_3 -> func_18
    protected function controlleradd_ins_buildinserttabs(&$arLines)
    {
        $this->arTranslation[] = $this->sTranslatePrefix."instabs_1";
        $this->arTranslation[] = $this->sTranslatePrefix."instabs_2";        
        
        $arLines[] = "\t\t\$arTabs = array();";
        $arLines[] = "\t\t//\$sUrlTab = \$this->build_url(\$this->sModuleName,NULL,\"insert\");";
        $arLines[] = "\t\t//\$arTabs[\"insert1\"]=array(\"href\"=>\$sUrlTab,\"innerhtml\"=>$this->sTranslatePrefix"."instabs_1);";
        $arLines[] = "\t\t//\$sUrlTab = \$this->build_url(\$this->sModuleName,NULL,\"insert2\");";
        $arLines[] = "\t\t//\$arTabs[\"insert2\"]=array(\"href\"=>\$sUrlTab,\"innerhtml\"=>$this->sTranslatePrefix"."instabs_2);";
        $arLines[] = "\t\t\$oTabs = new AppHelperHeadertabs(\$arTabs,\"insert1\");";
        $arLines[] = "\t\treturn \$oTabs;";       
    }//controlleradd_lst_buildlisttabs

    //func_12
    protected function controlleradd_ins_buildinsertopbuttons(&$arLines)
    {
        $this->arTranslation[] = $this->sTranslatePrefix."insopbutton_list";
        $this->arTranslation[] = $this->sTranslatePrefix."insopbutton_extra1";
        
        $arLines[] = "\t\t\$arOpButtons = array();";
        $arLines[] = "\t\t\$arOpButtons[\"list\"] = array(\"href\"=>\$this->build_url(\$this->sModuleName,NULL,\"get_list\"),\"icon\"=>\"awe-search\",\"innerhtml\"=>$this->sTranslatePrefix"."insopbutton_list);";
        $arLines[] = "\t\t//\$arOpButtons[\"extra\"] = array(\"href\"=>\$this->build_url(),\"icon\"=>\"awe-xxxx\",\"innerhtml\"=>$this->sTranslatePrefix"."insopbutton_extra1);";
        $arLines[] = "\t\t\$oOpButtons = new AppHelperButtontabs($this->sTranslatePrefix"."entities);";
        $arLines[] = "\t\t\$oOpButtons->set_tabs(\$arOpButtons);";
        $arLines[] = "\t\treturn \$oOpButtons;";         
    }//controlleradd_ins_buildinsertscrumbs
    
    //func_13 TODO HAY QUE OPTIMIZAR ESTE METODO
    protected function controlleradd_ins_buildinsertfields(&$arLines,$addToTranslation=1)
    {
        $arLines[] = "\t\t\$arFields = array(); \$oAuxField = NULL; \$oAuxLabel = NULL;";
        //$arLines[] = "\t\t\$$this->sModelObjectName = new $this->sModelClassName();";
        $arLines[] = "\t\t\$arFields[]= new AppHelperFormhead($this->sTranslatePrefix"."entity_new);";        
        foreach($this->arFields as $sFieldName)
        {
            if(!in_array($sFieldName,$this->arNotInsert))
            {   
                $sComment = "";
                if($this->do_comment($sFieldName)) $sComment="//";
                
                $isIdForeign = $this->is_foreignkey($sFieldName);
                $sFieldType = $this->get_field_type($sFieldName);
                
                $sLabel = $this->sTranslatePrefix."ins_".$this->get_translation_label($sFieldName);
                if($addToTranslation) $this->arTranslation[] = $sLabel;            
                $sCamelName = sep_to_camel($sFieldName);
                $arLines[] = "\t\t//$sFieldName";
                    
                if($isIdForeign)
                {    
                    $sObjectName = $this->fieldname_to_objectname($sFieldName);
                    $sModelName = $this->fieldname_to_modelname($sFieldName);
                    $arLines[] = "\t\t$sComment\$$sObjectName = new $sModelName();";
                    if(!$this->is_foreignkey_array($sFieldName))
                        $arLines[] = "\t\t$sComment\$arOptions = \$$sObjectName"."->get_picklist();";
                    else 
                    {
                        $sType = str_replace("id_type_","",$sFieldName);
                        $arLines[] = "\t\t$sComment\$arOptions = \$$sObjectName"."->get_picklist_by_type(\"$sType\");";
                    }
                    $arLines[] = "\t\t$sComment\$oAuxField = new HelperSelect(\$arOptions,\"sel$sCamelName\",\"sel$sCamelName\");";
                    $arLines[] = "\t\t$sComment\$oAuxField->set_value_to_select(\$this->get_post(\"sel$sCamelName\"));";
                    $arLines[] = "\t\t$sComment\$oAuxWrapper = new ApphelperControlGroup(\$oAuxField,new HelperLabel(\"sel$sCamelName\",$sLabel));";
                }
                elseif($this->is_primarykey($sFieldName))
                {    
                    $arLines[] = "\t\t//\$oAuxField = new HelperInputText(\"txt$sCamelName\",\"txt$sCamelName\");";
                }                
                else
                {   
                    $arLines[] = "\t\t$sComment\$oAuxField = new HelperInputText(\"txt$sCamelName\",\"txt$sCamelName\");";
                }
                
                if($this->is_primarykey($sFieldName))
                    $arLines[] = "\t\t//\$oAuxField->is_primarykey();";             
                
                if($isIdForeign)
                {
                    $arLines[] = "\t\t$sComment"."if(\$usePost) \$oAuxField->set_value_to_select(\$this->get_post(\"sel$sCamelName\"));";
                    $arLines[] = "\t\t$sComment\$oAuxLabel = new HelperLabel(\"sel$sCamelName\",$sLabel,\"lbl$sCamelName\");";
                }
                elseif($this->is_primarykey($sFieldName))
                {
                        $arLines[] = "\t\t//if(\$usePost) \$oAuxField->set_value(\$this->get_post(\"txt$sCamelName\"));";
                }    
                else    
                {   
                    if($sFieldType=="int")
                    {   
                        $arLines[] = "\t\t$sComment\$oAuxField->set_value(0);";
                        $arLines[] = "\t\t$sComment"."if(\$usePost) \$oAuxField->set_value(dbbo_int(\$this->get_post(\"txt$sCamelName\")));";
                    }
                    elseif(in_array($sFieldType,$this->arDecimalTypes))
                    {    
                        $arLines[] = "\t\t$sComment\$oAuxField->set_value(\"0.00\");";
                        $arLines[] = "\t\t$sComment"."if(\$usePost) \$oAuxField->set_value(dbbo_numeric2(\$this->get_post(\"txt$sCamelName\")));";
                    }
                    else//string
                        $arLines[] = "\t\t$sComment"."if(\$usePost) \$oAuxField->set_value(\$this->get_post(\"txt$sCamelName\"));";
                    
                    $arLines[] = "\t\t$sComment\$oAuxLabel = new HelperLabel(\"txt$sCamelName\",$sLabel,\"lbl$sCamelName\");";
                }
                $arLines[] = "\t\t//\$oAuxField->readonly();\$oAuxField->add_class(\"readonly\");";
                //$arLines[] = "\t\t\$oAuxLabel->add_class(\"labelreq\");"; 
                if($this->is_primarykey($sFieldName))
                    $arLines[] = "\t\t//\$arFields[] = new ApphelperControlGroup(\$oAuxField,\$oAuxLabel);";
                else        
                    $arLines[] = "\t\t$sComment\$arFields[] = new ApphelperControlGroup(\$oAuxField,\$oAuxLabel);";
            }
        }//foreach arFields
        $this->arTranslation[] = $this->sTranslatePrefix."ins_savebutton";
        $arLines[] = "\t\t//SAVE BUTTON";
        $arLines[] = "\t\t\$oAuxField = new HelperButtonBasic(\"butSave\",$this->sTranslatePrefix"."ins_savebutton);";
        $arLines[] = "\t\t\$oAuxField->add_class(\"btn btn-primary\");";
        $arLines[] = "\t\t\$oAuxField->set_js_onclick(\"insert();\");";
        $arLines[] = "\t\t\$arFields[] = new ApphelperFormactions(array(\$oAuxField));";
        $arLines[] = "\t\t//POST INFO";
        $arLines[] = "\t\t\$oAuxField = new HelperInputHidden(\"hidAction\",\"hidAction\");";
        $arLines[] = "\t\t\$arFields[] = \$oAuxField;";
        $arLines[] = "\t\t\$oAuxField = new HelperInputHidden(\"hidPostback\",\"hidPostback\");";
        $arLines[] = "\t\t\$arFields[] = \$oAuxField;";        
        $arLines[] = "\t\treturn \$arFields;";        
    }//inset_fields(&$arLines,$addToTranslation=1)
        
    //func_14
    protected function controlleradd_ins_getinsertvalidate(&$arLines,$isUpdate=0)
    {
        $arLines[] = "\t\t\$arFieldsConfig = array();";
        //CREA CAMPOS PARA VALIDACION ANTES DE INSERTAR
        $arTmpLine = array();
        foreach($this->arFields as $sFieldName)
        {
            if(!in_array($sFieldName,$this->arNotInsert))
            {        
                $sComment = "";
                if($this->do_comment($sFieldName)) $sComment="//";
                
                $iLength = $this->get_field_length($sFieldName);
                $sType = $this->get_field_type($sFieldName);
                $sLabel = $this->sTranslatePrefix."ins_";
                if($isUpdate) $sLabel = $this->sTranslatePrefix."upd_";
                $sLabel .= $this->get_translation_label($sFieldName);
                $sCamelName = sep_to_camel($sFieldName);
                $arTypes = array();
                if($sType=="numeric") $arTypes[]="\"$sType\"";
                //TODO lo mejor seria marcar los ids comentados
                if(in_array($sFieldName,$this->arPks)) $arTypes[]="\"required\"";
                $sType = implode(",",$arTypes);
                
                if($this->is_foreignkey($sFieldName))
                    $sTmpLine = "\t\t//\$arFieldsConfig[\"$sFieldName\"] = array(\"controlid\"=>\"sel$sCamelName\",\"label\"=>$sLabel,\"length\"=>$iLength,\"type\"=>array());";
                elseif($this->is_primarykey($sFieldName))
                    if($isUpdate)
                        $sTmpLine = "\t\t$sComment\$arFieldsConfig[\"$sFieldName\"] = array(\"controlid\"=>\"txt$sCamelName\",\"label\"=>$sLabel,\"length\"=>$iLength,\"type\"=>array($sType));";
                    else
                        $sTmpLine = "\t\t//\$arFieldsConfig[\"$sFieldName\"] = array(\"controlid\"=>\"txt$sCamelName\",\"label\"=>$sLabel,\"length\"=>$iLength,\"type\"=>array($sType));";  
                else
                    $sTmpLine = "\t\t$sComment\$arFieldsConfig[\"$sFieldName\"] = array(\"controlid\"=>\"txt$sCamelName\",\"label\"=>$sLabel,\"length\"=>$iLength,\"type\"=>array($sType));";                
                
                $arTmpLine[]=$sTmpLine;
            }
        }//fin campos para validacion antes de insertar
        $arLines[] = implode("\n",$arTmpLine);
        $arLines[] = "\t\treturn \$arFieldsConfig;";          
    }//controlleradd_ins_getinsertvalidate()

    //func_15
    protected function controlleradd_ins_buildinsertform(&$arLines)
    {
        $arLines[] = "\t\t\$oForm = new HelperForm(\"frmInsert\");";
        $arLines[] = "\t\t\$oForm->add_class(\"form-horizontal\");";
        $arLines[] = "\t\t\$oForm->add_style(\"margin-bottom:0\");";
        $arLines[] = "\t\t\$arFields = \$this->build_insert_fields(\$usePost);";
        $arLines[] = "\t\t\$oForm->add_controls(\$arFields);";
        $arLines[] = "\t\treturn \$oForm;";        
    }
    
    //func_16
    protected function controlleradd_ins_insert(&$arLines)
    {
        $arLines[] = "\t\t\$this->go_to_401(\$this->oPermission->is_not_insert());";
        $arLines[] = "\t\t//php and js validation";
        $arLines[] = "\t\t\$arFieldsConfig = \$this->get_insert_validate();";
        $arLines[] = "\t\tif(\$this->is_inserting())";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$oAlert = new AppHelperAlertdiv();";
        $arLines[] = "\t\t\t\$oAlert->use_close_button();";
        $arLines[] = "\t\t\t\$arFieldsValues = \$this->get_fields_from_post();";
        $arLines[] = "\t\t\t\$oValidate = new ComponentValidate(\$arFieldsConfig,\$arFieldsValues);";
        $arLines[] = "\t\t\t\$arErrData = \$oValidate->get_error_field();";
        $arLines[] = "\t\t\tif(\$arErrData)";
        $arLines[] = "\t\t\t{";
        $arLines[] = "\t\t\t\t\$oAlert->set_type(\"e\");";
        $arLines[] = "\t\t\t\t\$oAlert->set_title(tr_mdb_data_not_saved);";
        $arLines[] = "\t\t\t\t\$oAlert->set_content(\"Field <b>\".\$arErrData[\"label\"].\"</b> \".\$arErrData[\"message\"]);";
        $arLines[] = "\t\t\t}";
        $arLines[] = "\t\t\telse";
        $arLines[] = "\t\t\t{";
        $arLines[] = "\t\t\t\t//\$this->$this->sModelObjectName"."->log_save_insert();";
        $arLines[] = "\t\t\t\t\$this->$this->sModelObjectName"."->set_attrib_value(\$arFieldsValues);";
        $arLines[] = "\t\t\t\t\$this->$this->sModelObjectName"."->set_insert_user(\$this->oSessionUser->get_id());";
        $arLines[] = "\t\t\t\t//\$this->$this->sModelObjectName"."->set_platform(\$this->oSessionUser->get_platform());";
        $arLines[] = "\t\t\t\t\$this->$this->sModelObjectName"."->autoinsert();";
        $arLines[] = "\t\t\t\tif(\$this->$this->sModelObjectName"."->is_error())";
        $arLines[] = "\t\t\t\t{";
        $arLines[] = "\t\t\t\t\t\$oAlert->set_type(\"e\");";
        $arLines[] = "\t\t\t\t\t\$oAlert->set_title(tr_mdb_data_not_saved);";
        $arLines[] = "\t\t\t\t\t\$oAlert->set_content(tr_mdb_error_trying_to_save);";
        $arLines[] = "\t\t\t\t}";
        $arLines[] = "\t\t\t\telse//insert ok";
        $arLines[] = "\t\t\t\t{";
        $arLines[] = "\t\t\t\t\t\$this->set_get(\"id\",\$this->$this->sModelObjectName"."->get_last_insert_id());";        
        $arLines[] = "\t\t\t\t\t\$oAlert->set_title(tr_mdb_data_saved);";
        $arLines[] = "\t\t\t\t\t\$this->reset_post();";
        $arLines[] = "\t\t\t\t\t//\$this->go_to_after_succes_cud();";
        $arLines[] = "\t\t\t\t}";
        $arLines[] = "\t\t\t}//no error";
        $arLines[] = "\t\t}//fin if is_inserting (post action=save)";
        $arLines[] = "\t\t//Si hay errores se recupera desde post";
        $arLines[] = "\t\tif(\$arErrData || \$this->is_postback()) \$oForm = \$this->build_insert_form(1);";
        $arLines[] = "\t\telse \$oForm = \$this->build_insert_form();";
        $arLines[] = "\t\t//ANCHOR DOWN";
        $arLines[] = "\t\t//\$oAnchorDown = new HelperAnchor();";
        $arLines[] = "\t\t//SCRUMBS";
        $arLines[] = "\t\t\$oScrumbs = \$this->build_insert_scrumbs();";
        $arLines[] = "\t\t//TABS";
        $arLines[] = "\t\t\$oTabs = \$this->build_insert_tabs();";        
        $arLines[] = "\t\t//OPER BUTTONS";
        $arLines[] = "\t\t\$oOpButtons = \$this->build_insert_opbuttons();";
        $arLines[] = "\t\t//JAVASCRIPT";
        $arLines[] = "\t\t\$oJavascript = new HelperJavascript();";
        $arLines[] = "\t\t\$oJavascript->set_validate_config(\$arFieldsConfig);";
        $arLines[] = "\t\t\$oJavascript->set_formid(\"frmInsert\");";
        $arLines[] = "\t\t//\$oJavascript->set_focusid(\"id_all\");";
        $arLines[] = "\t\t//VIEW SET";
        $arLines[] = "\t\t\$this->oView->add_var(\$oAnchorDown,\"oAnchorDown\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oScrumbs,\"oScrumbs\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oTabs,\"oTabs\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oOpButtons,\"oOpButtons\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oAlert,\"oAlert\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oForm,\"oForm\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oJavascript,\"oJavascript\");";
        //$arLines[] = "\t\t\$this->oView->set_path_view(\"$this->sControllerName/view_insert\");";
        $arLines[] = "\t\t\$this->oView->show_page();";          
    }
    
    //func_17 -> func_2
    protected function controlleradd_upd_buildupdatescrumbs(&$arLines)
    {
        $arLines[] = "\t\t\$arLinks = array();";
        $arLines[] ="\t\t\$sUrlLink = \"\$this->build_url();";
        $arLines[] = "\t\t\$arLinks[\"list\"]=array(\"href\"=>\$sUrlLink,\"innerhtml\"=>$this->sTranslatePrefix"."entities);";
        $arLines[] = "\t\t\$sUrlLink = \$this->build_url(\$this->sModuleName,NULL,\"update\",\"id=\".\$this->get_get(\"id\"));";
        $arLines[] = "\t\t\$arLinks[\"detail\"]=array(\"href\"=>\$sUrlLink,\"innerhtml\"=>$this->sTranslatePrefix"."entity.\": \".\$this->$this->sModelObjectName"."->get_id().\" - \".\$this->$this->sModelObjectName"."->get_description());";
        $arLines[] = "\t\t\$oScrumbs = new AppHelperBreadscrumbs(\$arLinks);";        
        $arLines[] = "\t\treturn \$oScrumbs;";        
    }            
    
    //func_18
    protected function controlleradd_upd_buildupdatetabs(&$arLines)
    {
        //$this->arTranslation[] = $this->sTranslatePrefix."entity_detail";
        $this->arTranslation[] = $this->sTranslatePrefix."updtabs_detail";
        $this->arTranslation[] = $this->sTranslatePrefix."updtabs_foreigndata";        
        
        $arLines[] = "\t\t\$arTabs = array();";
        $arLines[] = "\t\t\$sUrlTab = \$this->build_url(\$this->sModuleName,NULL,\"update\",\"id=\".\$this->get_get(\"id\"));";
        $arLines[] = "\t\t\$arTabs[\"detail\"]=array(\"href\"=>\$sUrlTab,\"innerhtml\"=>$this->sTranslatePrefix"."updtabs_detail);";
        $arLines[] = "\t\t//\$sUrlTab =\$this->build_url(\$this->sModuleName,\"foreingamodule\",\"get_list_by_foreign=\".\$this->get_get(\"id_parent_foreign\"));";
        $arLines[] = "\t\t//\$arTabs[\"foreigndata\"]=array(\"href\"=>\$sUrlTab,\"innerhtml\"=>$this->sTranslatePrefix"."updtabs_foreigndata);";
        $arLines[] = "\t\t\$oTabs = new AppHelperHeadertabs(\$arTabs,\"detail\");";
        $arLines[] = "\t\treturn \$oTabs;";        
    }
    
    //func_19 -> func_3
    protected function controlleradd_upd_buildupdateopbuttons(&$arLines)
    {
        $this->arTranslation[] = $this->sTranslatePrefix."updopbutton_reload";//en un futuro se necesitará
        $this->arTranslation[] = $this->sTranslatePrefix."updopbutton_list";
        $this->arTranslation[] = $this->sTranslatePrefix."updopbutton_insert";
        $this->arTranslation[] = $this->sTranslatePrefix."updopbutton_quarantine";
        $this->arTranslation[] = $this->sTranslatePrefix."updopbutton_delete";
        $this->arTranslation[] = $this->sTranslatePrefix."updopbutton_multiassign";//en un futuro se necesitará
        $this->arTranslation[] = $this->sTranslatePrefix."updopbutton_singleassign";//en un futuro se necesitará
        
        $arLines[] = "\t\t\$arOpButtons = array();";
        $arLines[] = "\t\tif(\$this->oPermission->is_select())";
        $arLines[] = "\t\t\t\$arOpButtons[\"list\"]=array(\"href\"=>\$this->build_url(),\"icon\"=>\"awe-search\",\"innerhtml\"=>$this->sTranslatePrefix"."updopbutton_list);";
        $arLines[] = "\t\t//if(\$this->oPermission->is_insert())";
        $arLines[] = "\t\t\t//\$arOpButtons[\"insert\"]=array(\"href\"=>\$this->build_url(\$this->sModuleName,NULL,\"insert\"),\"icon\"=>\"awe-plus\",\"innerhtml\"=>$this->sTranslatePrefix"."updopbutton_insert);";
        $arLines[] = "\t\tif(\$this->oPermission->is_quarantine())";
        $arLines[] = "\t\t\t\$arOpButtons[\"delete\"]=array(\"href\"=>\$this->build_url(\$this->sModuleName,NULL,\"quarantine\",\"id=\".\$this->get_get(\"id\"),\"icon\"=>\"awe-remove\",\"innerhtml\"=>$this->sTranslatePrefix"."updopbutton_quarantine);";
        $arLines[] = "\t\t//if(\$this->oPermission->is_delete())";
        $arLines[] = "\t\t\t//\$arOpButtons[\"delete\"]=array(\"href\"=>\$this->build_url(\$this->sModuleName,NULL,\"delete\",\"id=\".\$this->get_get(\"id\"),\"icon\"=>\"awe-remove\",\"innerhtml\"=>$this->sTranslatePrefix"."updopbutton_delete);";        
        $arLines[] = "\t\t\$oOpButtons = new AppHelperButtontabs($this->sTranslatePrefix"."entities);";
        $arLines[] = "\t\t\$oOpButtons->set_tabs(\$arOpButtons);";
        $arLines[] = "\t\treturn \$oOpButtons;";        
    }
    
    //func_20
    protected function controlleradd_upd_buildupdatefields(&$arLines)
    {
        $arLines[] = "\t\t\$arFields = array(); \$oAuxField = NULL; \$oAuxLabel = NULL;";

        //UPDATE
        foreach($this->arFields as $sFieldName)
        {
            if(!in_array($sFieldName,$this->arNotInsert))
            {   
                $sComment = "";
                if($this->do_comment($sFieldName)) $sComment="//";
                
                $isIdForeign = $this->is_foreignkey($sFieldName);
                $sFieldType = $this->get_field_type($sFieldName);
                $sLabel = $this->sTranslatePrefix."upd_".$this->get_translation_label($sFieldName);
                $this->arTranslation[] = $sLabel;            
                $sCamelName = sep_to_camel($sFieldName);
                $arLines[] = "\t\t//$sFieldName";
                if($isIdForeign)
                {    
                    $sObjectName = $this->fieldname_to_objectname($sFieldName);
                    $sModelName = $this->fieldname_to_modelname($sFieldName);
                    $arLines[] = "\t\t$sComment\$$sObjectName = new $sModelName();";
                    if(!$this->is_foreignkey_array($sFieldName))
                        $arLines[] = "\t\t$sComment\$arOptions = \$$sObjectName"."->get_picklist();";
                    else 
                    {
                        $sType = str_replace("id_type_","",$sFieldName);
                        $arLines[] = "\t\t$sComment\$arOptions = \$$sObjectName"."->get_picklist_by_type(\"$sType\");";
                    }
                    $arLines[] = "\t\t$sComment\$oAuxField = new HelperSelect(\$arOptions,\"sel$sCamelName\",\"sel$sCamelName\");";
                    $arLines[] = "\t\t$sComment\$oAuxField->set_value_to_select(\$this->get_post(\"sel$sCamelName\"));";
                    $arLines[] = "\t\t$sComment\$oAuxWrapper = new ApphelperControlGroup(\$oAuxField,new HelperLabel(\"sel$sCamelName\",$sLabel));";
                }
                else
                    $arLines[] = "\t\t$sComment\$oAuxField = new HelperInputText(\"txt$sCamelName\",\"txt$sCamelName\");";
                
                if(in_array($sFieldName,$this->arPks))
                    $arLines[] = "\t\t$sComment\$oAuxField->is_primarykey();";             
                
                if($isIdForeign)
                {
                    $arLines[] = "\t\t$sComment\$oAuxField->set_value_to_select(\$this->$this->sModelObjectName"."->get_$sFieldName());";
                    $arLines[] = "\t\t$sComment"."if(\$usePost) \$oAuxField->set_value_to_select(\$this->get_post(\"sel$sCamelName\"));";
                    $arLines[] = "\t\t$sComment\$oAuxLabel = new HelperLabel(\"sel$sCamelName\",$sLabel,\"lbl$sCamelName\");";
                }
                else    
                {   
                    if($sFieldType=="int")
                    {   
                        $arLines[] = "\t\t$sComment\$oAuxField->set_value(\$this->$this->sModelObjectName"."->get_$sFieldName());";
                        $arLines[] = "\t\t$sComment"."if(\$usePost) \$oAuxField->set_value(dbbo_int(\$this->get_post(\"txt$sCamelName\")));";
                    }
                    elseif(in_array($sFieldType,$this->arDecimalTypes))
                    {    
                        $arLines[] = "\t\t$sComment\$oAuxField->set_value(dbbo_numeric2(\$this->$this->sModelObjectName"."->get_$sFieldName()));";
                        $arLines[] = "\t\t$sComment"."if(\$usePost) \$oAuxField->set_value(dbbo_numeric2(\$this->get_post(\"txt$sCamelName\")));";
                    }
                    else//string
                    {                       
                        $arLines[] = "\t\t$sComment\$oAuxField->set_value(\$this->$this->sModelObjectName"."->get_$sFieldName());";
                        $arLines[] = "\t\t$sComment"."if(\$usePost) \$oAuxField->set_value(\$this->get_post(\"txt$sCamelName\"));";
                    }
                    $arLines[] = "\t\t$sComment\$oAuxLabel = new HelperLabel(\"txt$sCamelName\",$sLabel,\"lbl$sCamelName\");";
                }
                $arLines[] = "\t\t$sComment\$oAuxLabel->add_class(\"labelpk\");"; 
                $arLines[] = "\t\t//\$oAuxField->readonly();\$oAuxField->add_class(\"readonly\");";
                $arLines[] = "\t\t$sComment\$arFields[] = new ApphelperControlGroup(\$oAuxField,\$oAuxLabel);";
            }
        }//foreach update fields
        $this->arTranslation[] = $this->sTranslatePrefix."upd_savebutton";
        $arLines[] = "\t\t//BUTTON SAVE";
        $arLines[] = "\t\t\$oAuxField = new HelperButtonBasic(\"butSave\",$this->sTranslatePrefix"."upd_savebutton);";
        $arLines[] = "\t\t\$oAuxField->add_class(\"btn btn-primary\");";
        $arLines[] = "\t\t\$oAuxField->set_js_onclick(\"update();\");";
        $arLines[] = "\t\tif(\$this->oPermission->is_update())";
        $arLines[] = "\t\t\t\$arFields[] = new ApphelperFormactions(array(\$oAuxField));";        
        $arLines[] = "\t\t//AUDIT INFO";
        $arLines[] = "\t\t\$sRegInfo = \$this->get_audit_info(\$this->$this->sModelObjectName"."->get_insert_user(),\$this->$this->sModelObjectName"."->get_insert_date()";
        $arLines[] = "\t\t,\$this->$this->sModelObjectName"."->get_update_user(),\$this->$this->sModelObjectName"."->get_update_date());";
        $arLines[] = "\t\t\$oAuxField = new AppHelperFormhead(null,\$sRegInfo);";
        $arLines[] = "\t\t\$oAuxField->set_span();";
        $arLines[] = "\t\t\$arFields[] = \$oAuxField;";
        $arLines[] = "\t\t//POST INFO";
        $arLines[] = "\t\t\$oAuxField = new HelperInputHidden(\"hidAction\",\"hidAction\");";
        $arLines[] = "\t\t\$arFields[] = \$oAuxField;";
        $arLines[] = "\t\t\$oAuxField = new HelperInputHidden(\"hidPostback\",\"hidPostback\");";
        $arLines[] = "\t\t\$arFields[] = \$oAuxField;";
        $arLines[] = "\t\treturn \$arFields;";        
    }
    
    //func_21
    protected function controlleradd_upd_buildupdateform(&$arLines)
    {
        //$arLines[] = "\t\t\$id = \$this->$this->sModelObjectName"."->get_id();";
        //$arLines[] = "\t\tif(\$id)";
        //$arLines[] = "\t\t{";
        $arLines[] = "\t\t\$oForm = new HelperForm(\"frmUpdate\");";
        $arLines[] = "\t\t\$oForm->add_class(\"form-horizontal\");";
        $arLines[] = "\t\t\$oForm->add_style(\"margin-bottom:0\");";
        $arLines[] = "\t\tif(\$this->oPermission->is_read()&&\$this->oPermission->is_not_update())";
        $arLines[] = "\t\t\t\$oForm->readonly();";
        $arLines[] = "\t\t\$arFields = \$this->build_update_fields(\$usePost);";
        $arLines[] = "\t\t\$oForm->add_controls(\$arFields);";
        //$arLines[] = "\t\t}//if(id)";
        //$arLines[] = "\t\telse//!id";
        //$arLines[] = "\t\t\t\$this->go_to_404();";
        $arLines[] = "\t\treturn \$oForm;";        
    }
    
    //func_22
    protected function controlleradd_upd_update(&$arLines)
    {
        $arLines[] = "\t\t//\$this->go_to_401((\$this->oPermission->is_not_read() && \$this->oPermission->is_not_update())||\$this->oSessionUser->is_not_dataowner());";
        $arLines[] = "\t\t\$this->go_to_401(\$this->oPermission->is_not_read() && \$this->oPermission->is_not_update());";
        //$this->go_to_404(!$this->oSuspicionHead->is_in_table());
        $arLines[] = "\t\t\$this->go_to_404(!\$$this->sModelObjectName"."->is_in_table());";
        $arLines[] = "\t\t//Validacion con PHP y JS";
        $arLines[] = "\t\t\$arFieldsConfig = \$this->get_update_validate();";
        $arLines[] = "\t\tif(\$this->is_updating())";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$oAlert = new AppHelperAlertdiv();";
        $arLines[] = "\t\t\t\$oAlert->use_close_button();";
        $arLines[] = "\t\t\t\$arFieldsValues = \$this->get_fields_from_post();";
        $arLines[] = "\t\t\t\$oValidate = new ComponentValidate(\$arFieldsConfig,\$arFieldsValues);";
        $arLines[] = "\t\t\t\$arErrData = \$oValidate->get_error_field();";
        $arLines[] = "\t\t\tif(\$arErrData)";
        $arLines[] = "\t\t\t{";
        $arLines[] = "\t\t\t\t\$oAlert->set_type(\"e\");";
        $arLines[] = "\t\t\t\t\$oAlert->set_title(tr_mdb_data_not_saved);";
        $arLines[] = "\t\t\t\t\$oAlert->set_content(\"Field <b>\".\$arErrData[\"label\"].\"</b> \".\$arErrData[\"message\"]);";
        $arLines[] = "\t\t\t}";
        $arLines[] = "\t\t\telse";
        $arLines[] = "\t\t\t{";
        $arLines[] = "\t\t\t\t\$this->$this->sModelObjectName"."->set_attrib_value(\$arFieldsValues);";
        $arLines[] = "\t\t\t\t//\$this->$this->sModelObjectName"."->set_description(\$$this->sModelObjectName"."->get_field1().\" \".\$$this->sModelObjectName"."->get_field2());";
        $arLines[] = "\t\t\t\t\$this->$this->sModelObjectName"."->set_update_user(\$this->oSessionUser->get_id());";
        //$arLines[] = "\t\t\t\t//\$this->$this->sModelObjectName"."->set_platform(\$this->oSessionUser->get_platform());";
        $arLines[] = "\t\t\t\t\$this->$this->sModelObjectName"."->autoupdate();";
        $arLines[] = "\t\t\t\tif(\$this->$this->sModelObjectName"."->is_error())";
        $arLines[] = "\t\t\t\t{";
        $arLines[] = "\t\t\t\t\t\$oAlert->set_type(\"e\");";
        $arLines[] = "\t\t\t\t\t\$oAlert->set_title(tr_mdb_data_not_saved);";
        $arLines[] = "\t\t\t\t\t\$oAlert->set_content(tr_mdb_error_trying_to_save);";
        $arLines[] = "\t\t\t\t}//no error";
        $arLines[] = "\t\t\t\telse//update ok";
        $arLines[] = "\t\t\t\t{";
        $arLines[] = "\t\t\t\t\t//\$this->$this->sModelObjectName"."->load_by_id();";
        $arLines[] = "\t\t\t\t\t\$oAlert->set_title(tr_mdb_data_saved);";
        $arLines[] = "\t\t\t\t\t\$this->reset_post();";
        $arLines[] = "\t\t\t\t\t//\$this->go_to_after_succes_cud();";
        $arLines[] = "\t\t\t\t}//error save";
        $arLines[] = "\t\t\t}//error validation";
        $arLines[] = "\t\t}//is_updating()";
        $arLines[] = "\t\tif(\$arErrData) \$oForm = \$this->build_update_form(1);";
        $arLines[] = "\t\telse \$oForm = \$this->build_update_form(); ";
        $arLines[] = "\t\t//ANCHOR DOWN";
        $arLines[] = "\t\t//\$oAnchorDown = new HelperAnchor();";        
        $arLines[] = "\t\t//SCRUMBS";       
        $arLines[] = "\t\t\$oScrumbs = \$this->build_update_scrumbs();";        
        $arLines[] = "\t\t//TABS";
        $arLines[] = "\t\t\$oTabs = \$this->build_update_tabs();";
        $arLines[] = "\t\t//OPER BUTTONS";
        $arLines[] = "\t\t\$oOpButtons = \$this->build_update_opbuttons();";
        $arLines[] = "\t\t//JAVASCRIPT";
        $arLines[] = "\t\t\$oJavascript = new HelperJavascript();";
        $arLines[] = "\t\t\$oJavascript->set_updateaction();";
        $arLines[] = "\t\t\$oJavascript->set_validate_config(\$arFieldsConfig);";
        $arLines[] = "\t\t\$oJavascript->set_formid(\"frmUpdate\");";
        $arLines[] = "\t\t//\$oJavascript->set_focusid(\"id_all\");";
        $arLines[] = "\t\t//VIEW SET";
        $arLines[] = "\t\t\$this->oView->add_var(\$oAnchorDown,\"oAnchorDown\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oScrumbs,\"oScrumbs\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oTabs,\"oTabs\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oOpButtons,\"oOpButtons\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oAlert,\"oAlert\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oForm,\"oForm\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oJavascript,\"oJavascript\");";
        //$arLines[] = "\t\t\$this->oView->set_path_view(\"$this->sControllerName/view_update\");";
        $arLines[] = "\t\t\$this->oView->show_page();";        
    }
    
    protected function controlleradd_multiassign(&$arLines)
    {
        $arLines[] = "\t\t\$this->go_to_401(\$this->oPermission->is_not_pick());";
        $arLines[] = "\t\t\$oAlert = new AppHelperAlertdiv();";
        $arLines[] = "\t\t\$oAlert->use_close_button();";
        $arLines[] = "\t\t\$sMessage = \$this->get_session_message(\$sMessage);";
        $arLines[] = "\t\tif(\$sMessage)";
        $arLines[] = "\t\t\t\$oAlert->set_title(\$sMessage);";
        $arLines[] = "\t\t\$sMessage = \$this->get_session_message(\$sMessage,\"e\");";
        $arLines[] = "\t\tif(\$sMessage)";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$oAlert->set_type();";
        $arLines[] = "\t\t\t\$oAlert->set_title(\$sMessage);";
        $arLines[] = "\t\t}";
        $arLines[] = "\t\t//build controls and add data to global arFilterControls and arFilterFields";
        $arLines[] = "\t\t\$arColumns = \$this->get_multiassign_columns();";
        //FILTROS
        $arLines[] = "\t\t//FILTERS";
        $arLines[] = "\t\t//Indica los filtros que se recuperarán. Hace un \$this->arFilters = arra(fieldname=>value=>..)";
        $arLines[] = "\t\t\$this->load_config_multiassign_filters();";        
        $arLines[] = "\t\t\$oFilter = new ComponentFilter();";
        $arLines[] = "\t\t\$oFilter->set_fieldnames(\$this->get_filter_fieldnames());";
        $arLines[] = "\t\t//Indica que no se guardara en sesion por nombre de campo sino por nombre de control";
        $arLines[] = "\t\t//para esto es necesario respetar el estricto camelcase";
        $arLines[] = "\t\t\$oFilter->use_field_prefix();";
        $arLines[] = "\t\t//Guarda en sesion y post los campos enviados, los de orden y página";
        $arLines[] = "\t\t\$oFilter->refresh();";
        $arLines[] = "\t\t\$this->set_multiassignfilters_from_post();";
        $arLines[] = "\t\t\$arObjFilter = \$this->get_multiassign_filters();";
        
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_orderby(\$this->get_orderby());";
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_ordertype(\$this->get_ordertype());";
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_filters(\$this->get_filter_searchconfig());";
        $arLines[] = "\t\t//hierarchy recover";
        $arLines[] = "\t\t//\$this->$this->sModelObjectName"."->set_select_user(\$this->oSessionUser->get_id());";
        $arLines[] = "\t\t//RECOVER DATALIST";
        $arLines[] = "\t\t\$arList = \$this->$this->sModelObjectName"."->get_select_all_ids();";
        $arLines[] = "\t\t\$iRequestPage = \$this->get_post(\"selPage\");";
        $arLines[] = "\t\t\$oPage = new ComponentPage(\$arList,\$iRequestPage);";
        $arLines[] = "\t\t\$arList = \$oPage->get_items_to_show();";
        $arLines[] = "\t\t\$arList = \$this->$this->sModelObjectName"."->get_select_all_by_ids(\$arList);";
        $arLines[] = "\t\t//TABLE";
        $arLines[] = "\t\t//This method adds objects controls to search list form";
        $arLines[] = "\t\t\$oTableAssign = new HelperTableTyped(\$arList,\$arColumns);";
        $arLines[] = "\t\t\$oTableAssign->set_fields(\$arObjFilter);";
        $arLines[] = "\t\t\$oTableAssign->set_module(\$this->get_current_module());";
        $arLines[] = "\t\t\$oTableAssign->add_class(\"table table-striped table-bordered table-condensed\");";

        $arTmpLine = array();
        foreach($this->arPks as $sPk)
            $arTmpLine[]="\"$sPk\"";
        
        $arLines[] = "\t\t\$oTableAssign->set_keyfields(array(".implode(",",$arTmpLine)."));";
        $arLines[] = "\t\t\$oTableAssign->set_orderby(\$this->get_orderby());";
        $arLines[] = "\t\t\$oTableAssign->set_orderby_type(\$this->get_ordertype());";        
        $arLines[] = "\t\t\$oTableAssign->set_column_pickmultiple();//columna checks";
        $arLines[] = "\t\t\$oTableAssign->merge_pks();//claves separadas por coma";
        $arLines[] = "\t\t\$oTableAssign->set_column_picksingle();//crea funcion";
        $arLines[] = "\t\t\$oTableAssign->set_column_detail();//detail column";
        $arLines[] = "\t\t//esto se define en el padre";
        $arLines[] = "\t\t//\$oTableAssign->set_multiassign(array(\"keys\"=>array(\"k\"=>1,\"k2\"=>2)));";
        $arLines[] = "\t\t\$oTableAssign->set_multiadd(array(\"keys\"=>array(\"k\"=>\$this->get_get(\"k\"),\"k2\"=>\$this->get_get(\"k2\"))));";
        //$arLines[] = "\t\t//\$oTableAssign->set_column_delete();";
        $arLines[] = "\t\t\$oTableAssign->set_current_page(\$oPage->get_current());";
        $arLines[] = "\t\t\$oTableAssign->set_first_page(\$oPage->get_first());";
        $arLines[] = "\t\t\$oTableAssign->set_prvious_page(\$oPage->get_previous());";
        $arLines[] = "\t\t\$oTableAssign->set_next_page(\$oPage->get_next());";
        $arLines[] = "\t\t\$oTableAssign->set_last_page(\$oPage->get_last());";
        $arLines[] = "\t\t\$oTableAssign->set_total_regs(\$oPage->get_total_regs());";
        $arLines[] = "\t\t\$oTableAssign->set_total_pages(\$oPage->get_total());";
        $arLines[] = "\t\t//CRUD BUTTONS BAR";
        $arLines[] = "\t\t\$oOpButtons = new AppHelperButtontabs($this->sTranslatePrefix"."entities);";
        $arLines[] = "\t\t\$oOpButtons->set_tabs(\$this->build_multiassign_buttons());";
        $arLines[] = "\t\t\$oJavascript = new HelperJavascript();";        
        $arLines[] = "\t\t\$oJavascript->set_filters(\$this->get_filter_controls_id());";
        $arLines[] = "\t\t\$oJavascript->set_focusid(\"id_all\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oJavascript,\"oJavascript\");";
        $arLines[] = "\t\t\$this->oView->set_layout(\"onecolumn\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oOpButtons,\"oOpButtons\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oAlert,\"oAlert\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oTableAssign,\"oTableAssign\");";
        //$arLines[] = "\t\t\$this->oView->set_path_view(\"$this->sControllerName/view_assign\");";
        $arLines[] = "\t\t\$this->oView->show_page();";
    }
    
    protected function controlleradd_singleassign(&$arLines,$sModuleName)
    {
        $arLines[] = "\t\t\$this->go_to_401(\$this->oPermission->is_not_pick());";        
        $arLines[] = "\t\t\$oAlert = new AppHelperAlertdiv();";
        $arLines[] = "\t\t\$oAlert->use_close_button();";
        $arLines[] = "\t\t\$sMessage = \$this->get_session_message(\$sMessage);";
        $arLines[] = "\t\tif(\$sMessage) \$oAlert->set_title(\$sMessage);";
        $arLines[] = "\t\t\$sMessage = \$this->get_session_message(\$sMessage,\"e\");";
        $arLines[] = "\t\tif(\$sMessage)";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$oAlert->set_type();";
        $arLines[] = "\t\t\t\$oAlert->set_title(\$sMessage);";
        $arLines[] = "\t\t}";
        //FILTROS
        $arLines[] = "\t\t//build controls and add data to global arFilterControls and arFilterFields";
        $arLines[] = "\t\t\$arColumns = \$this->get_singleassign_columns();";
        $arLines[] = "\t\t//Indica los filtros que se recuperarán. Hace un \$this->arFilters = arra(fieldname=>value=>..)";
        $arLines[] = "\t\t\$this->load_config_singleassign_filters();";
        //FILTROS
        $arLines[] = "\t\t\$oFilter = new ComponentFilter();";        
        $arLines[] = "\t\t\$oFilter->set_fieldnames(\$this->get_filter_fieldnames());";
        $arLines[] = "\t\t//Indica que no se guardara en sesion por nombre de campo sino por nombre de control";
        $arLines[] = "\t\t//para esto es necesario respetar el estricto camelcase";
        $arLines[] = "\t\t\$oFilter->use_field_prefix();";
        $arLines[] = "\t\t//Guarda en sesion y post los campos enviados, los de orden y página";
        $arLines[] = "\t\t\$oFilter->refresh();";
        $arLines[] = "\t\t\$this->set_singleassignfilters_from_post();";
        $arLines[] = "\t\t\$arObjFilter = \$this->get_singleassign_filters();";
        
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_orderby(\$this->get_orderby());";
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_ordertype(\$this->get_ordertype());";
        $arLines[] = "\t\t\$this->$this->sModelObjectName"."->set_filters(\$this->get_filter_searchconfig());";
        //$arLines[] = "\t\t//\$this->$this->sModelObjectName->set_select_user(\$this->oSessionUser->get_id());";
        $arLines[] = "\t\t\$arList = \$this->$this->sModelObjectName"."->get_select_all_ids();";
        $arLines[] = "\t\t\$iRequestPage = \$this->get_post(\"selPage\");";
        $arLines[] = "\t\t\$oPage = new ComponentPage(\$arList,\$iRequestPage);";
        $arLines[] = "\t\t\$arList = \$oPage->get_items_to_show();";
        $arLines[] = "\t\t\$arList = \$this->$this->sModelObjectName"."->get_select_all_by_ids(\$arList);";
        
        $arLines[] = "\t\t//TABLE";
        $arLines[] = "\t\t\$oTableAssign = new HelperTableTyped(\$arList,\$arColumns);";
        $arLines[] = "\t\t\$oTableAssign->set_fields(\$arObjFilter);";
        $arLines[] = "\t\t\$oTableAssign->set_module(\$this->get_current_module());";
        $arLines[] = "\t\t\$oTableAssign->add_class(\"table table-striped table-bordered table-condensed\");";
        
        $arTmpLine = array();
        foreach($this->arPks as $sPk)
            $arTmpLine[]="\"$sPk\"";
        
        $arLines[] = "\t\t\$oTableAssign->set_keyfields(array(".implode(",",$arTmpLine)."));";
        $arLines[] = "\t\t\$oTableAssign->set_orderby(\$this->get_orderby());";
        $arLines[] = "\t\t\$oTableAssign->set_orderby_type(\$this->get_ordertype());";        
        $arLines[] = "\t\t\$oTableAssign->set_column_picksingle();";
        $arLines[] = "\t\t\$oTableAssign->set_singleadd(array(\"destkey\"=>\"txtCode\",\"destdesc\"=>\"Desc\",\"keys\"=>\"id\",\"descs\"=>\"description,bo_login\",\"close\"=>1));";
        $arLines[] = "\t\t\$oTableAssign->set_current_page(\$oPage->get_current());";
        $arLines[] = "\t\t\$oTableAssign->set_next_page(\$oPage->get_next());";
        $arLines[] = "\t\t\$oTableAssign->set_first_page(\$oPage->get_first());";
        $arLines[] = "\t\t\$oTableAssign->set_last_page(\$oPage->get_last());";
        $arLines[] = "\t\t\$oTableAssign->set_total_regs(\$oPage->get_total_regs());";
        $arLines[] = "\t\t\$oTableAssign->set_total_pages(\$oPage->get_total());";
        $arLines[] = "\t\t//BARRA CRUD";
        $arLines[] = "\t\t\$oOpButtons = new AppHelperButtontabs($this->sTranslatePrefix"."entities);";
        $arLines[] = "\t\t\$oOpButtons->set_tabs(\$this->build_singleassign_buttons());";
        $arLines[] = "\t\t//JAVASCRIPT";
        $arLines[] = "\t\t\$oJavascript = new HelperJavascript();";
        $arLines[] = "\t\t\$oJavascript->set_filters(\$this->get_filter_controls_id());";
        $arLines[] = "\t\t\$oJavascript->set_focusid(\"id_all\");";
        $arLines[] = "\t\t//TO VIEW";
        $arLines[] = "\t\t\$this->oView->add_var(\$oJavascript,\"oJavascript\");";
        $arLines[] = "\t\t\$this->oView->set_layout(\"onecolumn\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oOpButtons,\"oOpButtons\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oAlert,\"oAlert\");";
        $arLines[] = "\t\t\$this->oView->add_var(\$oTableAssign,\"oTableAssign\");";
        //$arLines[] = "\t\t\$this->oView->set_path_view(\"$sModuleName/view_assign\");";
        $arLines[] = "\t\t\$this->oView->show_page();";     
    }
    
    protected function get_controller_content()
    {
        //crea el acronimo de lat tabla para las traducciones
        $this->load_table_acronym();
        //genera el prefijo de traduccion tipo tr[acronimo]_
        $this->load_translate_prefix();
        //carga las primeras traducciones sobre todo de entidades.
        $this->load_extra_translate();
        
        $this->sTimeNow = date("d-m-Y H:i")." (SPAIN)";
        $arLines = array();
        $arLines[] = "<?php";
        $arLines[] = "/**
 * @author Module Builder 1.1.4
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name $this->sControllerClassName
 * @file $this->sControllerFileName   
 * @date $this->sTimeNow
 * @observations: 
 * @requires:
 */";   
        //IMPORTACION DE CLASES:
        $arLines[] = "//TFW";
        $arLines[] = "import_component(\"page,validate,filter\");";
        $arLines[] = "import_helper(\"form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed\");";
        $arLines[] = "import_helper(\"input_password,button_basic,raw,div,javascript\");";
        $arLines[] = "//APP";
        $sImport = $this->get_import_models();
        $arLines[] = "import_model(\"user,$sImport\");";
        $arLines[] = "import_appmain(\"controller,view,behaviour\");";
        $arLines[] = "import_appbehaviour(\"picklist\");";
        $arLines[] = "import_apphelper(\"listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs\");";
        $arLines[] = "";
        
        //BEGIN CLASS
        $arLines[] = "class $this->sControllerClassName extends TheApplicationController";
        $arLines[] = "{";
        $arLines[] = "\tprotected \$$this->sModelObjectName;";
        $arLines[] = "";
        $arLines[] = "\tpublic function __construct()";
        $arLines[] = "\t{";
        $this->controlleradd_func_construct($arLines);
        $arLines[] = "\t}";
        $arLines[] = "";
        
//<editor-fold defaultstate="collapsed" desc="BUILDING LIST">
        $arLines[] = "//<editor-fold defaultstate=\"collapsed\" desc=\"LIST\">";
        //build_list_scrumbs
        $arLines[] = "\t//list_1";
        $arLines[] = "\tprotected function build_list_scrumbs()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_buildlistscrumbs($arLines);
        $arLines[] = "\t}";
        $arLines[] = "";
        
        $arLines[] = "\t//list_2";
        $arLines[] = "\tprotected function build_list_tabs()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_buildlisttabs($arLines);
        $arLines[] = "\t}";
        $arLines[] = "";
        
        //build_listoperation_buttons();
        $arLines[] = "\t//list_3";
        $arLines[] = "\tprotected function build_listoperation_buttons()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_buildlistoperationbuttons($arLines);
        $arLines[] = "\t}//build_listoperation_buttons()";
        $arLines[] = "";
        
        //load_config_list_filters();
        $arLines[] = "\t//list_4";
        $arLines[] = "\tprotected function load_config_list_filters()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_loadconfiglistfilters($arLines);
        $arLines[] = "\t}//load_config_list_filters()";
        $arLines[] = "";        
        
        $arLines[] = "\t//list_5";
        $arLines[] = "\tprotected function set_listfilters_from_post()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_setlistfiltersfrompost($arLines);
        $arLines[] = "\t}//set_listfilters_from_post()";
        $arLines[] = "";
        
        //get_list_filters();
        $arLines[] = "\t//list_6";
        $arLines[] = "\tprotected function get_list_filters()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t//CAMPOS";
        $this->controlleradd_lst_getlistfilters($arLines);        
        $arLines[] = "\t\treturn \$arFields;";
        $arLines[] = "\t}//get_list_filters()";
        $arLines[] = "";        
        //get_list_columns();
        $arLines[] = "\t//list_7";
        $arLines[] = "\tprotected function get_list_columns()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_getlistcolumns($arLines);
        $arLines[] = "\t\treturn \$arColumns;";
        $arLines[] = "\t}//get_list_columns()";
        $arLines[] = "";        
        $arLines[] = "\t//list_8";
        $arLines[] = "\tpublic function get_list()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_getlist($arLines);
        $arLines[] = "\t}//get_list()";
        $arLines[] = "//</editor-fold>";
        $arLines[] = "";
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="BUILDING INSERT">        
        $arLines[] = "//<editor-fold defaultstate=\"collapsed\" desc=\"INSERT\">";
        //build_insert_srumbs
        $arLines[] = "\t//insert_1";
        $arLines[] = "\tprotected function build_insert_scrumbs()";
        $arLines[] = "\t{";
        $this->controlleradd_ins_buildinsertscrumbs($arLines);
        $arLines[] = "\t}//build_insert_scrumbs()";
        $arLines[] = "";
        $arLines[] = "\t//insert_2";
        $arLines[] = "\tprotected function build_insert_tabs()";
        $arLines[] = "\t{";
        $this->controlleradd_ins_buildinserttabs($arLines);
        $arLines[] = "\t}//build_insert_tabs()";       
        
        //build_insert_opbuttons
        $arLines[] = "\t//insert_3";
        $arLines[] = "\tprotected function build_insert_opbuttons()";
        $arLines[] = "\t{";
        $this->controlleradd_ins_buildinsertopbuttons($arLines);
        $arLines[] = "\t}//build_insert_opbuttons()";
        $arLines[] = "";

        //build_insert_fields
        $arLines[] = "\t//insert_4";
        $arLines[] = "\tprotected function build_insert_fields(\$usePost=0)";
        $arLines[] = "\t{";
        $this->controlleradd_ins_buildinsertfields($arLines);
        $arLines[] = "\t}//build_insert_fields()";
        $arLines[] = "";
        
        //get_insert_validate
        $arLines[] = "\t//insert_5";
        $arLines[] = "\tprotected function get_insert_validate()";
        $arLines[] = "\t{";
        $this->controlleradd_ins_getinsertvalidate($arLines);
        $arLines[] = "\t}//get_insert_validate";
        $arLines[] = "";

        //build_insert_form
        $arLines[] = "\t//insert_6";
        $arLines[] = "\tprotected function build_insert_form(\$usePost=0)";
        $arLines[] = "\t{";
        $this->controlleradd_ins_buildinsertform($arLines);
        $arLines[] = "\t}//build_insert_form()";
        $arLines[] = "";
        
        //insert()
        $arLines[] = "\t//insert_7";
        $arLines[] = "\tpublic function insert()";
        $arLines[] = "\t{";
        $this->controlleradd_ins_insert($arLines);
        $arLines[] = "\t}//insert()";
        $arLines[] = "//</editor-fold>";
        $arLines[] = "";
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="BUILDING UPDATE">        
        $arLines[] = "//<editor-fold defaultstate=\"collapsed\" desc=\"UPDATE\">";
        $arLines[] = "\t//update_1";
        $arLines[] = "\tprotected function build_update_scrumbs()";
        $arLines[] = "\t{";
        $this->controlleradd_upd_buildupdatescrumbs($arLines);
        $arLines[] = "\t}//build_update_scrumbs()";
        $arLines[] = "";
        $arLines[] = "\t//update_2";
        $arLines[] = "\tprotected function build_update_tabs()";
        $arLines[] = "\t{";
        $this->controlleradd_upd_buildupdatetabs($arLines);
        $arLines[] = "\t}//build_update_tabs()";
        $arLines[] = "";
        $arLines[] = "\t//update_3";
        $arLines[] = "\tprotected function build_update_opbuttons()";
        $arLines[] = "\t{";
        $this->controlleradd_upd_buildupdateopbuttons($arLines);
        $arLines[] = "\t}//build_update_opbuttons()";
        $arLines[] = "";
        //build_update_fields
        $arLines[] = "\t//update_4";
        $arLines[] = "\tprotected function build_update_fields(\$usePost=0)";
        $arLines[] = "\t{";
        $this->controlleradd_upd_buildupdatefields($arLines);
        $arLines[] = "\t}//build_update_fields()";
        $arLines[] = "";
        //get_update_validate
        $arLines[] = "\t//update_5";
        $arLines[] = "\tprotected function get_update_validate()";
        $arLines[] = "\t{";
        $this->controlleradd_ins_getinsertvalidate($arLines,1);
        $arLines[] = "\t}//get_update_validate";
        $arLines[] = "";        
        $arLines[] = "\t//update_6";
        $arLines[] = "\tprotected function build_update_form(\$usePost=0)";
        $arLines[] = "\t{";        
        //$arLines[] = "\t\t\$id = \$this->get_get(\"id\");";
        $this->controlleradd_upd_buildupdateform($arLines);
        $arLines[] = "\t}//build_update_form()";
        $arLines[] = "";
        $arLines[] = "\t//update_7";
        $arLines[] = "\tpublic function update()";
        $arLines[] = "\t{";
        $this->controlleradd_upd_update($arLines);
        $arLines[] = "\t}//update()";
        $arLines[] = "//</editor-fold>";
        $arLines[] = "";
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="BUILDING DELETE">
        $arLines[] = "//<editor-fold defaultstate=\"collapsed\" desc=\"DELETE\">";
        $arLines[] = "\t//delete_1";
        $arLines[] = "\tprotected function single_delete()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t\$id = \$this->get_get(\"id\");";
        $arLines[] = "\t\tif(\$id)";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->set_id(\$id);";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->autodelete();";
        $arLines[] = "\t\t\tif(\$this->$this->sModelObjectName"."->is_error())";
        $arLines[] = "\t\t\t{";
        $arLines[] = "\t\t\t\t\$this->isError = TRUE;";
        $arLines[] = "\t\t\t\t\$this->set_session_message(tr_mdb_error_trying_to_delete);";
        $arLines[] = "\t\t\t}";
        $arLines[] = "\t\t\telse";
        $arLines[] = "\t\t\t{";
        $arLines[] = "\t\t\t\t\$this->set_session_message(tr_mdb_data_deleted);";
        $arLines[] = "\t\t\t}";
        $arLines[] = "\t\t}//si existe el id";
        $arLines[] = "\t\telse";
        $arLines[] = "\t\t\t\$this->set_session_message(tr_mdb_error_key_not_supplied,\"e\");";
        $arLines[] = "\t}//single_delete()";
        $arLines[] = "";
        $arLines[] = "\t//delete_2";
        $arLines[] = "\tprotected function multi_delete()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t//Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro";
        $arLines[] = "\t\t\$arKeys = \$this->get_listkeys();";
        $arLines[] = "\t\tforeach(\$arKeys as \$sKey)";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$id = \$sKey;";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->set_id(\$id);";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->autodelete();";
        $arLines[] = "\t\t\tif(\$this->$this->sModelObjectName"."->is_error())";
        $arLines[] = "\t\t\t{";
        $arLines[] = "\t\t\t\t\$this->isError = true;";
        $arLines[] = "\t\t\t\t\$this->set_session_message(tr_mdb_error_trying_to_delete,\"e\");";
        $arLines[] = "\t\t\t}";
        $arLines[] = "\t\t}//foreach arkeys";
        $arLines[] = "\t\tif(!\$this->isError)";
        $arLines[] = "\t\t\t\$this->set_session_message(tr_mdb_data_deleted);";
        $arLines[] = "\t}//multi_delete()";
        $arLines[] = "";
        $arLines[] = "\t//delete_3";
        $arLines[] = "\tpublic function delete()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t//\$this->go_to_401(\$this->oPermission->is_not_delete()||\$this->oSessionUser->is_not_dataowner());";
        $arLines[] = "\t\t\$this->go_to_401(\$this->oPermission->is_not_delete());";
        $arLines[] = "\t\t\$this->isError = FALSE;";
        $arLines[] = "\t\t//Si ocurre un error se guarda en isError";
        $arLines[] = "\t\tif(\$this->is_multidelete())";
        $arLines[] = "\t\t\t\$this->multi_delete();";
        $arLines[] = "\t\telse";
        $arLines[] = "\t\t\t\$this->single_delete();";
        $arLines[] = "\t\t//Si no ocurrio errores en el intento de borrado";
        $arLines[] = "\t\tif(!\$this->isError)";
        $arLines[] = "\t\t\t\$this->go_to_after_succes_cud();";
        $arLines[] = "\t\telse//delete ok";
        $arLines[] = "\t\t\t\$this->go_to_list();";
        $arLines[] = "\t}\t//delete()";
        $arLines[] = "//</editor-fold>";
        $arLines[] = "";
        $arLines[] = "//<editor-fold defaultstate=\"collapsed\" desc=\"QUARANTINE\">";
        $arLines[] = "\t//quarantine_1";
        $arLines[] = "\tprotected function single_quarantine()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t\$id = \$this->get_get(\"id\");";
        $arLines[] = "\t\tif(\$id)";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->set_id(\$id);";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->autoquarantine();";
        $arLines[] = "\t\t\tif(\$this->$this->sModelObjectName"."->is_error())";
        $arLines[] = "\t\t\t\t\$this->set_session_message(tr_mdb_error_trying_to_delete);";
        $arLines[] = "\t\t\telse";
        $arLines[] = "\t\t\t\t\$this->set_session_message(tr_mdb_data_deleted);";
        $arLines[] = "\t\t}//else no id";
        $arLines[] = "\t\telse";
        $arLines[] = "\t\t\t\$this->set_session_message(tr_mdb_error_key_not_supplied,\"e\");";
        $arLines[] = "\t}//single_quarantine()";
        $arLines[] = "";        
        $arLines[] = "\t//quarantine_2";
        $arLines[] = "\tprotected function multi_quarantine()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t\$this->isError = FALSE;";
        $arLines[] = "\t\t//Intenta recuperar pkeys sino pasa a id, y en ultimo caso lo que se ha pasado por parametro";
        $arLines[] = "\t\t\$arKeys = \$this->get_listkeys();";
        $arLines[] = "\t\tforeach(\$arKeys as \$sKey)";
        $arLines[] = "\t\t{";
        $arLines[] = "\t\t\t\$id = \$sKey;";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->set_id(\$id);";
        $arLines[] = "\t\t\t\$this->$this->sModelObjectName"."->autoquarantine();";
        $arLines[] = "\t\t\tif(\$this->$this->sModelObjectName"."->is_error())";
        $arLines[] = "\t\t\t{";
        $arLines[] = "\t\t\t\t\$isError = true;";
        $arLines[] = "\t\t\t\t\$this->set_session_message(tr_mdb_error_trying_to_delete,\"e\");";
        $arLines[] = "\t\t\t}";
        $arLines[] = "\t\t}";
        $arLines[] = "\t\tif(!\$isError)";
        $arLines[] = "\t\t\t\$this->set_session_message(tr_mdb_data_deleted);";
        $arLines[] = "\t}//multi_quarantine()";
        $arLines[] = "";
        $arLines[] = "\t//quarantine_3";
        $arLines[] = "\tpublic function quarantine()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t//\$this->go_to_401(\$this->oPermission->is_not_quarantine()||\$this->oSessionUser->is_not_dataowner());";        
        $arLines[] = "\t\t\$this->go_to_401(\$this->oPermission->is_not_quarantine());";        
        $arLines[] = "\t\tif(\$this->is_multiquarantine())";
        $arLines[] = "\t\t\t\$this->multi_quarantine();";
        $arLines[] = "\t\telse";
        $arLines[] = "\t\t\t\$this->single_quarantine();";
        $arLines[] = "\t\t\$this->go_to_list();";
        $arLines[] = "\t\tif(!\$this->isError)";
        $arLines[] = "\t\t\t\$this-go_to_after_succes_cud();";
        $arLines[] = "\t\telse //quarantine ok";        
        $arLines[] = "\t\t\t\$this->go_to_list();";
        $arLines[] = "\t}//quarantine()";
        $arLines[] = "";
        $arLines[] = "//</editor-fold>";
        $arLines[] = "";
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="BUILDING MULTIASSIGN">
        $this->arTranslation[] = $this->sTranslatePrefix."clear_filters";
        $this->arTranslation[] = $this->sTranslatePrefix."refresh";
        $this->arTranslation[] = $this->sTranslatePrefix."multiadd";
        $this->arTranslation[] = $this->sTranslatePrefix."closeme";
        $this->arTranslation[] = $this->sTranslatePrefix."entities";
        
        $arLines[] = "//<editor-fold defaultstate=\"collapsed\" desc=\"MULTIASSIGN\">";
        $arLines[] = "\t//multiassign_1";
        $arLines[] = "\tprotected function build_multiassign_buttons()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t\$arOpButtons = array();";
        $arLines[] = "\t\t\$arOpButtons[\"filters\"]=array(\"href\"=>\"javascript:reset_filters();\",\"icon\"=>\"awe-magic\",\"innerhtml\"=>$this->sTranslatePrefix"."clear_filters);";
        $arLines[] = "\t\t\$arOpButtons[\"reload\"]=array(\"href\"=>\"javascript:TfwControl.form_submit();\",\"icon\"=>\"awe-refresh\",\"innerhtml\"=>$this->sTranslatePrefix"."refresh);";
        $arLines[] = "\t\t\$arOpButtons[\"multiadd\"]=array(\"href\"=>\"javascript:multiadd();\",\"icon\"=>\"awe-external-link\",\"innerhtml\"=>$this->sTranslatePrefix"."multiadd);";
        $arLines[] = "\t\t\$arOpButtons[\"closeme\"]=array(\"href\"=>\"javascript:closeme();\",\"icon\"=>\"awe-remove-sign\",\"innerhtml\"=>$this->sTranslatePrefix"."closeme);";
        $arLines[] = "\t\t\$oOpButtons = new AppHelperButtontabs($this->sTranslatePrefix"."entities);";
        $arLines[] = "\t\t\$oOpButtons->set_tabs(\$arOpButtons);";
        $arLines[] = "\t\treturn \$oOpButtons;";
        $arLines[] = "\t}//build_multiassign_buttons()";
        $arLines[] = "";
        //load_config_multiassign_filters();
        $arLines[] = "\t//multiassign_2";
        $arLines[] = "\tprotected function load_config_multiassign_filters()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_loadconfiglistfilters($arLines);
        $arLines[] = "\t}//load_config_multiassign_filters()";
        $arLines[] = "";
        $arLines[] = "\t//multiassign_3";
        $arLines[] = "\tprotected function get_multiassign_filters()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t//CAMPOS";
        $this->controlleradd_lst_getlistfilters($arLines,0);
        $arLines[] = "\t\treturn \$arFields;";
        $arLines[] = "\t}//get_multiassign_filters()";
        $arLines[] = "";       
        $arLines[] = "\t//multiassign_4";
        $arLines[] = "\tprotected function set_multiassignfilters_from_post()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_setlistfiltersfrompost($arLines);
        $arLines[] = "\t}//set_multiassignfilters_from_post()";
        $arLines[] = "";        
        //get_multiassign_columns();
        $arLines[] = "\t//multiassign_5";
        $arLines[] = "\tprotected function get_multiassign_columns()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_getlistcolumns($arLines,0);
        $arLines[] = "\t\treturn \$arColumns;";
        $arLines[] = "\t}//get_multiassign_columns()";
        $arLines[] = ""; 
        $arLines[] = "\t//multiassign_6";
        $arLines[] = "\tpublic function multiassign()";
        $arLines[] = "\t{";
        $this->controlleradd_multiassign($arLines);
        $arLines[] = "\t}//multiassign()";
        $arLines[] = "//</editor-fold>";
        $arLines[] = "";
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="BUILDING SINGLEASSIGN">        
        $arLines[] = "//<editor-fold defaultstate=\"collapsed\" desc=\"SINGLEASSIGN\">";
        $arLines[] = "\t//singleassign_1";
        $arLines[] = "\tprotected function build_singleassign_buttons()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t\$arButTabs = array();";
        $arLines[] = "\t\t\$arButTabs[\"filters\"]=array(\"href\"=>\"javascript:reset_filters();\",\"icon\"=>\"awe-magic\",\"innerhtml\"=>$this->sTranslatePrefix"."clear_filters);";
        $arLines[] = "\t\t\$arButTabs[\"reload\"]=array(\"href\"=>\"javascript:TfwControl.form_submit();\",\"icon\"=>\"awe-refresh\",\"innerhtml\"=>$this->sTranslatePrefix"."refresh);";
        $arLines[] = "\t\t\$arButTabs[\"closeme\"]=array(\"href\"=>\"javascript:closeme();\",\"icon\"=>\"awe-remove-sign\",\"innerhtml\"=>$this->sTranslatePrefix"."closeme);";
        $arLines[] = "\t\treturn \$arButTabs;";
        $arLines[] = "\t}//build_singleassign_buttons()";
        $arLines[] = "";
        //load_config_multiassign_filters();
        $arLines[] = "\t//singleassign_2";
        $arLines[] = "\tprotected function load_config_singleassign_filters()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_loadconfiglistfilters($arLines);
        $arLines[] = "\t}//load_config_singleassign_filters()";
        $arLines[] = "";        
        
        //get_singleassign_filters();
        $arLines[] = "\t//singleassign_3";
        $arLines[] = "\tprotected function get_singleassign_filters()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t//CAMPOS";
        $this->controlleradd_lst_getlistfilters($arLines,0);
        $arLines[] = "\t\treturn \$arFields;";
        $arLines[] = "\t}//get_singleassign_filters()";
        $arLines[] = "";        
        
        $arLines[] = "\t//singleassign_4";
        $arLines[] = "\tprotected function set_singleassignfilters_from_post()";
        $arLines[] = "\t{";
        $this->controlleradd_lst_setlistfiltersfrompost($arLines);
        $arLines[] = "\t}//set_singleassignfilters_from_post()";
        $arLines[] = "";
        
        //get_singleassign_columns();
        $arLines[] = "\t//singleassign_5";
        $arLines[] = "\tprotected function get_singleassign_columns()";
        $arLines[] = "\t{";
        //SINGLEASSIGN - COLUMNS
        $this->controlleradd_lst_getlistcolumns($arLines,0);
        $arLines[] = "\t\treturn \$arColumns;";
        $arLines[] = "\t}//get_singleassign_columns()";
        $arLines[] = ""; 
        $arLines[] = "\t//singleassign_6";
        $arLines[] = "\tpublic function singleassign()";
        $arLines[] = "\t{";
        $this->controlleradd_singleassign($arLines);        
        $arLines[] = "\t}//singleassign()";
        $arLines[] = "//</editor-fold>";
        $arLines[] = "";
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="BUILDING EXTRAS">        
        $arLines[] = "//<editor-fold defaultstate=\"collapsed\" desc=\"EXTRAS\">";
        $arLines[] = "\tpublic function addsellers()";
        $arLines[] = "\t{";
        $arLines[] = "\t\t\$sUrl = \$this->get_assign_backurl(array(\"k\",\"k2\"));";
        $arLines[] = "\t\tif(\$this->get_get(\"close\"))";
        $arLines[] = "\t\t\t\$this->js_colseme_and_parent_refresh();";
        $arLines[] = "\t\telse";
        $arLines[] = "\t\t\t\$this->js_parent_refresh();";
        $arLines[] = "\t\t\$this->js_go_to(\$sUrl);";
        $arLines[] = "\t}";
        $arLines[] = "//</editor-fold>";
//</editor-fold>        
        $arLines[] = "}//end controller";//fin clase
        $sContent = implode("\n",$arLines);
        return $sContent;        
    }//get_controller_content()  
 
//</editor-fold> 

//<editor-fold defaultstate="collapsed" desc="INSERT (CREATES NEW MODULE)">
    //insert_1
    private function build_insert_scrumbs()
    {        
        $sUrlTab = $this->build_url();
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_entities);
        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_entity_insert);
        
        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    private function build_insert_tabs()
    {        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Builder");
        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"queries");
        $arTabs["queries"]=array("href"=>$sUrlTab,"innerhtml"=>"Queries");

        $sUrlTab = $this->build_url($this->sModuleName,"php");
        $arTabs["php"]=array("href"=>$sUrlTab,"innerhtml"=>"Php");
        
        $sUrlTab = $this->build_url($this->sModuleName,"javascript");
        $arTabs["javascript"]=array("href"=>$sUrlTab,"innerhtml"=>"Javascript");
        
        $oTabs = new AppHelperHeadertabs($arTabs,"lines");
        return $oTabs;
    }//build_list_tabs
        
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arButTabs = array();
        //$arButTabs["list"]=array("href"=>build_url(),"icon"=>"awe-search","innerhtml"=>tr_mdb_list);
        
        $oOpButtons = new AppHelperButtontabs(tr_mdb_entities);
        $oOpButtons->set_tabs($arButTabs);
        return $oOpButtons;
    }//build_insert_opbuttons()

    protected function build_insert_form($usePost=0)
    {
        $oForm = new HelperForm("frmInsert");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_insert_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_insert_form()
    
    protected function build_insert_fields($usePost=0)
    {   //bugpg();
        //bug($arFields);die;
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_mdb_new.tr_mdb_entity);
               
        $oBehavBuilder = new AppBehaviourModuleBuilder();
        $arTables = $oBehavBuilder->get_db_tables();

        $oAuxField = new HelperSelect($arTables,"selTable","selTable");
        $oAuxField->add_class("input-large");
        $oAuxField->is_primarykey();
        if($usePost) $oAuxField->set_value($this->get_post("selTable"));
        $oAuxLabel = new HelperLabel("selTable",tr_mdb_model,"lblModel");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
      
        $oAuxField = new HelperButtonBasic("butSave",tr_mdb_ins_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("insert();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;
       
        return $arFields;
    }//build_insert_fields()
    
    /**
     * Funcion general que realiza toda la lógica de creacion de M, C y Vistas
     * Antes de llamar a este metodo se necesita dar un valor a $this->sTableName
     */
    protected function build_mvc()
    {
        $oBehavBuilder = new AppBehaviourModuleBuilder($this->sTableName);
        $this->arFields = $oBehavBuilder->get_fields_and_types_for_model();
        //separa los tipos campos y longitudes en los atributos tipo array creados para este fin
        $this->load_fieldtype($this->arFields);
        
        $sTempPath = "C:/temp_eaf";
        //MODELO
        $sPathModelFolder = TFW_PATH_FOL_ROOTDS."the_application/models";
        
        $sModelClassName = $this->table_to_classname();
        $sModelFileName = $this->table_to_filename();
        //$this->oFile->set_path_folder_target($sPathModelFolder);
        $this->oFile->set_path_folder_target($sTempPath);
        $this->oFile->set_filename_target($sModelFileName);
        if($this->oFile->target_exists()) $this->oFile->target_remove();
        $sContent = $this->get_model_content($sModelClassName);
        $this->oFile->add_content($sContent);
        
        //CONTROLADOR
        $this->arFields = $oBehavBuilder->get_fields_and_types_for_controller();
        $this->load_fieldtype($this->arFields);        
        $sPathModelFolder = TFW_PATH_FOL_ROOTDS."the_application/controllers";
        $sModuleName = $this->table_to_controllername();
        //bug($sModuleName); die;
        $sControllerFileName = $this->table_to_filename("controller","c");
        $sFolderName = $this->table_to_foldername();
        $sPathControllerFolder = $sPathModelFolder.DS.$sFolderName;
        if(!is_dir($sPathControllerFolder)) mkdir($sPathControllerFolder);
        
        //$this->oFile->set_path_folder_target($sPathControllerFolder);
        $this->oFile->set_path_folder_target($sTempPath);
        $this->oFile->set_filename_target($sControllerFileName);
        if($this->oFile->target_exists()) $this->oFile->target_remove();
        $sContent = $this->get_controller_content();
        $this->oFile->add_content($sContent);

        //VISTAS
        $sPathViewFolder = TFW_PATH_FOL_ROOTDS."the_application/views/";
        $sPathViewFolder .= $sFolderName;
        if(!is_dir($sPathViewFolder)) mkdir($sPathViewFolder);
        
        $arViewTemplates = array("view_assign","view_index","view_insert","view_update");
        //if($sPathViewFolder.)
        $sPathViewTemplateFolder = TFW_PATH_FOL_ROOTDS."the_application/views/users/";
        
        $this->oFile->set_path_folder_source($sPathViewTemplateFolder);
        //$this->oFile->set_path_folder_target($sPathViewFolder);
        $this->oFile->set_path_folder_target($sTempPath);
        foreach($arViewTemplates as $sViewName)
        {
            $sFileName = $sViewName.".php";
            $this->oFile->set_filename_source($sFileName);
            $this->oFile->set_filename_target($sFileName);
            if(!$this->oFile->target_exists());
                $this->oFile->copy();
        }
        //ENGLISH TRANSLATION FILE
        $sTransFile = "translate_$sModuleName.php";
        $sTransFolder = TFW_PATH_FOL_ROOTDS."the_application/translations/english";
        $this->oFile->set_path_folder_target($sTempPath);
        //$this->oFile->set_path_folder_target($sTransFolder);
        $this->oFile->set_filename_target($sTransFile);
        if($this->oFile->target_exists())
            $this->oFile->target_remove();
        $this->oFile->add_content($this->get_translation_content($sModuleName));
        
        //bug($this->arFields);
    }//build_mvc()

    public function insert()
    {
        //Validacion con PHP y JS
        $arFieldsConfig = array();
        $arFieldsConfig["table"] = array("id"=>"selTable","label"=>tr_mdb_table,"length"=>100,"type"=>array("required"));

        if($this->is_inserting())
        {
            //array de configuracion length=>i,type=>array("numeric","required")
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
           
            $arFieldsValues = $this->get_fields_from_post();
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            //bug($arErrData); die;
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_mdb_module_not_built);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            //no error
            else
            {
                $this->build_mvc();
            }//no error
        }//fin if post action=save

        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_insert_form(1);
        else $oForm = $this->build_insert_form();
        //bug($oForm); die;
       
        //TABS
        $oTabs = $this->build_insert_tabs();
        //SCRUMBS
        $oScrumbs = $this->build_insert_scrumbs();
        //OPER BUTTONS
        $oOpButtons = $this->build_insert_opbuttons();
                
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_focusid("id_all");

        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        //$this->oView->set_path_view("view_insert");
        $this->oView->show_page();

    }//insert()
    
//</editor-fold>
    
//<editor-fold defaultstate="collapsed" desc="PARSETOOL">
    //insert_1
    private function build_parsetool_scrumbs()
    {        
        $sUrlTab = $this->build_url();
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_entities);
        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_entity_insert);
        
        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    private function build_parsetool_tabs()
    {        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Builder");
        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"queries");
        $arTabs["queries"]=array("href"=>$sUrlTab,"innerhtml"=>"Queries");

        $sUrlTab = $this->build_url($this->sModuleName,"php");
        $arTabs["php"]=array("href"=>$sUrlTab,"innerhtml"=>"Php");
        
        $sUrlTab = $this->build_url($this->sModuleName,"javascript");
        $arTabs["javascript"]=array("href"=>$sUrlTab,"innerhtml"=>"Javascript");
        
        $oTabs = new AppHelperHeadertabs($arTabs,"lines");
        return $oTabs;
    }//build_list_tabs
    
    protected function build_parsetool_opbuttons()
    {
        $arButTabs = array();
        $arButTabs["list"]=array("href"=>build_url(),"icon"=>"awe-search","innerhtml"=>tr_mdb_list);
        return $arButTabs;
    }//build_parsetool_opbuttons()

    protected function build_parsetool_form($usePost=0)
    {
        $oForm = new HelperForm("frmParseTool");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_parsetool_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_parsetool_form()
    
    protected function build_parsetool_fields()
    {   //bugpg();
        //bug($arFields);die;
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_mdb_entity.tr_mdb_parse_tool);
        //txaText
        $oAuxField = new HelperTextarea("txaText","txaText");
        $oAuxField->add_class("input-large");
        $oAuxField->set_innerhtml($this->get_post("txaText"));
        //$oAuxField->set_js_keypress("postback(this);");
        $oAuxField->on_entersubmit();
        $oAuxLabel = new HelperLabel("txaText",tr_mdb_parse_text,"lblText");
        
        $oAuxField->add_style("width:800px; height:200px;");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //txaParsed
        $oAuxField = new HelperTextarea("txaParsed","txaParsed");
        $oAuxField->add_style("width:800px; height:200px; background-color:#ddd;");
        $oAuxField->readonly();
        $oAuxField->set_innerhtml($this->get_post("txaParsed"));
        $oAuxLabel = new HelperLabel("txaParsed",tr_mdb_parsed_text,"lblParsed");
        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        $oAuxField = new HelperButtonBasic("butSave",tr_mdb_upd_savebutton);
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
    }//build_parsetool_fields()
    
    public function parsetool()
    {
        $sTextToParse = $this->get_post("txaText");
        $sParsedText = $this->parse_text($sTextToParse);
        //bug($sParsedText);
        $this->set_post("txaParsed",$sParsedText);
        $oForm = $this->build_parsetool_form();       
        $oJavascript = new HelperJavascript();
        $oJavascript->set_formid("frmParseTool");

        //bug($oForm); die;
        //TABS
        $oTabs = $this->build_parsetool_tabs();
        //SCRUMBS
        $oScrumbs = $this->build_parsetool_scrumbs();

        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_focusid("id_all");

        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        //$this->oView->set_path_view("modulebuilder/view_parsetool");
        $this->oView->set_path_view("_base/view_insert");
        $this->oView->show_page();
    }//parsetool()
    
    protected function parse_text($sTextToParse)
    {
        $sTextToParse = str_replace("$","\\$",$sTextToParse);
        $sTextToParse = str_replace("\"","\\\"",$sTextToParse);
        $sTextToParse = str_replace("\t","\\t",$sTextToParse);
        //eol:\r\n
        $sTextToParse = str_replace("\n","\n\$arLines[] = \"",$sTextToParse);
        $sTextToParse = str_replace("\r","\";\r",$sTextToParse);
        $sTextToParse = "\$arLines[] = \"".$sTextToParse."\"";
        return $sTextToParse.";";
    }

//</editor-fold>
        
//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    private function build_list_scrumbs()
    {        
        $sUrlTab = $this->build_url();
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>"modulebuilder");
                
        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_list_scrumbs()
    
    //list_2
    private function build_list_tabs()
    {        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Builder");
        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"queries");
        $arTabs["lines"]=array("href"=>$sUrlTab,"innerhtml"=>"Queries");

        $sUrlTab = $this->build_url($this->sModuleName,NULL,"parsetool");
        $arTabs["parsetool"]=array("href"=>$sUrlTab,"innerhtml"=>"Parse Build");

        $oTabs = new AppHelperHeadertabs($arTabs,"lines");
        return $oTabs;
    }//build_list_tabs

    //list_3
    private function build_listoperation_buttons()
    {
        $arButTabs = array();
//        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_mdb_clear_filters);
//        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_mdb_refresh);
//        if($this->oPermission->is_insert())
//            $arButTabs["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_mdb_new);
//        if($this->oPermission->is_delete())
//            $arButTabs["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_mdb_delete_selection);
        //if($this->oPermission->is_quarantine())
        //$arButTabs["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_mdb_quarantine);
        //crea ventana
        //$arButTabs["multiassign"]=array("href"=>"javascript:multiassign_window('order_head',null,'multiassign','order_head','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_mdb_asign_selection);
        //$arButTabs["singleassign"]=array("href"=>"javascript:single_pick('order_head','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_mdb_asign_selection);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_entities);
        $oOpButtons->set_tabs($arButTabs);        
        return $oOpButtons;
    }//build_listoperation_buttons()
    
    protected function build_table()
    {
        $sUrlBase = "/images/custom/";
        $arLinks = array();
        $arLinks[] = array("src"=>$sUrlBase."icon_dash1.png","href"=>$this->build_url("customers"),"innerhtml"=>"inner: customers","alt"=>"alt: to customers","text"=>"Moudlo Customers");
        $arLinks[] = array("src"=>$sUrlBase."icon_dash1_340x75_.png","href"=>build_url(),"innerhtml"=>"2","alt"=>"ok2","text"=>array("text","link"));
        $arLinks[] = array("src"=>$sUrlBase."icon_dash1_496_133.jpg","href"=>build_url(),"innerhtml"=>"3","alt"=>"ok3","text"=>array("text","link"));
        $arLinks[] = array("src"=>$sUrlBase."icon_dash1_181x40_.png","href"=>build_url("products"),"innerhtml"=>"inner: products","alt"=>"alt:to products","text"=>"text to products");
        $arLinks[] = array("src"=>$sUrlBase."icon_dash1_340x75_.png","href"=>build_url(),"innerhtml"=>"2","alt"=>"ok2","text"=>3);
        $arLinks[] = array("src"=>$sUrlBase."icon_dash1_496_133.jpg","href"=>build_url(),"innerhtml"=>"3","alt"=>"ok3","text"=>"el texto medio que hay");
        $arLinks[] = array("src"=>$sUrlBase."icon_dash1_181x40_.png","href"=>$this->build_url("customers"),"innerhtml"=>"1","alt"=>"ok1","text"=>2);
        
        //$arLinks = $this->oPicture->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $iRequestPage = 1;
        $iItemsPerPage = $this->get_post("selItemsPerPage");
        $oPage = new ComponentPage($arLinks,$iRequestPage,$iItemsPerPage);
        
        $arLink = $oPage->get_items_to_show();
        //bug($oPage);
        //bugss();
        //bug($arLinks);
        //$arLinks = $this->oPicture->get_select_all_by_ids($arLinks);
        
        $oTableList = new HelperImagelist($arLinks,"ulAlbum");
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_items_per_page($oPage->get_items_per_page());
        $oTableList->set_first_page($oPage->get_first());
        $oTableList->set_previous_page($oPage->get_previous());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs($oPage->get_total_regs());
        $oTableList->set_total_pages($oPage->get_total());
        
        $oTableList->set_li_class("span3");
        return $oTableList;
    }//build_table

    //list_7
    public function get_list()
    {
        //img/assets/sample-image.png
        //errorson();
        //redirige en caso de no tener permiso
        //$this->go_to_401($this->oPermission->is_not_select());
        
        $oAlert = new AppHelperAlertdiv();
        $oAlert->use_close_button();
        $sMessage = $this->get_session_message($sMessage);
        if($sMessage)
            $oAlert->set_title($sMessage);
        $sMessage = $this->get_session_message($sMessage,"e");
        if($sMessage)
        {
            $oAlert->set_type();
            $oAlert->set_title($sMessage);
        }

        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        //$oJavascript->set_focusid("id_all");
        $oScrumbs = $this->build_list_scrumbs();
        $oTabs = $this->build_list_tabs();
        $oOpButtons = $this->build_listoperation_buttons();
        $oTableList = $this->build_table();
        
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableList,"oTableList");
        $this->oView->show_page();

    }//get_list()
//</editor-fold>
    
    //===================
    //       GETS
    //===================    
    protected function get_field_type($sFieldName)
    {
        foreach($this->arFields as $i=>$sName)
            if($sFieldName==$sName)
                return $this->arType[$i];
        return "nf";
    }//get_field_type
    
    protected function get_field_length($sFieldName)
    {
        foreach($this->arFields as $i=>$sName)
            if($sFieldName==$sName)
                return $this->arLength[$i];
        return "nf";        
    }//get_field_length
    
    protected function is_foreignkey($sFieldName){if(strstr($sFieldName,"id_"))return true;return false;}//is_foreignkey()
    protected function is_foreignkey_array($sFieldName){if(strstr($sFieldName,"id_type_"))return true;return false;}//is_foreignkey()
    protected function is_primarykey($sFieldName){return in_array($sFieldName,$this->arPks);}
    protected function do_comment($sFieldName){return in_array($sFieldName,$this->arFieldsComment);}
    protected function get_translation_label($sFieldName){return strtolower($sFieldName);}//get_translation_label()
}
