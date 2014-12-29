<body <?php if($this->sBodyClass) echo " class=\"$this->sBodyClass\"";?>>
<?php
//bug("twocolumn_page_body");
//bug($this->isPermaLink,"permalink");
$this->js_load();
include("page_body_header.php");
include("page_body_content.php");
include("page_body_footer.php");
?>
</body>
