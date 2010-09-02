<?php
require_once 'lib/limonade.php';

function configure()
{
  $env = preg_match('/^localhost(\:\d+)?/', $_SERVER['HTTP_HOST']) ? ENV_DEVELOPMENT : ENV_PRODUCTION;
  $dsn = $env == ENV_PRODUCTION ? 'sqlite:storage/prod.db' : 'sqlite:storage/dev.db';
  option('env', $env);
  option('dsn', $dsn);

  require_once option('lib_dir').'/Doctrine.php';
  spl_autoload_register(array('Doctrine', 'autoload'));
  spl_autoload_register(array('Doctrine', 'modelsAutoload'));
  $manager = Doctrine_Manager::getInstance();
  $manager->openConnection(option('dsn'), 'doctrine');
  $manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);
  $manager->setAttribute(Doctrine_Core::ATTR_EXPORT, Doctrine_Core::EXPORT_ALL);
  $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
  $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
  $manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
  Doctrine_Core::loadModels('models');
  require_once_dir('models');
}

dispatch('/', helloworld);
function helloworld()
{
  /*
  $post = new Post();
  $post['title'] = 'title3';
  $post['body'] = 'body3';
  $post->save();
   */
  return 'helloworld';
}

dispatch_post('/gateway', gateway);
function gateway()
{
  ini_set('display_errors', 1);
  $server = new Zend_Amf_Server();
  $server->setProduction(true);
  $server->addFunction('helloworld');
  $server->addFunction('getPosts');
  $response = $server->handle();
  return $response;
}
function getPosts()
{
  $q = new Doctrine_RawSql();
  $q->select('{p.*}')->from('post p')->addComponent('p', 'Post p');
  $posts = $q->execute();
  return $posts->getData();
}


run();

?>
