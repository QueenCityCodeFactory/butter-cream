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
    echo $this->Html->meta('viewport', 'width=device-width, initial-scale=1, shrink-to-fit=no');
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
$skinClass = $this->getRequest()->getSession()->read('Auth.User.theme');
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

if (!(isset($noModalScript) && $noModalScript === true)) :
$this->append('script'); ?>
<script>
    var sessionTimeout = "<?= Configure::read('Session.timeout') ?>";
    var lastAccessTime = "<?= $this->getRequest()->getSession()->read('SessionTimeoutFilter.lastAccess') ?>";
    var sessionUserName = "<?= $this->getRequest()->getSession()->read('Auth.username') ?>";
    var sessionUserEmail = "<?= $this->getRequest()->getSession()->read('Auth.email') ?>";
</script>
<script id="modal-template" type="text/x-jsrender">
    {{if id}}
    <div id="{{:id}}" class="modal fade">
    {{else}}
    <div class="modal fade">
    {{/if}}
         <div class="modal-dialog{{:modalSizeClass}}">
              <div class="modal-content">
                   <div class="modal-header">
                        {{if closeButton}}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span>
                        </button>
                        {{/if}}
                        <h4 class="modal-title">{{:title}}</h4>
                   </div>
                   <div class="modal-body">
                        {{if login}}
                        <div id="expired-alert-message" class="alert alert-danger">Your session has expired due to inactivity.</div>
                        <div class="form-group text">
                            <label class="control-label" for="username">Username or Email</label>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-at"></i>
                                    </span>
                                </span>
                                <input type="text" name="username" autocomplete="off" id="expired-username" class="form-control">
                            </div>
                        </div>
                        <div class="form-group password">
                            <label class="control-label" for="password">Password</label>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-key"></i>
                                    </span>
                                </span>
                                <input type="password" name="password" autocomplete="off" id="expired-password" class="form-control">
                            </div>
                        </div>
                        {{else}}
                            {{:html}}
                        {{/if}}
                   </div>
                   {{if buttons}}
                   <div class="modal-footer">
                        {{for buttons}}
                            {{:button}}
                        {{/for}}
                   </div>
                   {{/if}}
              </div>
         </div>
    </div>
</script>
<?php
$this->end();
endif;
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
    <?= $this->fetch('body_start') ?>
        <?= $this->fetch('header') ?>
        <?= $this->fetch('content') ?>
        <?= $this->fetch('footer') ?>
        <?= $this->fetch('script') ?>
        <?= $this->fetch('modal') ?>
    <?= $this->fetch('body_end') ?>
</html>
