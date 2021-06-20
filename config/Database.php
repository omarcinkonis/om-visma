<?php
class Database
{
    private $host = 'localhost';
    private $dbname = 'om-visma';
    private $user = 'root';
    private $password = '';

    private $conn;
    private $dsn;

    // Set PDO options
    private $options = array(
        // Throw PDOException on error
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        // Set default fetch mode
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        // Disable prepare emulation (enable prepares on database)
        PDO::ATTR_EMULATE_PREPARES => false
    );

    public function connect()
    {
        $this->conn = null;
        $this->dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;

        try {
            $this->conn = new PDO($this->dsn, $this->user, $this->password, $this->options);
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
