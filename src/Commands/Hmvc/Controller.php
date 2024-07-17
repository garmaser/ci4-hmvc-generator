<?php

namespace App\Commands\Hmvc;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\GeneratorTrait;

class Controller extends BaseCommand
{
    use GeneratorTrait;

    protected $group       = 'HMVC';
    protected $name        = 'hmvc:controller';
    protected $description = 'Creates a new controller for HMVC';
    protected $usage       = 'hmvc:controller [module] [name] [options]';
    protected $arguments   = [
        'module' => 'The name of the HMVC module',
        'name'   => 'The name of the controller class',
    ];

    public function run(array $params)
    {
        // Check if HMVC is configured
        if (!$this->isHmvcConfigured()) {
            CLI::error('HMVC is not configured. Please run "php spark hmvc:setup" first.');
            return;
        }

        $module = $params[0] ?? CLI::prompt('Module name', null, 'required');
        $module = ucfirst($module);
        // Check if the module exists
        if (!$this->moduleExists($module)) {
            CLI::error("The module '{$module}' does not exist. Please create the module first with 'php spark hmvc:module {$module}'.");
            return;
        }

        $name = $params[1] ?? CLI::prompt('Controller name', null, 'required');
        $name = ucfirst($name);

        // Validate the controller name format
        if (!$this->isValidControllerName($name)) {
            CLI::error("The controller name '{$name}' is not valid. It must start with a capital letter and contain only letters.");
            return;
        }       

        $path = APPPATH . 'Modules/' . $module . '/Controllers/' . $name . 'Controller.php';

        // Check if the controller already exists
        if (file_exists($path)) {
            CLI::error("The controller '{$name}Controller' already exists in the '{$module}' module.");
            return;
        }

        $template = $this->getControllerTemplate($module, $name);

        $this->generateFile($path, $template);

        CLI::write('HMVC Controller created: ' . CLI::color($path, 'green'));
    }

    private function isHmvcConfigured()
    {
        return file_exists(APPPATH . 'Modules/.hmvc_configured');
    }

    private function moduleExists($module)
    {
        return is_dir(APPPATH . 'Modules/' . $module);
    }

    private function isValidControllerName($name)
    {
        return preg_match('/^[A-Z][a-zA-Z]*$/', $name);
    }

    private function getControllerTemplate($module, $name)
    {
        return <<<EOD
<?php

namespace Modules\\{$module}\\Controllers;

use App\Controllers\BaseController;

class {$name}Controller extends BaseController
{   
    private \$module = '{$module}'; // Module name

    public function index()
    {
        // Example data to send to the view
        \$data = [
            'title' => 'Welcome to {$module} module',
            'message' => 'This is an HMVC example in CodeIgniter 4'
        ];

        // Example of how to call a view with hmvcView
        // Syntax: hmvcView(string \$module, string \$view, array \$data = [], array \$options = [])
        //
        // \$module: Name of the module (folder) where the view is located
        // \$view: Name of the view file without the .php extension
        // \$data: Associative array with the data to pass to the view (optional)
        // \$options: Array of additional options for the view (optional)
        //
        // Usage example:
        return hmvcView(\$this->module, 'index', \$data, ['cache' => 300]);
    }
}
EOD;
    }
}