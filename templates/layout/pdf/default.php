<?php
use Cake\Core\Configure;
use Cake\Utility\Inflector;

/**
 * Load assets - These assets need provided by the application.
 */
if (Configure::read('debug') === true) {
    $this->prepend('css', $this->Html->css(['app.css?cb=' . Configure::read('CacheBuster.cssCB')], ['fullBase' => true]));
} else {
    $this->prepend('css', $this->Html->css(['app.min.css?cb=' . Configure::read('CacheBuster.cssCB')], ['fullBase' => true]));
}

if ($this instanceof \CakePdf\View\PdfView) {
    $this->renderer()->header([
        'left' => $this->fetch('pageNumbers') ? 'Page [page] of [toPage]' : '',
        'center' => $this->fetch('name') ? $this->fetch('name') : '',
        'right' => $this->fetch('header') ? $this->fetch('header') : ''
    ]);
}

/**
 * Default `body` block.
 */
$bodyClasses = [Configure::read('App.environment'), $this->request->getParam('controller'), $this->request->getParam('action'), 'pdf-page'];
$skinClass = $this->request->getSession()->read('Auth.User.theme');
if (!empty($skinClass)) {
    $bodyClasses[] = $skinClass;
}

$bodyAttributes = $this->get('bodyAttributes');

if (empty($bodyAttributes)) {
    $bodyAttributes = ['class' => $bodyClasses];
}

if (!empty($bodyAttributes['class']) && is_array($bodyAttributes['class'])) {
    $bodyAttributes['class'] = array_unique($bodyAttributes['class']);
}

if (!$this->fetch('body_start')) {
    $this->assign('body_start', $this->Html->tag('body', null, $bodyAttributes));
}

if (!$this->fetch('body_end')) {
    $this->assign('body_end', '</body>');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $this->fetch('title') ?></title>
        <?= $this->fetch('css') ?>
    </head>
    <?= $this->fetch('body_start') ?>
        <div class="print-container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </div>
        </div>
    <?= $this->fetch('body_end') ?>
</html>
