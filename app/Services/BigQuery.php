<?php

namespace App\Services;

use App\Http\Resources\BigQuery\ReplayResource;
use App\Replay;
use Google\Cloud\BigQuery\BigQueryClient;
use Log;

class BigQuery
{
    /**
     * @param Replay $replay replay object to upload
     * @return void
     * @throws \Exception
     */
    public function insertRow(Replay $replay)
    {
        $bigQuery = new BigQueryClient(['keyFilePath' => __DIR__.'/../../.gcloud.json', 'projectId' => 'cloud-project-179020']);

        $table = $bigQuery->dataset('hotsapi')->table('replays');
        $res = new ReplayResource($replay);
        // todo optimize next line
        $row = json_decode(json_encode($res->toResponse(app('request'))->getData()), true);
        $result = $table->insertRow($row, ['insertId' => $replay->parsed_id]);

        if (!$result->isSuccessful()) {
            foreach ($result->failedRows() as $row) {
                $error = "Failed to insert row into BigQuery:" . PHP_EOL;
                $error .= print_r($row['rowData'], true) . PHP_EOL;

                foreach ($row['errors'] as $err) {
                    $error .=  $err['reason'] . ': ' . $err['message'] . PHP_EOL;
                }

                throw new \Exception($error);
            }
        }
    }
}


