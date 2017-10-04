<?php

namespace App\Http\Controllers;

use App\Http\Resources\TalentResource;
use App\Talent;
use Illuminate\Http\Request;

class TalentController extends Controller
{
    /**
     * Show talent list
     *
     * @return mixed
     */
    public function index()
    {
        $talents = Talent::on('mysql_slave')->with('heroes')->get();
        return TalentResource::collection($talents);
    }

    /**
     * Show specified talent by id or name
     *
     * @param $id
     * @return TalentResource
     */
    public function show($id)
    {
        $talent = Talent::on('mysql_slave')->with('heroes')->where('id', $id)->orWhere('name', $id)->firstOrFail();
        return new TalentResource($talent);
    }

}
