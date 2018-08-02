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
     * @param null   $fields
     * @return string
     * @throws \Exception
     */
    public function create($tableName, $fields = [])
    {

        $fieldNames = array_pluck($fields, "name");


        $controllerClassName = studly_case(camel_case(str_singular($tableName)))."Controller";
        $modelClassName = studly_case(camel_case(str_singular($tableName)));
        $path = $this->getpath($controllerClassName);

        if ($this->files->exists($path)) {
            throw new \Exception("Controller [$controllerClassName] already exists!");
        }

        $stub = $this->files->get($this->getStub());

        $this->files->put($path, $this->replace($stub, $controllerClassName, $modelClassName, $tableName,$fieldNames));

        return $path;
    }



    /**
     * @param string $stub
     * @param string $controllerNameClass
     * @param string $modelNameClass
     *
     * @param        $tableName
     * @param        $fieldNames
     * @return string
     */
    protected function replace($stub, $controllerNameClass, $modelNameClass, $tableName,$fieldNames)
    {
        return str_replace(
            [
                'DummyModelNamespace',
                'DummyModel',
                'DummyClass',
                'DummyNamespace',
                'DummyName',
                'DummyGridConfig',
                'DummyFormConfig',
            ],
            [
                $this->config->model_namespace.'\\'.class_basename($modelNameClass),
                class_basename($modelNameClass),
                $controllerNameClass,
                $this->controllerNamespace,
                $tableName,
                $this->gridGenerator($fieldNames),
                $this->formGenerator($fieldNames),
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



    /**
     * 生成默认的grid代码
     *
     * @param $fieldNames
     * @return string
     */
    private function gridGenerator($fieldNames)
    {
        $temp = "";
        foreach ($fieldNames as $fieldName) {
            $temp.='$grid->'.$fieldName.'();'.PHP_EOL;
        }
        return $temp;

    }

    /**
     * 生成默认的form代码
     *
     * @param $fieldNames
     * @return string
     */
    private function formGenerator($fieldNames)
    {
        $temp = "";
        foreach ($fieldNames as $fieldName) {
            $temp.='$form->text("'.$fieldName.'");'.PHP_EOL;
        }
        return $temp;
    }
}
