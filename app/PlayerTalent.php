<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Yadakhov\InsertOnDuplicateKey;

class PlayerTalent extends Pivot
{
    use InsertOnDuplicateKey;

}
