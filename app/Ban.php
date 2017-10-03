<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Ban
 *
 * @property int $id
 * @property int $replay_id
 * @property int|null $hero_id
 * @property string|null $hero_name
 * @property int $team
 * @property int $index
 * @property-read \App\Hero|null $hero
 * @property-read \App\Replay $replay
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereHeroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereHeroName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereReplayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereTeam($value)
 * @mixin \Eloquent
 */
class Ban extends Model
{
    use InsertOnDuplicateKey;

    public function replay()
    {
        return $this->belongsTo(Replay::class);
    }

    public function hero()
    {
        return $this->belongsTo(Hero::class);
    }
}
