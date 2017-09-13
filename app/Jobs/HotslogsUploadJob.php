<?php

namespace App\Jobs;

use App\HotslogsUpload;
use App\Services\HotslogsUploader;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HotslogsUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $uploader = new HotslogsUploader($this->upload);
        if (!$uploader->upload()) {
            // todo: better maintenance handling
            self::dispatch($this->upload)->delay(Carbon::now()->addHours(1));
        };
    }
}
