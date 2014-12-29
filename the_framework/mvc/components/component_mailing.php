<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.4
 * @name ComponentMailing
 * @file component_mailing.php
 * @date 20-04-2014 20:49 (SPAIN)
 */
class ComponentMailing extends TheFrameworkComponent
{
    private $sFromTitle = "AUTO SENDER";
    private $sEmailFrom = "yournoreply@domain.com";
    private $mxEmailsTo;
    private $arEmailsCc;
    private $arEmailsBcc;
    private $_subject;
    private $_content;
    private $_header;
    
    private $arHeaders = array();
    
    /**
     * 
     * @param string|array $mxEmailTo array tipo array(email1,email2...)
     * @param string $sSubject 
     * @param string|array $mxContent array tipo $arLines = array("line text 1","line text 2"..) or string
     */
    public function __construct($mxEmailTo="",$sSubject="",$mxContent="")
    {
        parent::__construct();
        $this->_header[] = "MIME-Version: 1.0";
        $this->_header[] = "Content-type: text/html; charset=\"iso-8859-1\"";
        //add boundary string and mime type specification
        $this->_header[] = "Content-Transfer-Encoding: 7bit";
        $this->mxEmailsTo = $mxEmailTo;
        $this->_subject = $sSubject;
        
        if(is_array($mxContent)) 
            $mxContent = implode("\r\n",$mxContent);
        $this->_content = $mxContent;
    }
    
    /**
     * Utiliza la funcion mail. Se puede recuperar el error con $this->get_error_message();
     * @return boolean TRUE if error occurred
     */
    public function send()
    {  
        $this->build_header_from();
        //$this->build_header_to(); //esto se crea como primer parametro
        $this->build_header_cc();
        $this->build_header_bcc();
        //crea los header en $this->_header
        $this->build_header();
        
        if($this->mxEmailsTo)
        {
            if(is_array($this->mxEmailsTo)) 
                $this->mxEmailsTo = implode(", ",$this->mxEmailsTo);
            
            //TRUE if success
            $mxStatus = mail($this->mxEmailsTo,$this->_subject,$this->_content, $this->_header);
            if($mxStatus == false)
                $this->add_error("Error sending email!");        
        }
        else 
        {
            $this->add_error("No target emails!");
        }
        return $this->isError;
    }
    
    private function build_header()
    {
        $sHeader = implode("\r\n",$this->arHeaders);
        $this->_header = $sHeader;
    }
    
    private function build_header_from()
    {
        if($this->sEmailFrom)
            $this->arHeaders[] = "From: $this->sFromTitle <$this->sEmailFrom>";
    }
    
//    private function build_header_to()
//    {
//        if($this->mxEmailsTo)
//            $this->arHeaders[] = "To: ".implode(", ",$this->mxEmailsTo);
//    }
    
    private function build_header_cc()
    {
        if($this->arEmailsCc)
            $this->arHeaders[] = "Cc: ".implode(", ",$this->arEmailsCc);
    }    
    
    private function build_header_bcc()
    {
        if($this->arEmailsBcc)
            $this->arHeaders[] = "Bcc: ".implode(", ",$this->arEmailsBcc);
    }      

    //**********************************
    //             SETS
    //**********************************
    public function set_subject($sSubject){$this->_subject = $sSubject;}
    public function set_email_from($sEmail){$this->sEmailFrom = $sEmail;}
    public function set_emails_to($arEmails){$this->mxEmailsTo = $arEmails;}
    public function set_emails_cc($arEmails){$this->arEmailsCc = $arEmails;}
    public function set_emails_bcc($arEmails){$this->arEmailsBcc = $arEmails;}
    public function set_header($sHeader){$this->_header = $sHeader;}
    public function set_content($mxContent){(is_array($mxContent))? $this->_content = implode("\r\n",$mxContent): $this->_content = $mxContent;}
    public function set_title_from($sTitle){$this->sFromTitle = $sTitle;}
    
    /**
     *  Required
        "MIME-Version: 1.0"
        "Content-type: text/html; charset=iso-8859-1"
        Optional
     *  "From: Recordatorio <cumples@example.com>"
        "To: Mary <mary@example.com>, Kelly <kelly@example.com>"
        "Cc: birthdayarchive@example.com"
        "Bcc: birthdaycheck@example.com"
     * mail($to,$subject,$message,$headers);
     * 
     * @param string $sHeader Cualquer linea anterior
     */
    public function add_header($sHeader){$this->arHeaders[] = $sHeader;}
    public function clear_headers(){$this->arHeaders=array();}
    
    //**********************************
    //             GETS
    //**********************************
}