/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name Javascript class for canvas stroke plotting  
 * @uses 
 * @date 17-12-2012
 */
//http://permadi.com/blog/2010/10/html5-saving-canvas-image-data-using-php-and-ajax/
//http://canvas.sjmorrow.com/
var bug = function(value,title)
{
    if(window.console != undefined)
    {    
        if(title!=null) console.debug(title);
        console.debug(value);
    }
};

//Se añade el metodo contains a los objetos string
String.prototype.contains = function(sSearch)
{ 
    return this.indexOf(sSearch)!=-1; 
};

function CanvasPlotting(oCanvas) 
{
    var oCanvas = oCanvas || null;
    var oCanvasContext = oCanvas.getContext("2d");
    var isDrawing = false;
    var iDivHeaderHeight = 0;
    
    //Aplica eventos y sus controladores
    __construct();

    function start(oEvent)
    {
        isDrawing = true;
        oCanvasContext.beginPath();
        oCanvasContext.moveTo(get_coord_x(oEvent),get_coord_Y(oEvent));
        oEvent.preventDefault();
    }

    function draw(oEvent)
    {
        if(isDrawing)
        {
            oCanvasContext.lineTo(get_coord_x(oEvent), get_coord_Y(oEvent));
            oCanvasContext.stroke();
        }
        oEvent.preventDefault();
    }

    function stop(oEvent)
    {
        if(isDrawing)
        {
            oCanvasContext.stroke();
            oCanvasContext.closePath();
            isDrawing = false;
        }
        oEvent.preventDefault();
    }

    function get_coord_x(oEvent)
    {
        //bug(oEvent,"event en x");
        if(oEvent.type.contains("touch"))
            return oEvent.targetTouches[0].pageX;
        else
            return oEvent.layerX;
    }

    function get_coord_Y(oEvent) 
    {
        //bug(oEvent,"event en y");
        if(oEvent.type.contains("touch"))
            return oEvent.targetTouches[0].pageY-iDivHeaderHeight;
        else
            return oEvent.layerY;
    }

    this.clear = function() 
    {
        oCanvasContext.clearRect(0,0,oCanvas.width,oCanvas.height);
    }

    this.convert_into_image = function()
    {
        var sCanvasData = oCanvas.toDataURL("image/png");
        var oImgFromCanvas = document.getElementById("imgFromCanvas");
        oImgFromCanvas.setAtttribute("src",sCanvasData);
    }

    this.set_strokewidth = function(iWidth)
    {
        oCanvasContext.lineWidth = iWidth;
    }

    function __construct() 
    {
        if(oCanvas!=null)
        {
            //START
            oCanvas.addEventListener("touchstart",start,false);
            oCanvas.addEventListener("mousedown",start,false);
            //DRAW
            oCanvas.addEventListener("touchmove",draw,false);
            oCanvas.addEventListener("mousemove",draw,false);
            //STOP
            oCanvas.addEventListener("touchend",stop,false);
            oCanvas.addEventListener("mouseup",stop,false);
            oCanvas.addEventListener("mouseout",stop,false);
        }
    }
}
