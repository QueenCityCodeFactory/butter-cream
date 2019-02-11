<?php
use Cake\Error\Debugger;
?>
<a href="#" class="toggle-link toggle-vendor-frames btn btn-primary btn-block">Toggle Vendor/App Stack</a>

<ul class="stack-trace list-group">
<?php foreach ($error->getTrace() as $i => $stack): ?>
    <?php $class = (isset($stack['file']) && strpos($stack['file'], APP) === false) ? 'vendor-frame' : 'app-frame'; ?>
    <li class="list-group-item stack-frame <?= $class ?>">
    <?php if (isset($stack['function'])): ?>
        <a href="#" data-target="stack-frame-<?= $i ?>">
            <?php if (isset($stack['class'])): ?>
                <strong class="stack-function">&rang; <?= h($stack['class'] . $stack['type'] . $stack['function']) ?></strong>
            <?php else: ?>
                <strong class="stack-function">&rang; <?= h($stack['function']) ?></strong>
            <?php endif; ?>
            <span class="stack-file">
            <?php if (isset($stack['file'], $stack['line'])): ?>
                <?= h(Debugger::trimPath($stack['file'])) ?>, line <?= $stack['line'] ?>
            <?php else: ?>
                [internal function]
            <?php endif ?>
            </span>
        </a>
    <?php else: ?>
        <a href="#">&rang; [internal function]</a>
    <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
