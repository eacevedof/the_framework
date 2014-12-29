<body class="error-page">
    <!-- Error page container -->
    <section class="error-container">
        <h1>401</h1>
        <p class="description"><?php tr("tr_401_description"); ?></p>
        <p>
            <?php tr("tr_401_info"); ?> 
            <a href="#"><?php tr("tr_401_email"); ?></a>.
        </p>
        <a href="<?php $this->show_last_url_referer(); ?>" class="btn btn-alt btn-primary btn-large" title="<?php tr("tr_401_backbutton"); ?>">
            <?php tr("tr_401_backbutton"); ?>
        </a>
    </section>
    <!-- /Error page container -->
</body>