<?php

namespace Jakmall\Recruitment\Calculator\Http\Controller;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;

use Illuminate\Http\Request;

class HistoryController
{
    protected $commandHistory;
    public function index()
    {
        // todo: modify codes to get history

        // dd('create history logic here');
        $this->commandHistory = new CommandHistoryServiceProvider();
        
        return $this->commandHistory->findAll();

    }

    public function show($id)
    {
        // dd('create show history by id here');
        $this->commandHistory = new CommandHistoryServiceProvider();
        return $this->commandHistory->findAll($id);
    }

    public function remove($id)
    {
        // todo: modify codes to remove history
        // dd('create remove history logic here');
        $this->commandHistory = new CommandHistoryServiceProvider();
        
        $this->commandHistory->clearAll($id);
        return "success";

    }
}
