<body class="error-page">
    <!-- Error page container -->
    <section class="error-container">
        <h1>503</h1>
        <p class="description"><?php tr("tr_503_description"); ?></p>
        <p>
            <?php tr("tr_503_info"); ?> 
            <a href="#"><?php tr("tr_503_email"); ?></a>.
        </p>
        <a href="<?php $this->show_last_url_referer(); ?>" class="btn btn-alt btn-primary btn-large" title="<?php tr("tr_503_backbutton"); ?>">
            <?php tr("tr_503_backbutton"); ?>
        </a>
    </section>
    <!-- /Error page container -->
</body>