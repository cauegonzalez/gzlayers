<?php

namespace CaueGonzalez\GzLayers\Core;

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
     * Generate model class from stubs
     *
     * @param $name string name of model class
     * @param $table string name of DB table
     * @param $timestamps boolean set timestamps true | false
     * @return bool|int
     */
    public function model($name, $table, $timestamps)
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
        return $this->files->put("app/Models/{$name}.php", $content);
    }

    /**
     * Generate model class from stubs
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
    public function trait($name)
    {
        $content = $this->stub->parseStub('Trait', $name);

        if (!$this->files->exists("app/BO/Traits/")) {
            $this->files->makeDirectory("app/BO/Traits/");
        }
        return $this->files->put("app/BO/Traits/{$name}Trait.php", $content);
    }

    /**
     * Create controller from controller.stub
     *
     * @param $name string name of model class
     * @return bool|int
     */
    public function controller($name)
    {
        $content = $this->stub->parseStub('Controller', $name);

        return $this->files->put("app/Http/Controllers/{$name}Controller.php", $content);
    }

    /**
     * Generate Request from request.stub
     *
     * @param $name
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function request($name)
    {
        $content = $this->stub->parseStub('Request', $name);

        if (!$this->files->exists("app/Http/Requests/")) {
            $this->files->makeDirectory("app/Http/Requests/");
        }
        return $this->files->put("app/Http/Requests/{$name}Request.php", $content);
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
     * Generate BO from BO.stub
     *
     * @param $name
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function bo($name)
    {
        $content = $this->stub->parseStub('BO', $name);

        if (!$this->files->exists("app/BO/")) {
            $this->files->makeDirectory("app/BO/");
        }
        return $this->files->put("app/BO/{$name}BO.php", $content);
    }

    /**
     * Generate Repository from Repository.stub
     *
     * @param $name
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function repository($name)
    {
        $content = $this->stub->parseStub('Repository', $name);

        if (!$this->files->exists("app/Repositories/")) {
            $this->files->makeDirectory("app/Repositories/");
        }
        return $this->files->put("app/Repositories/{$name}Repository.php", $content);
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
