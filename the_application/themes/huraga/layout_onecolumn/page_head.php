<?php
//bug("page_head_1column");
//bug($this,"this en page_head");die;
?>
<!DOCTYPE html>
<!--[if IE 8]><html class="no-js ie8 ie" lang="en"><![endif]-->
<!--[if IE 9]><html class="no-js ie9 ie" lang="en"><![endif]-->
<!--[if gt IE 9]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <!-- 1col.page_head @version: 1.0.1 -->
    <meta charset="utf-8">
    <title><?php $this->show_page_title(); ?></title>
    <meta name="description" content="">
    <meta name="author" content="Eduardo A. F. | www.eduardoaf.com">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- jQuery Visualize Styles -->
    <link rel="stylesheet" type="text/css" href="/style/huraga/plugins/jquery.visualize.css">
    <link rel="stylesheet" type="text/css" href="/style/jquery/ui/jquery-ui.css"/>

    <!-- jQuery jGrowl Styles -->
    <link rel="stylesheet" type="text/css" href="/style/huraga/plugins/jquery.jgrowl.css">

    <!-- CSS styles -->
    <link rel="stylesheet" type="text/css" href="/style/huraga/huraga-red.css">
    <link rel="stylesheet" type="text/css" href="/style/custom/huraga_override.css">
    <!-- en iis añadir el tipo mime: text/css con extension.less -->
    <!--<link rel="stylesheet/less" type="text/less" href="style/custom/less_huraga_override.less">-->
    <link rel="stylesheet" type="text/css" href="/style/custom/app.css">

    <!-- Fav and touch icons -->
    <link rel="shortcut icon" href="/images/huraga/icons/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/images/huraga/icons/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/images/huraga/icons/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/images/huraga/icons/apple-touch-icon-57-precomposed.png">

    <!-- JS Libs -->
    <script src="/js/the_framework/cssless/cssless_1.3.3.js" type="text/javascript"></script>
    <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>-->
    <script href="/js/jquery/js_jquery_v1.10.2.js"></script>
    <script href="/js/jquery/ui/jquery_ui_v1.10.3.custom.min.js"></script>
    <script href="/js/jquery/ui/jquery.ui.datepicker-es.js"></script>
    
    <script type="text/javascript">window.jQuery || document.write("<script src=\"./js/huraga/libs/jquery.js\"><\/script>");</script>
    <script href="/js/huraga/libs/modernizr.js"></script>
    <script href="/js/huraga/libs/selectivizr.js"></script>

    <script href="/js/huraga/bootstrap/bootstrap-tooltip.js" type="text/javascript"></script>
    <script href="/js/huraga/bootstrap/bootstrap-alert.js" type="text/javascript"></script>
    <!--
<script src="js/huraga/bootstrap/bootstrap-tooltip.js" type="text/javascript"></script>
<script src="js/huraga/navigation.js" type="text/javascript"></script>
<script src="js/huraga/bootstrap/bootstrap-affix.js" type="text/javascript"></script>

<script src="js/huraga/bootstrap/bootstrap-dropdown.js" type="text/javascript"></script>
<script src="js/huraga/bootstrap/bootstrap-tab.js" type="text/javascript"></script>
<script src="js/huraga/bootstrap/bootstrap-collapse.js" type="text/javascript"></script>
<script src="js/huraga/bootstrap/bootstrap-button.js" type="text/javascript"></script>

<script src="js/huraga/bootstrap/bootstrap-popover.js" type="text/javascript"></script>
<script src="js/huraga/bootstrap/bootstrap-modal.js" type="text/javascript"></script>
<script src="js/huraga/bootstrap/bootstrap-transition.js" type="text/javascript"></script>
<script src="js/huraga/plugins/snippet/jquery.snippet.min.js" type="text/javascript"></script>
<script src="js/huraga/plugins/jGrowl/jquery.jgrowl.js" type="text/javascript"></script>
-->
<script type="text/javascript">
jQuery(document).on
(   
    "ready",
    function()
    {
        // Tooltips
        jQuery("[title]").tooltip
        (
            {
                placement: "top"
            }
        );
    }
);
</script>
<script type="text/javascript">
jQuery(document).on
(   
    "ready",
    function()
    {
        // Tooltips
        jQuery("[title]").tooltip
        (
            {
                placement: "top"
            }
        );
    }
);
//es equivalente a jquery(document).ready(function(){})
jQuery
(
    //Carga de jquery calendar
    function()
    {
        //todo lo que tenga tipo date
        jQuery("input[type='date'],input[type='text'][as='date']").datepicker
        (
            {
                changeMonth: true,
                changeYear: true
            }
        );
        
        jQuery("input").focus
        (
            function() 
            {
                var sElementValue = this.value;
                var oJqInput = jQuery(this);
                oJqInput.val("");
                setTimeout(function(){oJqInput.val(sElementValue);},1);
            }
        );
    }
);    
</script>
    
</head>