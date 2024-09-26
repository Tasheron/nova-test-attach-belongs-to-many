<?php

namespace Tasheron\AttachBelongsToMany\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    const FIND_LIMIT = 10;

    private static function getModel(Request $request): Model
    {
        return new $request->resource();
    }

    public function find(Request $request)
    {
        return self::getModel($request)::query()
            ->filter($request->filter, $request->resourceId)
            ->limit(self::FIND_LIMIT)
            ->get();
    }

    public function attach(Request $request, int $resource, int $attachResource)
    {
        $resourceModel = self::getModel($request)::find($resource);

        return $resourceModel->{$request->attachFunction}($attachResource);
    }

    public function detach(Request $request, int $resource, int $attachResource)
    {
        $resourceModel = self::getModel($request)::find($resource);

        $resourceModel->{$request->detachFunction}($attachResource);
    }

    public function changeIndex(Request $request, int $resource, int $attachResource)
    {
        $resourceModel = self::getModel($request)::find($resource);

        $resourceModel->{$request->indexFunction}($attachResource, $request->index);

        return $resourceModel->{$request->btm}()->get()->toArray();
    }

    public function updatePivot(Request $request, int $resource, int $attachResource)
    {
        $resourceModel = self::getModel($request)::find($resource);

        return $resourceModel->{$request->pivotFunction}($attachResource, $request->newValues);
    }
}
