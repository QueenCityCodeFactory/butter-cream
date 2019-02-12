<?php
use Cake\Core\Configure;

$pluginPath = Configure::read('App.paths.plugins.0');

$this->assign('title', 'Missing Plugin');
$this->assign('templateName', 'missing_plugin.ctp');

$this->start('subheading');
?>
    <strong>Error: </strong>
    The application is trying to load a file from the <em><?= h($plugin) ?></em> plugin.
    <br>
    <br>
    Make sure your plugin <em><?= h($plugin) ?></em> is in the <?= h($pluginPath) ?> directory and was loaded.
<?php $this->end() ?>

<?php $this->start('file') ?>
<?php
$code = <<<PHP
<?php
Plugin::load('{$plugin}');
PHP;

?>
<div class="code-dump"><?php highlight_string($code) ?></div>

<div class="notice alert alert-warning">
    <strong>Loading all plugins: </strong>
    <?= sprintf('If you wish to load all plugins at once, use the following line in your %s file', 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php'); ?>
</div>

<?php
$code = <<<PHP
<?php
Plugin::loadAll();
PHP;
?>
<div class="code-dump"><?php highlight_string($code) ?></div>
<?php $this->end() ?>
