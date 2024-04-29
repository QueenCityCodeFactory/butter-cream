<?php
    $sessionMonitor = $this->get('sessionMonitor', false);
    if ($sessionMonitor !== false) {
        $this->set('sessionMonitor', $sessionMonitor);
    }
?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./layout/common'); ?>
<?php if (!$this->fetch('index.card.footer') && $this->get('index.noCardFooter', false) !== true) : ?>
    <?php $this->start('index.card.footer'); ?>
        <div class="card-footer">
            <?= $this->element('ButterCream.paginate') ?>
        </div>
    <?php $this->end(); ?>
<?php endif; ?>
<?= $this->fetch('index.card.before') ?>
<div class="card">
    <div class="card-header">
        <div class="row justify-content-between">
            <div class="col-auto">
                <?= $this->fetch('page.heading') ?>
                <?php if ($this->fetch('page.description')) : ?>
                    <small><?= $this->fetch('page.description') ?></small>
                <?php endif; ?>
            </div>
            <?php if ($this->fetch('index.card.buttons')) : ?>
                <div class="col-auto card-header-buttons">
                    <?= $this->fetch('index.card.buttons') ?>
                </div>
            <?php endif ?>
        </div>
    </div>
    <?= $this->fetch('index.card.body') ?>
    <div class="table-responsive">
        <?= $this->fetch('content') ?>
    </div>
    <?= $this->fetch('index.card.footer') ?>
</div>
<?= $this->fetch('index.card.after') ?>
