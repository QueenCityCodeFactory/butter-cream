<?php
use Cake\Error\Debugger;

$this->assign('title', 'Database Error');
$this->assign('templateName', 'pdo_error.ctp');

$this->start('subheading');
?>
    <strong>Error: </strong>
    <?= h($message); ?>
<?php $this->end() ?>

<?php $this->start('file') ?>
<div class="notice alert alert-warning">
    If you are using SQL keywords as table column names, you can enable identifier
    quoting for your database connection in config/app.php.
</div>
<?php if (!empty($error->queryString)) : ?>
    <div class="notice alert alert-warning">
        <strong>SQL Query: </strong>
    </div>
    <pre><?= h($error->queryString); ?></pre>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <pre><?= h(Debugger::dump($error->params)); ?></pre>
<?php endif; ?>
<?= $this->element('auto_table_warning'); ?>
<?php $this->end() ?>
