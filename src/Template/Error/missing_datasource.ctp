<?php
$pluginDot = empty($plugin) ? null : $plugin . '.';

$this->layout = 'dev_error';

$this->assign('title', 'Missing Datasource');
$this->assign('templateName', 'missing_datasource.ctp');

$this->start('subheading');
?>
<strong>Error: </strong>
Datasource class <em><?= h($pluginDot . $class) ?></em> could not be found.
    <?php if (isset($message)):  ?>
        <?= h($message); ?>
    <?php endif; ?>
<?php $this->end() ?>
