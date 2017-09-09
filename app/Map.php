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
    protected $hidden = ['id', 'translations'];
    protected $appends = ['versions'];
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(MapTranslation::class);
    }

    public function getVersionsAttribute()
    {
        return $this->translations->pluck('name');
    }
}
