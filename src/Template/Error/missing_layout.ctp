<?php
$this->layout = 'dev_error';

$this->assign('title', 'Missing Layout');
$this->assign('templateName', 'missing_layout.ctp');

$this->start('subheading');
?>
    <strong>Error: </strong>
    The layout file <em><?= h($file) ?></em> can not be found or does not exist.
<?php $this->end() ?>

<?php $this->start('file') ?>
<p>
    Confirm you have created the file: <?= h($file) ?> in one of the following paths:
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
