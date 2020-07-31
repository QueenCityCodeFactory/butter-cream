<?php
    $sessionMonitor = $this->get('sessionMonitor', false);
    if ($sessionMonitor !== false) {
        $this->set('sessionMonitor', $sessionMonitor);
    }
?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./layout/common'); ?>
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-9">
                <?= $this->fetch('page_heading') ?>
                <?php if ($this->fetch('page_description')) : ?>
                    <small><?= $this->fetch('page_description') ?></small>
                <?php endif; ?>
            </div>
            <?php if ($this->fetch('view.card_buttons')) : ?>
                <div class="col-3 text-right">
                    <?= $this->fetch('view.card_buttons') ?>
                </div>
            <?php endif ?>
        </div>
    </div>
    <div class="card-body">
        <?= $this->fetch('content') ?>
    </div>
    <?= $this->fetch('view.card_footer') ?>
</div>

<?= $this->fetch('related_data') ?>
