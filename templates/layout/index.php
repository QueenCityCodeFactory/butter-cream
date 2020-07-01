<?php $this->set('sessionMonitor', true); ?>
<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./layout/common'); ?>
<?php if (!$this->fetch('index.card_footer') && $this->get('index.noCardFooter', false) !== true) : ?>
    <?php $this->start('index.card_footer'); ?>
        <div class="card-footer">
            <?= $this->element('ButterCream.paginate') ?>
        </div>
    <?php $this->end(); ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-auto mr-auto">
                <?= $this->fetch('page_heading') ?>
                <?php if ($this->fetch('page_description')) : ?>
                    <small><?= $this->fetch('page_description') ?></small>
                <?php endif; ?>
            </div>
            <?php if ($this->fetch('index.card_buttons')) : ?>
                <div class="col-auto card-header-buttons">
                    <?= $this->fetch('index.card_buttons') ?>
                </div>
            <?php endif ?>
        </div>
    </div>
    <?= $this->fetch('index.card_body'); ?>
    <div class="table-responsive">
        <?= $this->fetch('content') ?>
    </div>
    <?= $this->fetch('index.card_footer') ?>
</div>
