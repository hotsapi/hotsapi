<?php namespace App\Services;

use App\HeroTranslation;
use App\MapTranslation;
use App\Replay;
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
    const GAME_TYPE_BRAWL = "Brawl";
    const GAME_TYPE_AI = "AI";
    const GAME_TYPE_UNKNOWN = "Unknown";


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

            $fingerprint_v2 = $this->getFingerprint($replay);
            $fingerprint_v1 = $replay->initdata->m_syncLobbyState->m_gameDescription->m_randomValue;
            if (!$skipDuplicateCheck && $duplicate = Replay::where('fingerprint', $fingerprint_v2)->first()) {
                $result->status = self::STATUS_DUPLICATE;
                $result->replay = $duplicate;
                return $result;
            }

            $result->data = [
                'fingerprint' => $fingerprint_v2,
                'fingerprint_old' => $fingerprint_v1,
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
                'game_map' => $this->translateMapName(utf8_decode($replay->details->m_title)),
                'game_version' => "$version->m_major.$version->m_minor.$version->m_revision.$version->m_build",
            ];

            if ($result->data['game_type'] == self::GAME_TYPE_AI) {
                $result->status = self::STATUS_AI_DETECTED;
                return $result;
            }

            $winnerCheck = false;
            $result->region = null;
            foreach ($replay->details->m_playerList as $i => $player) {
                if ($result->region === null) {
                    $result->region = $player->m_toon->m_region;
                }

                $playerData = [
                    // todo extract full battletag from battlelobby
                    'battletag' => utf8_decode($player->m_name),
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

                if ($attr->{self::ATTR_PLAYER_TYPE}[0]->value != "Humn" || $playerData["hero"] == "Random Hero" || strpos($playerData["battletag"], ' ') !== false || $player->m_observe == 2) {
                    $result->status = self::STATUS_AI_DETECTED;
                    return $result;
                }

                if ($result->region > 90) {
                    $result->status = self::STATUS_PTR_REGION;
                    return $result;
                }

                if ($playerData['battletag']) {
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
                $player['hero'] = null;
            } else {
                $player['hero'] = $heroTranslation->hero->name;
            }
        }
    }

    /**
     * Translates map to canonical english name
     *
     * @param $name
     * @return string|null
     */
    private function translateMapName($name)
    {
        $name = mb_strtolower($name);
        $mapTranslation = MapTranslation::where('name', $name)->with('map')->first();
        if (!$mapTranslation) {
            Log::error("Error translating map: " . $name);
            return null;
        } else {
            return $mapTranslation->map->name;
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
        return Carbon::createFromDate(1601, 1, 1)->addSeconds($time / 10000000);
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
        $guid_byte_order = [3, 2, 1, 0, 5, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
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
        $process = new Process("heroprotocol --json --header --details --initdata --attributeevents '$filename'");
        if (0 !== $process->run()) {
            if (strpos($process->getErrorOutput(), "Unsupported base build") !== false) {
                return false;
            }
            throw new ProcessFailedException($process);
        }
        $output = $process->getOutput();
        $lines = explode(PHP_EOL, $output);
        $result = (object)[
            "header" => json_decode($lines[0]),
            "details" => json_decode($lines[1]),
            "initdata" => json_decode($lines[3]), // lines[2] contains cache entries
            "attributeevents" => json_decode($lines[4]),
        ];
        if (!$result->header || !$result->details || !$result->initdata || !$result->attributeevents) {
            throw new Exception("Error parsing parser output:\n$output\n");
        }
        return $result;
    }
}
