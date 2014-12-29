<body <?php if($this->sBodyClass) echo " class=\"$this->sBodyClass\"";?>>
<?php
$this->js_load();
include($this->sPathView);
?>
</body>
