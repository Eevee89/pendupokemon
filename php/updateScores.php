<?php
include "service.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = [
        "code" => 200,
        "message" => "OK"
    ];

    if ($_POST['name'] !== "Entrez votre pseudo") {
        $data = [
            "name" => $_POST["name"],
            "score" => $_POST['score']
        ];
 
        $dbservice = new Service();
        $result = $dbservice->insertOrUpdate($data);

        if (!$result) {
            $res["code"] = 500;
            $res["message"] = "Une erreur interne est survenue";
        }
    }
    else {
        $res["code"] = 207;
        $res["message"] = "Pas de pseudo pour la sauvegarde";
    }

    echo json_encode($res);
}
?>