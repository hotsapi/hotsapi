<?php

namespace App\Http\Controllers;

use App\Hero;
use App\Http\Resources\HeroResource;

class HeroController extends Controller
{
    /**
     * Show hero list
     *
     * @return mixed
     */
    public function index()
    {
        $heroes = Hero::on('mysql_slave')->with('translations', 'talents', 'abilities')->get();
        return HeroResource::collection($heroes);
    }

    /**
     * Show specified hero by id, name, or short name
     *
     * @param $id
     * @return HeroResource
     */
    public function show($id)
    {
        $hero = Hero::on('mysql_slave')->with('translations', 'talents', 'abilities')->where('id', $id)->orWhere('name', $id)->orWhere('short_name', $id)->firstOrFail();
        return new HeroResource($hero);
    }

    /**
     * Show hero ability by hotkey
     *
     * @param $hero
     * @param $ability
     * @return mixed
     */
    public function showAbility($hero, $ability)
    {
        // currently pulls all abilities and then selects the correct one in memory
        // we probably can optimize this
        $result = Hero::on('mysql_slave')->with('abilities')->where('id', $hero)->orWhere('name', $hero)->orWhere('short_name', $hero)->firstOrFail()->abilities->where('name', $ability)->first();
        return $result;
    }
}
