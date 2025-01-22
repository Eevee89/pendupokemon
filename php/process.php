<?php
include "service.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = [
        "code" => 200,
        "message" => "OK",
        "nbErrors" => -1,
        "found" => false,
        "guess" => "",
        "answer" => "",
        "pokedex" => -1,
        "score" => -1,
        "hint" => ["", ""],
    ];

    if ($_POST["SENDER"] === "KEYDOWN") {
        if (isset($_SESSION["Pokemon"]) && str_contains(strtoupper($_SESSION["Pokemon"]["Nom"]), $_POST["KEYDOWN"])) {
            $res["message"] = "Char in name";

            $tmp = "";

            for ($i=0; $i<strlen($_SESSION["Pokemon"]["Nom"]); $i++) {
                if ($_SESSION["Guess"][$i] !== "_" ||
                    strtoupper($_SESSION["Pokemon"]["Nom"][$i]) === $_POST["KEYDOWN"]
                ) {
                    $tmp .= strtoupper($_SESSION["Pokemon"]["Nom"][$i]);
                } else {
                    $tmp .= "_";
                }
            }

            $_SESSION["Guess"] = $tmp;

            if (strtoupper($_SESSION["Pokemon"]["Nom"]) === $_SESSION["Guess"]) {
                $res["found"] = true;
                $res["pokedex"] = $_SESSION["Pokemon"]["Pokedex"];
                $_SESSION["Score"] += 10 - $_SESSION["Errors"];

                if (isset($_SESSION["Username"])) {
                    $data = [
                        "name" => $_SESSION["Username"],
                        "score" => $_SESSION["Score"]
                    ];
            
                    $dbservice = new Service();
                    $result = $dbservice->update($data);
            
                    if (!$result) {
                        $res["code"] = 500;
                        $res["message"] = "Une erreur interne est survenue";
                    }
                }
                else {
                    $res["code"] = 207;
                    $res["message"] = "Pas de pseudo pour la sauvegarde";
                }
            }

        } else {
            $_SESSION["Errors"] += 1;
            $res["message"] = "Char not in name";
            if ($_SESSION["Errors"] >= 10) {
                $res["answer"] = strtoupper($_SESSION["Pokemon"]["Nom"]);
                $res["pokedex"] = $_SESSION["Pokemon"]["Pokedex"];
                $_SESSION["Score"] = 0;
                $res["score"] = 0;
            }
        }

        $res["score"] = $_SESSION["Score"];
        $res["guess"] = $_SESSION["Guess"];
        $res["nbErrors"] =  $_SESSION["Errors"];
    }

    else if ($_POST["SENDER"] === "HINT") {
        $res["hint"] = [$_SESSION["Pokemon"]["Type1"]];
        if (isset($_SESSION["Pokemon"]["Type2"])) {
            $res["hint"] = [$_SESSION["Pokemon"]["Type1"], $_SESSION["Pokemon"]["Type2"]];
        }
        $_SESSION["Score"] -= 2;
    }

    else if ($_POST["SENDER"] === "NEWGAME") {
        if ($_POST["REPLAY"] === 0) {
            $_SESSION["Score"] = 0; 
        }

        $_SESSION["Gens"] = $_POST["GENS"];
        $_SESSION["Errors"] = 0;

        $json = file_get_contents('../pokemons.json'); 
        if ($json === false) {
            die('Error reading the JSON file');
        }

        $pokemons = json_decode($json, true); 
        if ($pokemons === null) {
            die('Error decoding the JSON file');
        }

        $tmp = [];
        foreach($pokemons as $pokemon) {
            if (in_array($pokemon["Generation"], $_SESSION["Gens"])) {
                $tmp[] = $pokemon;
            }
        }
        $id = random_int(0, count($tmp));
        $_SESSION["Pokemon"] = $tmp[$id];

        $tmp = "";
        for ($i = 0; $i < strlen($_SESSION["Pokemon"]["Nom"]); $i++) {
            $tmp .= "_";
        }
        $_SESSION["Guess"] = $tmp;

        $res["score"] = $_SESSION["Score"];
        $res["nbErrors"] = $_SESSION["Errors"];
        $res["guess"] = $_SESSION["Guess"];
    }

    header('Content-Type: application/json'); 
    echo json_encode($res);
}
?>