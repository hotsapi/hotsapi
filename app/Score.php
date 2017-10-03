<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Score
 *
 * @property int $id
 * @property int|null $level
 * @property int|null $kills
 * @property int|null $assists
 * @property int|null $takedowns
 * @property int|null $deaths
 * @property int|null $highest_kill_streak
 * @property int|null $hero_damage
 * @property int|null $siege_damage
 * @property int|null $structure_damage
 * @property int|null $minion_damage
 * @property int|null $creep_damage
 * @property int|null $summon_damage
 * @property int|null $time_cc_enemy_heroes
 * @property int|null $healing
 * @property int|null $self_healing
 * @property int|null $damage_taken
 * @property int|null $experience_contribution
 * @property int|null $town_kills
 * @property int|null $time_spent_dead
 * @property int|null $merc_camp_captures
 * @property int|null $watch_tower_captures
 * @property int|null $meta_experience
 * @property-read \App\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereAssists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereCreepDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereDamageTaken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereDeaths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereExperienceContribution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereHealing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereHeroDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereHighestKillStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereKills($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereMercCampCaptures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereMetaExperience($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereMinionDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereSelfHealing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereSiegeDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereStructureDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereSummonDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereTakedowns($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereTimeCcEnemyHeroes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereTimeSpentDead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereTownKills($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Score whereWatchTowerCaptures($value)
 * @mixin \Eloquent
 */
class Score extends Model
{
    use InsertOnDuplicateKey;

    protected $hidden = ['id', 'player_id'];

    public function player()
    {
        return $this->belongsTo(Player::class, 'id');
    }
}
