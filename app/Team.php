<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Team
 *
 * @property int $id
 * @property int $replay_id
 * @property int $index
 * @property int|null $first_pick
 * @property int|null $winner
 * @property int $team_level
 * @property int $structure_xp
 * @property int $creep_xp
 * @property int $hero_xp
 * @property int $minion_xp
 * @property int $trickle_xp
 * @property int $total_xp
 * @property-read \App\Replay $replay
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereCreepXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereFirstPick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereHeroXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereMinionXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereReplayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereStructureXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereTeamLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereTotalXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereTrickleXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereWinner($value)
 * @mixin \Eloquent
 */
class Team extends Model
{
    use InsertOnDuplicateKey;

    protected $guarded = ['id', 'replay_id', 'index'];
    protected $hidden = ['id', 'replay_id', 'index'];

    public function replay()
    {
        return $this->belongsTo(Replay::class);
    }
}
