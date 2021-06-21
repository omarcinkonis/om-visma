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
            echo "User found. Hello, " . $this->person->name . ".\n";
        } else {
            echo "Enter registrant's name:\n";
            $name = chop(fgets(STDIN));

            echo "Enter a contact email:\n";
            $email = chop(fgets(STDIN));

            echo "Enter a contact phone number:\n";
            $phone = chop(fgets(STDIN));

            $personDetails = [$name, $email, $phone, $code];

            if ($this->person->create($personDetails)) {
                echo $name . " added to database.\n\n";
            } else {
                echo "Registration failed. Returning to menu.\n\n";
                $this->menu();
            }
        }

        if ($this->person->visit->readSingle($code)) {
            $this->appointment($code);
        } else {
            $this->assignDate();
        }

        $this->menu();
    }

    private function assignDate($update = false)
    {
        echo "Choose appointment date:\n";
        $date = $this->pickDate();

        // For the sake of simplicity, all appointments are set to 12:00:00
        // echo "Choose appointment time:\n";
        // pickTime();

        $datetime = $date . ' 12:00:00';
        $visitDetails = [$this->person->id, $datetime];

        if ($update === true)
        {
            if ($this->person->visit->update($visitDetails)) {
                echo $this->person->name . ' registered for vaccination on ' . $datetime . "\n";
                return true;
            } else {
                echo "Failed to update time. Returning to menu.\n\n";
                return false;
            }
        }

        if ($this->person->visit->create($visitDetails)) {
            echo $this->person->name . ' registered for vaccination on ' . $datetime . "\n\n";
            return true;
        } else {
            echo "Registration failed. Returning to menu.\n\n";
            return false;
        }
    }

    private function pickDate()
    {
        $date = date('Y-m-d');
        $availableDates = [];

        for ($i = 0; $i < 5; $i++) {
            if (
                $this->getWeekday($date) == 6 ||
                $this->getWeekday($date) == 7
            ) {
                $i--;
                continue;
            }
            echo "  " . ($i+1) . "                       " . $date . "\n";
            array_push($availableDates, $date);
            $date = $this->addDay($date);
        }

        $userPick = chop(fgets(STDIN))-1;

        while($userPick > 4)
        {
            echo "Choice not valid, please enter one of the given options.";
            $userPick = chop(fgets(STDIN)) - 1;
        }

        return $availableDates[$userPick];
    }

    private function getWeekday($date)
    {
        return date('w', strtotime($date));
    }

    private function addDay($date)
    {
        $date = date_create($date);
        $date = date_add($date, date_interval_create_from_date_string("1 day"));
        return $date->format('Y-m-d');
    }

    private function appointment($code = null)
    {
        if ($code === null) {
            echo "Enter registrant's national identification number:\n";
            $code = chop(fgets(STDIN));
        }

        if ($this->person->visit->readSingle($code)) {
            $this->person->readSingle($code);
            echo "\nAppointment found:";
            echo "\n  Registrant ID:          " . $this->person->id;
            echo "\n  Registrant name:        " . $this->person->name;
            echo "\n  Contact email:          " . $this->person->email;
            echo "\n  Contact phone number:   " . $this->person->phone;
            echo "\n  Appointment time:       " . $this->person->visit->time;
            echo "\n\n";

            echo "What would you like to do?\n";
            echo "  remove                  remove appointment\n";
            echo "  time                    change time\n";
            echo "  nothing                 do nothing\n";

            $command = chop(fgets(STDIN));

            if ($command == 'remove') {
                $this->person->visit->delete();
                echo "Registration removed.\n";
            } elseif ($command == 'time') {
                $this->assignDate(true);
            } elseif ($command == 'nothing') {
                //
            } else {
                echo "Command unrecognized, returning to menu.\n";
                $this->menu();
            }
        } else {
            echo "Please register to manage your appointment.\n";
        }

        echo "\n";
        $this->menu();
    }

    private function all()
    {
        $result = $this->person->visit->read();
        echo "(name, email, phone, national ID, visit date, visit time)\n";
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
