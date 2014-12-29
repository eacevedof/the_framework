
<section class="container login" role="main">
<?php
if($this->sWarningMessage)
{
?>    
    <div class="alert">
        <button data-dismiss="alert" class="close">×</button>
        <strong><?php echo $this->sWarningMessage;?></strong> 
    </div>
<?php
}
?>    
    <div class="data-block">
<?php
foreach($arLinks as $oLink)
{
    $oLink->show();
}
?>   
    </div>
</section>
