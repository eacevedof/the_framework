<body class="error-page">
    <!-- Error page container -->
    <section class="error-container">
        <h1>404</h1>
        <p class="description"><?php tr("tr_404_description"); ?></p>
        <p>
            <?php tr("tr_404_info"); ?> 
            <a href="#"><?php tr("tr_404_email"); ?></a>.
        </p>
        <a href="<?php $this->show_last_url_referer(); ?>" class="btn btn-alt btn-primary btn-large" title="<?php tr("tr_404_backbutton"); ?>">
            <?php tr("tr_404_backbutton"); ?>
        </a>
    </section>
    <!-- /Error page container -->
</body>