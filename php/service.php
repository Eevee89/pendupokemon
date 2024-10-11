<?php

class Service {
    private $pdo;
    public $tableName = "tb_hanging_scores";

    public function __construct() {
        $env = parse_ini_file('.env');
        $tmp = new PDO("mysql:host=".$env["DATABASE_HOST"].";dbname=".$env["DATABASE_NAME"], $env["DATABASE_USER"], $env["DATABASE_PASS"]);
        $tmp->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $this->pdo = $tmp;
    }

    // Example method to fetch data from the database
    public function getData($query) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllScores() {
        return $this->getData("SELECT name, score FROM $this->tableName ORDER BY score DESC;");
    }

    public function getPassword($name) {
        $row = "SELECT password FROM $this->tableName WHERE name = ?;";
        $stmt = $this->pdo->prepare($row);
        $stmt->execute([$name]);
        return $stmt->fetchColumn();
    }

    public function createAccount($name, $password) {
        $sql = "INSERT INTO $this->tableName (name, score, password) ";
        $sql .= "VALUES ('". $name ."', 0, '";
        $sql .= $password ."');";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute();
    }

    public function checkIfNameExists($value) {
        // True if name exists
        $sql = "SELECT COUNT(*) FROM $this->tableName WHERE name = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$value]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    public function update($data, $primaryKey = 'id') {
        // Prepare the SQL statement based on whether the primary key exists

        $sql = "";

        if ($this->checkIfNameExists($data["name"])) {
            $row = $this->getData("SELECT score FROM $this->tableName WHERE name = '".$data["name"]."';");;
            $score = $row[0]['score']; 

            if ($score < $data["score"]) {
                $sql .= "UPDATE $this->tableName ";
                $sql .= "SET score = " . strval($data["score"]) . " ";
                $sql .= "WHERE name = '" . $data["name"] . "';";
            }
        }

        if($sql !== "") {
            // Prepare the PDO statement
            $stmt = $this->pdo->prepare($sql);
            // Execute the statement and return the result
            $result = $stmt->execute();
            return $result;
        }
        else {
            return -1;
        }
    }
}  