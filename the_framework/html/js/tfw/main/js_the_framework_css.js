/**
 * @Author: Eduardo Acevedo Farje.
 * @Url: eduardoaf.com
 * @File: js_the_framework_css.js  
 * @Name: TfwCss  
 * @Version: 1.0.0;
 * @Date: 17-03-2013 13:58 (SPAIN)
 * @DEPENDENCIES: none 
 * @Comments: This is an css class with get and sets methods that reference
 *            all element css sColorerties.
 */
var TfwCss =
{
    //======================== BOX  ===============================
    /**
     * Sets border width, style and color
     * @param string sElementId Element id.
     * @param string sWidth thin, medium, thick, or length value eg. "15px"
     * @param string sStyle none, dotted, dashed, solid, double, groove, ridge, inset, outset.
     * @param string sColor A color value, color, #RRGGBB
     */
    set_border: function(sElementId, sWidth, sStyle, sColor)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderWidth = sWidth;
        eAnyElement.style.borderStyle = sStyle;
        eAnyElement.style.borderColor = sColor;
        //console.debug(eAnyElement);
    },    

    /**
     * Sets border width, style and color
     * @param string sElementId Element id.
     * @param string sWidth thin, medium, thick, or length value eg. "15px"
     * @param string sStyle none, dotted, dashed, solid, double, groove, ridge, inset, outset.
     * @param string sColor A color value, color, #RRGGBB
     */
    set_border_bottom: function(sElementId, sWidth, sStyle, sColor)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderBottomWidth = sWidth;
        eAnyElement.style.borderBottomStyle = sStyle;
        eAnyElement.style.borderBottomColor = sColor;
        console.debug(eAnyElement);
    },

    set_border_bottom_width: function(sElementId, sWidth)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderBottomWidth = sWidth;        
    },
    
    set_border_color: function(sElementId, sColor)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderColor = sColor;        
    }, 

    set_border_left: function(sElementId, sWidth, sStyle, sColor)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderLeftWidth = sWidth;
        eAnyElement.style.borderLeftStyle = sStyle;
        eAnyElement.style.borderLeftColor = sColor;
        //console.debug(eAnyElement);
    },


    set_border_left_width : function(sElementId, sWidth)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderLeftWidth = sWidth;
    },

    set_border_right: function(sElementId, sWidth, sStyle, sColor)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderRightWidth = sWidth;
        eAnyElement.style.borderRightStyle = sStyle;
        eAnyElement.style.borderRightColor = sColor;
        //console.debug(eAnyElement);
    },

    set_border_right_width: function(sElementId, sWidth)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderRightWidth = sWidth;
    },
    
    set_border_style: function(sElementId, sStyle)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderStyle = sStyle;
    },    
    
    set_border_top: function(sElementId, sWidth, sStyle, sColor)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderTopWidth = sWidth;
        eAnyElement.style.borderTopStyle = sStyle;
        eAnyElement.style.borderTopColor = sColor;
        //console.debug(eAnyElement);
    },

    set_border_top_width: function(sElementId, sWidth)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderTopWidth = sWidth;
    },
    
       
    set_border_width: function(sElementId, sWidth)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.borderWidth = sWidth;
    },    
    /**
     */
    set_float_clear: function(sElementId, none_left_right_both)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.clear = none_left_right_both;
    },

    set_text_float: function(sElementId, none_left_right)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.clear = none_left_right;
    },

