<?php
class Display
{
    private $name;

    public function __construct()
    {
        $this->menu();
    }

    private function menu()
    {
        echo "Enter a command:\n";
        echo "  register          register for an appointment\n";
        echo "  quit              quit application\n\n";
        $command = chop(fgets(STDIN));

        if ($command == 'register') {
            $this->register();
        }

        if ($command == 'quit') {
            $this->quit();
        }
    }

    private function register()
    {
        echo "Enter your name\n";
        $this->name = chop(fgets(STDIN));
        echo $this->name . " registered!\n\n";

        $this->menu();
    }

    private function quit()
    {
        exit();
    }
}
