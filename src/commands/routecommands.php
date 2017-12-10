<?php

/**
 * @Author: ahmadnorin
 * @Date:   2017-11-27 23:39:21
 * @Last Modified by:   jdi-juma
 * @Last Modified time: 2017-12-09 23:29:37
 */

namespace Bantenprov\BantenprovSso\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use File;

class RouteCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bantenprov-sso:add-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add sso rotes  to web route';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $drip;

    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    protected function content()
    {

        $replace_middleware = File::get(__DIR__.'/../stubs/route.stub');
        return $replace_middleware;
    }
    

    public function handle()
    {               
        $this->info('Route add success'); 
        File::append(base_path('routes/web.php'),"\n".$this->content());        
    }
}
