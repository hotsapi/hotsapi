<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MapTranslation
 *
 * @property-read \App\Map $map
 * @mixin \Eloquent
 * @property int $id
 * @property int $map_id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MapTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MapTranslation whereMapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MapTranslation whereName($value)
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
