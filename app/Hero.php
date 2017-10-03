<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Hero
 *
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property string|null $role
 * @property string|null $type
 * @property string|null $release_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ability[] $abilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Talent[] $talents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HeroTranslation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hero whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hero whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hero whereReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hero whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hero whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hero whereType($value)
 * @mixin \Eloquent
 */
class Hero extends Model
{
    use InsertOnDuplicateKey;

    protected $guarded = ['id'];
    protected $hidden = ['id'];
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(HeroTranslation::class);
    }

    public function talents()
    {
        return $this->belongsToMany(Talent::class)->using(HeroTalent::class);
    }

    public function abilities()
    {
        return $this->hasMany(Ability::class);
    }
}
