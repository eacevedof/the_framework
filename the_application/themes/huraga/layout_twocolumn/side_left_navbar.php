<?php
//bug($this->oSessionUser); die;
//$this->oSessionUser = new ModelUser();
//bug($this->oSessionUser);
?>
<div class="navigation-block">

     <!-- User profile -->
     <section class="user-profile">
         <figure>
             <img alt="<?php echo $this->oSessionUser->get_description(); ?>" 
                  src="<?php echo $this->oSessionUser->get_path_picture(); ?>">
             <figcaption>
                 <strong>
                     <a href="#" class="">
                     <!--<a href="<?php echo $this->build_url("users",NULL,"update","id=".$this->oSessionUser->get_id()); ?>" class="">-->
                     <?php echo $this->oSessionUser->get_description(); ?>
                     </a>
                 </strong>
                 <em><?php echo $this->oSessionUser->get_lowest_group(); ?></em>
                 <!--
                 <ul>
                     <li><a class="btn btn-primary btn-flat" href="#" title="Account settings">settings</a></li>
                     <li><a class="btn btn-primary btn-flat" href="#" title="Message inbox">inbox</a></li>
                 </ul>
                 -->
             </figcaption>
         </figure>
     </section>
     <!-- /User profile -->

     <!-- Sample left search bar --
     <form class="side-search">
         <input type="text" class="rounded" placeholder="To search type and hit enter">
     </form>
     <!-- /Sample left search bar -->
<?php
include_once("elem_verticalmenu.php");
?>
     <!-- Sample side note --
     <section class="side-note">
         <div class="side-note-container">
             <h2>Sample Side Note</h2>
             <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quis erat dui, quis purus.</p>
         </div>
         <div class="side-note-bottom"></div>
     </section>
     <!-- /Sample side note -->

 </div>
<!-- /navigation-block -->