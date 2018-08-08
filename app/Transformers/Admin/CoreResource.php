<?php

namespace Modules\Core\Transformers\Admin;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Database\Eloquent\Collection;

class CoreResource extends Resource
{
    /**
     * Attributes format rules
     */
    public function editRules($resource) 
    {
        return [];
    }

    /**
     * Rebuild item
     */
    public function edit($resource)
    {
        $adjusts = $this->editRules($resource);
        $newItem = new $resource($adjusts);

        foreach ($adjusts as $attr => $value) {
            if (!isset($newItem->$attr)) $newItem->$attr = $value;
        }

        return $newItem;
    }

    /**
     * Return formatted resource item
     */
    public function format()
    {
        return $this->edit($this->resource);
    }

    /**
     * Return formatted resource collection
     */
    public function formatCollection()
    {
        $collections = $this->resource;

        if($collections instanceof AbstractPaginator || $collections instanceof Paginator) {
            //TODO: process pagination
        }
        else if ($collections instanceof Collection) {
            $len = $collections->count();
            for($i=0; $i<$len; $i++) {
                $collections[$i] = $this->edit($collections[$i]);
            }
        }

        return $collections;
    }
}
