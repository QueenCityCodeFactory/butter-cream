<?php
use Cake\Utility\Inflector;

$this->assign('title', 'Missing Template');
$this->assign('templateName', 'missing_template.ctp');

$isEmail = strpos($file, 'Email/') === 0;

$this->start('subheading');
?>
<?php if ($isEmail): ?>
    <strong>Error: </strong>
    <?= sprintf('The template %s</em> was not found.', h($file)); ?>
<?php else: ?>
    <strong>Error: </strong>
    <?= sprintf('The view for <em>%sController::%s()</em> was not found.', h(Inflector::camelize($this->request->getParam('controller'))), h($this->request->getParam('action'))); ?>
<?php endif ?>
<?php $this->end() ?>

<?php $this->start('file') ?>
<p>
    <?= sprintf('Confirm you have created the file: "%s"', h($file)) ?>
    in one of the following paths:
</p>
<ul>
<?php
    $paths = $this->_paths($this->plugin);
    foreach ($paths as $path):
        if (strpos($path, CORE_PATH) !== false) {
            continue;
        }
        echo sprintf('<li>%s%s</li>', h($path), h($file));
    endforeach;
?>
</ul>
<?php $this->end() ?>
