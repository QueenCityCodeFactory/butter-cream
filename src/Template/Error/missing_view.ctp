<?php
use Cake\Core\Plugin;
use Cake\Core\Configure;
$namespace = Configure::read('App.namespace');

$pluginPath = Configure::read('App.paths.plugins.0');
$pluginDot = empty($plugin) ? null : $plugin . '.';

if (empty($plugin)) {
    $filePath = APP_DIR . DIRECTORY_SEPARATOR;
    $namespace = str_replace('/', '\\', $plugin);
}
if (!empty($plugin) && Plugin::loaded($plugin)) {
    $filePath = Plugin::classPath($plugin);
}
if (!empty($plugin) && !Plugin::loaded($plugin)) {
    $filePath = $pluginPath . h($plugin) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
}

$this->assign('title', 'Missing View');
$this->assign('templateName', 'missing_view.ctp');

$this->start('subheading');
?>
    <strong>Error: </strong>
    <em><?= h($pluginDot . $class) ?></em> could not be found.
    <?php if (!empty($plugin) && !Plugin::loaded($plugin)): ?>
    Make sure your plugin <em><?= h($plugin) ?></em> is in the <?= h($pluginPath) ?> directory and was loaded.
    <?php endif ?>
    <?= $this->element('plugin_class_error', ['pluginPath' => $pluginPath]) ?>

<?php $this->end() ?>

<?php $this->start('file') ?>
<div class="error alert alert-info">
    <strong>Error: </strong>
    <?= sprintf('Create the class <em>%s</em> below in file: %s', h($class), $filePath . 'View' . DIRECTORY_SEPARATOR . h($class) . '.php'); ?>
</div>
<?php
$code = <<<PHP
<?php
namespace {$namespace}\View;

use Cake\View\View;

class {$class}View extends View
{

}
PHP;
?>
<div class="code-dump"><?php highlight_string($code) ?></div>
<?php $this->end() ?>
