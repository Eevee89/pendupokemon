<?php
include "service.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbservice = new Service();
    $result = $dbservice->isConnOpen() ? $dbservice->getAllScores() : [];
    echo json_encode($result);
}
?>