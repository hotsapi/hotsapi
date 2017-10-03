<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Yadakhov\InsertOnDuplicateKey;

class HeroTalent extends Pivot
{
    use InsertOnDuplicateKey;

}
