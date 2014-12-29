<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ComponentSerie
 * @file component_serie.php 
 * @date 30-10-2013 18:37 (SPAIN)
 * @observations:
 * @requires 
 */
class ComponentSerie extends TheFrameworkComponent
{
    private $sSerieName;
    private $arValues;
   
    public function __construct($sSerieName="serie",$arValues=array())
    {
        $this->sSerieName = $sSerieName;
        $this->arValues = $arValues;
    }
    
    public function set_nombre($value){$this->sSerieName = $value;}
    public function set_valores($arValues=array()){$this->arValues = $arValues;}
   
    public function get_nombre(){return $this->sSerieName;}  
    public function get_valores(){return $this->arValues;}  

    /**
    * Devuelve un string en json: name: 'un nombre', (termina con coma)
    * despues de esto se deberia llamar a get_valores_en_json()
    */
    private function get_nombre_en_json()
    {
        $sNombre = $this->sSerieName;
        $sNombre = "name: '$sNombre',";
        return $sNombre;
    }
   
    /**
    * Devuelve un string en json: data: [valor_1,valor_2,...,valor_n]
    */
    private function get_valores_en_json()
    {
        /**
        {
            name: 'Agua',
            data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
        },
        */
        $arValues = $this->arValues;
        $sValue = "data: [";
        $sValue .= implode(",",$this->arValues);
        $sValue .= "]";
        return $sValue;
    }
   
    /**
    * Devuelve el objeto en un string json tipo:
    * {
    *       name: 'el nombre',
    *       data: [valor_1,valor_2,...,valor_n]
    * }
    */
    public function get_in_json()
    {
        $sInJson = "";
        $sInJson .= "{\n";
        $sNombreJson = $this->get_nombre_en_json();  //esto acaba con ,
        $sValuesJson = $this->get_valores_en_json();
        $sInJson .= $sNombreJson ."\n";
        $sInJson .= $sValuesJson . "\n";
        $sInJson .= "}";
        return $sInJson;
    }
}