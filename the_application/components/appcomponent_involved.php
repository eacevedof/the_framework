<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.0
 * @name AppComponentInvolved
 * @file appcomponent_involved.php   
 * @date 25-08-2014 0:33 (SPAIN)
 * @observations: 
 *      Para aplicación de Unión Caribe. Meldings sobre transacciones
 * @requires:
 */
include_once("theapplication_component.php");
class AppComponentInvolved extends TheApplicationComponent
{
    private $oSuspicionArray;
    private $sIdSuspicion;
    private $sIdTransfer;
    //private $sIdObserv;
    
    public function __construct(ModelSuspicionArray $oSsuspicionArray=NULL)
    {
        $this->oSuspicionArray = $oSuspicionArray;
        if(!$this->oSuspicionArray) $this->oSuspicionArray = new ModelSuspicionArray();
    }
    
    public function get_insert_fields($usePost)
    {
        //$arFields = array();
        $arFields1 = $this->build_insert_fields($usePost);
        $arFields2 = $this->build_insert_fields($usePost,1);
        return array_merge($arFields1,$arFields2);
    }//get_insert_fields
    
    public function get_update_fields($usePost)
    {
        $arFields1 = $this->build_update_fields($usePost);
        $arFields2 = $this->build_update_fields($usePost,1);
        return array_merge($arFields1,$arFields2);        
    }//get_update_fields
    
    public function build_insert_fields($usePost,$iInvolved=0)
    {
        $sBackGround = "black";
        $sForeColor = "white";
        //id_suspicion
//        $oSuspicion = new ModelSuspicionHead();
//        $arOptions = $oSuspicion->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdSuspicion","selIdSuspicion");
//        $oAuxField->set_value_to_select($this->get_post("selIdSuspicion"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdSuspicion",tr_sni_ins_id_suspicion));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdSuspicion"));
//        $oAuxLabel = new HelperLabel("selIdSuspicion",tr_sni_ins_id_suspicion,"lblIdSuspicion");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $iInvolvedUi = $iInvolved+1;
        
        if($iInvolvedUi=="1") 
        {
            $sBackGround="#FFBF00";
            $sForeColor="black";
        }
        elseif($iInvolvedUi=="2")
        {
            $sBackGround="#F3FF35";
            $sForeColor="black";            
        }
        
        //number 
        $oAuxField = new HelperInputHidden("hidNumberInvolved_$iInvolved","hidNumberInvolved[]");
        $oAuxField->set_value($iInvolvedUi);
        $arFields[] = $oAuxField;
        
        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">A. INVOLVED PERSON $iInvolvedUi</p>");
        $arFields[] = $oAuxField;
        
        //head3
        $arOptions = $this->oSuspicionArray->get_by_involved1();
        $oAuxField = new ApphelperTableCheckGroup($arOptions,"chkInvolved_$iInvolved");
        $oAuxField->set_sectionid("Involved_$iInvolved");
        if($usePost) $oAuxField->set_values_to_check($this->get_post("chkInvolved_$iInvolved"));
        $arFields[] = $oAuxField;        

