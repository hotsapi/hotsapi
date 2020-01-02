<?php

namespace App\Console\Commands;

use App\Ability;
use App\Hero;
use App\HeroTalent;
use App\Talent;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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

    private $cloneDir = "/tmp/heroes-talents";

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
        $this->fetchHeroes();
        list($heroes, $abilities, $talents) = $this->processHeroFiles();

        $this->info('FetchTalents: Saving heroes data...');
        Hero::insertOnDuplicateKey($heroes);
        Ability::insertOnDuplicateKey($abilities);
        HeroTalent::insertOnDuplicateKey($talents);

        $this->info('FetchTalents: Finished');
    }

    private function fetchHeroes() {
        $this->info('FetchTalents: Cloning heroes data from Github...');

        $process = new Process("rm -rf " . $this->cloneDir);
        $process->run();

        $process = new Process("git clone --depth 1 https://github.com/heroespatchnotes/heroes-talents.git " . $this->cloneDir);
        if (0 !== $process->run()) {
            throw new ProcessFailedException($process);
        }
    }

    private function processHeroFiles() {
        $this->info('FetchTalents: Processing heroes data...');
        $files = array_diff(scandir($this->cloneDir . '/hero'), array('.', '..'));

        $heroes = [];
        $abilities = [];
        $talents = [];
        foreach ($files as $file) {
            $content = file_get_contents("$this->cloneDir" . '/hero/' . $file);
            $data = json_decode($content);

            $heroes[] = [
                'id' => $data->id,
                'name' => $data->name,
                'short_name' => $data->shortName,
                'c_hero_id' => $data->cHeroId,
                'c_unit_id' => $data->cUnitId,
                'role' => $data->role,
                'type' => $data->type,
                'release_date' => $data->releaseDate,
                'release_patch' => $data->releasePatch
            ];

            foreach ($data->abilities as $owner => $abilityArray) {
                foreach ($abilityArray as $ability) {
                    $abilities[] = [
                        'hero_id' => $data->id,
                        'owner' => $owner,
                        'name' => isset($ability->abilityId) ? preg_replace('/^.*\|/', '', $ability->abilityId) : null,
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
                        'name' => $talent->talentTreeId
                    ], [
                        'title' => $talent->name,
                        'description' => $talent->description,
                        'icon' => $talent->icon,
                        'ability_id' => isset($talent->abilityId) ? preg_replace('/^.*\|/', '', $talent->abilityId) : null,
                        'sort' => $talent->sort,
                        'level' => $level,
                        'cooldown' => $talent->cooldown ?? null,
                        'mana_cost' => $talent->mana_cost ?? null,
                    ]);

                    $talents[] = [
                        'hero_id' => $data->id,
                        'talent_id' => $srcTalent->id,
                    ];
                }
            }
        }

        return array($heroes, $abilities, $talents);
    }
}
