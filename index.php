<?php
include "php/header.php";
 
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