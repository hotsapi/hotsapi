<?php

namespace App\Console\Commands;

use App\Ability;
use App\Hero;
use App\HeroTalent;
use App\Talent;
use Guzzle;
use Illuminate\Console\Command;

class FetchTalents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotsapi:fetch-talents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $heroes = [];
        $abilities = [];
        $talents = [];
        $talents_pivot = [];
        foreach (Hero::all() as $hero) {
            $this->info("Getting talents for $hero->name");
            $response = Guzzle::get("https://raw.githubusercontent.com/heroespatchnotes/heroes-talents/master/hero/$hero->short_name.json");
            if ($response->getStatusCode() != 200) {
                $this->error("Error getting data for hero $hero, code " . $response->getStatusCode());
                continue;
            }
            $data = json_decode($response->getBody());

            $heroes[] = [
                'id' => $hero->id,
                'name' => $hero->name,
                'role' => $data->role,
                'type' => $data->type,
                'release_date' => $data->releaseDate,
            ];

            foreach ($data->abilities as $owner => $abilityArray) {
                foreach ($abilityArray as $ability) {
                    $abilities[] = [
                        'hero_id' => $hero->id,
                        'owner' => $owner,
                        'name' => preg_replace('/^.*\|/', '', $ability->abilityId),
                        'title' => $ability->name,
                        'description' => $ability->description,
                        'hotkey' => $ability->hotkey ?? null,
                        'cooldown' => $ability->cooldown ?? null,
                        'mana_cost' => isset($ability->manaCost) ? preg_replace('/ per second/', '', $ability->manaCost) : null,
                        'trait' => $ability->trait ?? false,
                    ];
                }
            }

            foreach ($data->talents as $level => $talentArray) {
                foreach ($talentArray as $talent) {
                    // We can't use bulk upsert for talents because we need to obtain `id` field
                    $srcTalent = Talent::updateOrCreate([
                            'name' => $talent->tooltipId
                        ], [
                            'title' => $talent->name,
                            'description' => $talent->description,
                            'icon' => $talent->icon,
                            'ability_id' => preg_replace('/^.*\|/', '', $talent->abilityId),
                            'sort' => $talent->sort,
                            'level' => $level,
                            'cooldown' => $talent->cooldown ?? null,
                            'mana_cost' => $talent->mana_cost ?? null,
                    ]);

                    $talents_pivot[] = [
                        'hero_id' => $hero->id,
                        'talent_id' => $srcTalent->id,
                    ];
                }
            }
        }

        HeroTalent::insertOnDuplicateKey($talents_pivot);
        Hero::insertOnDuplicateKey($heroes);
        Ability::insertOnDuplicateKey($abilities);
    }
}
