<?php

namespace CaueGonzalez\GzLayers\Core;

use Carbon\Carbon;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Generator
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The String support instance
     *
     * @var \Illuminate\Support\Str
     */
    protected $str;

    /**
     * The Stub support instance
     *
     * @var \CaueGonzalez\GzLayers\Core\Stub;
     */
    protected $stub;

    public function __construct(Filesystem $files, Str $str, Stub $stub)
    {
        $this->files = $files;
        $this->str   = $str;
        $this->stub  = $stub;
    }

    /**
     * Generate BO from BO.stub
     *
     * @param $name
     * @param $overwrite
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function bo($name, $overwrite = true)
    {
        $content = $this->stub->parseStub('BO', $name);

        if (!$this->files->exists("app/BO/")) {
            $this->files->makeDirectory("app/BO/");
        }
        $path = "app/BO/{$name}BO.php";
        if (!$overwrite && $this->files->exists($path)) {
            $uniqueId = Carbon::now()->format('YmiHis');
            $path = "app/BO/{$name}BO_{$uniqueId}.php";
        }
        return $this->files->put($path, $content);
    }

    /**
     * Create controller from controller.stub
     *
     * @param $name string name of model class
     * @param $overwrite
     * @return bool|int
     */
    public function controller($name, $overwrite = true)
    {
        $content = $this->stub->parseStub('Controller', $name);

        $path = "app/Http/Controllers/{$name}Controller.php";
        if (!$overwrite && $this->files->exists($path)) {
            $uniqueId = Carbon::now()->format('YmiHis');
            $path = "app/Http/Controllers/{$name}Controller_{$uniqueId}.php";
        }
        return $this->files->put($path, $content);
    }

    /**
     * Generate scope trait from stubs
     *
     * @return bool|int
     */
    public function scopeTrait()
    {
        $content = $this->stub->parseStub('ScopeTrait', 'Scope', [
            'activeStatus' => 'ACTIVE',
        ]);

        if (!$this->files->exists("app/Models/Traits/")) {
            $this->files->makeDirectory("app/Models/Traits/");
            return $this->files->put("app/Models/Traits/Scope.php", $content);
        }
        return false;
    }

    /**
     * Generate model class from stubs
     *
     * @param $name string name of model class
     * @param $table string name of DB table
     * @param $timestamps boolean set timestamps true | false
     * @param $overwrite boolean
     * @return bool|int
     */
    public function model($name, $table, $timestamps, $overwrite)
    {
        $tableDeclaration = $table !== "default" ? "\n\n".'    protected $table = "'.$table.'";' : "";

        $timeDeclaration = "";
        if ($timestamps === false) {
            $timeDeclaration = "\n\n".'    public $timestamps = false;';
        }

        $content = $this->stub->parseStub('Model', $name, [
            'tableDeclaration' => $tableDeclaration,
            'timestamps' => $timeDeclaration
        ]);

        if (!$this->files->exists("app/Models/")) {
            $this->files->makeDirectory("app/Models/");
        }

        $path = "app/Models/{$name}.php";
        if (!$overwrite && $this->files->exists($path)) {
            $uniqueId = Carbon::now()->format('YmiHis');
            $path = "app/Models/{$name}_{$uniqueId}.php";
        }
        return $this->files->put($path, $content);
    }

    /**
     * Generate Repository from Repository.stub
     *
     * @param $name
     * @param $overwrite
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function repository($name, $overwrite)
    {
        $content = $this->stub->parseStub('Repository', $name);

        if (!$this->files->exists("app/Repositories/")) {
            $this->files->makeDirectory("app/Repositories/");
        }
        $path = "app/Repositories/{$name}Repository.php";
        if (!$overwrite && $this->files->exists($path)) {
            $uniqueId = Carbon::now()->format('YmiHis');
            $path = "app/Repositories/{$name}Repository_{$uniqueId}.php";
        }
        return $this->files->put($path, $content);
    }

    /**
     * Generate Request from request.stub
     *
     * @param $name
     * @param $overwrite
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function request($name, $overwrite)
    {
        $content = $this->stub->parseStub('Request', $name);

        if (!$this->files->exists("app/Http/Requests/")) {
            $this->files->makeDirectory("app/Http/Requests/");
        }
        $path = "app/Http/Requests/{$name}Request.php";
        if (!$overwrite && $this->files->exists($path)) {
            $uniqueId = Carbon::now()->format('YmiHis');
            $path = "app/Http/Requests/{$name}Request_{$uniqueId}.php";
        }
        return $this->files->put($path, $content);
    }

    /**
     * Generate customRulesRequest class from stubs
     *
     * @return bool|int
     */
    public function customRulesRequest()
    {
        $content = $this->stub->parseStub('CustomRulesRequest', 'CustomRulesRequest');

        if (!$this->files->exists("app/Http/Requests/CustomRulesRequest.php")) {
            return $this->files->put("app/Http/Requests/CustomRulesRequest.php", $content);
        }
        return false;
    }

    /**
     * Generate model class from stubs
     *
     * @return bool|int
     */
    public function prepareTrait()
    {
        $content = $this->stub->parseStub('PrepareTrait', 'Prepare');

        if (!$this->files->exists("app/Resources/Traits/")) {
            $this->files->makeDirectory("app/Resources/Traits/", 0755, true);
            return $this->files->put("app/Resources/Traits/PrepareTrait.php", $content);
        }
        return false;
    }

    /**
     * Generate model class from stubs
     *
     * @return bool|int
     */
    public function trait($name, $overwrite = true)
    {
        $content = $this->stub->parseStub('Trait', $name);

        if (!$this->files->exists("app/BO/Traits/")) {
            $this->files->makeDirectory("app/BO/Traits/");
        }
        $path = "app/BO/Traits/{$name}Trait.php";
        if (!$overwrite && $this->files->exists($path)) {
            $uniqueId = Carbon::now()->format('YmiHis');
            $path = "app/BO/Traits/{$name}Trait_{$uniqueId}.php";
        }
        return $this->files->put($path, $content);
    }

    /**
     * Generate Resource from Resource.stub
     *
     * @param $name
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function resource($name)
    {
        $content = $this->stub->parseStub('Resource', $name);

        if (!$this->files->exists("app/Http/Resources/")) {
            $this->files->makeDirectory("app/Http/Resources/");
        }
        return $this->files->put("app/Http/Resources/{$name}Resource.php", $content);
    }

    /**
     * Generate factory from Factory.stub
     *
     * @param $name
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function factory($name)
    {
        $content = $this->stub->parseStub('Factory', $name);

        if (!$this->files->exists("database/factories/")) {
            $this->files->makeDirectory("database/factories/");
        }
        return $this->files->put("database/factories/{$name}Factory.php", $content);
    }

    /**
     * Generate unit test
     *
     * @param $name
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function test($name)
    {
        $content = $this->stub->parseStub('Test', $name);

        if (!$this->files->exists("tests/Feature/")) {
            $this->files->makeDirectory("tests/Feature/");
        }
        return $this->files->append("tests/Feature/{$name}Test.php", $content);
    }
}
