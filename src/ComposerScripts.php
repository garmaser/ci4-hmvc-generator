<?php

namespace garmaser\Ci4HmvcGenerator;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class ComposerScripts
{
    public static function postInstall(Event $event)
    {
        self::copyCommands($event);
    }

    public static function postUpdate(Event $event)
    {
        self::copyCommands($event);
    }

    private static function copyCommands(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $baseDir = dirname($vendorDir);

        $sourceDir = $baseDir . '/vendor/garmaser/ci4-hmvc-generator/src/Commands/Hmvc';
        $targetDir = $baseDir . '/app/Commands/Hmvc';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        foreach (glob($sourceDir . '/*.php') as $file) {
            $filename = basename($file);
            copy($file, $targetDir . '/' . $filename);
        }

        $event->getIO()->write('HMVC commands have been copied to ' . $targetDir);
    }
}