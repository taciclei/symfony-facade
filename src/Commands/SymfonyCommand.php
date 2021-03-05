<?php namespace Phpjit\SymfonysFacade\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Phpjit\SymfonysFacade\Facades\Commands\SymfonyCommandsFacade;

class SymfonyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sf:cmd {cmd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute Symfony command';

    /**
     * Create a new command instance.
     *
     * @param SymfonyCommandsFacade $symfonyCommandsFacade
     */
    public function __construct(SymfonyCommandsFacade $symfonyCommandsFacade)
    {
        parent::__construct();

        $this->scf = $symfonyCommandsFacade;
    }

    /**
     *
     */
    public function handle()
    {
        $response = $this->scf->runCommand($this->argument('cmd'));

        $this->info('');
        $this->info('Symfony responds: ');
        $this->info('');
        $this->info($response);
    }
}
