<div class="content-block" role="main" container="viewinclude">
<?php
//errorson();
//bug($this->sPathView);
//bugfile("$this->sPathView.php","$this->sPathView.php");
//bugipath();
//bugfileipath($this->sPathView);
//$sPathFound = get_absolute_path($this->sPathView);
//bug(getcwd(),"getcwd");
//bug(include $this->sPathView,$this->sPathView);
//$sPathFound = "C:\\inetpub\\wwwroot\\proy_tasks\\the_public\\..\\the_application\\views".$this->sPathView;
//bugfile($sPathFound);
//include $this->sPathView or die("error con: $this->sPathView");
//bug($this->sPathView);
//require($this->sPathView);
(@include($this->sPathView)) OR pr("no file: $this->sPathView");
//require($sPathFound);

?>
</div>
<!--/side_right_content (end viewinclude)-->