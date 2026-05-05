<?php
// Start the session
// if(isset($title))
// {
//     echo $title;
// }
?>
<h2><?= $title;?></h2>
<?=
header("Refresh:0; url=overview.php");
?>