<!-- Main page container -->
<section class="container" role="main">
<!-- Left (navigation) side -->
<?php
if($this->showLeftcolumn)
include($this->sPathLayoutDs."side_left_navbar.php");
?>
<!-- Left (navigation) side -->

<!-- Right (content) side -->
<?php
//incluir la vista
//bugfileipath($this->sPathLayoutDs."side_right_content.php");
include($this->sPathLayoutDs."side_right_content.php");
?>
<!-- /Right (content) side -->
</section>
<!-- /Main page container -->