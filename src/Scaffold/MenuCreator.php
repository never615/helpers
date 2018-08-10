<?php
/**
 * Copyright (c) 2018. Mallto.Co.Ltd.<mall-to.com> All rights reserved.
 */

namespace Encore\Admin\Helpers\Scaffold;

use Mallto\Admin\Data\Menu;

class MenuCreator
{

    private $menuPath;
    private $config;
    private $namespace;
    private $className;


    /**
     *
     *
     * @param $inputs
     * @param $tableName
     * @param $config
     * @return null|string
     * @throws \Exception
     */
    public function create($inputs, $tableName, $config)
    {
        $this->config = $config;
        $this->namespace = $config->base_namespace."\\Seeder\\Menu";
        $className = studly_case(camel_case(str_singular($tableName)))."MenuSeeder";
        $this->className = $className;
        //1.生成菜单seeder
        $files = app('files');
        $menuPath = $this->getMenuPath();

        if ($files->exists($menuPath)) {
            throw new \Exception("Menu [$className] already exists!");
        }

        $stub = $files->get($this->getStub());

        $parent = Menu::find($inputs["parent_id"]);

        $parentUri = "dummy_not_exist";
        if ($parent) {
            $parentUri = $parent->uri;
        }

        $files->put($menuPath,
            $this->replace($stub, $tableName, $inputs, $parentUri));

        //2.配置调用seeder的代码
        //DummySeeder
        $tablesSeederPath = $this->getMenuTableSeederPath();
        $tablesSeederStub = $files->get($tablesSeederPath);
        $files->put($tablesSeederPath,
            $this->replace2($tablesSeederStub));

        //3. 直接使用代码生成菜单
        $path = "";
        if ($parent) {
            if (!empty($parent->path)) {
                $path = $parent->path.$parent->id.".";
            } else {
                $path = ".".$parent->id.".";
            }
        }

        Menu::updateOrCreate([
            "uri" => $tableName.".index",
        ], [
            "parent_id" => $inputs["parent_id"],
            "title"     => $inputs["title"],
            "icon"      => $inputs["icon"],
            "path"      => $path,
        ]);


        return $menuPath;
    }


    /**
     * @param $stub
     * @param $tableName
     * @param $inputs
     * @param $parentUri
     * @return mixed
     */
    protected function replace($stub, $tableName, $inputs, $parentUri)
    {
        return str_replace(
            [
                'DummyNamespace',
                'DummyClass',
                'DummyParentMenuUri',
                'DummyMenuUri',
                'DummyMenuTitle',
                'DummyMenuIcon',
            ],
            [
                $this->namespace,
                $this->className,
                $parentUri,
                $tableName,
                $inputs["title"],
                $inputs["icon"],
            ],
            $stub
        );
    }


    /**
     * @param $stub
     * @return mixed
     */
    protected function replace2($stub)
    {
        //$this->call(WechatTemplateSeeder::class);
        return str_replace(
            [
                '//DummySeeder',
            ],
            [
                '$this->call('."\\".$this->namespace."\\".$this->className."::class".');'.PHP_EOL.'//DummySeeder',
            ],
            $stub
        );
    }


    /**
     * Get file path from
     *
     *
     * @return string
     */
    public function getMenuPath()
    {
        return base_path($this->config->base_path."/src/Seeder/Menu/".$this->className.'.php');
    }


    /**
     * Get file path from
     *
     *
     * @return string
     */
    public function getMenuTableSeederPath()
    {
        return base_path($this->config->base_path."/src/Seeder/MenuTablesSeeder.php");
    }

    /**
     * Get stub file path.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/stubs/menuSeeder.stub';
    }


}
