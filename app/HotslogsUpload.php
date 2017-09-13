<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HotslogsUpload
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $replay_id
 * @property string|null $status
 * @property string|null $result
 * @property-read \App\Replay $replay
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotslogsUpload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotslogsUpload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotslogsUpload whereReplayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotslogsUpload whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotslogsUpload whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotslogsUpload whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HotslogsUpload extends Model
{
    public function replay()
    {
        return $this->belongsTo(Replay::class);
    }
}
