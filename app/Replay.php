<?php

namespace App;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Replay
 *
 * @mixin \Eloquent
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $filename
 * @property int $size
 * @property string|null $game_date
 * @property string|null $game_length
 * @property string|null $game_map
 * @property string|null $game_type
 * @property string $fingerprint
 * @property string|null $game_version
 * @property int|null $region
 * @property-read string $url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereFingerprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameVersion($value)
 */
class Replay extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'filename', 'size', 'created_at', 'updated_at', 'players'];
    protected $hidden = ['created_at', 'updated_at', 'fingerprint'];
    protected $appends = ['url'];
    protected $dates = [
        'created_at',
        'updated_at',
        'game_date'
    ];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Get full url to replay file
     *
     * @return bool
     */
    public function getUrlAttribute()
    {
        return "http://" . env('AWS_BUCKET') . ".s3-website-" . env('AWS_REGION') . ".amazonaws.com/" . $this->filename . ".StormReplay";
    }

//    /**
//     * Gets game time
//     *
//     * @param $value
//     * @return CarbonInterval
//     */
//    public function getGameLengthAttribute($value)
//    {
//        return CarbonInterval::seconds($value);
//    }
//
//    /**
//     * Sets game time
//     *
//     * @param CarbonInterval $value
//     */
//    public function setGameLengthAttribute(CarbonInterval $value)
//    {
//        $this->attributes['game_length'] = $value->seconds;
//    }
}
