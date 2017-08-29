<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Player
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $replay_id
 * @property string $battletag
 * @property string $hero
 * @property int $hero_level
 * @property int $team
 * @property bool $winner
 * @property int|null $region
 * @property int|null $blizz_id
 * @property-read \App\Replay $replay
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereBattletag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereHero($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereHeroLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereReplayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereWinner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereBlizzId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereRegion($value)
 */
class Player extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'replay_id'];
    protected $hidden = ['id', 'replay_id'];
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'winner' => 'boolean',
    ];

    public function replay()
    {
        return $this->belongsTo(Replay::class);
    }
}
