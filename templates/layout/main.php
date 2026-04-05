<?php
use Cake\Core\Configure;
use Cake\Utility\Inflector;

/**
 * Default `html` block.
 */
if (!$this->fetch('html')) {
    $this->assign('html', $this->Html->tag('html', null, ['lang' => Configure::read('App.language')]));
}

/**
 * Default title
 */
if (!$this->fetch('title')) {
    $this->assign('title', Configure::read('App.title') . ': ' . Inflector::humanize(Inflector::underscore($this->getRequest()->getParam('controller'))));
}

/**
 * Default `meta` block.
 */
if (!$this->fetch('meta')) {
    $this->start('meta');
    echo $this->Html->meta('viewport', 'width=device-width, initial-scale=1');
    echo $this->Html->meta('description', '');
    echo $this->Html->meta('author', '');
    $this->end();
}

/**
 * Default `Google Analytics` tracking code.
 */
if (!$this->fetch('google_analytics')) {
    $this->assign('google_analytics', $this->element('google_analytics', [], ['ignoreMissing' => true, 'plugin' => false]));
}

/**
 * Default `body` block.
 */
$bodyClasses = [Configure::read('App.environment'), $this->getRequest()->getParam('controller'), $this->getRequest()->getParam('action')];
if ($this->get('sessionMonitor') === true) {
    $bodyClasses[] = 'session-monitor';
}
$skinClass = $this->getRequest()->getSession()->read('Auth.theme');
if (!empty($skinClass)) {
    $bodyClasses[] = $skinClass;
}

$extraBodyClasses = $this->get('extraBodyClasses', []);
if (!empty($extraBodyClasses)) {
    $bodyClasses = array_merge($bodyClasses, $extraBodyClasses);
}

$bodyAttributes = $this->get('bodyAttributes');

if (empty($bodyAttributes)) {
    $bodyAttributes = ['class' => $bodyClasses];
}

// Session monitor config via data attributes (no inline script globals)
if ($this->get('sessionMonitor') === true) {
    $bodyAttributes['data-session-timeout'] = Configure::read('Session.timeout');
    $bodyAttributes['data-last-access-time'] = $this->getRequest()->getSession()->read('SessionTimeoutFilter.lastAccess');
    $bodyAttributes['data-session-username'] = $this->getRequest()->getSession()->read('Auth.username');
    $bodyAttributes['data-session-user-email'] = $this->getRequest()->getSession()->read('Auth.email');
}

if (!empty($bodyAttributes['class']) && is_array($bodyAttributes['class'])) {
    $bodyAttributes['class'] = array_unique($bodyAttributes['class']);
}

if (!$this->fetch('body.start')) {
    $this->assign('body.start', $this->Html->tag('body', null, $bodyAttributes));
}

if (!$this->fetch('body.end')) {
    $this->assign('body.end', '</body>');
}

/**
 * Load assets - These assets need provided by the application.
 */
if (Configure::read('debug') === true) {
    $this->prepend('css', $this->Html->css(['app.css?cb=' . Configure::read('CacheBuster.cssCB')]));
    $this->prepend('script', $this->Html->script(['app.js?cb=' . Configure::read('CacheBuster.jsCB')]));
} else {
    $this->prepend('css', $this->Html->css(['app.min.css?cb=' . Configure::read('CacheBuster.cssCB')]));
    $this->prepend('script', $this->Html->script(['app.min.js?cb=' . Configure::read('CacheBuster.jsCB')]));
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
    <?= $this->fetch('body.start') ?>
        <?= $this->fetch('header') ?>
        <?= $this->fetch('content') ?>
        <?= $this->fetch('footer') ?>
        <?= $this->fetch('script') ?>
        <?= $this->fetch('modal') ?>
    <?= $this->fetch('body.end') ?>
</html>
