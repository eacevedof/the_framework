<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ComponentBaseLine
 * @file component_basic_line.php 
 * @date 29-10-2013 19:33 (SPAIN)
 * @observations:
 * @requires  
 */
class ComponentBaseline extends TheFrameworkComponent
{
    //chart:
    private $sIdDivContainer;
    private $sDefaultSeriesType;
    private $iMarginRight;
    private $iMarginBottom;
   
    //Title
    private $sTitleText;
    private $iTitleX;
   
    //Subtitle
    private $sSubtitleText;
    private $iSubtitleX;
   
    //Legend
    private $sLegendLayout; //vertical
    private $sLegendAlign; //right
    private $sLegendVerticalAlign; //top
    private $iLegendX;
    private $iLegendY;
    private $iLegendBorderWidth;
    private $sUnit;
    
    //EjeY
    private $sEjeYTitle;
   
    //EjeX
    private $arEjeXCategorias;
    private $arObjSeriesEjeXY; //cada item debe tener name: 'nombre' y data: array   [6.9, 9.5]
   
    /**
    * put your comment there...
    *
    * @param array $arLabelsX Array tipo array(sLabel_1,..,sLabeln) Lo que se imprimira como labels
    * en el eje X
    * @param array $arObjSeries Array con objetos de la clase CSerie. Requiere include o IMPORT
    * @return ComponentChartBaseline
    */
    public function __construct($arLabelsX=array(),$arObjSeries=array())
    {
        //parent::__construct();
        //chart:
        $this->sIdDivContainer = "divBaseline";
        $this->sDefaultSeriesType = "line";
        $this->iMarginRight = 130;
        $this->iMarginBottom = 25;
        //title
        $this->sTitleText = "Title Principal";
        $this->iTitleX = -20;
        //Subtitle
        $this->sSubtitleText = "Subtitle";
        $this->iSubtitleX = -50;
        //Legend
        $this->sLegendLayout = "Vertical";
        $this->sLegendAlign = "right";
        $this->sLegendVerticalAlign = "top";
        $this->iLegendX = -10;
        $this->iLegendY = 100;
        $this->iLegendBorderWidth = 0;
        $this->sUnit = "€";
        //Eje Y
        $this->sEjeYTitle = "titulo eje y";
        //Eje X
        $this->arEjeXCategorias = $arLabelsX;
        $this->arObjSeriesEjeXY = $arObjSeries;
    }
   
   
    public function get_container()
    {
        $sIdDiv = $this->sIdDivContainer;
        $sHtmlDiv = "<div id=\"$sIdDiv\" style=\"margin:0 auto;padding:0;\">
                    </div>";
        echo $sHtmlDiv;
    }    
   
    public function get_javascript()
    {
?>
    <script type="text/javascript">
        var oChart;
        jQuery(document).ready
        (
            function()
            {
                oChart = new Highcharts.Chart
                (
                    {
                        chart:
                        {
                            renderTo: '<?php echo $this->sIdDivContainer; ?>',
                            defaultSeriesType: '<?php echo $this->sDefaultSeriesType; ?>',
                            marginRight: <?php echo $this->iMarginRight; ?>,
                            marginBottom: <?php echo $this->iMarginBottom; ?>
                        },
                        title:
                        {
                            text: '<?php echo $this->sTitleText; ?>',
                            x: <?php echo $this->iTitleX; ?> //center
                        },
                        subtitle:
                        {
                            text: '<?php echo $this->sSubtitleText; ?>',
                            x: <?php echo $this->iSubtitleX; ?>
                        },
                        legend:
                        {
                            layout: '<?php echo $this->sLegendLayout; ?>',
                            align: '<?php echo $this->sLegendAlign; ?>',
                            verticalAlign: '<?php echo $this->sLegendVerticalAlign; ?>',
                            x: <?php echo $this->iLegendX; ?>,
                            y: <?php echo $this->iLegendY; ?>,
                            borderWidth: <?php echo $this->iLegendBorderWidth; ?>
                        },
                        tooltip:
                        {
                            formatter: function()
                            {
                                return '<b>'+ this.series.name +'</b><br/>'+
                                this.x +': '+ this.y +'<?php echo $this->sUnit; ?>';
                            }
                        },
                        yAxis:
                        {
                            title:
                            {
                                text: '<?php echo $this->sEjeYTitle; ?>'
                            },
                            plotLines:  //Lineas guia horizontales
                            [
                                {
                                    value: 0,
                                    width: 1,
                                    color: '#808080'
                                }
                            ]
                        },                        
                        xAxis:
                        {
                            //Labels eje X
                            categories: <?php echo $this->get_categorias_in_json(); ?>
                        },
                        series:
                        [
                            <?php echo $this->get_series_in_json(); ?>  
                        ]
                    }
                );
           }
        );
    </script>
<?php
    }
   
