<?php
//BODY HEADER elem_bodyheader.php
?>
<!-- Main page header element_bodyheader-->
<header class="container">
    <!-- Main page logo -->
    <h1><a id="ancLogo" href="<?php echo $this->sUriLoggedHome;?>" class="brand"><?php tr("tr_enterprise_name");?></a></h1>
    <!-- Main page headline -->
    <p><?php tr("tr_enterprise_slogan");?></p>
    <!-- Alternative navigation -->
    <nav>
        <ul>
            <!--
            <li>
                <form id="frmTopSearch" method="post" class="nav-search" action="?<?php echo $this->sUriModule;?>">
                    <input type="text" id="txtTopSearch" name="txtTopSearch" placeholder="Search&hellip;">
                </form>
            </li>
            
            <li>
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        Configuration
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="401.html"><span class="awe-flag"></span> Error page 401</a></li>
                        <li><a href="403.html"><span class="awe-flag"></span> Error page 403</a></li>
                        <li><a href="404.html"><span class="awe-flag"></span> Error page 404</a></li>
                        <li><a href="500.html"><span class="awe-flag"></span> Error page 500</a></li>
                        <li><a href="503.html"><span class="awe-flag"></span> Error page 503</a></li>
                    </ul>
                </div>
            </li>
            -->
            <li>
                <a href="<?php echo $this->build_url("homes",NULL,"logout");?>">Logout</a>
            </li>
        </ul>
    </nav>
    <!-- /Alternative navigation -->
</header>

<script type="text/javascript" in="element_bodyheader">
Huraga.logo_header("<?php echo APP_LOGIN_LOGO_FILENAME; ?>");
</script>
<!-- /element_bodyheader-->