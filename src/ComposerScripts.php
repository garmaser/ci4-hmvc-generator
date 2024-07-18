private static function copyCommands(Event $event)
{
    $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
    $baseDir = dirname($vendorDir);
    
    $sourceDir = $baseDir . '/vendor/garmaser/ci4-hmvc-generator/src/Commands/Hmvc';
    $targetDir = $baseDir . '/app/Commands/Hmvc';
    
    $event->getIO()->write('Source directory: ' . $sourceDir);
    $event->getIO()->write('Target directory: ' . $targetDir);
    
    if (!is_dir($sourceDir)) {
        $event->getIO()->write('Source directory does not exist!');
        return;
    }
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    foreach (glob($sourceDir . '/*.php') as $file) {
        $filename = basename($file);
        $event->getIO()->write('Copying file: ' . $filename);
        if (copy($file, $targetDir . '/' . $filename)) {
            $event->getIO()->write('Successfully copied: ' . $filename);
        } else {
            $event->getIO()->write('Failed to copy: ' . $filename);
        }
    }
    
    $event->getIO()->write('HMVC commands have been copied to ' . $targetDir);
}