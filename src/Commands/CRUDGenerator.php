<?php

namespace CaueGonzalez\GzLayers\Commands;

use CaueGonzalez\GzLayers\Core\Generator;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CRUDGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gzlayers:makecrud
    {name=name : Class (singular) for example User}
    {--table=default : Table name (plural) for example users | Default is generated-plural}
    {--timestamps=false : Set default timestamps}
    {--interactive=false : Interactive mode}
    {--all=false : Interactive mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create entire CRUD skeleton with a multilayer structure';

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
        $table = $this->option('table');
        $timestamps = $this->option('timestamps');
        $this->generate($name, $table, $timestamps);
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
                $columns = Schema::getColumnListing($table);
                $name = ucwords($this->str->singular($table));
                in_array('created_at', $columns) ? $timestamps = true : $timestamps = false;
                $this->generate($name, $table, $timestamps);
            }
        }
        catch (QueryException $exception) {
            $this->error("Error: " . $exception->getMessage());
        }
    }


    /**
     * Generate CRUD in interactive mode
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function interactive()
    {
        $this->info("Welcome in Interactive mode");

        $this->comment("This command will guide you through creating your CRUD");
        $name = $this->ask('What is name of your Model?');
        $name = ucwords($name);
        $table = $this->ask("Table name [" . strtolower($this->str->plural($name)) . "]:");
        if ($table == "") {
            $table = $this->str->plural($name);
        }
        $table = strtolower($table);
        $choice = $this->choice('Do your table has timestamps column?', ['No', 'Yes'], 0);
        $choice === "Yes" ? $timestamps = true : $timestamps = false;
        $this->info("Please confim this data");
        $this->line("Name: $name");
        $this->line("Table: $table");
        $this->line("Timestamps: $choice");

        $confirm = $this->ask("Press y to confirm, type N to restart");
        if ($confirm == "y") {
            $this->generate($name, $table, $timestamps);
            return;
        }
        $this->error("Aborted!");
    }


    /**
     * Handle data generation
     * @param $name string Model Name
     * @param $table string Table Name
     * @param $timestamps boolean
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function generate($name, $table, $timestamps)
    {
        $this->comment("Generating {$name} CRUD");
        $this->comment("...");
        $this->generator->bo($name);
        $this->info("Generated {$name} BO!");
        $this->generator->controller($name);
        $this->info("Generated {$name} Controller!");
        $this->generator->model($name, $table, $timestamps);
        $this->info("Generated {$name} Model!");
        $scopeTraitGenerated = $this->generator->scopeTrait();
        if ($scopeTraitGenerated) {
            $this->info("Generated {$name} ScopeTrait");
        }
        $this->generator->repository($name);
        $this->info("Generated {$name} Repository!");
        $this->generator->request($name);
        $this->info("Generated {$name} Request!");
        $customRulesRequestGenerated = $this->generator->customRulesRequest();
        if ($customRulesRequestGenerated) {
            $this->comment("Generating CustomRulesRequest");
            $this->info("Generated CustomRulesRequest");
        }
        $this->generator->resource($name);
        $this->info("Generated {$name} Resource!");
        $this->generator->trait($name);
        $this->info("Generated {$name} Trait!");
        $this->comment("-----");
    }
}
