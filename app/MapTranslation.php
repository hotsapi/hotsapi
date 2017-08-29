<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MapTranslation
 *
 * @property-read \App\Map $map
 * @mixin \Eloquent
 */
class MapTranslation extends Model
{
    protected $guarded = ['id', 'map_id'];
    protected $hidden = ['id', 'map_id'];
    public $timestamps = false;

    public function map()
    {
        return $this->belongsTo(Map::class);
    }
}
