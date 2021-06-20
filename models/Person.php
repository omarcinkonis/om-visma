<?php
include_once './config/Database.php';
include_once 'models/Visit.php';

class Person
{
    // PDO instance
    private $conn;
    
    public $visit;

    public $id;
    public $name;
    public $email;
    public $phone;
    public $code;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();

        $visit = new Visit($this->conn);
        $this->visit = $visit;
    }

    public function create($personDetails)
    {      
        // Create query
        $query = '
        INSERT INTO person
        SET
            p_name = :name,
            p_email = :email,
            p_phone = :phone,
            p_code = :code
        ';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean and assign data
        $this->name = htmlspecialchars(strip_tags($personDetails[0]));
        $this->email = htmlspecialchars(strip_tags($personDetails[1]));
        $this->phone = htmlspecialchars(strip_tags($personDetails[2]));
        $this->code = htmlspecialchars(strip_tags($personDetails[3]));

        // Bind data
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':code', $this->code);

        // Execute query
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            return false;
        }

        return true;        
    }

    // Read all registrants
    public function read()
    {
        $query = '
            SELECT p_id, p_name, p_email, p_phone, p_code
            FROM person
            ORDER BY p_id
        ';

        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            return false;
        }

        return $stmt;
    }

    // Read one project
    public function readSingle($code)
    {
        $query = '
            SELECT p_id, p_name, p_email, p_phone, p_code
            FROM person
            WHERE p_code = ?
        ';

        $stmt = $this->conn->prepare($query);

        $code = htmlspecialchars(strip_tags($code));

        try {
            $stmt->execute([$code]);
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            return false;
        }

        // Ensure person exists
        $num = $stmt->rowCount();
        if ($num < 1)
        {
            printf('ERROR: person not found.');
            return false;
        }

        $row = $stmt->fetch();

        // Set properties
        $this->id = $row['p_id'];
        $this->name = $row['p_name'];
        $this->email = $row['p_email'];
        $this->phone = $row['p_phone'];
        $this->code = $row['p_code'];

        return true;
    }
}
