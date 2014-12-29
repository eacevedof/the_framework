/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_document.js  
 * @Name: TfwDocument
 * @Version: 1.0.0;
 * @Date: 17-03-2013 13:58 (SPAIN)
 * @DEPENDENCIES: js_the_framework_core.js
 */
var TfwDocument =
{
    oCore : TfwCore,
    
    on_dom_content_loaded: function(oFunction, doBubble)
    {
        var oCore = TfwCore;
        var isBubbling = false;
        if(doBubble != null)
            isBubbling = doBubble;
        //set_event check if function is null before adding it
        this.oCore.set_event(document,"DOMContentLoaded",oFunction, isBubbling);
    } 
}