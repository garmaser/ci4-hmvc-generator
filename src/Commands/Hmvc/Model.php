<?php

namespace App\Commands\Hmvc;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\GeneratorTrait;

class Model extends BaseCommand
{
    use GeneratorTrait;

    protected $group       = 'HMVC';
    protected $name        = 'hmvc:model';
    protected $description = 'Creates a new model for HMVC';
    protected $usage       = 'hmvc:model [module] [name] [table] [options]';
    protected $arguments   = [
        'module' => 'The name of the HMVC module',
        'name'   => 'The name of the model class',
        'table'  => 'The name of the table associated with the model (optional)',
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

        $name = $params[1] ?? CLI::prompt('Model name', null, 'required');
        $name = ucfirst($name);
        
        // Validate the model name format
        if (!$this->isValidModelName($name)) {
            CLI::error("The model name '{$name}' is not valid. It must start with a capital letter and contain only letters.");
            return;
        }

        $path = APPPATH . 'Modules/' . $module . '/Models/' . $name . 'Model.php';

        // Check if the model already exists
        if (file_exists($path)) {
            CLI::error("The model '{$name}Model' already exists in the '{$module}' module.");
            return;
        }

        // Ask for the table name (optional)
        $table = $params[2] ?? CLI::prompt('Table name (optional)', null, 'required');

        $template = $this->getModelTemplate($module, $name, $table);

        $this->generateFile($path, $template);

        CLI::write('HMVC Model created: ' . CLI::color($path, 'green'));
    }

    private function isHmvcConfigured()
    {
        return file_exists(APPPATH . 'Modules/.hmvc_configured');
    }

    private function moduleExists($module)
    {
        return is_dir(APPPATH . 'Modules/' . $module);
    }

    private function isValidModelName($name)
    {
        return preg_match('/^[A-Z][a-zA-Z]*$/', $name);
    }

    private function getModelTemplate($module, $name, $table)
    {
        $tableName = $table ? "'{$table}'" : "''";
        
        return <<<EOD
<?php

namespace Modules\\{$module}\\Models;

use CodeIgniter\Model;

class {$name}Model extends Model
{
    protected \$table      = {$tableName}; // Table name
    protected \$primaryKey = 'id';

    protected \$useAutoIncrement = true;

    protected \$returnType     = 'array';
    protected \$useSoftDeletes = true;

    protected \$allowedFields = [];

    // Dates
    protected \$useTimestamps = true;
    protected \$dateFormat    = 'datetime';
    protected \$createdField  = 'created_at';
    protected \$updatedField  = 'updated_at';
    protected \$deletedField  = 'deleted_at';

    // Validation
    protected \$validationRules      = [];
    protected \$validationMessages   = [];
    protected \$skipValidation       = false;
    protected \$cleanValidationRules = true;

    // Callbacks
    protected \$allowCallbacks = true;
    protected \$beforeInsert   = [];
    protected \$afterInsert    = [];
    protected \$beforeUpdate   = [];
    protected \$afterUpdate    = [];
    protected \$beforeFind     = [];
    protected \$afterFind      = [];
    protected \$beforeDelete   = [];
    protected \$afterDelete    = [];
}
EOD;
    }
}