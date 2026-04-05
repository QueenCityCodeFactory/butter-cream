<?php $this->getLayout() === 'ajax' ?: $this->extend('ButterCream./layout/common'); ?>
<?= $this->fetch('form.content.before') ?>
<div class="row">
    <?= $this->fetch('content'); ?>
</div>
<?= $this->fetch('form.content.after') ?>
