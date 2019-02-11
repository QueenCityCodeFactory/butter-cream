<?php $this->assign('session_monitor', true); ?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./Layout/common'); ?>
<div class="row">
    <?= $this->fetch('content'); ?>
</div>
