<?php

namespace App\Commands\Hmvc;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Module extends BaseCommand
{
    protected $group       = 'HMVC';
    protected $name        = 'hmvc:module';
    protected $description = 'Creates a new HMVC module';
    protected $usage       = 'hmvc:module [module_name]';
    protected $arguments   = [
        'module_name' => 'The name of the HMVC module to create',
    ];

    public function run(array $params)
    {
        // Check if HMVC is configured
        if (!$this->isHmvcConfigured()) {
            CLI::error('HMVC is not configured. Please run "php spark hmvc:setup" first.');
            return;
        }

        $moduleName = $params[0] ?? CLI::prompt('Module name', null, 'required');
        $moduleName = ucfirst($moduleName);

        $modulePath = APPPATH . 'Modules/' . $moduleName;

        // Check if the module already exists
        if (is_dir($modulePath)) {
            CLI::error("The module '{$moduleName}' already exists.");
            return;
        }

        // Folder structure to create
        $folders = [
            $modulePath,
            $modulePath . '/Controllers',
            $modulePath . '/Models',
            $modulePath . '/Views',
            $modulePath . '/Config',
        ];

        // Create folders
        foreach ($folders as $folder) {
            mkdir($folder, 0755, true);
            CLI::write('Folder created: ' . CLI::color($folder, 'green'));
        }

        // Create Routes.php file
        $routesFile = $modulePath . '/Config/Routes.php';
        $routesContent = $this->getRoutesContent($moduleName);
        if (file_put_contents($routesFile, $routesContent) !== false) {
            CLI::write('Routes.php file created: ' . CLI::color($routesFile, 'green'));
        } else {
            CLI::error('Failed to create Routes.php file');
        }

        CLI::write('HMVC module created successfully.', 'green');
    }

    private function isHmvcConfigured()
    {
        return file_exists(APPPATH . 'Modules/.hmvc_configured');
    }

    private function getRoutesContent($moduleName)
    {   
        $moduleRouteName = lcfirst($moduleName);
        return <<<EOD
<?php

// Define module routes
\$routes->group('{$moduleRouteName}', ['namespace' => 'Modules\\{$moduleName}\\Controllers'], function(\$routes) {
    \$routes->get('/', '{$moduleName}Controller::index');
    // Add more routes here
});

EOD;
    }
}