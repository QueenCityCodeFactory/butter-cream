<?php $this->layout == 'ajax' ? null : $this->extend('ButterCream./Layout/common'); ?>

<?php if (!$this->fetch('index_panel_footer') && empty($this->fetch('nopaginate'))) : ?>
    <?php $this->start('index_panel_footer'); ?>
        <div class="card-footer">
            <div class="row">
                <div class="col-auto mr-auto">
                    <div class="record-counter">
                        <?= $this->Form->control('limit', [
                            'id' => null,
                            'label' => false,
                            'data-url' => Cake\Routing\Router::url(['?' => $this->request->getQuery()]),
                            'data-update' => $this->fetch('ajax_update_element'),
                            'options' => [10 => 10, 20 => 20, 50 => 50, 100 => 100, 200 => 200],
                            'default' => $this->request->getQuery('limit') ? $this->request->getQuery('limit') : 20,
                            'templates' => [
                                'select' => '<select class="input-xs ' . ($this->layout == 'ajax' ? 'ajax-set-pagination-limit' : 'set-pagination-limit') . '"{{attrs}}>{{content}}</select>',
                                'inputContainer' => 'Show: {{content}}',
                            ]
                        ]) ?>
                        <?= $this->Paginator->counter('Showing {{start}}-{{end}} of {{count}} records') ?>
                    </div>
                </div>
                <div class="col-auto">
                    <nav aria-label="Navigate Results">
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                            <?= $this->Paginator->hasPrev() ? $this->Paginator->prev() : '' ?>
                            <?= $this->Paginator->numbers() ?>
                            <?= $this->Paginator->hasNext() ? $this->Paginator->next() : '' ?>
                        </ul>
                    </nav>
                </div>
            </div>
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
            <?php if ($this->fetch('panel_buttons')) : ?>
                <div class="col-auto card-header-buttons">
                    <?= $this->fetch('panel_buttons') ?>
                </div>
            <?php endif ?>
        </div>
    </div>
    <?= $this->fetch('index_panel_body'); ?>
    <div class="table-responsive">
        <?= $this->fetch('content') ?>
    </div>
    <?= $this->fetch('index_panel_footer') ?>
</div>
