<?php $this->getLayout() === 'ajax' ?: $this->extend('ButterCream./layout/common'); ?>
<?= $this->fetch('view.card.before') ?>
<div class="card">
    <div class="card-header">
        <div class="row justify-content-between">
            <div class="col-auto">
                <?= $this->fetch('page.heading') ?>
                <?php if ($this->fetch('page.description')) : ?>
                    <small><?= $this->fetch('page.description') ?></small>
                <?php endif; ?>
            </div>
            <?php if ($this->fetch('view.card.buttons')) : ?>
                <div class="col-auto card-header-buttons">
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
