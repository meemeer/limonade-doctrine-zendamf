<?php  

define('DOCTRINE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'lib');
define('MODELS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'models');
define('FIXTURES_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'fixtures');
define('MIGRATIONS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'migrations');
define('YAML_SCHEMA_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'schemas');
define('STORAGE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'dev.db');
define('DSN', 'sqlite:'.STORAGE_PATH);

require_once(DOCTRINE_PATH . DIRECTORY_SEPARATOR . 'Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$manager = Doctrine_Manager::getInstance();
$manager->openConnection(DSN, 'doctrine');

$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);
$manager->setAttribute(Doctrine_Core::ATTR_EXPORT, Doctrine_Core::EXPORT_ALL);
$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);

Doctrine_Core::loadModels('models');
//Doctrine_Core::setModelsDirectory('models');

$config = array(
    'data_fixtures_path'  =>  FIXTURES_PATH,
    'models_path'         =>  MODELS_PATH,
    'migrations_path'     =>  MIGRATIONS_PATH,
    'yaml_schema_path'    =>  YAML_SCHEMA_PATH,
    'generate_models_options' => array(
        'pearStyle' => true,
        'generateTableClasses' => true,
        'baseClassPrefix' => 'Base',
        'baseClassesDirectory' => null,
    )
);

$cli = new Doctrine_Cli($config);
$cli->run($_SERVER['argv']);
