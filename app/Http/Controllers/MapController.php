<?php

namespace App\Http\Controllers;

use App\Http\Resources\MapResource;
use App\Map;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Show map list
     *
     * @return mixed
     */
    public function index()
    {
        $maps = Map::on('mysql_slave')->with('translations')->get();
        return MapResource::collection($maps);
    }

    /**
     * Show specified map by id or name
     *
     * @param $id
     * @return MapResource
     */
    public function show($id)
    {
        $map = Map::on('mysql_slave')->with('translations')->where('id', $id)->orWhere('name', $id)->firstOrFail();
        return new MapResource($map);
    }
}
