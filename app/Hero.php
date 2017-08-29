<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Hero
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HeroTranslation[] $translations
 * @mixin \Eloquent
 */
class Hero extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['id'];
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(HeroTranslation::class);
    }
}
