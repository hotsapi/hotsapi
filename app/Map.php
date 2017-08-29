<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Map
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MapTranslation[] $translations
 * @mixin \Eloquent
 */
class Map extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['id'];
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(MapTranslation::class);
    }
}