//Block elements and IMG, INPUT, TEXTAREA, SELECT, and OBJECT height
    set_height: function(sLimElementId, sHeight)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sLimElementId);
        eAnyElement.style.height = sHeight;        
    },
    
    set_margin: function(sElementId, top, right, bottom, left)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.marginTop = top;
        eAnyElement.style.marginRight = right;
        eAnyElement.style.marginBottom = bottom;
        eAnyElement.style.marginLeft = left; 
    },
    
    set_margin_bottom: function(sElementId, bottom)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.marginBottom = bottom;
    },

    set_margin_left: function(sElementId, sMargin)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.marginBottom = sMargin;
    },
    set_margin_right: function(sElementId, sMargin)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.marginRight = sMargin;
    },
    
    set_margin_top: function(sElementId, sMargin)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.marginTop = sMargin;
    },
    
    set_padding: function(sElementId, sPadding)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.padding = sPadding;
    },
    
    set_padding_bottom: function(sElementId, sPadding)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.paddingBottom = sPadding;
    },
    
    set_padding_left: function(sElementId, sPadding)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.paddingLeft = sPadding;
    },
    
    set_padding_right: function(sElementId, sPadding)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.paddingRight = sPadding;
    },  
    
    set_padding_top: function(sElementId, sPadding)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.paddingTop = sPadding;
    },
    
    set_width: function(sElementId, sWidth)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.width = sWidth;   
    },
    
    //======================== END BOX  =============================    
    
    //================== BACKGROUND AND COLOR  ====================
    //wether a image is scrolled or fixed
    set_background_attachment: function(sElementId, scroll_fixed)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.backgroundAttachment = scroll_fixed;   
    },
    
    set_background_color: function(sElementId, sColor)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.backgroundColor = sColor;   
    },    

    set_background_image: function(sElementId, sPath)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.backgroundImage = sPath;   
    },
 

    set_background_position :function(sElementId, sPath)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.backgroundImage = sPath;   
    },
    
    
    /**
     * Configure the element ba
     * @param string sElementId Element id.
     * @param string sRepeat repeat, repeat-x, repeat-y, no-repeat.
     */
    set_background_repeat : function(sElementId, sRepeat)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.backgroundImage = sRepeat;          
    },
    
  
    /**
    * Sets element color.
    * @param string sElementId Element id.
    * @param string sColor Named or value color.
    */    
    set_color: function(sElementId, sColor)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.color = sColor;          
    },
    
    //================= END BACKGROUND AND COLOR  ==================    
    
    //====================== CLASSIFICATION  ====================    
    /**
    * Sets the type of element. Element Type: All
    * @param string sElementId Element id.
    * @param string sDisplay block, inline, list-item, none.
    */
    set_display: function(sElementId, sDisplay)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.display = sDisplay;          
    },
    
    
    /**
    * Sets list style type and/or position. Element Type: List-item
    * @param string sListItemId List item id
    * @param string sStyle armenian, circle, cjk-ideographic, decimal, decimal-leading-zero, disc, georgian, hebrew
    * hiragana, hiragana-iroha, inherit, katakana, katakana-iroha, lower-alpha, lower-greek
    * lower-latin, lower-roman, none, square, upper-alpha, upper-latin, upper-roman<br>
    * inside, outside, inherit<br>
    * e.g: sStyle: "decimal inside".
    */    
    set_list_style: function(sListItemId, sStyle)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sListItemId);
        eAnyElement.style.listStyle = sStyle;          
    },
   
    /**
    * Sets image to be used as the list item marker. Type: List-item
    * @param string sListItemId List item id.
    * @param string sUrl absolute, relative or http url.
    */
    set_list_style_image: function(sListItemId, sUrl)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sListItemId);
        eAnyElement.style.listStyleImage = "url("+sUrl+")";          
    },
    
    /**
    * Sets list style type. Element Type: List-item
    * @param string sListItemId List item id.
    * @param string sType armenian, circle, cjk-ideographic, decimal, decimal-leading-zero, disc, georgian, hebrew
    * hiragana, hiragana-iroha, inherit, katakana, katakana-iroha, lower-alpha, lower-greek
    * lower-latin, lower-roman, none, square, upper-alpha, upper-latin, upper-roman.
    */
    set_list_style_type: function(sListItemId, sType)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sListItemId);
        eAnyElement.style.listStyleType = sType;          
    },
    
    /**
    * Sets treatment of spaces inside the element. Element Type: List-item
    * @param string sDivId block item id.
    * @param string sType normal, nowrap, pre, pre-line, pre-wrap
    */    
    set_whitespace: function(sDivId, sType)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sDivId);
        eAnyElement.style.whiteSpace = sType;         
    },
    //====================== END CLASSIFICATION  ==================== 
    
    //====================== FONT  ====================   
    
    /**
    * Used to define font properties. Element Type: All.
    * @param string sElementId Element id.
    * @param string sStyle font-style normal, italic, oblique, inherit
    *                   <br>font-variant normal, small-caps, inherit,
    *                   <br>font-weight normal, bold, bolder, lighter, inherit 
    *                   <br>font-size size in pixels,
    *                   <br>font-family "times", "courier", "arial"
    */
    set_font: function(sElementId, sStyle)
    {
        //italic small-caps bold 12px arial,sans-serif
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.font = sStyle;
    },
   
    
    /**
    * Used to define font family to use. Element Type: All.
    * @param string sElementId Element id.
    * @param string sFamily "times", "courier", "arial" or "serif", "sans-serif", "cursive", "fantasy", "monospace" 
    *                       <br> e.g "Times New Roman",Georgia,Serif
    */
    set_font_family: function(sElementId, sFamily)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        //TODO if not null
        eAnyElement.style.fontFamily = sFamily;
    },
    
    /**
    * Used to define font size to use. Element Type: All.
    * @param string sElementId Element id.
    * @param string sSize Size in pixels, em, pt
    */
    set_font_size: function(sElementId, sSize)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.fontSize = sSize;
    },
    
    /**
    * Used to define font style to use. Element Type: All.
    * @param string sElementId Element id.
    * @param string sStyle normal, italic, oblique, inherit
    */
    set_font_style: function(sElementId, sStyle)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.fontStyle = sStyle;
    },
    
    /**
    * Used to determine whether to use normal or small caps. Element Type: All.
    * @param string sElementId Element id.
    * @param string sVariant normal, small-caps, inherit
    */
    set_font_variant: function(sElementId, sVariant)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.fontVariant = sVariant;
    },
    
    /**
    * Sets font weight. Element Type: All.
    * @param string sElementId Element id.
    * @param string sWeight normal, bold, bolder, lighter, inherit, 
                    <br> Defines from thin to thick characters. 400 is the same as normal, and 700 is the same as bold. 
    */
    set_font_weight: function(sElementId, sWeight)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.fontWeight = sWeight;
    },
    
    //====================== END FONT  ====================       
    
    //====================== TEXT  ====================   
    /**
    * Sets the space between characters. Element Type: All
    * @param string sElementId Element id.
    * @param string sSpacing normal or length value.
    */
    set_text_letter_spacing: function(sElementId, sSpacing)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.letterSpacing = sSpacing;   
    },
    
    /**
    * Sets the height of lines. Element Type: All
    * @param string sElementId Element id.
    * @param string sHeight normal, a number, a percent of the element font size,
    */
    set_text_line_height: function(sElementId, sHeight)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.lineHeight = sHeight;           
    },
    
    /**
    * Sets the alignment of text. Element Type: Block
    * @param string sBlockId block id
    * @param string sAlign left, right, center, justify.
    */
    set_text_align: function(sBlockId, sAlign)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sBlockId);
        eAnyElement.style.textAlign = sAlign;           
    },
    
    /**
    * Sets element color. Element Type: All
    * @param string sElementId Element id.
    * @param string sTextDecoration none, overline, underline, line-through, blink.
    */
    set_text_decoration: function(sElementId, sTextDecoration)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.textDecoration = sTextDecoration;           
    },
    
    
    /**
    * Sets the element"s first line amount of indentation. Element Type: Block
    * @param string sElementId Element id.
    * @param string sIndent Length or percent value.
    */
    set_text_indent: function(sElementId, sIndent)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.textIndent = sIndent;
    },
    
    /**
    * Transforms text to one of the set values. Element Type: All
    * @param string sElementId Element id.
    * @param string sTransform 	none, capitalize, uppercase, lowercase.
    */
    set_text_transform: function(sElementId, sTransform)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.textTransform = sTransform;   
    },
    
    /**
    * Transforms text to one of the set values. Element Type: Inline
    * @param string sElementId Element id.
    * @param string sVerticalAlign baseline, sub. super, top, middle, bottom, text-top, text-bottom, or percent value
    */
    set_text_vertical_align: function(sElementId, sVerticalAlign)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.verticalAlign = sVerticalAlign;         
    },
    
    /**
    * Sets extra space between words. Element Type: Inline
    * @param string sElementId Element id.
    * @param string sVerticalAlign normal or length value
    */
    set_text_word_spacing: function(sElementId, sWordSpacing)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        eAnyElement.style.verticalAlign = sWordSpacing;         
    },    
    
    //====================== TEXT  ====================  
    get_border: function(sElementId)
    {
        var sMessage = "";
        var eAnyElement = document.getElementById(sElementId);
        console.debug(eAnyElement);
        var oBorder = {};
        oBorder["width"] = eAnyElement.style.borderWidth;
        oBorder["style"] = eAnyElement.style.borderStyle;
        oBorder["color"] = eAnyElement.style.borderColor;
         return oBorder;
    }    
};