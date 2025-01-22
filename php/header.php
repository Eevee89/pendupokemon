<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));
$isios = stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false || stripos($userAgent, 'iPod') !== false;

function isStringValid($string) {
    if (strlen($string) > 15) {
        return false;
    }

    $forbiddenChars = ["-", ";", "'", " ", ".", ",", "/", "\\", "\"", ":", "!", "?", "<", ">"];
    foreach($forbiddenChars as $char) {
        if (str_contains($string, $char)) {
            return false;
        }
    }

    return true;
}

$json = file_get_contents('pokemons.json'); 

if ($json === false) {
    die('Error reading the JSON file');
}

$pokemons = json_decode($json, true); 

if ($pokemons === null) {
    die('Error decoding the JSON file');
}

$id = random_int(0, count($pokemons));
$_SESSION["Pokemon"] = $pokemons[$id];

$_SESSION["Score"] = 0;
$_SESSION["Errors"] = 0;
$_SESSION["Gens"] = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$tmp = "";
for ($i = 0; $i < strlen($_SESSION["Pokemon"]["Nom"]); $i++) {
    $tmp .= "_";
}
$_SESSION["Guess"] = $tmp;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta property="og:url" content="https://jorismartin.fr/" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Pendu Pokémon" />
    <meta property="og:image" content="https://jorismartin.fr/pikachu.png" />
    <meta property="og:image:width" content="595" />
    <meta property="og:image:height" content="419" />
    <meta property="og:image:alt" content="Jouez au pendu avec des pokémons" />
    <title>Pendu Pokemon</title>
    <link rel="icon" href="images/pokeball.png"/>
    
    <?php if($isMob): ?>
        <link rel="stylesheet" href="css/mobile.css"/>
    <?php else: ?>
        <link rel="stylesheet" href="css/desktop.css"/>
    <?php endif; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
</head>
</html>