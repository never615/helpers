<?php
/**
 * Copyright (c) 2018. Mallto.Co.Ltd.<mall-to.com> All rights reserved.
 */

namespace Encore\Admin\Helpers\Scaffold;

class RouteCreator
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $tableName;


    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    private $path;
    private $controllerClassName;


    /**
     * ModelCreator constructor.
     *
     * @param string $tableName
     * @param        $path
     * @param null   $files
     */
    public function __construct($tableName, $path, $files = null)
    {
        $this->tableName = $tableName;
        $this->path = $path;
        $this->files = $files ?: app('files');


        $this->controllerClassName = studly_case(camel_case(str_singular($tableName)))."Controller";
    }

    public function create()
    {
        $route = <<<EOF
Route::resource('$this->tableName', '$this->controllerClassName');
//DummyRoutePlaceholder
EOF;

        $path = base_path($this->path);

        $stub = $this->files->get($path);

        $stub = $this->replace($stub, $route);


        $this->files->put($path, $stub);

        return $path;
    }

    protected function replace($stub, $route)
    {

        return str_replace(
            ['//DummyRoutePlaceholder'],
            [$route],
            $stub
        );
    }


    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param        $route
     * @return string
     */
    protected function replaceClass($stub, $route)
    {
        return str_replace(['DummyRoutePlaceholder'], [$route], $stub);
    }

}
