<?php
use Cake\Core\Configure;
use Cake\Utility\Inflector;

$this->prepend('css', $this->Html->css(['app.min'], ['fullBase' => true]));
if ($this instanceof \CakePdf\View\PdfView) {
    $this->renderer()->header([
        'left' => $this->fetch('pageNumbers') ? 'Page [page] of [toPage]' : '',
        'center' => $this->fetch('name') ? $this->fetch('name') : '',
        'right' => $this->fetch('header') ? $this->fetch('header') : ''
    ]);
}

if (!$this->fetch('body_tag_attrs')) {
    $bodyClasses = [Configure::read('App.environment'),  strtolower(Inflector::slug(Inflector::dasherize($this->request->getParam('controller')))),  strtolower(Inflector::slug(Inflector::dasherize($this->request->getParam('action')))), 'pdf'];
    $skinClass = $this->request->getSession()->read('Auth.User.theme');
    if (!empty($skinClass)) {
        $bodyClasses[] = $skinClass;
    }

    $this->assign('body_tag_attrs', ' class="' . join(' ', $bodyClasses) . '"');
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
    <body <?= $this->fetch('body_tag_attrs'); ?>>
        <div class="print-container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
