<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSimone;
use Illuminate\Console\Command;

class ProcessQuestionsCommand extends Command
{
    protected $signature = 'process:questions';
    protected $description = 'Process questions';

    public function handle()
    {
        $this->info('Processing questions...');

        ProcessSimone::dispatch();

        $this->info('Questions processed successfully!');
    }
}
