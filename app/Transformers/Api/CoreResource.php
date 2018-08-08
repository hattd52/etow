<?php

namespace App\Transformers\Api;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Support\HelperData;

class CoreResource extends Resource
{
    /**
     * @var array
     */
    private $extraData = [];

    /**
     * Add extra data to Resource
     */
    public function addData($key, $value)
    {
        if (isset($key) && isset($value)) $this->extraData[$key] = $value;

        return $this;
    }

    /**
     * To Array with extra data merged
     */
    public function resourceFormat()
    {
        $arr = $this->jsonSerialize();

        return array_merge($arr, $this->extraData);
    }

    /**
     * @inheritdoc
     * Create new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        $collection = new AnonymousResourceCollection($resource, get_called_class());
        if ($meta = HelperData::getMeta($resource)) {
            $collection->additional($meta);
        }

        return $collection;
    }
}
