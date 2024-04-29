<?php
    $sessionMonitor = $this->get('sessionMonitor', false);
    if ($sessionMonitor !== false) {
        $this->set('sessionMonitor', $sessionMonitor);
    }
?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./layout/common'); ?>
<?= $this->fetch('form.content.before') ?>
<div class="row">
    <?= $this->fetch('content'); ?>
</div>
<?= $this->fetch('form.content.after') ?>
