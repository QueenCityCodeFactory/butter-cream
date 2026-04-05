<?php
/**
 * Pass the sessionMonitor view variable through to the parent layout.
 */
$sessionMonitor = $this->get('sessionMonitor', false);
if ($sessionMonitor !== false) {
    $this->set('sessionMonitor', $sessionMonitor);
}

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
 * Breadcrumbs
 */
if (!$this->fetch('breadcrumbs')) {
    $this->start('breadcrumbs');
    if ($this->helpers()->has('Breadcrumbs')) {
        echo $this->Breadcrumbs->render();
    }
    $this->end();
}
?>
<div class="main-wrapper container-fluid">
    <div class="row">
        <?= $this->fetch('common.main.before') ?>
        <main role="main" class="col" spellcheck="true">
            <?= $this->fetch('flash') ?>
            <noscript>
                <div class="bs-callout bs-callout-danger">
                    <h4>JavaScript is Disabled</h4>
                    <p>Without JavaScript enabled this web application may not function as intended. Please enable JavaScript before continuing.</p>
                </div>
            </noscript>
            <?= $this->fetch('breadcrumbs') ?>
            <?= $this->fetch('callout') ?>
            <?= $this->fetch('common.content.before') ?>
            <?= $this->fetch('content') ?>
            <?= $this->fetch('common.content.after') ?>
        </main>
        <?= $this->fetch('common.main.after') ?>
    </div>
</div>
