<?php

namespace App\Commands\Hmvc;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Setup extends BaseCommand
{
    protected $group       = 'HMVC';
    protected $name        = 'hmvc:setup';
    protected $description = 'Configures the basic structure for HMVC';

    public function run(array $params)
    {
        if ($this->isHmvcConfigured()) {
            CLI::write('HMVC configuration is already set up.', 'yellow');
            return;
        }

        CLI::write('Configuring HMVC structure...', 'green');

        $this->createModulesDirectory();
        $this->modifyRoutesFile();
        $this->modifyAutoloadFile();
        $this->createHmvcHelperFile();

        $this->markHmvcAsConfigured();

        CLI::write('HMVC configuration completed.', 'green');
    }

    private function isHmvcConfigured()
    {
        return file_exists(APPPATH . 'Modules/.hmvc_configured');
    }

    private function markHmvcAsConfigured()
    {
        file_put_contents(APPPATH . 'Modules/.hmvc_configured', date('Y-m-d H:i:s'));
    }

    private function createModulesDirectory()
    {
        $modulesDir = APPPATH . 'Modules';
        if (!is_dir($modulesDir)) {
            mkdir($modulesDir, 0755, true);
            CLI::write('Modules directory created: ' . CLI::color($modulesDir, 'green'));
        }
    }

    private function modifyRoutesFile()
    {
        $routesFile = APPPATH . 'Config/Routes.php';
        $routesContent = file_get_contents($routesFile);
        $hmvcRoutes = $this->getHmvcRoutesContent();

        if (strpos($routesContent, '// HMVC Routes') === false) {
            $routesContent .= $hmvcRoutes;
            file_put_contents($routesFile, $routesContent);
            CLI::write('Routes.php modified.', 'green');
        }
    }

    private function modifyAutoloadFile()
    {
        $autoloadFile = APPPATH . 'Config/Autoload.php';
        $autoloadContent = file_get_contents($autoloadFile);
        $autoloadContent = str_replace(
            "public \$psr4 = [",
            "public \$psr4 = [\n        'Modules' => APPPATH . 'Modules',",
            $autoloadContent
        );
        $autoloadContent = str_replace(
            "public \$helpers = [",
            "public \$helpers = ['Hmvc',",
            $autoloadContent
        );
        file_put_contents($autoloadFile, $autoloadContent);
        CLI::write('Autoload.php modified.', 'green');
    }

    private function createHmvcHelperFile()
    {
        $helperContent = $this->getHelperContent();
        $helperFile = APPPATH . 'Helpers/Hmvc_helper.php';
        file_put_contents($helperFile, $helperContent);
        CLI::write('Hmvc_helper.php created.', 'green');
    }

    private function getHmvcRoutesContent()
    {
        return <<<EOD

// HMVC Routes
\$modules = scandir(APPPATH . 'Modules');
foreach (\$modules as \$module) {
    if (\$module === '.' || \$module === '..') continue;
    if (is_dir(APPPATH . 'Modules/' . \$module)) {
        \$routesPath = APPPATH . 'Modules/' . \$module . '/Config/Routes.php';
        if (file_exists(\$routesPath)) {
            require \$routesPath;
        }
    }
}
EOD;
    }

    private function getHelperContent()
    {
        return <<<EOD
<?php

if (!function_exists('hmvcView')) {
    function hmvcView(string \$module, string \$view, array \$data = [], array \$options = [])
    {
        \$modulesPath = APPPATH . 'Modules/';
        \$viewPath = "{\$module}/Views/{\$view}";
        \$fullPath = \$modulesPath . \$viewPath . '.php';
        if (!file_exists(\$fullPath)) {
            throw new RuntimeException("HMVC view not found: {\$fullPath}");
        }
        \$adjustedViewPath = '../Modules/' . \$viewPath;
        return view(\$adjustedViewPath, \$data, \$options);
    }
}
EOD;
    }
}