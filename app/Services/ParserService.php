<?php namespace App\Services;

use App\Hero;
use App\HeroTalent;
use App\HeroTranslation;
use App\MapTranslation;
use App\Replay;
use App\Talent;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Log;
use stdClass;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ParserService
{
    const STATUS_SUCCESS = "Success";
    const STATUS_DUPLICATE = "Duplicate";
    const STATUS_AI_DETECTED = "AiDetected";
    const STATUS_CUSTOM_GAME = "CustomGame";
    const STATUS_PTR_REGION = "PtrRegion";
    const STATUS_TOO_OLD = "TooOld";
    const STATUS_UPLOAD_ERROR = "UploadError";
    const STATUS_INCOMPLETE = "Incomplete";

    const MIN_SUPPORTED_BUILD = 43905;

    const ATTR_CHARACTER_LEVEL = 4008;
    const ATTR_PLAYER_TYPE = 500;

    const GAME_TYPE_QUICK_MATCH = "QuickMatch";
    const GAME_TYPE_UNRANKED_DRAFT = "UnrankedDraft";
    const GAME_TYPE_HERO_LEAGUE = "HeroLeague";
    const GAME_TYPE_TEAM_LEAGUE = "TeamLeague";
    const GAME_TYPE_STORM_LEAGUE = "StormLeague";
    const GAME_TYPE_BRAWL = "Brawl";
    const GAME_TYPE_AI = "AI";
    const GAME_TYPE_UNKNOWN = "Unknown";

    const GAMES_WITH_BANS = [self::GAME_TYPE_UNRANKED_DRAFT, self::GAME_TYPE_HERO_LEAGUE, self::GAME_TYPE_TEAM_LEAGUE, self::GAME_TYPE_STORM_LEAGUE];

    /**
     * Talent cache
     *
     * @var \Illuminate\Database\Eloquent\Collection|\App\Talent[]
     */
    private $talents;
    const TALENT_LEVELS = [1, 4, 7, 10, 13, 16, 20];

    /**
     * Hero cache
     *
     * @var \Illuminate\Database\Eloquent\Collection|\App\Hero[]
     */
    private $heroes;


    /**
     * Extract metadata from replay
     *
     * @param string $filename
     * @param bool $skipDuplicateCheck Don't check for duplicates. Needed for reparse command.
     * @return stdClass
     */
    public function analyze($filename, $skipDuplicateCheck = false)
    {
        // todo this big method needs refactoring
        $result = new stdClass();

        try {
            $replay = $this->parse($filename);

            if ($replay === false) {
                $result->status = self::STATUS_TOO_OLD;
                return $result;
            }

            $version = $replay->header->m_version;
            if ($version->m_build < self::MIN_SUPPORTED_BUILD) {
                $result->status = self::STATUS_TOO_OLD;
                return $result;
            }

            $fingerprint = $this->getFingerprint($replay);
            if (!$skipDuplicateCheck && $duplicate = Replay::where('fingerprint', $fingerprint)->first()) {
                $result->status = self::STATUS_DUPLICATE;
                $result->replay = $duplicate;
                return $result;
            }

            $result->data = [
                'fingerprint' => $fingerprint,
                'players' => []
            ];

            if (!$replay->initdata->m_syncLobbyState->m_gameDescription->m_gameOptions->m_amm) {
                $result->status = self::STATUS_CUSTOM_GAME;
                return $result;
            }

            // After this point all data is considered non-essential
            // We can still add such replays even if parsing fails

            $result->data += [
                // todo better duplicates detection
                'game_type' => $this->DetectGameMode($replay->initdata->m_syncLobbyState->m_gameDescription->m_gameOptions->m_ammId),
                'game_date' => $this->FiletimeToDatetime($replay->details->m_timeUTC),
                'game_length' => (int)($replay->header->m_elapsedGameLoops / 16),
                'game_map_id' => $this->translateMapName(utf8_decode($replay->details->m_title))->id ?? null,
                'game_version' => "$version->m_major.$version->m_minor.$version->m_revision.$version->m_build",
            ];

            if ($result->data['game_type'] == self::GAME_TYPE_AI) {
                $result->status = self::STATUS_AI_DETECTED;
                return $result;
            }

            $winnerCheck = false;
            $result->data['region'] = null;
            foreach ($replay->details->m_playerList as $i => $player) {
                if ($result->data['region'] === null) {
                    $result->data['region'] = $player->m_toon->m_region;
                }

                $playerData = [
                    // todo extract full battletag from battlelobby
                    'battletag_name' => utf8_decode($player->m_name),
                    'hero' => mb_strtolower(utf8_decode($player->m_hero)), // to lower for translation
                    'team' => $player->m_teamId,
                    'winner' => $player->m_result == 1,
                    'blizz_id' => $player->m_toon->m_id,
                ];

                $attr = $replay->attributeevents->scopes->{$i + 1};
                $playerData['hero_level'] = (int)$attr->{self::ATTR_CHARACTER_LEVEL}[0]->value;
                if ($playerData['winner']) {
                    $winnerCheck = true;
                }

                if ($attr->{self::ATTR_PLAYER_TYPE}[0]->value != "Humn" || $playerData["hero"] == "Random Hero" || strpos($playerData["battletag_name"], ' ') !== false || $player->m_observe == 2) {
                    $result->status = self::STATUS_AI_DETECTED;
                    return $result;
                }

                if ($result->data['region'] > 90) {
                    $result->status = self::STATUS_PTR_REGION;
                    return $result;
                }

                if ($playerData['battletag_name']) {
                    $result->data['players'] [] = $playerData;
                }
            }

            $this->translateHeroNames($result->data['players']);

            if (count($result->data['players']) <= 5 || $replay->initdata->m_syncLobbyState->m_gameDescription->m_maxUsers != 10) {
                $result->status = self::STATUS_AI_DETECTED;
                return $result;
            }

            if (!$winnerCheck || $result->data['game_length'] < 120) {
                $result->status = self::STATUS_INCOMPLETE;
                return $result;
            }

            $result->status = self::STATUS_SUCCESS;
            if ($result->data['game_type'] == self::GAME_TYPE_UNKNOWN) {
                Log::error("Error parsing replay: Unknown game type");
                $result->data['game_type'] = null; // in case upload broken replays is enabled
                $result->status = self::STATUS_UPLOAD_ERROR;
            }
            return $result;
        } catch (Exception $e) {
            Log::error("Error parsing replay: $e");
            $result->status = self::STATUS_UPLOAD_ERROR;
            return $result;
        }
    }

    /**
     * Translates map and hero names to canonical english names
     * @param $players
     */
    private function translateHeroNames(&$players)
    {
        $heroTranslations = HeroTranslation::whereIn('name', collect($players)->pluck('hero'))->with('hero')->get();
        foreach ($players as &$player) {
            $heroTranslation = $heroTranslations->where('name', $player['hero'])->first();
            if (!$heroTranslation) {
                Log::error("Error translating hero: " . $player['hero']);
                $player['hero_id'] = null;
            } else {
                $player['hero_id'] = $heroTranslation->hero->id;
            }
        }
    }

    /**
     * Translates map to canonical english name
     *
     * @param $name
     * @return \App\Map|null
     */
    private function translateMapName($name)
    {
        $name = mb_strtolower($name);
        $mapTranslation = MapTranslation::where('name', $name)->with('map')->first();
        if (!$mapTranslation) {
            Log::error("Error translating map: " . $name);
            return null;
        } else {
            return $mapTranslation->map;
        }
    }

    /**
     * Converts FILETIME to a Carbon instance
     *
     * @param $time
     * @return Carbon
     */
    public function FiletimeToDatetime($time)
    {
        // Filetime: Contains a 64-bit value representing the number of 100-nanosecond intervals since January 1, 1601 (UTC).
        return Carbon::createFromDate(1601, 1, 1)->startOfDay()->addSeconds($time / 10000000);
    }

    /**
     * Detect game mode name by id
     *
     * @param $gameModeId
     * @return string
     */
    public function DetectGameMode($gameModeId)
    {
        switch ($gameModeId)
        {
            case 50021: // Versus AI (Cooperative)
            case 50041: // Practice
                return self::GAME_TYPE_AI;

            case 50001:
                return self::GAME_TYPE_QUICK_MATCH;

            case 50031:
                return self::GAME_TYPE_BRAWL;

            case 50051:
                return self::GAME_TYPE_UNRANKED_DRAFT;

            case 50061:
                return self::GAME_TYPE_HERO_LEAGUE;

            case 50071:
                return self::GAME_TYPE_TEAM_LEAGUE;

            case 50091:
                return self::GAME_TYPE_STORM_LEAGUE;

            default:
                Log::error("Unknown game type: '$gameModeId'");
                return self::GAME_TYPE_UNKNOWN;
        }
    }

    /**
     * Get unique hash of replay. Compatible with HotsLogs
     *
     * @param $replay
     * @return string
     */
    public function getFingerprint($replay)
    {
        try {
            $ids = collect($replay->details->m_playerList)->map->m_toon->map->m_id->sort();
            $string = implode('', $ids->toArray()) . $replay->initdata->m_syncLobbyState->m_gameDescription->m_randomValue;
            $hash = md5($string, true);
            $guid = $this->bytesToGuid($hash);
            return $guid;
        } catch (\Exception $e) {
            Log::error($e, "Error getting replay fingerprint");
            return null;
        }

    }

    /**
     * Transform byte string to a windows GUID
     *
     * @param $bytes
     * @return string
     */
    public function bytesToGuid($bytes)
    {
        $guid_byte_order = [3, 2, 1, 0, 5, 4, 7, 6, 8, 9, 10, 11, 12, 13, 14, 15];
        $dash_positions = [3, 5, 7, 9];
        $result = "";
        for ($i = 0; $i < 16; $i++) {
            $result = sprintf($result . "%02x", ord($bytes[$guid_byte_order[$i]]));
            if (in_array($i, $dash_positions)) {
                $result .= "-";
            }
        }
        return $result;
    }

    /**
     * Invoke external python parser
     *
     * @param $filename
     * @return bool|stdClass
     * @throws Exception
     */
    public function parse($filename)
    {
        $host = env('HEROPROTOCOL_HOST', 'heroprotocol:5000');
        $filename = str_replace('/tmp/', '', $filename);
        return json_decode(\Guzzle::get('http://' . $host . '/' . $filename . '?header&details&initdata&attributeevents')->getBody());
    }

    public function extractExtendedData($filename)
    {
        $host = env('PARSER_HOST', 'parser:8080');
        $filename = str_replace('/tmp/', '', $filename);
        return json_decode(\Guzzle::get('http://' . $host . '/' . $filename)->getBody());
    }

    public function analyzeExtended($filename, Replay $replay)
    {
        $data = $this->extractExtendedData($filename);

        if (!$this->talents) {
            $this->talents = Talent::with('heroes')->get();
        }
        if (!$this->heroes) {
            $this->heroes = Hero::all();
        }

        $scores = [];
        $talents = [];
        $bans = [];
        $players = [];
        $teams = [];
        foreach($data->players as $index => $player) {
            $srcPlayer = $replay->players->where('blizz_id', $player->blizz_id)->first();
            if (!$srcPlayer) {
                Log::warning("Can't find player with blizz_id $player->blizz_id in replay $replay->id");
                continue;
            }

            $players []= [
                'replay_id' => $srcPlayer->replay_id,
                'blizz_id' => $srcPlayer->blizz_id,
                'silenced' => $player->silenced,
                'battletag_name' => $player->battletag_name,
                'battletag_id' => $player->battletag_id,
                'party' => $player->party,
                'index' => $index
            ];

            if ($player->score != null) {
                $scores[] = [
                    'id' => $srcPlayer->id,
                    'level' => $player->score->Level,
                    'kills' => $player->score->SoloKills,
                    'assists' => $player->score->Assists,
                    'takedowns' => $player->score->Takedowns,
                    'deaths' => $player->score->Deaths,
                    'highest_kill_streak' => $player->score->HighestKillStreak,
                    'hero_damage' => $player->score->HeroDamage,
                    'siege_damage' => $player->score->SiegeDamage,
                    'structure_damage' => $player->score->StructureDamage,
                    'minion_damage' => $player->score->MinionDamage,
                    'creep_damage' => $player->score->CreepDamage,
                    'summon_damage' => $player->score->SummonDamage,
                    'time_cc_enemy_heroes' => $this->toSeconds($player->score->TimeCCdEnemyHeroes),
                    'healing' => $player->score->Healing,
                    'self_healing' => $player->score->SelfHealing,
                    'damage_taken' => $player->score->DamageTaken,
                    'experience_contribution' => $player->score->ExperienceContribution,
                    'town_kills' => $player->score->TownKills,
                    'time_spent_dead' => $this->toSeconds($player->score->TimeSpentDead),
                    'merc_camp_captures' => $player->score->MercCampCaptures,
                    'watch_tower_captures' => $player->score->WatchTowerCaptures,
                    'meta_experience' => $player->score->MetaExperience,
                    'damage_soaked' => $player->score->DamageSoaked,
                    'physical_damage' => $player->score->PhysicalDamage,
                    'spell_damage' => $player->score->SpellDamage,
                    'protection_given_to_allies' => $player->score->ProtectionGivenToAllies,
                    'teamfight_damage_taken' => $player->score->TeamfightDamageTaken,
                    'teamfight_escapes_performed' => $player->score->TeamfightEscapesPerformed,
                    'teamfight_healing_done' => $player->score->TeamfightHealingDone,
                    'teamfight_hero_damage' => $player->score->TeamfightHeroDamage,
                    'time_rooting_enemy_heroes' => $player->score->TimeRootingEnemyHeroes,
                    'time_silencing_enemy_heroes' => $player->score->TimeSilencingEnemyHeroes,
                    'time_stunning_enemy_heroes' => $player->score->TimeStunningEnemyHeroes,
                    'multikill' => $player->score->Multikill,
                    'outnumbered_deaths' => $player->score->OutnumberedDeaths,
                    'vengeances_performed' => $player->score->VengeancesPerformed,
                    'escapes_performed' => $player->score->EscapesPerformed,
                    'clutch_heals_performed' => $player->score->ClutchHealsPerformed,
                ];
            }

            foreach ($player->talents as $i => $talent) {
                if (!$talent) {
                    continue;
                }
                $srcTalent = $this->talents->where('name', $talent)->first();
                if (!$srcTalent) {
                    $srcTalent = Talent::firstOrCreate(['name' => $talent]);
                    $this->talents->add($srcTalent);
                }
                // Don't connect talents from previous patches to heroes
                //if ($srcTalent->heroes->where('hero_id', $player->hero_id)->isEmpty()) {
                //    HeroTalent::insertIgnore(['talent_id' => $srcTalent->id, 'hero_id' => $player->hero_id]);
                //}
                $talents []= [
                    'player_id' => $srcPlayer->id,
                    'talent_id' => $srcTalent->id,
                    'level' => self::TALENT_LEVELS[$i]
                ];
            }
        }

        if (in_array($replay->game_type, self::GAMES_WITH_BANS)) {
            foreach ($data->bans as $team => $replayBans) {
                foreach ($replayBans as $index => $ban) {
                    if ($ban) {
                        $hero = $this->heroes->where('attribute_id', $ban)->first();
                        if (!$hero) {
                            throw new Exception("Can't find hero for ban $ban");
                        }
                    }
                    $bans[] = [
                        'replay_id' => $replay->id,
                        'hero_id' => $ban ? $hero->id : null,
                        'team' => $team,
                        'index' => $index
                    ];
                }
            }
        }

        foreach ($data->teams as $teamIndex => $team) {
            $teams[] = [
                'replay_id' => $replay->id,
                'index' => $teamIndex,
                'first_pick' => $team->FirstPick,
                'winner' => $team->Winner,
                'team_level' => $team->TeamLevel,
                'structure_xp' => $team->StructureXP,
                'creep_xp' => $team->CreepXP,
                'hero_xp' => $team->HeroXP,
                'minion_xp' => $team->MinionXP,
                'trickle_xp' => $team->TrickleXP,
                'total_xp' => $team->TotalXP,
            ];
        }

        return compact('bans', 'players', 'talents', 'scores', 'teams');
    }


    /**
     * Convert time string to seconds
     *
     * @param $interval
     * @return int
     */
    public function toSeconds($interval)
    {
        // maybe someone knows a better way to do it?
        if (preg_match('/^\d+\./', $interval)) {
            // more than 1 day, return max dey seconds
            return 86400;
        } else {
            return (new Carbon($interval))->diffInSeconds((new Carbon())->startOfDay());
        }
    }
}
