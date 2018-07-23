<?php

namespace Encore\Admin\Helpers\Scaffold;

class ControllerCreator
{
//    /**
//     * Controller full name.
//     *
//     * @var string
//     */
//    protected $name;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    private $controllerNamespace;
    private $controllerPath;
    private $config;

    /**
     * ControllerCreator constructor.
     *
     * @param      $config
     * @param null $files
     */
    public function __construct($config, $files = null)
    {
        $this->files = $files ?: app('files');
        $this->controllerNamespace = $config->controller_namespace;
        $this->controllerPath = $config->controller_path;

        $this->config = $config;
    }

    /**
     * Create a controller.
     *
     * @param string $tableName
     *
     * @throws \Exception
     *
     * @return string
     */
    public function create($tableName)
    {
        $controllerClassName = studly_case(camel_case(str_singular($tableName)))."Controller";
        $modelClassName = studly_case(camel_case(str_singular($tableName)));
        $path = $this->getpath($controllerClassName);

        if ($this->files->exists($path)) {
            throw new \Exception("Controller [$controllerClassName] already exists!");
        }

        $stub = $this->files->get($this->getStub());

        $this->files->put($path, $this->replace($stub, $controllerClassName, $modelClassName, $tableName));

        return $path;
    }

    /**
     * @param string $stub
     * @param string $controllerNameClass
     * @param string $modelNameClass
     *
     * @param        $tableName
     * @return string
     */
    protected function replace($stub, $controllerNameClass, $modelNameClass, $tableName)
    {
        return str_replace(
            ['DummyModelNamespace', 'DummyModel', 'DummyClass', 'DummyNamespace', 'DummyName'],
            [
                $this->config->model_namespace.'\\'.class_basename($modelNameClass),
                class_basename($modelNameClass),
                $controllerNameClass,
                $this->controllerNamespace,
                $tableName,
            ],
            $stub
        );
    }


    /**
     * Get file path from giving controller name.
     *
     * @param $modelClassName
     *
     * @return string
     */
    public function getPath($modelClassName)
    {
        return base_path($this->controllerPath."/".$modelClassName.'.php');
    }

    /**
     * Get stub file path.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/stubs/controller2.stub';
    }
}
