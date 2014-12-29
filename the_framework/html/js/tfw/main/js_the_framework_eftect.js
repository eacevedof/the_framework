/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_effect.js
 * @Name: TfwEffect  
 * @Version: 1.0.0
 * @date: 27-07-2013 15:06 (SPAIN)
 * @DEPENDENCIES:
 */
var TfwEffect =
{

    fadeout : function(iSecconds,oElement,sBackColor,sClassName) 
    {
        var iMiliSecconds = iSecconds * 100;
        var iOpacity = 1; 
        oElement.style.opacity = 1;

        if(sBackColor) oElement.style.backgroundColor = sBackColor;
        if(sClassName) oElement.className = sClassName;

        var iThreadId = setInterval
        (
            function()
            {
                iOpacity -= 0.1;
                oElement.style.opacity = iOpacity.toFixed(1);            
                if(iOpacity<=0)
                {
                    clearInterval(iThreadId);
                    oElement.style.display = "none";
                }
            }
            , iMiliSecconds
        );
        return this;
    },
    
    fadein : function(iSecconds,oElement,sOrigDisplay,sOrigBackColor) 
    {
        var iMiliSecconds = iSecconds * 100;
        var iOpacity = 0; 

        //Se aplican los valores originales
        if(sOrigDisplay) oElement.style.display = sOrigDisplay;
        if(sOrigBackColor) oElement.style.backgroundColor = sOrigBackColor;

        var iThreadId = setInterval
        (
            function()
            {
                iOpacity += 0.1;
                oElement.style.opacity = iOpacity.toFixed(1);
                if(iOpacity>=1 || iOpacity>=1.0)
                    clearInterval(iThreadId);
            }
            ,iMiliSecconds
        );
        return this;
    },
    
    blink: function(oElement,sClassName,iSecconds) 
    {
        var iSecconds = iSecconds || 5;
        var iMiliSecconds = iSecconds * 10;
        var iOpacity = 1; 
        var isFadedOut = false;

        var sOrigClassName = oElement.className;
        var sOrigDisplay = oElement.style.display;

        oElement.style.opacity = iOpacity;
        if(sClassName) oElement.className = sClassName;

        var iThreadId = setInterval
        (
            function()
            {
                if(!isFadedOut)
                {
                    //si opacidad 1 -> estado inicial -> decrece
                    if(iOpacity>0 && iOpacity<=1)
                    {
                        iOpacity -= 0.1;
                        oElement.style.opacity = iOpacity.toFixed(1);
                    }
                    else if(iOpacity<=0)
                    {
                        isFadedOut = true;
                    }
                }
                //isFadeOut=true
                else
                {
                    iOpacity += 0.1;
                    oElement.style.opacity = iOpacity.toFixed(1);
                    //si opacidad 1 -> estado inicial -> decrece
                    if(iOpacity>=1)
                    { 
                        oElement.className = sOrigClassName;
                        oElement.style.display = sOrigDisplay;
                        clearInterval(iThreadId);
                    }
                }
            }
            ,iMiliSecconds
        );
        return this;
    }
}