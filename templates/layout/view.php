<?php
    $sessionMonitor = $this->get('sessionMonitor', false);
    if ($sessionMonitor !== false) {
        $this->set('sessionMonitor', $sessionMonitor);
    }
?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./layout/common'); ?>
<?= $this->fetch('view.card.before') ?>
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-9">
                <?= $this->fetch('page.heading') ?>
                <?php if ($this->fetch('page.description')) : ?>
                    <small><?= $this->fetch('page.description') ?></small>
                <?php endif; ?>
            </div>
            <?php if ($this->fetch('view.card.buttons')) : ?>
                <div class="col-3 text-end">
                    <?= $this->fetch('view.card.buttons') ?>
                </div>
            <?php endif ?>
        </div>
    </div>
    <div class="card-body">
        <?= $this->fetch('content') ?>
    </div>
    <?= $this->fetch('view.card.footer') ?>
</div>
<?= $this->fetch('view.card.after') ?>

<?= $this->fetch('related_data') ?>
