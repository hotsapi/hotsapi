<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HeroTranslation
 *
 * @property-read \App\Hero $hero
 * @mixin \Eloquent
 */
class HeroTranslation extends Model
{
    protected $table = 'hero_translations';
    protected $guarded = ['id', 'hero_id'];
    protected $hidden = ['id', 'hero_id'];
    public $timestamps = false;

    public function hero()
    {
        return $this->belongsTo(Hero::class);
    }
}
