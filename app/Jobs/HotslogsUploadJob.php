<?php

namespace App\Jobs;

use App\HotslogsUpload;
use App\Services\HotslogsUploader;
use Cache;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HotslogsUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const HOTSLOGS_MAINTENANCE = 'hotslogs_maintenance';

    /**
     * @var HotslogsUpload
     */
    private $upload;

    /**
     * Create a new job instance.
     * @param HotslogsUpload $upload
     */
    public function __construct(HotslogsUpload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(Cache::get(self::HOTSLOGS_MAINTENANCE)) {
            $this->requeue();
            return;
        }
        $uploader = new HotslogsUploader($this->upload);
        if (!$uploader->upload()) {
            Cache::set(self::HOTSLOGS_MAINTENANCE, 1, CarbonInterval::hours(1));
            $this->requeue();
        };
    }

    /**
     * Push the current job back to queue
     *
     * @return void
     */
    public function requeue()
    {
        self::dispatch($this->upload)->delay(Carbon::now()->addHours(1));
    }
}
