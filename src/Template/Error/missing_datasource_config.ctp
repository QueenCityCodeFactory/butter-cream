<?php
$this->assign('title', 'Missing Datasource Configuration');
$this->assign('templateName', 'missing_datasource_config.ctp');

$this->start('subheading');
?>
    <strong>Error: </strong>
    <?php if (isset($name)): ?>
        The datasource configuration <em><?= h($name) ?></em> was not found in config<?= DIRECTORY_SEPARATOR . 'app.php' ?>.
    <?php else: ?>
        <?= h($message) ?>
    <?php endif; ?>
<?php $this->end() ?>
