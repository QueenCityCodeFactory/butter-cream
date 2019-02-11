<?php
use Cake\Core\Plugin;

if (empty($plugin)) {
    return;
}

echo '<br><br>';

if (!Plugin::loaded($plugin)):
    echo sprintf('Make sure your plugin <em>%s</em> is in the %s directory and was loaded.', h($plugin), $pluginPath);
else:
    echo sprintf('Make sure your plugin was loaded from %s and Composer is able to autoload its classes, see %s and %s',
        '<em>config' . DIRECTORY_SEPARATOR . 'bootstrap.php</em>',
        '<a href="https://book.cakephp.org/3.0/en/plugins.html#loading-a-plugin">Loading a plugin</a>',
        '<a href="https://book.cakephp.org/3.0/en/plugins.html#autoloading-plugin-classes">Plugins - autoloading plugin classes</a>'
    );
endif;

?>
