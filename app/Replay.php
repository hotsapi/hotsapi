<?php

namespace App;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Replay
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $filename
 * @property int $size
 * @property string|null $game_type
 * @property \Carbon\Carbon|null $game_date
 * @property int|null $game_length
 * @property int|null $game_map_id
 * @property string|null $game_version
 * @property string $fingerprint
 * @property string $fingerprint_old
 * @property int|null $region
 * @property int $processed
 * @property int $deleted
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ban[] $bans
 * @property-read \App\Map|null $game_map
 * @property-read bool $url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HotslogsUpload[] $hotslogsUploads
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereFingerprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereFingerprintOld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameMapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereGameVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Replay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Replay extends Model
{
    use InsertOnDuplicateKey;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['game_type', 'game_date', 'game_length', 'game_version', 'game_map_id', 'region', 'fingerprint', 'fingerprint_old', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'fingerprint_old'];
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

    public function game_map()
    {
        return $this->belongsTo(Map::class, 'game_map_id');
    }

    public function bans()
    {
        return $this->hasMany(Ban::class);
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

    public function hotslogsUploads()
    {
        return $this->hasMany(HotslogsUpload::class);
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
