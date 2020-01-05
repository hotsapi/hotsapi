<?php

use App\Map;
use App\MapTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT IGNORE INTO counters (name, value) SELECT 'parsed_id', IFNULL(MAX(parsed_id), 0) FROM replays");
        $this->loadMapTranslations();
    }

    private function loadMapTranslations() {
        $mapTranslations = json_decode(file_get_contents(dirname(__FILE__) . '/map_translations.json'));
        $translations = [];
        foreach ($mapTranslations as $mapTranslation) {
            $map = Map::firstOrNew(['name' => $mapTranslation->name]);
            $map->save();

            foreach($mapTranslation->translations as $translation) {
                $translations[] = ['map_id' => $map->id, 'name' => $translation];
            }
        }

        MapTranslation::insertOnDuplicateKey($translations);
    }
}
