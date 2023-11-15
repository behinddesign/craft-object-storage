<?php

namespace behinddesign\ObjectStorage;

use craft\events\RegisterComponentTypesEvent;
use craft\services\Fs as FsService;
use yii\base\Event;

/**
 * behinddesign Object Storage plugin
 * provides a behinddesign\ObjectStorage\Fs
 */
class Plugin extends \craft\base\Plugin
{
    public string $schemaVersion = '2.0';

    public function init(): void
    {
        parent::init();

        Event::on(
            FsService::class,
            FsService::EVENT_REGISTER_FILESYSTEM_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Fs::class;
            }
        );
    }
}
