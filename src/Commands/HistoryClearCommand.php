<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;

class HistoryClearCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = "history:clear";

    /**
     * @var string
     */
    protected $description = "Clear saved history";

    /**
     * @var object
     */
    protected $history;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->history = new CommandHistoryServiceProvider();
        $this->history->clearAll();

        $this->comment('History cleared!');
    }

}
