<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\HeroTranslation
 *
 * @property int $id
 * @property int $hero_id
 * @property string $name
 * @property-read \App\Hero $hero
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HeroTranslation whereHeroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HeroTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HeroTranslation whereName($value)
 * @mixin \Eloquent
 */
class HeroTranslation extends Model
{
    use InsertOnDuplicateKey;

    protected $table = 'hero_translations';
    protected $guarded = ['id', 'hero_id'];
    protected $hidden = ['id', 'hero_id'];
    public $timestamps = false;

    public function hero()
    {
        return $this->belongsTo(Hero::class);
    }
}
