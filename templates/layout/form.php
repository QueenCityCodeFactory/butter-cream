<?php
    $sessionMonitor = $this->get('sessionMonitor', false);
    if ($sessionMonitor !== false) {
        $this->set('sessionMonitor', $sessionMonitor);
    }
?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./layout/common'); ?>
<div class="row">
    <?= $this->fetch('content'); ?>
</div>
