 <div class="col-sm-2 col-md-1 sidebar hidden-sm hidden-xs">
	<h4>Containers</h4>
          <ul class="nav nav-sidebar">
<?php
//display Navbar Menu
$menuItems=array("List Active","List All","Create New");
$activeItem=1;
if(isset($_GET['subMenuID']))$activeItem=$_GET['subMenuID'];
//no need to create active link if we are in the container view
if(isset($_GET['containerID']))$activeItem=0;
for($i=0;$i<sizeof($menuItems);$i++)
{
	 $activeItemClass="";	
	 if($activeItem==($i+1))$activeItemClass=' class="active"';
	 echo '<li'.$activeItemClass.'><a href="index.php?subMenuID='.($i+1).'">'.$menuItems[$i].'</a></li>';
}
?>
          </ul>
</div>