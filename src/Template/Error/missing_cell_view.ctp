<?php
use Cake\Utility\Inflector;

$this->layout = 'dev_error';

$this->assign('templateName', 'missing_cell_view.ctp');
$this->assign('title', 'Missing Cell View');

$this->start('subheading');
printf('The view for <em>%sCell</em> was not be found.', h(Inflector::camelize($name)));
$this->end();

$this->start('file');
?>
<p>
    Confirm you have created the file: "<?= h($file . $this->_ext) ?>"
    in one of the following paths:
</p>
<ul>
<?php
    $paths = $this->_paths($this->plugin);
    foreach ($paths as $path):
        if (strpos($path, CORE_PATH) !== false) {
            continue;
        }
        echo sprintf('<li>%sCell/%s/%s</li>', h($path), h($name), h($file . $this->_ext));
    endforeach;
?>
</ul>
<?php $this->end(); ?>
