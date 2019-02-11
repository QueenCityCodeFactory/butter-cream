<?php
use Cake\I18n\Time;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\View\Exception\MissingElementException;

/**
 * Default `html` block.
 */
if (!$this->fetch('html')) {
    $this->start('html');
    printf('<html lang="%s">', Configure::read('App.language'));
    $this->end();
}

$this->assign('title', Configure::read('App.title') . ': ' . Inflector::humanize(Inflector::underscore($this->request->getParam('controller'))));

/**
 * Default `footer` block.
 */
if (!$this->fetch('meta')) :
    $this->start('meta'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
<?php $this->end();
endif;

/**
 * Default `Google Analytics` tracking code.
 */
if (!$this->fetch('google_analytics')) {
    $this->start('google_analytics');
        try {
            echo $this->element('google_analytics');
        } catch (MissingElementException $e) {
            // Only load if it exists
        }
    $this->end();
}

/**
 * Load assets
 */
if (Configure::read('debug') === true) {
    $this->prepend('css', $this->Html->css(['app.css?cb=' . Configure::read('CacheBuster.cssCB')]));
    $this->prepend('script', $this->Html->script(['app.js?cb=' . Configure::read('CacheBuster.jsCB')]));
} else {
    $this->prepend('css', $this->Html->css(['app.min.css?cb=' . Configure::read('CacheBuster.cssCB')]));
    $this->prepend('script', $this->Html->script(['app.min.js?cb=' . Configure::read('CacheBuster.jsCB')]));
}

/**
 * Body Tag attributes
 */
if (!$this->fetch('body_tag_attrs')) {
    $bodyClasses = [Configure::read('App.environment'), $this->request->getParam('controller'), $this->request->getParam('action')];
    $this->assign('body_tag_attrs', ' class="' . join(' ', $bodyClasses) . '"');
}
?>

<!doctype html>
<?= $this->fetch('html') ?>
    <head>
        <?= $this->Html->charset() ?>
        <?= $this->fetch('meta') ?>
        <title><?= $this->fetch('title') ?></title>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('google_analytics') ?>
    </head>
    <body<?= $this->fetch('body_tag_attrs'); ?>>
        <?= $this->fetch('header') ?>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-7 col-md-5 col-lg-4">
                    <?= $this->fetch('content') ?>
                </div>
            </div>
        </div>
        <?= $this->fetch('footer') ?>
        <?= $this->fetch('script') ?>
    </body>
</html>