    /**
    * Crea un string json desde el array de categorias.
    * Devuelve algo como:
    * ['label en x1', 'label en x2',...,'label en xn']  
    */
    private function categorias_to_string()
    {
        //['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        $arLabelsX = $this->arEjeXCategorias;
        $sCagegorias = "['";
        $sCagegorias .= implode("','",$arLabelsX);
        $sCagegorias .= "']\n";
       
        return $sCagegorias;
    }
   
    /**
    * Devuelve el array categorias en un string json del siguiente tipo:
    * ['label en x1', 'label en x2',...,'label en xn']
    */
    private function get_categorias_in_json()
    {
        $sCategorias = $this->categorias_to_string();
        return $sCategorias;
    }
   
    private function get_in_quotes($sTexto){return "\'$sTexto\'";}
   
    /**
    * Devuelve un string en notacion json del tipo:
    *           {
    *               name: 'Nombre leyenda 1',
    *               data: [valor_en_y_1,valor_en_y_2,..,valor_en_y_n]
    *           },
    *           {
    *               name: 'Nombre leyenda 2',
    *               data: [valor_en_y_1,valor_en_y_2,..,valor_en_y_n]
    *           }
    */
    private function get_series_in_json()
    {
        $sSeriesInJson = "";
        //$sSeriesInJson .= "series:\n";
        //$sSeriesInJson .= "[\n";
        $arObjSeries = $this->arObjSeriesEjeXY;
        foreach($arObjSeries as $oSerie)
        {
            $sSeriesInJson .= $oSerie->get_in_json();
            $sSeriesInJson .= ",\n";  
        }
        //$sSeriesInJson .= "]";
        return $sSeriesInJson;
    }
   
    //********************* SET *************************    
    public function set_id_div_contenedor($value){$this->sIdDivContainer = $value;}
    public function set_categorias_eje_x($arValor){$this->arEjeXCategorias = $arValor;}
    /**
    * Las series son los 'valores' que toma cada 'nombre' en cada uno de los
    * items de las categorias (labels X), por lo tanto el valor se refleja en Y,
    * el 'nombre' en la leyenda  y ambos se posicionan para cada 'categoria' en X
    *
    * @param array $arObjectSeries Tipo $arObjectSeries[i]= new CSerie ('nombre',$arValores)
    */
    public function set_series_eje_xy($arObjectSeries){$this->arObjSeriesEjeXY = $arObjectSeries;}
    public function set_border_width($value){$this->iLegendBorderWidth = $value;}
    public function set_legend_x($value){$this->iLegendX = $value;}
    public function set_legend_y($value){$this->iLegendY = $value;}  
    public function set_margin_bottom($value){$this->iMarginBottom = $value;}
    public function set_margin_right($value){$this->iMarginRight = $value;}
    public function set_subtitulo_pos_x($value){$this->iSubtitleX = $value;}
    public function set_titulo_pos_x($value){$this->iTitleX = $value;}
    public function set_legend_align($value){$this->sLegendAlign = $value;}
    public function set_default_series_type($value){$this->sDefaultSeriesType = $value;}
    public function set_legend_layout($value){$this->sLegendLayout = $value;}
    /**
    * El titulo principal del grafico. Se pinta arriba
    * @param string $value El texto del titulo
    */
    public function set_subtitulo($value){$this->sSubtitleText = $value;}
    public function set_titulo_eje_y($value){$this->sEjeYTitle = $value;}
    /**
    * El subtitulo. Se pinta debajo del Title principal
    * @param string $value El texto del subtitulo
    */
    public function set_titulo($value){$this->sTitleText = $value;}    
    public function set_legend_vertical_align($value){$this->sLegendVerticalAlign = $value;}
    public function set_unit($value){$this->sUnit=$value;}
}