        //nr_account
        $oAuxField = new HelperInputText("txtNrAccount_$iInvolved","txtNrAccount[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtNrAccount",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrAccount_$iInvolved",tr_sni_ins_nr_account,"lblNrAccount_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_creditcard
        $oAuxField = new HelperInputText("txtNrCreditcard_$iInvolved","txtNrCreditcard[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtNrCreditcard",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrCreditcard_$iInvolved",tr_sni_ins_nr_creditcard,"lblNrCreditcard_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_mtcn
        $oAuxField = new HelperInputText("txtNrMtcn_$iInvolved","txtNrMtcn[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtNrMtcn",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrMtcn_$iInvolved",tr_sni_ins_nr_mtcn,"lblNrMtcn_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_police
        $oAuxField = new HelperInputText("txtNrPolice_$iInvolved","txtNrPolice[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtNrPolice",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrPolice_$iInvolved",tr_sni_ins_nr_police,"lblNrPolice_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_brief
        $oAuxField = new HelperInputText("txtNrBrief_$iInvolved","txtNrBrief[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtNrBrief",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrBrief_$iInvolved",tr_sni_ins_nr_brief,"lblNrBrief_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //nr_client
        $oAuxField = new HelperInputText("txtNrClient_$iInvolved","txtNrClient[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtNrClient",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrClient_$iInvolved",tr_sni_ins_nr_client,"lblNrClient_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_extra
        $oAuxField = new HelperInputText("txtNrExtra_$iInvolved","txtNrExtra[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtNrExtra",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrExtra_$iInvolved",tr_sni_ins_nr_extra,"lblNrExtra_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">Subject data</p>");
        $arFields[] = $oAuxField;        
        
        //============================
        //          SUBJECT 
        //============================
        $oTabGiros = new ModelTabgiros();
        $oTabGiros->set_gridgiro($this->sIdTransfer);
        $oTabGiros->load_by_gridgiro("AppComponentInvolved.build_insert_fields");

        //Si se ha encontrado el giro
        if($oTabGiros->get_gridgiro())
        {   
            $oTboObserv = new ModelTbobserv();
            $oCliente = new ModelClientes();
            $oOcupacion = new ModelOcupacio();

            $oTboObserv->set_obidgiro($oTabGiros->get_gridgiro());
            $oTboObserv->load_by_transfer();

            $oCliente->set_cicodcli($oTboObserv->get_obcodcli());
            $oCliente->load_by_cicodcli();

            $oOcupacion->set_occdocup($oTabGiros->get_grcdocup());
            $oOcupacion->load_by_occdocup();
        }
        //sino marco el giro como no existente 
        else 
        {
            $oTabGiros = NULL;
            $sMessage = "No existe el giro: $this->sIdTransfer";
            $this->add_error($sMessage);
        }
        
        //Solo Apellidos
        //subj_name APELLIDO (TABGIROS.GRAPSREM)  - BENEFICIARIO: APELLIDOS DEL BENEF. (TABGIROS.GRAPECLI)
        $oAuxField = new HelperInputText("txtSubjName_$iInvolved","txtSubjName[]");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $oAuxField->set_value($oTabGiros->get_grapsrem());   
        }
        else
        {
            $oAuxField->set_value($oTabGiros->get_grapecli());
        }
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjName",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjName_$iInvolved",tr_sni_ins_subj_name,"lblSubjName_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subj_infix1
        $oAuxField = new HelperInputText("txtSubjInfix1_$iInvolved","txtSubjInfix1[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjInfix1",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjInfix1_$iInvolved",tr_sni_ins_subj_infix1,"lblSubjInfix1_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //subj_married_name
        $oAuxField = new HelperInputText("txtSubjMarriedName_$iInvolved","txtSubjMarriedName[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjMarriedName",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjMarriedName_$iInvolved",tr_sni_ins_subj_married_name,"lblSubjMarriedName_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //subj_infix2
        $oAuxField = new HelperInputText("txtSubjInfix2_$iInvolved","txtSubjInfix2[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjInfix2",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjInfix2_$iInvolved",tr_sni_ins_subj_infix2,"lblSubjInfix2_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subj_initials
        $oAuxField = new HelperInputText("txtSubjInitials_$iInvolved","txtSubjInitials[]");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            //$sInitials = $this->get_initials($oTabGiros->get_grnmremi(),$oTabGiros->get_grapsrem());
            $sInitials = $this->get_initials($oTabGiros->get_grnmsrem());
            $oAuxField->set_value($sInitials);
        }
        elseif($oTabGiros)
        {
            //$sInitials = $this->get_initials($oTabGiros->get_grnomcli(),$oTabGiros->get_grapecli());
            $sInitials = $this->get_initials($oTabGiros->get_grnomcli());
            $oAuxField->set_value($sInitials);
        }
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjInitials",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjInitials_$iInvolved",tr_sni_ins_subj_initials,"lblSubjInitials_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        
        
        //Solo Nombres
        //subj_full_name - BENEFICIARIO: NOMBRE COMPLETO DEL BENEF.  (TABGIROS.GRNMBENE)
        $oAuxField = new HelperInputText("txtSubjFullName_$iInvolved","txtSubjFullName[]");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            //$oAuxField->set_value($oTabGiros->get_grnmremi());//Aqui guarda el nombre completo
            $oAuxField->set_value($oTabGiros->get_grnmsrem());//solo nombre
        }
        elseif($oTabGiros)
        {
            //$oAuxField->set_value($oTabGiros->get_grnmbene());//Nombre completo
            $oAuxField->set_value($oTabGiros->get_grnomcli());//solo nombre
        }
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oAuxField->add_class("span4");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjFullName",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjFullName_$iInvolved",tr_sni_ins_subj_full_name,"lblSubjFullName_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subj_type_sex
        $arOptions = $this->oSuspicionArray->get_by_sex();
        $oAuxField = new HelperSelect($arOptions, "selSubjTypeSex_$iInvolved","selSubjTypeSex_$iInvolved");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $this->oSuspicionArray->set_type("sex");
            $this->oSuspicionArray->set_code_erp($oTabGiros->get_gridsexo());
            $this->oSuspicionArray->load_by_erptype();
            $oAuxField->set_value_to_select($this->oSuspicionArray->get_id());
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selSubjTypeSex_$iInvolved"));
        $oAuxLabel = new HelperLabel("selSubjTypeSex_$iInvolved",tr_sni_ins_subj_type_sex,"lblSubjTypeSex_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subj_birthdate
        /*FECHA DE NACIMEINTO   MM/DD/YYYY (CLIENTES.CIFECNAC) SE RECUPERA CON EL ID DEL CLIENTE (CLIENTES.CICODCLI)*/        
        $oAuxField = new HelperDate("datSubjBirthdate_$iInvolved","datSubjBirthdate[]");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $sAuxDate = $oCliente->get_cifecnac();
            //pr("involved cifecnac");//1962-09-21 00:00:00
            //pr($sAuxDate);
            $sAuxDate = $this->get_datefixed($sAuxDate);
            //pr($sAuxDate); 21-09-1962
            $oAuxField->set_value($sAuxDate);
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value($this->get_post("datSubjBirthdate",$iInvolved));
        $oAuxLabel = new HelperLabel("datSubjBirthdate_$iInvolved",tr_sni_ins_subj_birthdate,"lblSubjBirthdate_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //subj_birthplace
        $oAuxField = new HelperInputText("txtSubjBirthplace_$iInvolved","txtSubjBirthplace[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjBirthplace",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjBirthplace_$iInvolved",tr_sni_ins_subj_birthplace,"lblSubjBirthplace_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //subj_birth_country
        $oAuxField = new HelperInputText("txtSubjBirthCountry_$iInvolved","txtSubjBirthCountry[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjBirthCountry",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjBirthCountry_$iInvolved",tr_sni_ins_subj_birth_country,"lblSubjBirthCountry_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        

        //subj_type_nation (insert)  - OJO DEBE IR EL Gentilicio EN EL PDF
        $oCountry = new ModelCountry();
        //$oCountry->set_id_language("3");//Dutch
        $oCountry->use_language();
        //$arNations = $this->oSuspicionArray->get_by_nation();
        $arNations = $oCountry->get_picklist();
        $oAuxField = new HelperSelect($arNations,"selSubjTypeNation_$iInvolved","selSubjTypeNation_$iInvolved");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $oCountry->set_code_erp($oCliente->get_cicodnac());
            $oCountry->load_by_erp();
            $oAuxField->set_value_to_select($oCountry->get_id());
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
        }        
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selSubjTypeNation_$iInvolved"));
        $oAuxLabel = new HelperLabel("selSubjTypeNation_$iInvolved",tr_sni_ins_subj_type_nation,"lblSubjTypeNation_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        

        //subj_type_profession
        $arOptions = $this->oSuspicionArray->get_by_profession();
        $oAuxField = new HelperSelect($arOptions,"selSubjTypeProfession_$iInvolved","selSubjTypeProfession_$iInvolved");
        $oAuxField->add_class("span6");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $this->oSuspicionArray->set_type("profession");
            $this->oSuspicionArray->set_code_erp($oTabGiros->get_grcdocup());
            $this->oSuspicionArray->load_by_erptype();
            $oAuxField->set_value_to_select($this->oSuspicionArray->get_id());
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }        
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selSubjTypeProfession_$iInvolved"));
        $oAuxLabel = new HelperLabel("selSubjTypeProfession_$iInvolved",tr_sni_ins_subj_type_profession,"lblSubjProfession_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        
        
        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">ID Data</p>");
        $arFields[] = $oAuxField;
        
        //============================
        //        ID DOCUMENT
        //============================        
        //iddoc
        $oAuxField = new HelperInputText("txtIddoc_$iInvolved","txtIddoc[]");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            //NUMERO DE DOCUMENTO (TABGIROS.GRCDREMI) SE TOMA (TABGIROS.GRIDENT)CON EL GRIDENT SE BUSCA EN  (CLIENTES.CIDOCUME)
            $oAuxField->set_value($oTabGiros->get_grcdremi());
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }        
        if($usePost) $oAuxField->set_value($this->get_post("txtIddoc",$iInvolved));
        $oAuxLabel = new HelperLabel("txtIddoc_$iInvolved",tr_sni_ins_iddoc,"lblIddoc_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //iddoc_type
        $arOptions = $this->oSuspicionArray->get_by_doctype();
        $oAuxField = new HelperSelect($arOptions,"selIddocType_$iInvolved","selIddocType_$iInvolved");
        //$oAuxField->add_class("span4");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $this->oSuspicionArray->set_type("iddocument");
            $this->oSuspicionArray->set_code_erp($oTabGiros->get_grtdremi());
            $this->oSuspicionArray->load_by_erptype();
            $oAuxField->set_value_to_select($this->oSuspicionArray->get_id());
            $oAuxField->readonly();$oAuxField->add_class("readonly");            
        }        
        
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIddocType_$iInvolved"));
        $oAuxLabel = new HelperLabel("selIddocType_$iInvolved",tr_sni_ins_iddoc_type,"lblIddocType_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //iddoc_issue_date
        $oAuxField = new HelperDate("datIddocIssueDate_$iInvolved","datIddocIssueDate[]");
        if($usePost) $oAuxField->set_value($this->get_post("datIddocIssueDate",$iInvolved));
        $oAuxLabel = new HelperLabel("datIddocIssueDate_$iInvolved",tr_sni_ins_iddoc_issue_date,"lblIddocIssueDate_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //iddoc_expiry_date
        $oAuxField = new HelperDate("datIddocExpiryDate_$iInvolved","datIddocExpiryDate[]");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $sAuxDate = $oCliente->get_cifecvcm();
            $sAuxDate = $this->get_datefixed($sAuxDate);
            $oAuxField->set_value($sAuxDate);
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value($this->get_post("datIddocExpiryDate",$iInvolved));
        $oAuxLabel = new HelperLabel("datIddocExpiryDate_$iInvolved",tr_sni_ins_iddoc_expiry_date,"lblIddocExpiryDate_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //iddoc_issue_place
        $oAuxField = new HelperInputText("txtIddocIssuePlace_$iInvolved","txtIddocIssuePlace[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtIddocIssuePlace",$iInvolved));
        $oAuxLabel = new HelperLabel("txtIddocIssuePlace_$iInvolved",tr_sni_ins_iddoc_issue_place,"lblIddocIssuePlace_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //iddoc_issue_country
        $oAuxField = new HelperInputText("txtIddocIssueCountry_$iInvolved","txtIddocIssueCountry[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtIddocIssueCountry",$iInvolved));
        $oAuxLabel = new HelperLabel("txtIddocIssueCountry_$iInvolved",tr_sni_ins_iddoc_issue_country,"lblIddocIssueCountry_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //iddoc_nationality
        $oAuxField = new HelperSelect($arNations,"selIddocNationality_$iInvolved","selIddocNationality_$iInvolved");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIddocNationality_$iInvolved"));
        $oAuxLabel = new HelperLabel("selIddocNationality_$iInvolved",tr_sni_ins_iddoc_nationality,"lblIddocNationality_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">Address Data</p>");
        $arFields[] = $oAuxField;        

        //============================
        //          ADDRESS
        //============================
        //Necesario para habilitar los campos direccion nr y letra del remitente
        $isArubaTransfer = (strtoupper($oTabGiros->get_grcdpais())=="CO");
        //address - BENEFICIARIO: DIRECCION DEL BENENF, CIUDAD.  (TABGIROS.GRDRBENE)
        $oAuxField = new HelperInputText("txtAddress_$iInvolved","txtAddress[]");
        $oAuxField->set_maxlength(125);
        if($oTabGiros && $iInvolvedUi=="1")
        {    
            $sAddress = $this->get_address($oTabGiros->get_grdrremi());
            $oAuxField->set_value($sAddress);
            if($isArubaTransfer)
            {    
                $oAuxField->readonly();$oAuxField->add_class("readonly");
            }            
        } 
        elseif($oTabGiros)
        {
            //Solo recupero la direccion hasta el #
            $oCiudad = new ModelCiudades();
            $oCiudad->set_cdcdciud($oTabGiros->get_grciudds());
            $oCiudad->load_by_cdcdciud();
            $sAddress = $oTabGiros->get_grdrbene();
            if($oCiudad->get_cdnombre()) $sAddress .= ", ".$oCiudad->get_cdnombre();            
            $oAuxField->set_value($sAddress);
        }
        
        
        $oAuxField->add_class("span8");
        if($usePost) $oAuxField->set_value($this->get_post("txtAddress",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddress_$iInvolved",tr_sni_ins_address,"lblAddress_$iInvolved");
        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //address_nr
        $oAuxField = new HelperInputText("txtAddressNr_$iInvolved","txtAddressNr[]");
        $oAuxField->set_maxlength(15);
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $sAuxNr = $oTabGiros->get_grdrremi();
            $sAuxNr = $this->get_number($sAuxNr);
            $oAuxField->set_value($sAuxNr);
            if($isArubaTransfer)
            {    
                $oAuxField->readonly();$oAuxField->add_class("readonly");
            }
        }
        if($usePost) $oAuxField->set_value($this->get_post("txtAddressNr",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddressNr_$iInvolved",tr_sni_ins_address_nr,"lblAddressNr_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //address_chars
        $oAuxField = new HelperInputText("txtAddressChars_$iInvolved","txtAddressChars[]");
        $oAuxField->set_maxlength(15);
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $sAuxNr = $oTabGiros->get_grdrremi();
            $sAuxNr = $this->get_chars($sAuxNr);
            $oAuxField->set_value($sAuxNr);
            if($isArubaTransfer)
            {    
                $oAuxField->readonly();$oAuxField->add_class("readonly");
            }
        }
        if($usePost) $oAuxField->set_value($this->get_post("txtAddressChars",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddressChars_$iInvolved",tr_sni_ins_address_chars,"lblAddressChars_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //address_place
        $oAuxField = new HelperInputText("txtAddressPlace_$iInvolved","txtAddressPlace[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtAddressPlace",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddressPlace_$iInvolved",tr_sni_ins_address_place,"lblAddressPlace_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //address_zip
        $oAuxField = new HelperInputText("txtAddressZip_$iInvolved","txtAddressZip[]");
        if($usePost) $oAuxField->set_value($this->get_post("txtAddressZip",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddressZip_$iInvolved",tr_sni_ins_address_zip,"lblAddressZip_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //address_type_country - 
        //TABGIROS.GRCDPAIS -- remitente
        //TABGIROS.GRPAISDS -- beneficiario
        //BENEFICIARIO: PAIS, LUGAR DEL GIRO (TABGIROS.GRPAISDS) 
        //SE TOMA EL CODIGO Y SE CONSULTA LA TABLA (TBPAISES.PSNOMBRE)
        $oAuxField = new HelperSelect($arNations,"selAddresTypeCountry_$iInvolved","selAddresTypeCountry_$iInvolved");
        //Remitente
        if($oTabGiros && $iInvolvedUi=="1")
        {
            if($isArubaTransfer)
            {
                $oAuxField->set_value_to_select("3");//ARUBA
                $oAuxField->readonly();$oAuxField->add_class("readonly");
            }
        }
        //Beneficiario
        elseif($oTabGiros)
        {
            $oCountry->set_code_erp($oTabGiros->get_grpaisds());
            $oCountry->load_by_erp();
            $oAuxField->set_value_to_select($oCountry->get_id());
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selAddresTypeCountry_$iInvolved"));
        $oAuxLabel = new HelperLabel("selAddresTypeCountry_$iInvolved",tr_sni_ins_address_type_country,"lblAddressCountry_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">Telephone Data</p>");
        $arFields[] = $oAuxField;
        
        //============================
        //          PHONE
        //============================        
        // - BENEFICIARIO TELEFONO/TIPO DE TELEF/PAIS.  (TABGIROS.GRTLBENE)/MANUAL/ GIRO (TABGIROS.GRPAISDS) 
        // SE TOMA EL CODIGO Y SE CONSULTA LA TABLA (TBPAISES.PSNOMBRE)
        //phone
        $oAuxField = new HelperInputText("txtPhone_$iInvolved","txtPhone[]");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $oAuxField->set_value($oTabGiros->get_grtlremi());
        }
        elseif($oTabGiros)
        {
            $oAuxField->set_value($oTabGiros->get_grtlbene());
        }
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value($this->get_post("txtPhone",$iInvolved));
        $oAuxLabel = new HelperLabel("txtPhone_$iInvolved",tr_sni_ins_phone,"lblPhone_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //phone_type
        $arOptions = $this->oSuspicionArray->get_by_phone();
        $oAuxField = new HelperSelect($arOptions,"selPhoneType_$iInvolved","selPhoneType_$iInvolved");
        if($oTabGiros && $iInvolvedUi=="1")
        {
            $oAuxField->set_value_to_select($this->get_id_phonetype($oTabGiros->get_grtlremi()));
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        elseif($oTabGiros)
        {
            //Selección manual
        }
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selPhoneType_$iInvolved"));
        $oAuxLabel = new HelperLabel("selPhoneType",tr_sni_ins_phone_type,"lblPhoneType_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //phone_type_country
        $oAuxField = new HelperSelect($arNations,"selPhoneCountry_$iInvolved","selPhoneCountry_$iInvolved");
        //Remitente
        if($oTabGiros && $iInvolvedUi=="1")
        {
            if($this->is_aruba_phone($oTabGiros->get_grtlremi()))
            {
                $oAuxField->set_value_to_select("3");
                $oAuxField->readonly();$oAuxField->add_class("readonly");    
            }
            //No Aruba. Se deja abierto para seleccionar el pais del telefono
            else
            {
                $oCountry->set_code_erp($oTabGiros->get_grcdpais());
                $oCountry->load_by_erp();
                $oAuxField->set_value_to_select($oCountry->get_id());
            }
        }
        //Beneficiario
        elseif($oTabGiros)
        {
            //bug($oTabGiros->get_grcdpais(),"pais benefi");
            $oCountry->set_code_erp($oTabGiros->get_grpaisds());
            $oCountry->load_by_erp();
            $oAuxField->set_value_to_select($oCountry->get_id());
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selPhoneCountry_$iInvolved"));
        $oAuxLabel = new HelperLabel("selPhoneCountry_$iInvolved",tr_sni_ins_phone_type_country,"lblPhoneCountry_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_sni_ins_description,"lblDescription");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        return $arFields;
    }//build_insert_fields

    public function build_update_fields($usePost,$iInvolved=0)
    {
        $sBackGround = "black";
        $sForeColor = "white";
        
        $iInvolvedUi = $iInvolved+1;
        if($iInvolvedUi=="1") 
        {
            $sBackGround="#FFBF00";
            $sForeColor="black";
        }
        elseif($iInvolvedUi=="2")
        {
            $sBackGround="#F3FF35";
            $sForeColor="black";            
        } 
        $oSuspicionInvolved = new ModelSuspicionInvolved();
        $oSuspicionInvolved->set_id_suspicion($this->sIdSuspicion);
        $oSuspicionInvolved->set_number($iInvolvedUi);
        $oSuspicionInvolved->load_by_number();
        
        $oSuspicionInvolvedDetails = new ModelSuspicionsInvolvedDetails();
        $oSuspicionInvolvedDetails->set_id_involved($oSuspicionInvolved->get_id());
	
        $oAuxField = new HelperInputHidden("hidIdInvolved_$iInvolved","hidIdInvolved[]");
        $oAuxField->set_value($oSuspicionInvolved->get_id());
        $arFields[] = $oAuxField;
        
        //number
        $oAuxField = new HelperInputHidden("hidNumberInvolved_$iInvolved","hidNumberInvolved[]");
        $oAuxField->set_value($iInvolvedUi);
        $arFields[] = $oAuxField;
        
        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">A. INVOLVED PERSON $iInvolvedUi</p>");
        $arFields[] = $oAuxField;
        
        //involved1 (Client Cateogry)
        $arOptions = $this->oSuspicionArray->get_by_involved1();
        $oAuxField = new ApphelperTableCheckGroup($arOptions,"chkInvolved_$iInvolved");
        $oAuxField->set_sectionid("Involved_$iInvolved");
        $oSuspicionInvolvedDetails->set_type("involved1");
        $arSelect = $oSuspicionInvolvedDetails->get_by_involved_and_type();
        $oAuxField->set_values_to_check($arSelect);        
        if($usePost) $oAuxField->set_values_to_check($this->get_post("chkInvolved_$iInvolved"));
        $arFields[] = $oAuxField;        

        //nr_account
        $oAuxField = new HelperInputText("txtNrAccount_$iInvolved","txtNrAccount[]");
        $oAuxField->set_value($oSuspicionInvolved->get_nr_account());
        if($usePost) $oAuxField->set_value($this->get_post("txtNrAccount",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrAccount_$iInvolved",tr_sni_ins_nr_account,"lblNrAccount_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_creditcard
        $oAuxField = new HelperInputText("txtNrCreditcard_$iInvolved","txtNrCreditcard[]");
        $oAuxField->set_value($oSuspicionInvolved->get_nr_creditcard());        
        if($usePost) $oAuxField->set_value($this->get_post("txtNrCreditcard",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrCreditcard_$iInvolved",tr_sni_ins_nr_creditcard,"lblNrCreditcard_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_mtcn
        $oAuxField = new HelperInputText("txtNrMtcn_$iInvolved","txtNrMtcn[]");
        $oAuxField->set_value($oSuspicionInvolved->get_nr_mtcn());
        if($usePost) $oAuxField->set_value($this->get_post("txtNrMtcn",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrMtcn_$iInvolved",tr_sni_ins_nr_mtcn,"lblNrMtcn_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_police
        $oAuxField = new HelperInputText("txtNrPolice_$iInvolved","txtNrPolice[]");
        $oAuxField->set_value($oSuspicionInvolved->get_nr_police());
        if($usePost) $oAuxField->set_value($this->get_post("txtNrPolice",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrPolice_$iInvolved",tr_sni_ins_nr_police,"lblNrPolice_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_brief
        $oAuxField = new HelperInputText("txtNrBrief_$iInvolved","txtNrBrief[]");
        $oAuxField->set_value($oSuspicionInvolved->get_nr_brief());
        if($usePost) $oAuxField->set_value($this->get_post("txtNrBrief",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrBrief_$iInvolved",tr_sni_ins_nr_brief,"lblNrBrief_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //nr_client
        $oAuxField = new HelperInputText("txtNrClient_$iInvolved","txtNrClient[]");
        $oAuxField->set_value($oSuspicionInvolved->get_nr_client());
        if($usePost) $oAuxField->set_value($this->get_post("txtNrClient",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrClient_$iInvolved",tr_sni_ins_nr_client,"lblNrClient_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //nr_extra
        $oAuxField = new HelperInputText("txtNrExtra_$iInvolved","txtNrExtra[]");
        $oAuxField->set_value($oSuspicionInvolved->get_nr_extra());
        if($usePost) $oAuxField->set_value($this->get_post("txtNrExtra",$iInvolved));
        $oAuxLabel = new HelperLabel("txtNrExtra_$iInvolved",tr_sni_ins_nr_extra,"lblNrExtra_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">Subject data</p>");
        $arFields[] = $oAuxField;        
        
        //============================
        //          SUBJECT 
        //============================
        //subj_name APELLIDO (TABGIROS.GRAPSREM)  - BENEFICIARIO: APELLIDOS DEL BENEF. (TABGIROS.GRAPCLI)
        $oAuxField = new HelperInputText("txtSubjName_$iInvolved","txtSubjName[]");
        $oAuxField->set_value($oSuspicionInvolved->get_subj_name());
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oAuxLabel = new HelperLabel("txtSubjName_$iInvolved",tr_sni_ins_subj_name,"lblSubjName_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subj_infix1
        $oAuxField = new HelperInputText("txtSubjInfix1_$iInvolved","txtSubjInfix1[]");
        $oAuxField->set_value($oSuspicionInvolved->get_subj_infix1());
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjInfix1",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjInfix1_$iInvolved",tr_sni_ins_subj_infix1,"lblSubjInfix1_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //subj_married_name
        $oAuxField = new HelperInputText("txtSubjMarriedName_$iInvolved","txtSubjMarriedName[]");
        $oAuxField->set_value($oSuspicionInvolved->get_subj_married_name());
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjMarriedName",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjMarriedName_$iInvolved",tr_sni_ins_subj_married_name,"lblSubjMarriedName_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //subj_infix2
        $oAuxField = new HelperInputText("txtSubjInfix2_$iInvolved","txtSubjInfix2[]");
        $oAuxField->set_value($oSuspicionInvolved->get_subj_infix2());
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjInfix2",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjInfix2_$iInvolved",tr_sni_ins_subj_infix2,"lblSubjInfix2_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subj_initials
        $oAuxField = new HelperInputText("txtSubjInitials_$iInvolved","txtSubjInitials[]");
        $oAuxField->set_value($oSuspicionInvolved->get_subj_initials());
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjInitials",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjInitials_$iInvolved",tr_sni_ins_subj_initials,"lblSubjInitials_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        
        
        //subj_full_name - BENEFICIARIO: NOMBRE COMPLETO DEL BENEF.  (TABGIROS.GRNMBENE)
        $oAuxField = new HelperInputText("txtSubjFullName_$iInvolved","txtSubjFullName[]");
        $oAuxField->set_value($oSuspicionInvolved->get_subj_full_name());
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oAuxField->add_class("span4");
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjFullName",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjFullName_$iInvolved",tr_sni_ins_subj_full_name,"lblSubjFullName_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subj_type_sex
        $arOptions = $this->oSuspicionArray->get_by_sex();
        $oAuxField = new HelperSelect($arOptions,"selSubjTypeSex_$iInvolved","selSubjTypeSex_$iInvolved");
        $oAuxField->set_value_to_select($oSuspicionInvolved->get_subj_type_sex());
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selSubjTypeSex_$iInvolved"));
        $oAuxLabel = new HelperLabel("selSubjTypeSex_$iInvolved",tr_sni_ins_subj_type_sex,"lblSubjTypeSex_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subj_birthdate
        /*FECHA DE NACIMEINTO   MM/DD/YYYY (CLIENTES.CIFECNAC) SE RECUPERA CON EL ID DEL CLIENTE (CLIENTES.CICODCLI)*/        
        $oAuxField = new HelperDate("datSubjBirthdate_$iInvolved","datSubjBirthdate[]");
        $oAuxField->set_value(dbbo_date($oSuspicionInvolved->get_subj_birthdate()));
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value($this->get_post("datSubjBirthdate",$iInvolved));
        $oAuxLabel = new HelperLabel("datSubjBirthdate_$iInvolved",tr_sni_ins_subj_birthdate,"lblSubjBirthdate_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //subj_birthplace
        $oAuxField = new HelperInputText("txtSubjBirthplace_$iInvolved","txtSubjBirthplace[]");
        $oAuxField->set_value($oSuspicionInvolved->get_subj_birthplace());
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjBirthplace",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjBirthplace_$iInvolved",tr_sni_ins_subj_birthplace,"lblSubjBirthplace_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //subj_birth_country
        $oAuxField = new HelperInputText("txtSubjBirthCountry_$iInvolved","txtSubjBirthCountry[]");
        $oAuxField->set_value($oSuspicionInvolved->get_subj_birth_country());
        if($usePost) $oAuxField->set_value($this->get_post("txtSubjBirthCountry",$iInvolved));
        $oAuxLabel = new HelperLabel("txtSubjBirthCountry_$iInvolved",tr_sni_ins_subj_birth_country,"lblSubjBirthCountry_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        

        //subj_type_nation - OJO DEBE IR EL Gentilicio EN EL PDF
        $oCountry = new ModelCountry();
        //$oCountry->set_id_language("3");//Dutch
        $oCountry->use_language();
        //$arNations = $this->oSuspicionArray->get_by_nation();
        $arNations = $oCountry->get_picklist();
        $oAuxField = new HelperSelect($arNations,"selSubjTypeNation_$iInvolved","selSubjTypeNation_$iInvolved");
        $oAuxField->set_value_to_select($oSuspicionInvolved->get_subj_type_nation());
//        if($iInvolvedUi=="1")
//        {
//            //Quitado a petición de UC
//            //$oAuxField->readonly();$oAuxField->add_class("readonly");       
//        }        
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selSubjTypeNation_$iInvolved"));
        $oAuxLabel = new HelperLabel("selSubjTypeNation_$iInvolved",tr_sni_ins_subj_type_nation,"lblSubjTypeNation_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        

        //subj_type_profession
        $arOptions = $this->oSuspicionArray->get_by_profession();
        $oAuxField = new HelperSelect($arOptions,"selSubjTypeProfession_$iInvolved","selSubjTypeProfession_$iInvolved");
        $oAuxField->add_class("span6");
        $oAuxField->set_value_to_select($oSuspicionInvolved->get_subj_type_profession());
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selSubjTypeProfession_$iInvolved"));
        $oAuxLabel = new HelperLabel("selSubjTypeProfession_$iInvolved",tr_sni_ins_subj_type_profession,"lblSubjProfession_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        
        
        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">ID Data</p>");
        $arFields[] = $oAuxField;
        //============================
        //        ID DOCUMENT
        //============================        
        //iddoc
        $oAuxField = new HelperInputText("txtIddoc_$iInvolved","txtIddoc[]");
        $oAuxField->set_value($oSuspicionInvolved->get_iddoc());        
        if($iInvolvedUi=="1")
        {
            //NUMERO DE DOCUMENTO (TABGIROS.GRCDREMI) SE TOMA (TABGIROS.GRIDENT)CON EL GRIDENT SE BUSCA EN  (CLIENTES.CIDOCUME)
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }        
        if($usePost) $oAuxField->set_value($this->get_post("txtIddoc",$iInvolved));
        $oAuxLabel = new HelperLabel("txtIddoc_$iInvolved",tr_sni_ins_iddoc,"lblIddoc_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //iddoc_type
        $arOptions = $this->oSuspicionArray->get_by_doctype();
        $oAuxField = new HelperSelect($arOptions,"selIddocType_$iInvolved","selIddocType_$iInvolved");
        $oAuxField->set_value_to_select($oSuspicionInvolved->get_iddoc_type());
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");            
        }        
        
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIddocType_$iInvolved"));
        $oAuxLabel = new HelperLabel("selIddocType_$iInvolved",tr_sni_ins_iddoc_type,"lblIddocType_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //iddoc_issue_date
        $oAuxField = new HelperDate("datIddocIssueDate_$iInvolved","datIddocIssueDate[]");
        $oAuxField->set_value(dbbo_date($oSuspicionInvolved->get_iddoc_issue_date()));
        if($usePost) $oAuxField->set_value($this->get_post("datIddocIssueDate",$iInvolved));
        $oAuxLabel = new HelperLabel("datIddocIssueDate_$iInvolved",tr_sni_ins_iddoc_issue_date,"lblIddocIssueDate_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //iddoc_expiry_date
        $oAuxField = new HelperDate("datIddocExpiryDate_$iInvolved","datIddocExpiryDate[]");
        $oAuxField->set_value(dbbo_date($oSuspicionInvolved->get_iddoc_expiry_date()));
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value($this->get_post("datIddocExpiryDate",$iInvolved));
        $oAuxLabel = new HelperLabel("datIddocExpiryDate_$iInvolved",tr_sni_ins_iddoc_expiry_date,"lblIddocExpiryDate_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //iddoc_issue_place
        $oAuxField = new HelperInputText("txtIddocIssuePlace_$iInvolved","txtIddocIssuePlace[]");
        $oAuxField->set_value($oSuspicionInvolved->get_iddoc_issue_place());
        if($usePost) $oAuxField->set_value($this->get_post("txtIddocIssuePlace",$iInvolved));
        $oAuxLabel = new HelperLabel("txtIddocIssuePlace_$iInvolved",tr_sni_ins_iddoc_issue_place,"lblIddocIssuePlace_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //iddoc_issue_country
        $oAuxField = new HelperInputText("txtIddocIssueCountry_$iInvolved","txtIddocIssueCountry[]");
        $oAuxField->set_value($oSuspicionInvolved->get_iddoc_issue_country());
        if($usePost) $oAuxField->set_value($this->get_post("txtIddocIssueCountry",$iInvolved));
        $oAuxLabel = new HelperLabel("txtIddocIssueCountry_$iInvolved",tr_sni_ins_iddoc_issue_country,"lblIddocIssueCountry_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //iddoc_nationality update
        $oAuxField = new HelperSelect($arNations,"selIddocNationality_$iInvolved","selIddocNationality_$iInvolved");
        $oAuxField->set_value_to_select($oSuspicionInvolved->get_iddoc_nationality());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIddocNationality_$iInvolved"));
        $oAuxLabel = new HelperLabel("selIddocNationality_$iInvolved",tr_sni_ins_iddoc_nationality,"lblIddocNationality_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">Address Data</p>");
        $arFields[] = $oAuxField;        

        //============================
        //          ADDRESS
        //============================
        //address - BENEFICIARIO: DIRECCION DEL BENENF, CIUDAD.  (TABGIROS.GRDRBENE)
        $oAuxField = new HelperInputText("txtAddress_$iInvolved","txtAddress[]");
        $oAuxField->set_maxlength(125);
        $oAuxField->set_value($oSuspicionInvolved->get_address());
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oAuxField->add_class("span8");
        if($usePost) $oAuxField->set_value($this->get_post("txtAddress",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddress_$iInvolved",tr_sni_ins_address,"lblAddress_$iInvolved");
        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //address_nr
        $oAuxField = new HelperInputText("txtAddressNr_$iInvolved","txtAddressNr[]");
        $oAuxField->set_value($oSuspicionInvolved->get_address_nr());
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value($this->get_post("txtAddressNr",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddressNr_$iInvolved",tr_sni_ins_address_nr,"lblAddressNr_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //address_chars
        $oAuxField = new HelperInputText("txtAddressChars_$iInvolved","txtAddressChars[]");
        $oAuxField->set_value($oSuspicionInvolved->get_address_chars());
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }        
        if($usePost) $oAuxField->set_value($this->get_post("txtAddressChars",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddressChars_$iInvolved",tr_sni_ins_address_chars,"lblAddressChars_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //address_place
        $oAuxField = new HelperInputText("txtAddressPlace_$iInvolved","txtAddressPlace[]");
        $oAuxField->set_value($oSuspicionInvolved->get_address_place());
        if($usePost) $oAuxField->set_value($this->get_post("txtAddressPlace",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddressPlace_$iInvolved",tr_sni_ins_address_place,"lblAddressPlace_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //address_zip
        $oAuxField = new HelperInputText("txtAddressZip_$iInvolved","txtAddressZip[]");
        $oAuxField->set_value($oSuspicionInvolved->get_address_zip());
        if($usePost) $oAuxField->set_value($this->get_post("txtAddressZip",$iInvolved));
        $oAuxLabel = new HelperLabel("txtAddressZip_$iInvolved",tr_sni_ins_address_zip,"lblAddressZip_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //address_type_country - BENEFICIARIO: PAIS,  LUGAR DEL GIRO (TABGIROS.GRPAISDS) SE TOMA EL CODIGO Y SE CONSULTA LA TABLA (TBPAISES.PSNOMBRE)
        $oAuxField = new HelperSelect($arNations,"selAddresTypeCountry_$iInvolved","selAddresTypeCountry_$iInvolved");
        $oAuxField->set_value_to_select($oSuspicionInvolved->get_address_type_country());
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        //if($usePost) $oAuxField->set_value_to_select($this->get_post("selAddresTypeCountry_$iInvolved"));
        $oAuxLabel = new HelperLabel("selAddresTypeCountry_$iInvolved",tr_sni_ins_address_type_country,"lblAddressCountry_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        $oAuxField = new HelperRaw("<p style=\"margin:0;padding:3px;color:$sForeColor;background-color:$sBackGround;font-weight:bold;\">Telephone Data</p>");
        $arFields[] = $oAuxField;        
        //============================
        //          PHONE
        //============================        
        // - BENEFICIARIO TELEFONO/TIPO DE TELEF/PAIS.  (TABGIROS.GRTLBENE)/MANUAL/ GIRO (TABGIROS.GRPAISDS) SE TOMA EL CODIGO Y SE CONSULTA LA TABLA (TBPAISES.PSNOMBRE)
        //phone
        $oAuxField = new HelperInputText("txtPhone_$iInvolved","txtPhone[]");
        $oAuxField->set_value($oSuspicionInvolved->get_phone());
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        //if($usePost) $oAuxField->set_value($this->get_post("txtPhone",$iInvolved));
        $oAuxLabel = new HelperLabel("txtPhone_$iInvolved",tr_sni_ins_phone,"lblPhone_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //phone_type
        $arOptions = $this->oSuspicionArray->get_by_phone();
        $oAuxField = new HelperSelect($arOptions,"selPhoneType_$iInvolved","selPhoneType_$iInvolved");
        $oAuxField->set_value_to_select($oSuspicionInvolved->get_phone_type());
        if($iInvolvedUi=="1")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selPhoneType_$iInvolved"));
        $oAuxLabel = new HelperLabel("selPhoneType",tr_sni_ins_phone_type,"lblPhoneType_$iInvolved");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //phone_type_country
        $oAuxField = new HelperSelect($arNations,"selPhoneCountry_$iInvolved","selPhoneCountry_$iInvolved");
        $oAuxField->set_value_to_select($oSuspicionInvolved->get_phone_type_country());
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        //if($usePost) $oAuxField->set_value_to_select($this->get_post("selPhoneCountry_$iInvolved"));
        $oAuxLabel = new HelperLabel("selPhoneCountry_$iInvolved",tr_sni_ins_phone_type_country,"lblPhoneCountry_$iInvolved");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_sni_ins_description,"lblDescription");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        return $arFields;
    }//build_update_fields()
    
    public function insert_involved($iInvolved=0)
    {
        $oSuspicionInvolved = new ModelSuspicionInvolved();
        $oSuspicionInvolved->set_id_suspicion($this->sIdSuspicion);
        $oSuspicionInvolved->set_number($this->get_post("hidNumberInvolved",$iInvolved));
        $oSuspicionInvolved->set_nr_account($this->get_post("txtNrAccount",$iInvolved));
        $oSuspicionInvolved->set_nr_creditcard($this->get_post("txtNrCreditcard",$iInvolved));
        $oSuspicionInvolved->set_nr_mtcn($this->get_post("txtNrMtcn",$iInvolved));
        $oSuspicionInvolved->set_nr_police($this->get_post("txtNrPolice",$iInvolved));
        $oSuspicionInvolved->set_nr_brief($this->get_post("txtNrBrief",$iInvolved));
        $oSuspicionInvolved->set_nr_client($this->get_post("txtNrClient",$iInvolved));
        $oSuspicionInvolved->set_nr_extra($this->get_post("txtNrExtra",$iInvolved));
        //SUBJECT DATA
        $oSuspicionInvolved->set_subj_name($this->get_post("txtSubjName",$iInvolved));
        $oSuspicionInvolved->set_subj_infix1($this->get_post("txtSubjInfix1",$iInvolved));
        $oSuspicionInvolved->set_subj_married_name($this->get_post("txtSubjMarriedName",$iInvolved));
        $oSuspicionInvolved->set_subj_infix2($this->get_post("txtSubjInfix2",$iInvolved));
        $oSuspicionInvolved->set_subj_initials($this->get_post("txtSubjInitials",$iInvolved));
        $oSuspicionInvolved->set_subj_full_name($this->get_post("txtSubjFullName",$iInvolved));
        $oSuspicionInvolved->set_subj_type_sex($this->get_post("selSubjTypeSex_$iInvolved"));
        
        $sAuxDate = bodb_date($this->get_post("datSubjBirthdate",$iInvolved));
        $oSuspicionInvolved->set_subj_birthdate($sAuxDate);
        $oSuspicionInvolved->set_subj_birthplace($this->get_post("txtSubjBirthplace",$iInvolved));
        $oSuspicionInvolved->set_subj_birth_country($this->get_post("txtSubjBirthCountry",$iInvolved));
        $oSuspicionInvolved->set_subj_type_nation($this->get_post("selSubjTypeNation_$iInvolved"));
        $oSuspicionInvolved->set_subj_type_profession($this->get_post("selSubjTypeProfession_$iInvolved"));
        //ID DATA
        $oSuspicionInvolved->set_iddoc($this->get_post("txtIddoc",$iInvolved));
        $oSuspicionInvolved->set_iddoc_type($this->get_post("selIddocType_$iInvolved"));
        
        $sAuxDate = bodb_date($this->get_post("datIddocIssueDate",$iInvolved));
        $oSuspicionInvolved->set_iddoc_issue_date($sAuxDate);
        
        $sAuxDate = bodb_date($this->get_post("datIddocExpiryDate",$iInvolved));
        $oSuspicionInvolved->set_iddoc_expiry_date($sAuxDate);
        
        $oSuspicionInvolved->set_iddoc_issue_place($this->get_post("txtIddocIssuePlace",$iInvolved));
        $oSuspicionInvolved->set_iddoc_issue_country($this->get_post("txtIddocIssueCountry",$iInvolved));
        //insert
        $oSuspicionInvolved->set_iddoc_nationality($this->get_post("selIddocNationality_$iInvolved"));
        //ADDRESS DATA
        $oSuspicionInvolved->set_address($this->get_post("txtAddress",$iInvolved));
        $oSuspicionInvolved->set_address_nr($this->get_post("txtAddressNr",$iInvolved));
        $oSuspicionInvolved->set_address_chars($this->get_post("txtAddressChars",$iInvolved));
        $oSuspicionInvolved->set_address_place($this->get_post("txtAddressPlace",$iInvolved));
        $oSuspicionInvolved->set_address_zip($this->get_post("txtAddressZip",$iInvolved));
        $oSuspicionInvolved->set_address_type_country($this->get_post("selAddresTypeCountry_$iInvolved"));
        //TELEPHONE DATA
        $oSuspicionInvolved->set_phone($this->get_post("txtPhone",$iInvolved));
        $oSuspicionInvolved->set_phone_type($this->get_post("selPhoneType_$iInvolved"));
        $oSuspicionInvolved->set_phone_type_country($this->get_post("selPhoneCountry_$iInvolved"));
        
        $oSuspicionInvolved->autoinsert();
        
        if(!$oSuspicionInvolved->is_error())
        {
            $idInvolved = $oSuspicionInvolved->get_last_insert_id();
            $this->insert_details($idInvolved,"chkInvolved_$iInvolved","involved1");
            //$this->delete_details($idInvolved,"chkInvolved_$iInvolved","involved1");
        }
        else
        {
            $sMessage = "Error: no se ha podido insertar en SuspicionInvolved ".$oSuspicionInvolved->get_error_message();
            $this->add_error($sMessage);
        }
    }//insert_involved
    
    protected function insert_details($idInvolved,$sCheckName,$sType)
    {
        //INSERT, UPDATE INVOLVED DETAILS
        $arCheck = $this->get_post($sCheckName);
        if(is_array($arCheck))
        {
            $oSuspicionsInvolvedDetails = new ModelSuspicionsInvolvedDetails();
            $oSuspicionsInvolvedDetails->set_id_involved($idInvolved);
            $oSuspicionsInvolvedDetails->set_type($sType);

            $arExistIdTypes = $oSuspicionsInvolvedDetails->get_by_involved_and_type();
            //Left insert, Right delete. Los que están solo en el check se insertan
            $arInsert = $this->get_array_joins($arCheck,$arExistIdTypes,"leftouter");
            $arInsert = $arInsert["leftouter"];
            
            foreach($arInsert as $idType)
            {
                $oSuspicionsInvolvedDetails->set_id_type($idType);
                $oSuspicionsInvolvedDetails->autoinsert();
            }
        }//arCheck is array
    }//insert_details
    
    protected function delete_details($idInvolved,$sCheckName,$sType)
    {
        //UPDATE INVOLVED DETAILS
        $arCheck = $this->get_post($sCheckName);
        if(is_array($arCheck))
        {
            $oSuspicionsInvolvedDetails = new ModelSuspicionsInvolvedDetails();
            $oSuspicionsInvolvedDetails->set_id_involved($idInvolved);
            $oSuspicionsInvolvedDetails->set_type($sType);

            $arExistIdTypes = $oSuspicionsInvolvedDetails->get_by_involved_and_type();
            //Left insert, Right delete. Los que están solo en bd se eliminan
            $arDelete = $this->get_array_joins($arCheck,$arExistIdTypes,"rightouter");
            $arDelete = $arDelete["rightouter"];

            foreach($arDelete as $idType)
            {
                $arCondition = array("id_involved"=>$idInvolved,"id_type"=>$idType,"type"=>$sType);
                $oSuspicionsInvolvedDetails->autodelete($arCondition);
            }
        }
    }//delete_details
    
    public function update_involved($iInvolved=0)
    {
        //UPDATE INVOLVED AND INVOLVED DETAILS
        $oSuspicionInvolved = new ModelSuspicionInvolved();
        $oSuspicionInvolved->set_id($this->get_post("hidIdInvolved",$iInvolved));
        //$oSuspicionInvolved->set_id_suspicion($this->sIdSuspicion);
        //$oSuspicionInvolved->set_number($this->get_post("hidNumberInvolved",$iInvolved));
        
        $oSuspicionInvolved->set_nr_account($this->get_post("txtNrAccount",$iInvolved));
        $oSuspicionInvolved->set_nr_creditcard($this->get_post("txtNrCreditcard",$iInvolved));
        $oSuspicionInvolved->set_nr_mtcn($this->get_post("txtNrMtcn",$iInvolved));
        $oSuspicionInvolved->set_nr_police($this->get_post("txtNrPolice",$iInvolved));
        $oSuspicionInvolved->set_nr_brief($this->get_post("txtNrBrief",$iInvolved));
        $oSuspicionInvolved->set_nr_client($this->get_post("txtNrClient",$iInvolved));
        $oSuspicionInvolved->set_nr_extra($this->get_post("txtNrExtra",$iInvolved));
        //SUBJECT DATA
        $oSuspicionInvolved->set_subj_name($this->get_post("txtSubjName",$iInvolved));
        $oSuspicionInvolved->set_subj_infix1($this->get_post("txtSubjInfix1",$iInvolved));
        $oSuspicionInvolved->set_subj_married_name($this->get_post("txtSubjMarriedName",$iInvolved));
        $oSuspicionInvolved->set_subj_infix2($this->get_post("txtSubjInfix2",$iInvolved));
        $oSuspicionInvolved->set_subj_initials($this->get_post("txtSubjInitials",$iInvolved));
        $oSuspicionInvolved->set_subj_full_name($this->get_post("txtSubjFullName",$iInvolved));
        $oSuspicionInvolved->set_subj_type_sex($this->get_post("selSubjTypeSex_$iInvolved"));
        
        $sAuxDate = bodb_date($this->get_post("datSubjBirthdate",$iInvolved));
        $oSuspicionInvolved->set_subj_birthdate($sAuxDate);
        
        $oSuspicionInvolved->set_subj_birthplace($this->get_post("txtSubjBirthplace",$iInvolved));
        $oSuspicionInvolved->set_subj_birth_country($this->get_post("txtSubjBirthCountry",$iInvolved));
        $oSuspicionInvolved->set_subj_type_nation($this->get_post("selSubjTypeNation_$iInvolved"));
        $oSuspicionInvolved->set_subj_type_profession($this->get_post("selSubjTypeProfession_$iInvolved"));
        
        //ID DATA
        $oSuspicionInvolved->set_iddoc($this->get_post("txtIddoc",$iInvolved));
        $oSuspicionInvolved->set_iddoc_type($this->get_post("selIddocType_$iInvolved"));
        
        $sAuxDate = bodb_date($this->get_post("datIddocIssueDate",$iInvolved));
        $oSuspicionInvolved->set_iddoc_issue_date($sAuxDate);
        
        $sAuxDate = bodb_date($this->get_post("datIddocExpiryDate",$iInvolved));
        $oSuspicionInvolved->set_iddoc_expiry_date($sAuxDate);
        
        $oSuspicionInvolved->set_iddoc_issue_place($this->get_post("txtIddocIssuePlace",$iInvolved));
        $oSuspicionInvolved->set_iddoc_issue_country($this->get_post("txtIddocIssueCountry",$iInvolved));
        //update
        $oSuspicionInvolved->set_iddoc_nationality($this->get_post("selIddocNationality_$iInvolved"));
        //ADDRESS DATA
        $oSuspicionInvolved->set_address($this->get_post("txtAddress",$iInvolved));
        $oSuspicionInvolved->set_address_nr($this->get_post("txtAddressNr",$iInvolved));
        $oSuspicionInvolved->set_address_chars($this->get_post("txtAddressChars",$iInvolved));
        $oSuspicionInvolved->set_address_place($this->get_post("txtAddressPlace",$iInvolved));
        $oSuspicionInvolved->set_address_zip($this->get_post("txtAddressZip",$iInvolved));
        $oSuspicionInvolved->set_address_type_country($this->get_post("selAddresTypeCountry_$iInvolved"));
        //TELEPHONE DATA
        $oSuspicionInvolved->set_phone($this->get_post("txtPhone",$iInvolved));
        $oSuspicionInvolved->set_phone_type($this->get_post("selPhoneType_$iInvolved"));
        $oSuspicionInvolved->set_phone_type_country($this->get_post("selPhoneCountry_$iInvolved"));
        
        $oSuspicionInvolved->autoupdate();
        
        //UPDATE INVOLVED DETAILS
        //Despues de haber actualizado la persona involucrada i actualizo su detalle
        if(!$oSuspicionInvolved->is_error())
        {
            $idInvolved = $oSuspicionInvolved->get_id();
            //tipo: involved1
            $this->delete_details($idInvolved,"chkInvolved_$iInvolved","involved1");
            $this->insert_details($idInvolved,"chkInvolved_$iInvolved","involved1");
        }
        else
        {
            $sMessage = "Error: no se ha podido insertar en SuspicionInvolved ".$oSuspicionInvolved->get_error_message();
            $this->add_error($sMessage);
        }
    }//update_involved
    
    /**
     * Un ejemplo de fecha recuperada para $sAuxDate es 1962-09-21 00:00:00
     * Corrige la fecha que se recupera desde la base de datos.
     * @param string $sAuxDate Fecha en format d/m/aaaa o dd/mm/aaaa 
     */
    private function get_datefixed($sAuxDate)
    {
        $sAuxDate = trim($sAuxDate);
        if($sAuxDate)
        {   
            $sAuxDate = substr($sAuxDate,0,10);
            if(strstr($sAuxDate,"-"))
                $arDate = explode("-",$sAuxDate);
            elseif(strstr($sAuxDate,"/"))
                $arDate = explode("/",$sAuxDate);
            
            $arDate[0] = sprintf("%04d",$arDate[0]);//yyyy
            $arDate[1] = sprintf("%02d",$arDate[1]);//mm
            $arDate[2] = sprintf("%02d",$arDate[2]);//dd
            
            $arDate["d"] = $arDate[2];
            $arDate["m"] = $arDate[1];
            $arDate["y"] = $arDate[0];
            unset($arDate[0]);unset($arDate[1]);unset($arDate[2]);
        }
        $sAuxDate = implode("-",$arDate);
        return $sAuxDate;
    }
    
    protected function get_id_phonetype($sPhone)
    {
        $arFixed = array("52","58");
        $sPhone = trim($sPhone);
        $sPhone = substr($sPhone,0,2);
        if(in_array($sPhone,$arFixed))
        {
            return "99";//fijo
        }
        return "100";//celular
    }
    
    protected function is_aruba_phone($sPhone)
    {
        //(52, 56,58,59, 7, 600)
        //bug($sPhone,"before");
        $sPhone = substr($sPhone,0,3);
        //bug($sPhone,"after");
        $arPhones = array("600");
        foreach($arPhones as $sIni)
            if($sIni==$sPhone)
                return TRUE;
            
        $arPhones = array("52","56","58","59");
        $sPhone = substr($sPhone,0,2);
        foreach($arPhones as $sIni)
            if($sIni==$sPhone)
                return TRUE;
            
        $arPhones = array("7");
        $sPhone = substr($sPhone,0,1);
        foreach($arPhones as $sIni)
            if($sIni==$sPhone)
                return TRUE;
           
        return FALSE;
    }
    
    public function set_suspicion_array($oObject){$this->oSuspicionArray = $oObject;}
    public function set_id_transfer($value){$this->sIdTransfer=$value;}
    public function set_id_suspicion($value){$this->sIdSuspicion=$value;}
    public function get_initials($sFullName)
    {
        $sFullName = trim($sFullName);
        if($sFullName)
        {    
            $arInfix = array(" DE "," DEL "," DE LA "," DE LOS "," DE LAS ");

            //Iniciales Nombres
            $sFullName = strtoupper($sFullName);
            $sFullName = str_replace($arInfix," ",$sFullName);
            //limpio el posible doble espacio
            $sFullName = str_replace("  "," ",$sFullName);

            $arFullName = explode(" ",$sFullName);
            
            $arInitials = array();
            foreach($arFullName as $sName)
                if($sName)
                    $arInitials[] = get_firstchar($sName);
            
            $sInitials = implode(". ",$arInitials);
            $sInitials .= ".";
            return $sInitials;
        }
        return $sFullName;
    }
    
    public function get_number($sValue)
    {
        $sPattern = "/#[\s]*[\d]+/";
        $sValue = trim($sValue);
        preg_match_all($sPattern,$sValue,$arMatch);
        //pr("number");pr($arMatch);
        $sAuxNr = $arMatch[0][0];
        $sAuxNr = str_replace(array("#"," "),"",$sAuxNr);
        return $sAuxNr;
    }
    
    public function get_address($sValue)
    {
        $sValue = trim($sValue);
        $sValue = explode("#",$sValue);
        $sValue = trim($sValue[0]);
        return $sValue;
    }
    
    public function get_chars($sValue)
    {
        $sPattern = "/#[\s]*[\d]*[\s]*[a-z,A-Z\s]+/";
        $sValue = trim($sValue);
        //bug($sValue,"value chars");
        //value: paradera #   44   klmon   ..djsijf
        //arMatch array (0 =>array (0 => '#   44   klmon',),)
        preg_match_all($sPattern,$sValue,$arMatch);
        //pr("chars");pr($arMatch);
        $sAuxNr = $arMatch[0][0];
        $sAuxNr = str_replace(array("#","0","1","2","3","4","5","6","7","8","9"),"",$sAuxNr);
        $sAuxNr = trim($sAuxNr);
        return $sAuxNr;
    }
}
