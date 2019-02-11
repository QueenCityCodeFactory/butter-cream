<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;
$this->assign('title', $code . ': ' . $message);
$this->assign('templateName', 'error400.ctp');

if (Configure::read('debug')):
    $this->start('file');

    if (!empty($error->queryString)) : ?>
        <p class="notice">
            <strong>SQL Query: </strong>
            <?= h($error->queryString) ?>
        </p>
    <?php
    endif;
    if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?= Debugger::dump($error->params) ?>
    <?php
    endif;

    if (extension_loaded('xdebug')):
        xdebug_print_function_stack();
    endif;

    $this->end();
endif;
?>

<div class="row">
    <div class="col-md-12">
        <div class="bg-bamboo text-center">
            <div class="error-bubble">
                <?= $code ?> - <?= $message ?>
            </div>
        </div>
    </div>
</div>
