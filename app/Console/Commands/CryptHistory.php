<?php

namespace App\Console\Commands;

use App\Service\Histories;
use Illuminate\Console\Command;

class CryptHistory extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crypt:history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Store the current price of the crypt";

    private $historiesService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Histories $historiesService)
    {
        parent::__construct();
        $this->historiesService = $historiesService;
    }

    /**
     * Execute the console command.
     *
     * @param  \App\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        $this->info('Storing current price...');
        $this->historiesService->store();
        $this->info('Success!');
    }
}
