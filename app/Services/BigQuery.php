<?php

namespace App\Services;

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
        $bigQuery = new BigQueryClient(['keyFilePath' => './../.gcloud.json', 'projectId' => 'cloud-project-179020']);

        $table = $bigQuery->dataset('hotsapi')->table('replays');
        $data = new \App\Http\Resources\BigQuery\ReplayResource($replay);
        $result = $table->insertRow($data->toArray(null));

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


