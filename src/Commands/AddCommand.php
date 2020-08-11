<?php

namespace Jakmall\Recruitment\Calculator\Commands;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;
use Jakmall\Recruitment\Calculator\CommandC;
use Illuminate\Console\Command;

class AddCommand extends Command implements OperationInterface
{
    /**
     * @var string
     */
    protected $signature = "add {numbers* : The numbers to be added}";

    /**
     * @var string
     */
    public $history;
    protected $description;

    protected function getCommandVerb(): string
    {
        return 'add';
    }

    protected function getCommandPassiveVerb(): string
    {
        return 'added';
    }

    public function __construct()
    {
        parent::__construct();
        $commandVerb = $this->getCommandVerb();

        $this->description = sprintf('%s all given Numbers', ucfirst($commandVerb));
    }


    public function handle(): void
    {
        $numbers = $this->getInput();
        $description = $this->generateCalculationDescription($numbers);
        $result = $this->calculateAll($numbers);
        $output = sprintf('%s = %s', $description, $result);
        $obj = new CommandC;
        $obj->command = $this->getCommandVerb();
        $obj->description = $description;
        $obj->result = $result;
        $obj->output = $output;
        
        $this->comment($output);
        
        $this->history = new CommandHistoryServiceProvider();
        

        if (!empty($this->history)) {
            $this->history->log($obj);
        }
    }

    public function handleOperation(array $inputs = null) {
        $numbers = $inputs;
        $description = $this->generateCalculationDescription($numbers);
        $result = $this->calculateAll($numbers);
        $output = sprintf('%s = %s', $description, $result);
        $obj = new CommandC;
        $obj->command = $this->getCommandVerb();
        $obj->description = $description;
        $obj->result = $result;
        $obj->output = $output;
        
        $this->comment($output);
        
        $this->history = new CommandHistoryServiceProvider();
        

        if (!empty($this->history)) {
            $this->history->log($obj);
        }
        $array = array("command" => $obj->command, "operation" => "add", "result" => $obj->result);

        $json = json_encode($array);

        return $json;
    }

    protected function getInput(): array
    {
        return $this->argument('numbers');
    }

    protected function generateCalculationDescription(array $numbers): string
    {
        $operator = $this->getOperator();
        $glue = sprintf(' %s ', $operator);

        return implode($glue, $numbers);
    }

    protected function getOperator(): string
    {
        return '+';
    }

    /**
     * @param array $numbers
     *
     * @return float|int
     */
    protected function calculateAll(array $numbers)
    {
        $number = array_pop($numbers);

        if (count($numbers) <= 0) {
            return $number;
        }

        return $this->calculate($this->calculateAll($numbers), $number);
    }

    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($number1, $number2)
    {
        return $number1 + $number2;
    }
}
