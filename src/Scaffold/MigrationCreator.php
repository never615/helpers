<?php

namespace Encore\Admin\Helpers\Scaffold;

use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;

class MigrationCreator extends BaseMigrationCreator
{
    /**
     * @var string
     */
    protected $bluePrint = '';


    /**
     * Create a new migration file.
     *
     * @param string    $name
     * @param string    $path
     * @param null      $table
     * @param bool|true $create
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function create($name, $path, $table = null, $create = true)
    {
        $this->ensureMigrationDoesntAlreadyExist($name);

        $path = base_path($path);

        $path = $this->getPath($name, $path);

        $stub = $this->files->get(__DIR__.'/stubs/create.stub');

        $this->files->put($path, $this->populateStub($name, $stub, $table));

        $this->firePostCreateHooks($table);

        return $path;
    }

    /**
     * @param      $name
     * @param      $path
     * @param null $table
     * @param bool $withSubject
     * @param bool $create
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function create2($name, $path, $table = null, $withSubject = true, $create = true)
    {
        $this->ensureMigrationDoesntAlreadyExist($name);

        $path = base_path($path);

        $path = $this->getPath($name, $path);

        if ($withSubject) {
            $stub = $this->files->get(__DIR__.'/stubs/create_with_subject.stub');
        } else {
            $stub = $this->files->get(__DIR__.'/stubs/create.stub');
        }

        $this->files->put($path, $this->populateStub($name, $stub, $table));

        $this->firePostCreateHooks($table);

        return $path;
    }

    /**
     * Populate stub.
     *
     * @param string $name
     * @param string $stub
     * @param string $table
     *
     * @return mixed
     */
    protected function populateStub($name, $stub, $table)
    {
        return str_replace(
            ['DummyClass', 'DummyTable', 'DummyStructure'],
            [$this->getClassName($name), $table, $this->bluePrint],
            $stub
        );
    }

    /**
     * Build the table blueprint.
     *
     * @param array      $fields
     * @param string     $keyName
     * @param bool|true  $useTimestamps
     * @param bool|false $softDeletes
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function buildBluePrint($fields = [], $keyName = 'id', $useTimestamps = true, $softDeletes = false)
    {
        $fields = array_filter($fields, function ($field) {
            return isset($field['name']) && !empty($field['name']);
        });

        if (empty($fields)) {
            throw new \Exception('Table fields can\'t be empty');
        }

        $rows[] = "\$table->increments('$keyName');\n";

        foreach ($fields as $field) {
            $column = "\$table->{$field['type']}('{$field['name']}')";

            if ($field['key']) {
                $column .= "->{$field['key']}()";
            }

            if (isset($field['default']) && $field['default']) {
                $column .= "->default('{$field['default']}')";
            }

            if (isset($field['comment']) && $field['comment']) {
                $column .= "->comment('{$field['comment']}')";
            }

            if (array_get($field, 'nullable') == 'on') {
                $column .= '->nullable()';
            }

            $rows[] = $column.";\n";
        }

        if ($useTimestamps) {
            $rows[] = "\$table->timestamps();\n";
        }

        if ($softDeletes) {
            $rows[] = "\$table->softDeletes();\n";
        }

        $this->bluePrint = trim(implode(str_repeat(' ', 12), $rows), "\n");

        return $this;
    }
}
