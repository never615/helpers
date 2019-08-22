<?php

namespace Encore\Admin\Helpers\Controllers;


use Encore\Admin\Facades\Admin;
use Encore\Admin\Helpers\Scaffold\ControllerCreator;
use Encore\Admin\Helpers\Scaffold\MenuCreator;
use Encore\Admin\Helpers\Scaffold\MigrationCreator;
use Encore\Admin\Helpers\Scaffold\ModelCreator;
use Encore\Admin\Helpers\Scaffold\PermissionCreator;
use Encore\Admin\Helpers\Scaffold\RouteCreator;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\MessageBag;
use Mallto\Admin\Data\Menu;
use Mallto\Admin\Data\Permission;

class ScaffoldController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('Scaffold');

            $dbTypes = [
                'string',
                'integer',
                'text',
                'float',
                'double',
                'decimal',
                'boolean',
                'date',
                'time',
                'dateTime',
                'timestamp',
                'char',
                'mediumText',
                'longText',
                'tinyInteger',
                'smallInteger',
                'mediumInteger',
                'bigInteger',
                'unsignedTinyInteger',
                'unsignedSmallInteger',
                'unsignedMediumInteger',
                'unsignedInteger',
                'unsignedBigInteger',
                'enum',
                'json',
                'jsonb',
                'dateTimeTz',
                'timeTz',
                'timestampTz',
                'nullableTimestamps',
                'binary',
                'ipAddress',
                'macAddress',
            ];

            $action = URL::current();

            //permission
            $permissions = Permission::selectOptions();
            //menu
            $menus = Menu::selectOptions();
            //config
            $configs = array_keys(config("helps"));

            $content->row(view('laravel-admin-helpers::scaffold',
                compact('dbTypes', 'action', 'permissions', 'menus', 'configs', 'templates')));
        });
    }

    public function store(Request $request)
    {
//        \Log::info($request->all());


        $config = (object) config("helps.".$request->config_id);
//        \Log::info(\GuzzleHttp\json_encode($config));

        $tableName = $request->table_name;
        $tableName = trim($tableName);

        $parentPermissionId = $request->permission_parent_id;

        $parentPermissionSlug = null;
        $parentPermission = Permission::find($parentPermissionId);
        if ($parentPermission) {
            $parentPermissionSlug = $parentPermission->slug;
        }

        $permissionInputs = [
            "name"        => $request->menu_title,
            "parent_slug" => $parentPermissionSlug,
        ];

        $menuInputs = [
            "parent_id" => $request->menu_parent_id,
            "title"     => $request->menu_title,
            "icon"      => $request->menu_icon,
        ];

        $title = $menuInputs['title'] ?: $tableName;


        $paths = [];
        $message = '';

        try {

            // 1. Create model.
            if (in_array('model', $request->get('create', []))) {
                $modelCreator = new ModelCreator(
                    $tableName, $config);

                $paths['model'] = $modelCreator->create(
                    $request->get('primary_key'),
                    $request->get('timestamps') == 'on',
                    $request->get('soft_deletes') == 'on'
                );
            }


            // 2. Create controller.
            if (in_array('controller', $request->get('create'))) {
                $paths['controller'] = (
                new ControllerCreator(
                    $config
                ))->create($tableName, $request->get('fields'), $title);
            }

            // 3. Create migration.
            if (in_array('migration', $request->get('create'))) {
                $migrationName = 'create_'.$tableName.'_table';

                $paths['migration'] = (new MigrationCreator(app('files')))
                    ->buildBluePrint(
                        $request->get('fields'),
                        $request->get('primary_key', 'id'),
                        $request->get('timestamps') == 'on',
                        $request->get('soft_deletes') == 'on'
                    )->create2(
                        $migrationName,
                        $config->base_path."/migrations",
                        $tableName
                    );
            }


            //4. create route
            if (in_array('route', $request->get('create'))) {
                (new RouteCreator($tableName, $config->base_path."/routes/web.php"))
                    ->create();
            }


            //5. Run migrate.
            if (in_array('migrate', $request->get('create'))) {
                Artisan::call('migrate');
                $message = Artisan::output();
            }


            //6. create permission
            if (in_array('permission', $request->get('create'))) {
                (new PermissionCreator())->create($permissionInputs, $tableName, $config);
            }

            //7. create menu
            if (in_array('menu', $request->get('create'))) {
                (new MenuCreator())->create($menuInputs, $tableName, $config);
            }


        } catch (\Exception $exception) {

            \Log::warning($exception);

            // Delete generated files if exception thrown.
            app('files')->delete($paths);

            return $this->backWithException($exception);
        }

        return $this->backWithSuccess($paths, $message);
    }

    protected function backWithException(\Exception $exception)
    {
        $error = new MessageBag([
            'title'   => 'Error',
            'message' => $exception->getMessage(),
        ]);

        return back()->withInput()->with(compact('error'));
    }

    protected function backWithSuccess($paths, $message)
    {
        $messages = [];

        foreach ($paths as $name => $path) {
            $messages[] = ucfirst($name).": $path";
        }

        $messages[] = "<br />$message";

        $success = new MessageBag([
            'title'   => 'Success',
            'message' => implode('<br />', $messages),
        ]);

        return back()->with(compact('success'));
    }
}
