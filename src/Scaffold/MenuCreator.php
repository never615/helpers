<?php
/**
 * Copyright (c) 2018. Mallto.Co.Ltd.<mall-to.com> All rights reserved.
 */

namespace Encore\Admin\Helpers\Scaffold;

use Mallto\Admin\Data\Menu;

class MenuCreator
{
    /**
     *
     *
     * @param $inputs
     */
    public function create($inputs)
    {
        $parent = Menu::find($inputs["parent_id"]);
        $path = null;
        if ($parent) {
            if (!empty($parent->path)) {
                $path = $parent->path.$parent->id.".";
            } else {
                $path = ".".$parent->id.".";
            }
        }

        Menu::create([
            "parent_id" => $inputs["parent_id"],
            "uri"       => $inputs["uri"],
            "title"     => $inputs["title"],
            "icon"      => $inputs["icon"],
            "path"      => $path,
        ]);
    }

}
