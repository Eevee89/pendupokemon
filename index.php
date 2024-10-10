<?php
session_start();
include "php/header.php";
include "php/service.php";
 
$dbservice = new Service();

if($isMob){ 
    include "php/mobile.php";
}else{ 
    include "php/desktop.php"; 
}

?>
<?php if($isMob): ?>
    <script src="js/mobile.js"></script>
<?php else: ?>
    <script src="js/desktop.js"></script>
<?php endif; ?>