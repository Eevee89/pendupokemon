<?php
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pendu Pokemon</title>
    <link rel="icon" href="pokeball.png"/>
    
    <?php if($isMob): ?>
        <link rel="stylesheet" href="css/mobile.css"/>
    <?php else: ?>
        <link rel="stylesheet" href="css/desktop.css"/>
    <?php endif; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="js/pokemons.js"></script>
</head>
</html>