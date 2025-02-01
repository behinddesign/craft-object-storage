<?php

declare(strict_types=1);

namespace behinddesign\ObjectStorage;

use Aws\Handler\GuzzleV6\GuzzleHandler;
use Craft;
use craft\flysystem\base\FlysystemFs;
use craft\helpers\App;
use craft\helpers\DateTimeHelper;
use DateTime;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

/**
 * Class ObjectStorageFs
 *
 * @property mixed  $settingsHtml
 * @property string $rootUrl
 */
class Fs extends FlysystemFs
{

    public static function displayName(): string
    {
        return 'Generic S3 Object Storage';
    }

    /**
     * @var string Subfolder to use
     */
    public string $subfolder = '';

    /**
     * @var string AWS key ID
     */
    public string $keyId = '';

    /**
     * @var string AWS key secret
     */
    public string $secret = '';

    /**
     * @var string Bucket to use
     */
    public string $bucket = '';

    /**
     * @var string Region to use
     */
    public string $region = '';

    /**
     * @var string Cache expiration period as a string, like '2 hours'
     */
    public string $expires = '';

    /**
     * @var string endpoint hostname
     */
    public string $endpoint = '';

    /**
    * @var string endpoint type style, e.g. 'path' or 'subdomain'. '' is treated as 'subdomain'.
    */
    public string $endpointType = '';

    /**
     * @var string Set ACL for Uploads
     */
    public string $visibility = 'private';

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['bucket', 'keyId', 'secret', 'endpoint', 'endpointType', 'visibility'], 'required'],
        ]);
    }


    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('behinddesign-object-storage/fsSettings', [
            'fs' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getRootUrl(): ?string
    {
        $rootUrl = parent::getRootUrl();

        if ($this->url === '$OBJECT_STORAGE_HOST' || $this->url === '') {
            $rootUrl =  'https://' . App::parseEnv('$OBJECT_STORAGE_HOST') . '/';
        }

        if ($rootUrl && $this->subfolder) {
            $rootUrl .= rtrim(App::parseEnv($this->subfolder), '/') . '/';
        }

        return $rootUrl;
    }

    /**
     * @inheritdoc
     * @return AwsS3V3Adapter
     */
    protected function createAdapter(): FilesystemAdapter
    {
        $client  = static::client($this->getConfigArray());

        return new AwsS3V3Adapter(
            $client,
            App::parseEnv($this->bucket),
            $this->getParsedSubfolder(),
            new PortableVisibilityConverter(),
            new FinfoMimeTypeDetector()
        );
    }

    protected static function client(array $config = []): S3Client
    {
        return new S3Client($config);
    }

    /**
     * @inheritdoc
     */
    protected function addFileMetadataToConfig(array $config): array
    {
        if (!empty($this->expires) && DateTimeHelper::isValidIntervalString($this->expires)) {
            $expires = new DateTime();
            $now = new DateTime();
            $expires->modify('+' . $this->expires);
            $diff = (int)$expires->format('U') - (int)$now->format('U');
            $config['CacheControl'] = 'max-age=' . $diff;
        }

        return parent::addFileMetadataToConfig($config);
    }


    private function getParsedSubfolder(): string
    {
        if ($this->subfolder && ($subfolder = rtrim(App::parseEnv($this->subfolder), '/')) !== '') {
            return $subfolder . '/';
        }

        return '';
    }


    /**
     * Get the config array for AWS Clients.
     */
    protected function getConfigArray(): array
    {
        $endpoint = App::parseEnv($this->endpoint);

        if (!str_contains($endpoint, 'https')) {
            $endpoint = 'https://' .  $endpoint;
        }

        return [
            'version'      => 'latest',
            'region'       => App::parseEnv($this->region),
            'endpoint'     => $endpoint,
            'request_checksum_calculation' => 'when_required',
            'response_checksum_validation' => 'when_required',
            'use_path_style_endpoint' => App::parseEnv($this->endpointType) === 'path',
            'http_handler' => new GuzzleHandler(Craft::createGuzzleClient()),
            'credentials'  => [
                'key'    => App::parseEnv($this->keyId),
                'secret' => App::parseEnv($this->secret)
            ]
        ];
    }


    /**
     * Returns the visibility setting for the Fs.
     *
     * @return string
     */
    protected function visibility(): string
    {
        return App::parseEnv($this->visibility) === 'public' ? Visibility::PUBLIC : Visibility::PRIVATE;
    }

    protected function invalidateCdnPath(string $path): bool
    {
        return true;
    }
}
