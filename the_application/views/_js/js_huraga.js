/**
 * @author Eduardo A.F.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name Huraga
 * @file js_huraga.js   
 * @date 15-06-2014 10:10 (SPAIN)
 * @observations: 
 *      Apply changes in huraga theme.
 * @requires:
 */
var Huraga =
{
    logo_login : function (sPathLogo)
    {
        var sPathLogo = "/images/custom/"+sPathLogo;
        var oH1 = document.getElementsByTagName("h1");
        oH1 = oH1[0];
        //bug(oH1);
        oH1.innerHTML = '<a class=\"brand\" href=\"#\"></a>';
        var oAnchorLogo = document.querySelectorAll("a.brand");
        //bug(oAnchorLogo);
        if(oAnchorLogo)
        {
            oAnchorLogo = oAnchorLogo[0];
            oAnchorLogo.style.backgroundSize="340px 75px";
            oAnchorLogo.style.backgroundImage="url('"+sPathLogo+"')";
        }
    },//logo_login

    logo_header : function(sPathLogo)
    {
        var sPathLogo = "/images/custom/"+sPathLogo;
        var oAnchorLogo = document.getElementById("ancLogo");
        if(oAnchorLogo)
        {
            oAnchorLogo.style.backgroundSize = "181px 41px";
            oAnchorLogo.style.backgroundImage = "url('"+sPathLogo+"')";
        }
    }//logo_header
}//Huraga
