<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Ability
 *
 * @property int $id
 * @property int $hero_id
 * @property string|null $owner
 * @property string $name
 * @property string|null $title
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $hotkey
 * @property int|null $cooldown
 * @property int|null $mana_cost
 * @property bool $trait
 * @property-read \App\Hero $hero
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereCooldown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereHeroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereHotkey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereManaCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ability whereTrait($value)
 * @mixin \Eloquent
 */
class Ability extends Model
{
    use InsertOnDuplicateKey;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'hero_id'];
    public $timestamps = false;

    protected $casts = [
        'trait' => 'boolean',
    ];

    public function hero()
    {
        return $this->belongsTo(Hero::class);
    }
}
