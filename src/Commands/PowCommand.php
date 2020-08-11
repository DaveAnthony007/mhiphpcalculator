<?php

namespace Jakmall\Recruitment\Calculator\Commands;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;
use Jakmall\Recruitment\Calculator\CommandC;
use Illuminate\Console\Command;

class PowCommand extends Command implements OperationInterface
{
    /**
     * @var string
     */
    protected $signature = "pow {base : The base number} {exp : The exponent number}";

    /**
     * @var string
     */
    public $history;
    protected $description;

    public function __construct()
    {
        parent::__construct();
        $commandVerb = $this->getCommandVerb();

        $this->description = sprintf('Exponent the given number', ucfirst($commandVerb));
    }

    protected function getCommandVerb(): string
    {
        return 'pow';
    }

    protected function getCommandPassiveVerb(): string
    {
        return 'powed';
    }

    public function handle(): void
    {
        $base = $this->argument('base');
        $exp = $this->argument('exp');
        $description = $this->generateCalculationDescription($base, $exp);
        $result = $this->calculate($base, $exp);

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
        $base = $input[0];
        $exp = $input[1];
        $description = $this->generateCalculationDescription($base, $exp);
        $result = $this->calculate($base, $exp);
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
        $array = array("command" => $obj->command, "operation" => "pow", "result" => $obj->result);

        $json = json_encode($array);

        return $json;
    }

    protected function generateCalculationDescription($base, $exp): string
    {
        $operator = $this->getOperator();
        $glue = sprintf(' %s ', $operator);

        return implode($glue, array($base, $exp));
    }

    protected function getOperator(): string
    {
        return '^';
    }

    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($base, $exp)
    {
        return pow($base, $exp);
    }
}
