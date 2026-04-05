<?php

/**
 * Test suite bootstrap for ButterCream.
 *
 * This function is used to find the location of CakePHP whether CakePHP
 * has been installed as a dependency of the plugin, or the plugin is itself
 * installed as a dependency of an application.
 */
declare(strict_types=1);

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);

    throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);

chdir($root);

require_once $root . '/vendor/autoload.php';

Configure::write('App.encoding', 'UTF-8');
Configure::write('App.fullBaseUrl', 'http://localhost');

if (!getenv('DB_URL')) {
    ConnectionManager::setConfig('test', [
        'url' => 'sqlite:///:memory:',
    ]);
}
