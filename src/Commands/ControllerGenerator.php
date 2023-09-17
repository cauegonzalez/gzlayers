<?php

namespace CaueGonzalez\GzLayers\Commands;

use CaueGonzalez\GzLayers\Core\Generator;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ControllerGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gzlayers:makecontroller
    {name=name : Class (singular) for example User}
    {--interactive=false : Interactive mode}
    {--all=false : Interactive mode}
    {--overwrite=true : If file exists, determine if overwrite}
    {--businesslayer=bo : Determines which nomenclature to use for business layer | Default is BO and the other accepted is Service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create controller with a multilayer structure';

    /**
     *
     * Generator support instance
     *
     * @var CaueGonzalez\GzLayers\Core\Generator
     */
    protected $generator;


    /**
     * The String support instance
     *
     * @var \Illuminate\Support\Str
     */
    protected $str;

    /**
     * Schema support instance
     *
     * @var \Illuminate\Support\Facades\Schema $schema
     */
    protected $schema;

    /**
     * Create a new command instance.
     *
     * @param Generator $generator
     * @param Str $str
     * @param Schema $schema
     */
    public function __construct(Generator $generator, Str $str, Schema $schema)
    {
        parent::__construct();
        $this->generator = $generator;
        $this->str       = $str;
        $this->schema    = $schema;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        // Checking interactive mode
        if ($this->option('interactive') == "") {
            $this->interactive();
            return 0;
        }

        // Checking all mode
        if ($this->option('all') == "") {
            $this->all();
            return 0;
        }

        // If here, no interactive || all selected
        $name = ucwords($this->argument('name'));
        $overwrite = ($this->option('overwrite') == 'false' ? false : true);
        $businessLayerNomenclature = $this->option('businesslayer') ?? 'bo';

        $this->generate($name, $overwrite, $businessLayerNomenclature);
        return 0;
    }

    /**
     * Handle all-db generation
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function all()
    {
        try {
            $tables =  \DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $ignoreTables = ['migrations', 'failed_jobs', 'password_resets'];
            foreach ($tables as $table) {
                if (in_array($table, $ignoreTables)) {
                    continue;
                }
                $table = ucwords(str_replace('_', ' ', $table));
                $table = str_replace(' ', '', $table);
                $name = ucwords($this->str->singular($table));
                $overwrite = ($this->option('overwrite') == 'false' ? false : true);
                $businessLayerNomenclature = $this->option('businesslayer') ?? 'bo';

                $this->generate($name, $overwrite, $businessLayerNomenclature);
            }
        } catch (QueryException $exception) {
            $this->error("Error: " . $exception->getMessage());
        }
    }


    /**
     * Generate Controller in interactive mode
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function interactive()
    {
        $this->info("Welcome in Interactive mode");

        $this->comment("This command will guide you through creating your Controller");
        $name = $this->ask('What is name of your Model?');
        $name = ucwords($name);
        $confirmOverwrite = $this->ask("If the file {$name}Controller already exists, do you want it to be overwritten? [Y,n]") ?? 'y';

        $overwrite = true;
        if (strtolower($confirmOverwrite) === 'n') {
            $overwrite = false;
        } elseif (strtolower($confirmOverwrite) !== 'y') {
            $this->error("Aborted!");
            return;
        }

        $businessLayer = $this->ask("Which nomenclature do you want to use for business rules layer? [bo,service]");
        $businessLayerNomenclature = strtolower($businessLayer);
        if (($businessLayerNomenclature !== 'bo') && ($businessLayerNomenclature !== 'service')) {
            $businessLayerNomenclature = 'bo';
        }

        $this->info("Please confim this data");
        $this->line("Name: $name");
        $this->line("Controller: {$name}Controller");

        $confirm = $this->ask("Press y to confirm, type N to restart");
        if ($confirm == "y") {
            $this->generate($name, $overwrite, $businessLayerNomenclature);
            return;
        }
        $this->error("Aborted!");
    }


    /**
     * Handle data generation
     * @param $name string Model Name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function generate($name, $overwrite, $businessLayerNomenclature)
    {
        $this->comment("Generating {$name} Controller");
        if ($businessLayerNomenclature == 'bo') {
            $this->generator->controller($name, $overwrite);
        } else {
            $this->generator->controllerWithService($name, $overwrite);
        }
        $this->info("Generated {$name} Controller!");
    }
}
