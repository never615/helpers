<?php
/**
 * Copyright (c) 2018. Mallto.Co.Ltd.<mall-to.com> All rights reserved.
 */

namespace Encore\Admin\Helpers\Scaffold;

use Mallto\Admin\Seeder\SeederMaker;

class PermissionCreator
{

    use SeederMaker;

    private $config;
    private $namespace;
    private $className;

    /**
     *
     *
     * @param $inputs
     * @param $tableName
     * @param $config
     * @return string
     * @throws \Exception
     */
    public function create($inputs, $tableName, $config)
    {

        $this->config = $config;
        $this->namespace = $config->base_namespace."\\Seeder\\Permission";
        $className = studly_case(camel_case(str_singular($tableName)))."PermissionSeeder";
        $this->className = $className;
        //1.生成权限seeder
        $files = app('files');
        $permissionPath = $this->getPermissionPath();

        if ($files->exists($permissionPath)) {
            throw new \Exception("Permission [$className] already exists!");
        }

        $stub = $files->get($this->getStub());


        $files->put($permissionPath,
            $this->replace($stub, $tableName, $inputs));

        //2.配置调用seeder的代码
        //DummySeeder
//        $tablesSeederPath = $this->getTableSeederPath();
//        $tablesSeederStub = $files->get($tablesSeederPath);
//        $files->put($this->getTableSeederPath(),
//            $this->replace2($tablesSeederStub));


        //3. 配置permissinTablesSeeder文件
        $permissionTablesSeederPath = $this->getPermissionTableSeederPath();
        $permissionTablesSeederStub = $files->get($permissionTablesSeederPath);
        $files->put($permissionTablesSeederPath,
            $this->replace3($permissionTablesSeederStub));

        //4. 直接使用代码生成菜单
        $parentId = $this->createPermissions($inputs["name"], $tableName);

        return $permissionPath;

    }

    /**
     * @param $stub
     * @param $tableName
     * @param $inputs
     * @return mixed
     */
    protected function replace($stub, $tableName, $inputs)
    {
        return str_replace(
            [
                'DummyNamespace',
                'DummyClass',
                'DummyTitle',
                'DummyUri',
            ],
            [
                $this->namespace,
                $this->className,
                $inputs["name"],
                $tableName,
            ],
            $stub
        );
    }

    /**
     * 替换tablesSeeder文件
     *
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
     * 替换permissinTablesSeeder
     *
     * @param $stub
     * @return mixed
     */
    protected function replace3($stub)
    {
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


//    /**
//     * Get file path from
//     *
//     *
//     * @return string
//     */
//    public function getTableSeederPath()
//    {
//        return base_path($this->config->base_path."/src/Seeder/TablesSeeder.php");
//    }


    /**
     * Get file path from
     *
     *
     * @return string
     */
    public function getPermissionTableSeederPath()
    {
        return base_path($this->config->base_path."/src/Seeder/PermissionTablesSeeder.php");
    }


    /**
     * Get file path from
     *
     * @param $className
     *
     * @return string
     */
    public function getPermissionPath()
    {
        return base_path($this->config->base_path."/src/Seeder/Permission/".$this->className.'.php');
    }

    /**
     * Get stub file path.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/stubs/permissionsSeeder.stub';
    }

}
