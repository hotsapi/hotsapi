<?php

namespace App\Console\Commands;

use App\Hero;
use App\HeroTranslation;
use App\Map;
use App\MapTranslation;
use Illuminate\Console\Command;

class FetchTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotsapi:fetch-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch hero and map translation data from hotslogs';

    /**
     * Create a new command instance.
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
        $this->fetchMaps();
        $this->fetchHeroes();
    }

    public function fetchMaps()
    {
        $maps = Map::with('translations')->get();
        $json = json_decode(\Guzzle::get('https://api.hotslogs.com/Public/Data/Maps')->getBody());
        foreach ($json as $map) {
            $dbMap = $maps->where('name', $map->PrimaryName)->first();
            if (!$dbMap) {
                $dbMap = Map::create(['name' => $map->PrimaryName]);
            }
            foreach (explode(',', $map->Translations) as $translation) {
                if ($dbMap->translations->where('name', $translation)->isEmpty()) {
                    $dbMap->translations()->save(new MapTranslation(['name' => $translation]));
                }
            }
        }
    }

    public function fetchHeroes()
    {
        $heroes = Hero::with('translations')->get();
        $json = json_decode(\Guzzle::get('https://api.hotslogs.com/Public/Data/Heroes')->getBody());
        foreach ($json as $hero) {
            $dbHero = $heroes->where('name', $hero->PrimaryName)->first();
            if (!$dbHero) {
                $dbHero = Hero::create(['name' => $hero->PrimaryName]);
            }
            foreach (explode(',', $hero->Translations) as $translation) {
                if ($dbHero->translations->where('name', $translation)->isEmpty()) {
                    $dbHero->translations()->save(new HeroTranslation(['name' => $translation]));
                }
            }
        }
    }
}
