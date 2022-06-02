<?php
$paginatorOptions = $this->get('paginator.options', [
    'limit' => [
        'show' => true,
        'options' => [
            20 => 20,
            40 => 40,
            60 => 60,
            80 => 80,
            100 => 100,
        ],
        'default' => 20,
    ],
]);
?>

<div class="row">
    <div class="col-auto mr-auto">
        <div class="record-counter">
            <?php if (!empty($paginatorOptions['limit']['show'])) : ?>
                <?= $this->Form->control('limit', [
                    'id' => null,
                    'label' => false,
                    'data-url' => Cake\Routing\Router::url(['?' => $this->getRequest()->getQuery()]),
                    'data-update' => $this->fetch('ajax_update_element'),
                    'options' => !empty($paginatorOptions['limit']['options']) && is_array($paginatorOptions['limit']['options']) ? $paginatorOptions['limit']['options'] : [20 => 20, 40 => 40, 60 => 60, 80 => 80, 100 => 100],
                    'default' => $this->getRequest()->getQuery('limit') ? $this->getRequest()->getQuery('limit') : $paginatorOptions['limit']['default'] ?? 20,
                    'templates' => [
                        'select' => '<select class="input-xs ' . ($this->layout == 'ajax' ? 'ajax-set-pagination-limit' : 'set-pagination-limit') . '"{{attrs}}>{{content}}</select>',
                        'inputContainer' => 'Show: {{content}}',
                    ],
                ]) ?>
            <?php endif; ?>
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
