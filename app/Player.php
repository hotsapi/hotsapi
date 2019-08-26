<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Player
 *
 * @property int $id
 * @property int $replay_id
 * @property int|null $hero_id
 * @property string|null $battletag_name
 * @property int|null $battletag_id
 * @property int|null $hero_level
 * @property int|null $team
 * @property bool $winner
 * @property int $blizz_id
 * @property int|null $party
 * @property int|null $silenced
 * @property int|null $index
 * @property-read mixed $battletag
 * @property-read \App\Hero|null $hero
 * @property-read \App\Replay $replay
 * @property-read \App\Score $score
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Talent[] $talents
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereBattletagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereBattletagName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereBlizzId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereHeroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereHeroLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereParty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereReplayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereSilenced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereWinner($value)
 * @mixin \Eloquent
 */
class Player extends Model
{
    use InsertOnDuplicateKey;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['battletag_name', 'battletag_id', 'hero_id', 'hero_level', 'team', 'winner', 'blizz_id', 'party', 'silenced'];
    protected $hidden = ['id', 'replay_id', 'battletag_name', 'battletag_id'];
    protected $appends = ['battletag'];
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

    public function hero()
    {
        return $this->belongsTo(Hero::class);
    }

    public function score()
    {
        return $this->hasOne(Score::class, 'id');
    }

    public function talents()
    {
        return $this->belongsToMany(Talent::class)->withPivot('level')->using(PlayerTalent::class);
    }

    public function getBattletagAttribute()
    {
        return $this->battletag_id ? "$this->battletag_name#$this->battletag_id" : $this->battletag_name;
    }
}
