<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Map
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MapTranslation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Map whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Map whereName($value)
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
