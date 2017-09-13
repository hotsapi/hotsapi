<?php namespace App\Services;

use App\HotslogsUpload;
use App\Jobs\HotslogsUploadJob;
use App\Replay;
use Exception;
use Guzzle;
use Log;
use Aws\S3\S3Client;

class HotslogsUploader
{
    const BASE_URL = "https://www.hotslogs.com/UploadFile?Source=HotsApi";

    const STATUS_SUCCESS = "success";
    const STATUS_ERROR = "error";
    const STATUS_SKIPPED = "skipped";

    /**
     * @var HotslogsUpload
     */
    private $upload;

    /**
     * HotslogsUploader constructor
     *
     * @param HotslogsUpload $upload
     */
    public function __construct(HotslogsUpload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Queue replay for upload to hotslogs
     *
     * @param Replay $replay
     */
    public static function queueForUpload(Replay $replay)
    {
        if (HotslogsUpload::where('replay_id', $replay->id)->where('status', self::STATUS_SUCCESS)->exists()) {
            // we already successfully uploaded this replay earlier
            return;
        }

        $upload = $replay->hotslogsUploads()->create();
        HotslogsUploadJob::dispatch($upload);
    }

    /**
     * Proceed uploading a queued replay to hotslogs
     *
     * @return bool Whether the upload result is final or upload should be retried due to hotslogs maintenance
     */
    public function upload()
    {
        if (HotslogsUpload::where('replay_id', $this->upload->replay->id)->where('status', self::STATUS_SUCCESS)->exists()) {
            // we already successfully uploaded this replay earlier
            // double check since job can run later than it was created
            $this->setStatus(self::STATUS_SKIPPED);
            return true;
        }

        if ($this->isDuplicate($this->upload->replay->fingerprint)) {
            Log::debug("HotslogsUploader: job " . $this->upload->id . " marked as a duplicate");
            $this->setStatus(self::STATUS_SUCCESS, "duplicate");
            return true;
        }

        try {
            $filename = $this->GUID() . ".StormReplay";
            $this->s3copy(env('AWS_BUCKET'), $this->upload->replay->filename . ".StormReplay", 'heroesreplays', $filename);
            $resp = strtolower(Guzzle::get(self::BASE_URL . "&FileName=$filename")->getBody()->getContents());
            switch ($resp) {
                case "success":
                case "prealphawipe":
                case "computerplayerfound":
                case "trymemode":
                    Log::debug("HotslogsUploader: Uploaded job " . $this->upload->id);
                    $this->setStatus(self::STATUS_SUCCESS, $resp);
                    break;
                case "duplicate":
                    Log::warning("HotslogsUploader: got duplicate status during upload, job " . $this->upload->id);
                    $this->setStatus(self::STATUS_SUCCESS, $resp);
                    break;
                case "maintenance":
                    Log::info("HotslogsUploader: Hotslogs is currently under maintenance");
                    return false;
                default:
                    Log::error("HotslogsUploader: Could not upload file. Unknown status received: $resp");
                    $this->setStatus(self::STATUS_ERROR);
            }
        } catch (Exception $e) {
            Log::error("HotslogsUploader: Could not upload file: $e");
            $this->setStatus(self::STATUS_ERROR);
        }
        return true;
    }

    /**
     * Check whether fingerprint if duplicate with hotslogs API
     *
     * @param string $fingerprint
     * @return bool
     */
    public static function isDuplicate($fingerprint)
    {
        try {
            $result = strtolower(Guzzle::get(self::BASE_URL . "&ReplayHash=$fingerprint")->getBody()->getContents());
            return $result == "duplicate";
        } catch (Exception $e) {
            Log::warning("HotslogsUploader: Error checking for duplicate: $e");
            return false;
        }
    }

    /**
     * Set upload status and save it to DB
     *
     * @param $status
     * @param null $result
     */
    private function setStatus($status, $result = null)
    {
        $this->upload->status = $status;
        $this->upload->result = $result;
        $this->upload->save();
    }

    // Hotslogs public bucket credentials
    const ACCESS_KEY = "AKIAIESBHEUH4KAAG4UA";
    const SECRET_KEY = "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";

    /**
     * Copies files from our S3 bucket to hotslogs
     *
     * @param $sourceBucket
     * @param $sourceName
     * @param $targetBucket
     * @param $targetName
     */
    public function s3copy($sourceBucket, $sourceName, $targetBucket, $targetName)
    {
        // Looks like Laravel can't copy files between buckets so we have to use AWS SDK instead
        $s3 = (new \Aws\Sdk)->createMultiRegionS3([
            'region' => 'us-west-2',           //env('AWS_REGION'),
            'version' => '2006-03-01',
            'credentials' => [
                'key'    => self::ACCESS_KEY,  //env('AWS_KEY'),
                'secret' => self::SECRET_KEY   //env('AWS_SECRET'),
            ]
        ]);

        // Also it appears that we can't directly copy object using AWS API either because
        // we don't have permission to upload file using our account, and don't have permission to
        // get file using hotslogs account. Working to resolve this with Barrett so for now we will copy
        // using this workaround

//        $s3->copyObject([
//            'Bucket'     => $targetBucket,
//            'Key'        => $targetName,
//            'CopySource' => "$sourceBucket/$sourceName",
//            'RequestPayer' => 'requester',
//        ]);

        $content = \Storage::cloud()->get($sourceName);
        $s3->putObject([
            'Bucket'     => $targetBucket,
            'Key'        => $targetName,
            'Body'       => $content
        ]);


    }

    /**
     * Generate a GUID
     * Not strictly according to GUID rules but this is fine in our case
     *
     * @return string
     */
    private static function GUID()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}
