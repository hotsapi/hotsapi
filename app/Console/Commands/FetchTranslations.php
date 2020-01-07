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
        $this->info('FetchTranslations: Finished');
    }

    public function fetchMaps()
    {
        // todo: fetch map translations
    }

    public function fetchHeroes()
    {
        $heroes = Hero::with('translations')->get();

        $this->info('FetchTranslations: Retrieving heroes data from HeroesProfile...');
        $json = json_decode(\Guzzle::get('https://api.heroesprofile.com/Heroes')->getBody());

        $this->info('FetchTranslations: Processing hero data...');
        $translations = [];
        foreach ($json as $hero) {
            $dbHero = $heroes->where('name', $hero->name)->first();
            if (!$dbHero) {
                continue; # we need accurate hero ids for replay parsing so we don't add heroes here.
            }

            $heroTranslations = array_map(function ($x) { return mb_strtolower($x); }, $hero->translations);
            $heroTranslations[] = mb_strtolower($hero->name);
            $heroTranslations = array_unique($heroTranslations);
            foreach ($heroTranslations as $heroTranslation) {
                $translations[] = ['hero_id' => $dbHero->id, 'name' => $heroTranslation];
            }
        }

        $this->info('FetchTranslations: Saving heroes translation data...');
        HeroTranslation::insertOnDuplicateKey($translations);
        return;
    }
}
