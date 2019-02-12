<?php
$this->assign('title', 'Fatal Error');
$this->assign('templateName', 'fatal_error.ctp');

$this->start('subheading');
?>
    <strong>Error: </strong>
    <?= h($error->getMessage()) ?>
    <br>

    <strong>File</strong>
    <?= h($error->getFile()) ?>
    <br>
    <strong>Line: </strong>
    <?= h($error->getLine()) ?>
<?php $this->end() ?>

<?php
$this->start('file');
if (extension_loaded('xdebug')):
    xdebug_print_function_stack();
endif;
$this->end();
