<?php $this->start('index_panel_footer'); ?>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-4">
                <div class="record-counter">
                    <?= $this->Paginator->counter('Showing {{start}}-{{end}} of {{count}} records') ?>
                </div>
            </div>
            <div class="col-sm-8">
                <nav aria-label="Navigate Results">
                    <ul class="pagination pagination-sm justify-content-end">
                        <?= $this->Paginator->hasPrev() ? $this->Paginator->prev() : '' ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->hasNext() ? $this->Paginator->next() : '' ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
<?php $this->end(); ?>
