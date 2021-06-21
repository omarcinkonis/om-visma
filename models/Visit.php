<?php
include_once './config/Database.php';

class Visit
{
    // PDO instance
    private $conn;

    // Visit belongs to (ID)
    private $personId;

    public $id;
    public $time;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($visitDetails)
    {
        $query = '
        INSERT INTO visit
        SET
            p_id = :personId,
            v_time = :time
        ';

        $stmt = $this->conn->prepare($query);

        $this->personId = htmlspecialchars(strip_tags($visitDetails[0]));
        $this->time = htmlspecialchars(strip_tags($visitDetails[1]));

        $stmt->bindParam(':personId', $this->personId);
        $stmt->bindParam(':time', $this->time);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage() . "\n";
            return false;
        }

        return true;
    }

    // Read all visits (for checking available times)
    public function read()
    {
        $query = '
            SELECT p_name, p_email, p_phone, p_code, v_id, v_time
            FROM visit
            NATURAL JOIN person
            ORDER BY p_name
        ';

        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage() . "\n";
            return false;
        }

        return $stmt;
    }

    // Read one visit
    public function readSingle($code)
    {
        $query = '
            SELECT p_name, p_email, p_phone, p_code, v_id, v_time
            FROM visit
            NATURAL JOIN person
            WHERE p_code = ?
            ORDER BY v_time DESC
        ';

        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([$code]);
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage() . "\n";
            return false;
        }

        // Ensure visit exists
        $num = $stmt->rowCount();
        if ($num < 1) {
            printf("Existing appointment not found.\n");
            return false;
        }

        $row = $stmt->fetch();

        // Set properties
        $this->id = $row['v_id'];
        $this->time = $row['v_time'];

        return true;
    }

    // Update time
    public function update($visitDetails)
    {
        $query = '
            UPDATE visit
            SET v_time = :time
            WHERE p_id = :personId
        ';

        $stmt = $this->conn->prepare($query);

        $this->personId = htmlspecialchars(strip_tags($visitDetails[0]));
        $this->time = htmlspecialchars(strip_tags($visitDetails[1]));

        $stmt->bindParam(':personId', $this->personId);
        $stmt->bindParam(':time', $this->time);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage() . "\n";
            return false;
        }

        return true;
    }

    // Delete appointment
    public function delete()
    {
        $query = '
            DELETE FROM visit
            WHERE v_id = :id
        ';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage() . "\n";
            return false;
        }

        return true;
    }
}
