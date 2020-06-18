<?php $this->set('sessionMonitor', true); ?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./layout/common'); ?>
<div class="row test">
    <?= $this->fetch('content'); ?>
</div>
