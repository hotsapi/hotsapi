<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Talent
 *
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $description
 * @property string|null $icon
 * @property int|null $level
 * @property string|null $ability_id
 * @property int|null $sort
 * @property int|null $cooldown
 * @property int|null $mana_cost
 * @property-read mixed $icon_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Hero[] $heroes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereAbilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereCooldown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereManaCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Talent whereTitle($value)
 * @mixin \Eloquent
 */
class Talent extends Model
{
    use InsertOnDuplicateKey;

    protected $guarded = ['id'];
    protected $hidden = ['id'];
    public $timestamps = false;

    public function heroes()
    {
        return $this->belongsToMany(Hero::class)->using(HeroTalent::class);
    }

    public function getIconUrlAttribute()
    {
        return $this->icon ? ["64x64" => "http://s3.hotsapi.net/img/talents/64x64/$this->icon"] : [];
    }
}
