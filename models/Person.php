<?php
class Person
{
    // PDO instance
    private $conn;

    public $id;
    public $name;
    public $email;
    public $phone;
    public $code;

    public function __construct($db)
    {
        $this->conn = $db;
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
        if ($stmt->execute()) {
            return true;
        }

        // Print error if failed to execute
        printf('ERROR: ', $stmt->error);

        return false;
    }

    public function read()
    {
        $query = '
            SELECT p_id, p_name, p_email, p_phone, p_code
            FROM project
            ORDER BY p_id
        ';

        $stmt = $this->conn->prepare($query);

        // Execute query
        if (!$stmt->execute()) {
            printf('ERROR: ', $stmt->error);
            return false;
        }

        return $stmt;
    }

    // Get one project
    public function readSingle($code)
    {
        // Create query
        $query = '
            SELECT p_id, p_name, p_email, p_phone, p_code
            FROM project
            WHERE p_code = ?
        ';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $code = htmlspecialchars(strip_tags($code));

        // Bind code and execute query
        if (!$stmt->execute([$code])) {
            printf('ERROR: ', $stmt->error);
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
