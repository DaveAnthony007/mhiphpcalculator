<?php

namespace Jakmall\Recruitment\Calculator\Commands;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;
use Jakmall\Recruitment\Calculator\CommandC;

interface OperationInterface
{
    public function handleOperation(array $inputs = null);
}