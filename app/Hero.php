<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Hero
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HeroTranslation[] $translations
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property-read mixed $versions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hero whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hero whereName($value)
 */
class Hero extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['id'];
    public $timestamps = false;
    protected $flattenTranslations = false;

    public function translations()
    {
        return $this->hasMany(HeroTranslation::class);
    }

    public function flattenTranslations()
    {
        $this->flattenTranslations = true;
        return $this;
    }

    public function toArray()
    {
        $result = parent::toArray();
        if ($this->flattenTranslations) {
            $result['translations'] =  array_column($result['translations'], 'name');
        }
        return $result;
    }
}
