<?php
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\Exception\MissingElementException;

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
 * Body tag attributes
 */
if (!$this->fetch('body_tag_attrs')) {
    $bodyClasses = [Configure::read('App.environment'), strtolower(Text::slug(Inflector::dasherize($this->request->getParam('controller')))), strtolower(Text::slug(Inflector::dasherize($this->request->getParam('action'))))];
    $skinClass = $this->request->getSession()->read('Auth.User.theme');
    if (!empty($skinClass)) {
        $bodyClasses[] = $skinClass;
    }
    if ($this->fetch('session_monitor')) {
        $bodyClasses[] = 'session-monitor';
    }

    $this->assign('body_tag_attrs', ' class="' . join(' ', $bodyClasses) . '"');
}
?>
<div class="container-fluid">
    <div class="row">
        <main role="main" class="col-12" spellcheck="true">
            <?= $this->fetch('flash') ?>
            <?= $this->fetch('callout') ?>
            <?= $this->fetch('content') ?>
        </main>
    </div>
</div>
