<?php

namespace behinddesign\ObjectStorage;

/**
 * Class S3Client
 *
 * @package behinddesign\ObjectStorage
 */
class S3Client extends \Aws\S3\S3Client
{
    const DEFAULT_MULTIPART_THRESHOLD = 100000000;

    /**
     * @see S3ClientInterface::upload()
     */
    public function upload(
        $bucket,
        $key,
        $body,
        $acl = 'private',
        array $options = []
    ) {

        $defaults = [
            'mup_threshold' => self::DEFAULT_MULTIPART_THRESHOLD,
        ];

        return $this
            ->uploadAsync($bucket, $key, $body, $acl, $options + $defaults)
            ->wait();
    }
}
