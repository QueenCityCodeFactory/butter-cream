<?php
use Cake\Core\Configure;

/**
 * ButterCream Error Layout
 *
 * Extends the main layout for consistent HTML shell and asset loading.
 * Adds error-page body class and error-specific content structure
 * with optional debug stack-trace panel.
 */
$this->extend('ButterCream.main');
$this->set('noModalScript', true);
$this->set('extraBodyClasses', ['error-page']);

/**
 * Flash Messages
 */
if (!$this->fetch('flash')) {
    $this->start('flash');
    if ($this->helpers()->has('Flash')) {
        echo $this->Flash->render();
    }
    $this->end();
}

/**
 * Default `header` block.
 */
if (!$this->fetch('header')) {
    $this->assign('header', $this->element('header', [], ['ignoreMissing' => true, 'plugin' => false]));
}

/**
 * Default `footer` block.
 */
if (!$this->fetch('footer')) {
    $this->assign('footer', $this->element('footer', [], ['ignoreMissing' => true, 'plugin' => false]));
}

/**
 * Debug stack-trace interaction handlers.
 * These enable the collapsible stack frames in the debug error card below.
 */
if (Configure::read('debug')) :
$this->append('script'); ?>
<script>
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
<?php
$this->end();
endif;
?>

<div class="container-fluid">
    <div class="row">
        <main role="main" class="col-12" spellcheck="true">
            <?= $this->fetch('flash') ?>
            <noscript>
                <div class="bs-callout bs-callout-danger">
                    <h4>JavaScript is Disabled</h4>
                    <p>Without JavaScript enabled this web application may not function as intended. Please enable JavaScript before continuing.</p>
                </div>
            </noscript>
            <?= $this->fetch('callout') ?>
            <?= $this->fetch('content') ?>
            <?php if (Configure::read('debug')) : ?>
                <div class="card border-danger">
                    <div class="card-header text-danger">
                        <div class="row">
                            <div class="col">
                                <?= h($this->fetch('title')) ?>
                            </div>
                            <div class="col text-end">
                                <small><?= isset($error) ? h($error::class) : '' ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 error-nav">
                                <?= $this->element('exception_stack_trace_nav') ?>
                            </div>
                            <div class="col-md-8 error-contents">
                                <?php if ($this->fetch('subheading')) : ?>
                                <div class="alert alert-info error-subheading">
                                    <?= $this->fetch('subheading') ?>
                                </div>
                                <?php endif; ?>

                                <?= $this->element('exception_stack_trace'); ?>

                                <div class="error-suggestion">
                                    <?= $this->fetch('file') ?>
                                </div>

                                <?php if ($this->fetch('templateName')) : ?>
                                <p class="customize">
                                    If you want to customize this error message, create
                                    <em><?= 'templates' . DIRECTORY_SEPARATOR . 'Error' . DIRECTORY_SEPARATOR . $this->fetch('templateName') ?></em>
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
