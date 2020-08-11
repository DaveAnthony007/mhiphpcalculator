<?php

namespace Jakmall\Recruitment\Calculator\Http\Controller;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;
use Jakmall\Recruitment\Calculator\CommandC;
use Illuminate\Http\Request;
use Jakmall\Recruitment\Calculator\Commands\AddCommand;
use Jakmall\Recruitment\Calculator\Commands\SubtractCommand;
use Jakmall\Recruitment\Calculator\Commands\DevideCommand;
use Jakmall\Recruitment\Calculator\Commands\MultiplyCommand;
use Jakmall\Recruitment\Calculator\Commands\PowCommand;



class CalculatorController
{
    public function calculate(Request $request, $action)
    {
        $obj = null;
        switch($action) {
            case "add":
                $obj = new AddCommand;
            break;
            case "subtract":
                $obj = new SubtractCommand;
            break;
            case "devide":
                $obj = new DevideCommand;
            break;
            case "multiply":
                $obj = new MultiplyCommand;
            break;
            case "pow":
                $obj = new PowCommand;
            break;

        }
        return $obj->handleOperation($request->input('input'));
        
    }
}
