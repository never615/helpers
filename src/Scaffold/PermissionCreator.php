<?php
/**
 * Copyright (c) 2018. Mallto.Co.Ltd.<mall-to.com> All rights reserved.
 */

namespace Encore\Admin\Helpers\Scaffold;

use Mallto\Admin\Data\Permission;

class PermissionCreator
{
    /**
     *
     *
     * @param $inputs
     */
    public function create($inputs)
    {

        if ($inputs && isset($inputs["parent_id"])) {
            $parent = Permission::find($inputs["parent_id"]);
            $path = null;
            if ($parent) {
                if (!empty($parent->path)) {
                    $path = $parent->path.$parent->id.".";
                } else {
                    $path = ".".$parent->id.".";
                }
            }

            Permission::create([
                "parent_id" => $inputs["parent_id"],
                "slug"      => $inputs["slug"],
                "name"      => $inputs["name"],
                "path"      => $path,
            ]);
        }
    }

}
