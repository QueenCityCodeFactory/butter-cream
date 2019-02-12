<?php
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\Exception\MissingElementException;

/**
 * Default `html` block.
 */
if (!$this->fetch('html')) {
    $this->start('html');
    printf('<html lang="%s">', Configure::read('App.language'));
    $this->end();
}

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
 * Flash Messages
 */
if (!$this->fetch('flash')) {
    $this->start('flash');
    if (isset($this->Flash)) {
        echo $this->Flash->render();
    }
    $this->end();
}

/**
 * Default `header` block.
 */
if (!$this->fetch('header')) {
    $this->start('header');
        echo $this->element('header', [], ['ignoreMissing' => true, 'plugin' => false]);
    $this->end();
}

/**
 * Default `footer` block.
 */
if (!$this->fetch('footer')) {
    $this->start('footer');
        echo $this->element('footer', [], ['ignoreMissing' => true, 'plugin' => false]);
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
 * Body tag attributes
 */
if (!$this->fetch('body_tag_attrs')) {
    $bodyClasses = ['error-page', Configure::read('App.environment'), strtolower(Text::slug(Inflector::dasherize($this->request->getParam('controller')))), strtolower(Text::slug(Inflector::dasherize($this->request->getParam('action'))))];
    $skinClass = $this->request->getSession()->read('Auth.User.theme');
    if (!empty($skinClass)) {
        $bodyClasses[] = $skinClass;
    }
    if ($this->fetch('session_monitor')) {
        $bodyClasses[] = 'session-monitor';
    }

    $this->assign('body_tag_attrs', ' class="' . join(' ', $bodyClasses) . '"');
}

$this->append('script'); ?>
<script type="text/javascript">
    function bindEvent(selector, eventName, listener) {
        var els = document.querySelectorAll(selector);
        for (var i = 0, len = els.length; i < len; i++) {
            els[i].addEventListener(eventName, listener, false);
        }
    }

    function toggleElement(el) {
        if (el.style.display === 'none') {
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    }

    function each(els, cb) {
        var i, len;
        for (i = 0, len = els.length; i < len; i++) {
            cb(els[i], i);
        }
    }

    window.addEventListener('load', function() {
        bindEvent('.stack-frame-args', 'click', function(event) {
            var target = this.dataset['target'];
            var el = document.getElementById(target);
            toggleElement(el);
            event.preventDefault();
        });

        var details = document.querySelectorAll('.stack-details');
        var frames = document.querySelectorAll('.stack-frame');
        bindEvent('.stack-frame a', 'click', function(event) {
            each(frames, function(el) {
                el.classList.remove('list-group-item-secondary');
            });
            this.parentNode.classList.add('list-group-item-secondary');

            each(details, function(el) {
                el.style.display = 'none';
            });

            var target = document.getElementById(this.dataset['target']);
            toggleElement(target);
            event.preventDefault();
        });

        bindEvent('.toggle-vendor-frames', 'click', function(event) {
            each(frames, function(el) {
                if (el.classList.contains('vendor-frame')) {
                    toggleElement(el);
                }
            });
            event.preventDefault();
        });
    });
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
        <div class="container-fluid">
            <div class="row">
                <main role="main" class="col-12" spellcheck="true">
                    <?= $this->fetch('flash') ?>
                    <?= $this->fetch('callout') ?>
                    <?= $this->fetch('content') ?>
                    <?php if (Configure::read('debug')) : ?>
                        <div class="card border-danger">
                            <div class="card-header text-danger">
                                <div class="row">
                                    <div class="col">
                                        <?= h($this->fetch('title')) ?>
                                    </div>
                                    <div class="col text-right">
                                        <small><?= get_class($error) ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 error-nav">
                                        <?= $this->element('exception_stack_trace_nav') ?>
                                    </div>
                                    <div class="col-md-8 error-contents">
                                        <?php if ($this->fetch('subheading')): ?>
                                        <div class="alert alert-info error-subheading">
                                            <?= $this->fetch('subheading') ?>
                                        </div>
                                        <?php endif; ?>

                                        <?= $this->element('exception_stack_trace'); ?>

                                        <div class="error-suggestion">
                                            <?= $this->fetch('file') ?>
                                        </div>

                                        <?php if ($this->fetch('templateName')): ?>
                                        <p class="customize">
                                            If you want to customize this error message, create
                                            <em><?= APP_DIR . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR . 'Error' . DIRECTORY_SEPARATOR . $this->fetch('templateName') ?></em>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
        <?= $this->fetch('footer') ?>
        <?= $this->fetch('script') ?>
    </body>
</html>
