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
         echo $this->element('google_analytics', [], ['ignoreMissing' => true, 'plugin' => false]);
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

$this->append('script'); ?>
<script>
    var sessionTimeout = "<?= Configure::read('Session.timeout') ?>";
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
                                <span class="input-group-addon"><i class="fa fa-at"></i></span>
                                <input type="text" name="username" autocomplete="off" id="expired-username" class="form-control">
                            </div>
                        </div>
                        <div class="form-group password">
                            <label class="control-label" for="password">Password</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key"></i></span>
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
<?php $this->end(); ?>

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
        <?= $this->fetch('content') ?>
        <?= $this->fetch('footer') ?>
        <?= $this->fetch('script') ?>
        <?= $this->fetch('modal') ?>
    </body>
</html>
