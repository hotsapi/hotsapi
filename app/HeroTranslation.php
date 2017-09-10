<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HeroTranslation
 *
 * @property-read \App\Hero $hero
 * @mixin \Eloquent
 * @property int $id
 * @property int $hero_id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HeroTranslation whereHeroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HeroTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HeroTranslation whereName($value)
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
