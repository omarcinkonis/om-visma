<?php
include_once 'Person.php';

class Display
{
    private $person;

    public function __construct()
    {
        $this->person = new Person();
        $this->menu();
    }

    private function menu()
    {
        echo "Enter a command:\n";
        echo "  register                register for an appointment\n";
        echo "  appointment             show and manage a scheduled appointment\n";
        echo "  all                     show all registrants\n";
        echo "  quit                    quit application\n\n";
        $command = chop(fgets(STDIN));

        if ($command == 'register') {
            $this->register();
        } elseif ($command == 'appointment') {
            $this->appointment();
        } elseif ($command == 'all') {
            $this->all();
        } elseif ($command == 'quit') {
            $this->quit();
        } else {
            echo "Command unrecognized, please try again.\n\n";
            $this->menu();
        }
    }

    private function register()
    {
        echo "Enter registrant's national identification number:\n";
        $code = chop(fgets(STDIN));

        if ($this->person->readSingle($code)) {
            if ($this->person->visit->readSingle($code)) {
                $this->appointment($code);
            }
        }

        echo "Enter registrant's name:\n";
        $name = chop(fgets(STDIN));

        echo "Enter a contact email:\n";
        $email = chop(fgets(STDIN));

        echo "Enter a contact phone number:\n";
        $phone = chop(fgets(STDIN));

        $personDetails = [$name, $email, $phone, $code];

        if ($this->person->create($personDetails)) {
            echo $name . " registered!\n\n";
        } else {
            echo "Registration failed.\n\n";
        }

        $this->menu();
    }

    private function appointment($code = null)
    {
        if ($code === null) {
            echo "Enter registrant's national identification number:\n";
            $code = chop(fgets(STDIN));
        }

        if ($this->person->readSingle($code)) {
            echo "\nFound appointment:\n";
            echo "  Registrant ID:          " . $this->person->id;
            echo "\n  Registrant name:        " . $this->person->name;
            echo "\n  Contact email:          " . $this->person->email;
            echo "\n  Contact phone number:   " . $this->person->phone . "\n\n";

            echo "What would you like to do?\n";
            echo "  remove                  remove appointment\n";
            echo "  time                    change time\n";
            echo "  nothing                 do nothing\n";

            $command = chop(fgets(STDIN));

            if ($command == 'remove') {
                //
            } elseif ($command == 'time') {
                //
            } elseif ($command == 'nothing') {
                //
            } else {
                echo "Command unrecognized, returning to menu.\n\n";
                $this->menu();
            }
        } else {
            echo "Could not find an appointment for the specified person.\n\n";
        }

        $this->menu();
    }

    private function all()
    {
        echo "all:\n";
        $result = $this->person->visit->read();
        while ($row = $result->fetch()) {
            echo $row['p_name'] . ' ' . $row['p_email'] . ' ' . $row['p_phone'] . ' ' . $row['p_code'] . ' ' . $row['v_time'] . "\n";
        }
        echo "\n";

        $this->menu();
    }

    private function quit()
    {
        exit();
    }
}
