<?php $this->set('sessionMonitor', true); ?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./layout/common'); ?>
<div class="row">
    <?= $this->fetch('content'); ?>
</div>
