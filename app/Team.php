<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Team
 *
 * @property-read \App\Replay $replay
 * @mixin \Eloquent
 */
class Team extends Model
{
    use InsertOnDuplicateKey;

    public function replay()
    {
        return $this->belongsTo(Replay::class);
    }
}
