<?php

namespace App\Console\Commands;

use App\Services\ReplayService;
use DB;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\File;

class Hotsdata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotsapi:download-hotsdata  {min_id=0} {max_id=1000000000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var ReplayService
     */
    private $replayService;

    /**
     * Create a new command instance.
     *
     * @param ReplayService $replayService
     */
    public function __construct(ReplayService $replayService)
    {
        parent::__construct();
        $this->replayService = $replayService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $min_id = $this->argument('min_id');
        $max_id = $this->argument('max_id');
        $this->info("Reparsing replays, id from $min_id to $max_id");
        DB::table('hotsdata')->where('id', '>=', $min_id)->where('id', '<=', $max_id)->where('result', null)->orderBy('id')->chunk(1000, function ($rows) {
            foreach ($rows as $row) {
                $tmpFile = tempnam('', 'replay_');
                try {
                    $remoteFilename = $row->file;

                    $s3 = (new \Aws\Sdk)->createMultiRegionS3([
                        'region' => 'us-west-2',
                        'version' => '2006-03-01',
                        'credentials' => [
                            'key' => 'AKIAJARRGHFKAW6JON6A',
                            'secret' => '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'
                        ]
                    ]);

                    $download = $s3->getObject([
                        'Bucket' => 'hotsreplays-processed',
                        'Key' => $remoteFilename,
                        'RequestPayer' => 'requester',
                    ]);

                    $content = $download['Body'];
                    file_put_contents($tmpFile, $content);
                    $file = new File($tmpFile);

                    $result = $this->replayService->store($file, false);

                    DB::table('hotsdata')->where('id', $row->id)->update(['result' => $result->status]);
                    $this->info("Processed replay $row->id - $remoteFilename, result: $result->status");
                } catch (Exception $e) {
                    $this->info("Error processing replay $remoteFilename: $e");
                    DB::table('hotsdata')->where('id', $row->id)->update(['result' => 'fail']);
                } finally {
                    unlink($tmpFile);
                }
            }
        });
    }
}
