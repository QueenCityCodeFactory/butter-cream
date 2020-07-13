<?php
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\Utility\Text;

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
    $this->assign('header', $this->element('header', [], ['ignoreMissing' => true, 'plugin' => false]));
}

/**
 * Default `footer` block.
 */
if (!$this->fetch('footer')) {
    $this->assign('footer', $this->element('footer', [], ['ignoreMissing' => true, 'plugin' => false]));
}
?>
<div class="main-wrapper container-fluid">
    <div class="row">
        <?= $this->fetch('common.main.before') ?>
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
        </main>
        <?= $this->fetch('common.main.after') ?>
    </div>
</div>
