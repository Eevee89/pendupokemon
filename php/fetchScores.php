<?php
include "service.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbservice = new Service();
    $result = $dbservice->getAllScores();
    echo json_encode($result);
}
?>