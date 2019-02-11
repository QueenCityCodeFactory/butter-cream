<?php
use Cake\Error\Debugger;

foreach ($error->getTrace() as $i => $stack):
    $excerpt = $params = [];

    if (isset($stack['file'], $stack['line'])):
        $excerpt = Debugger::excerpt($stack['file'], $stack['line'], 4);
    endif;

    if (isset($stack['file'])):
        $file = $stack['file'];
    else:
        $file = '[internal function]';
    endif;

    if ($stack['function']):
        if (!empty($stack['args'])):
            foreach ((array)$stack['args'] as $arg):
                $params[] = Debugger::exportVar($arg, 4);
            endforeach;
        else:
            $params[] = 'No arguments';
        endif;
    endif;
?>
    <div id="stack-frame-<?= $i ?>" style="display:none;" class="stack-details">
        <span class="stack-frame-file"><?= h($file) ?></span>
        <a href="#" class="toggle-link stack-frame-args btn btn-success btn-sm" data-target="stack-args-<?= $i ?>">toggle arguments</a>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
        <?php $lineno = isset($stack['line']) ? $stack['line'] - 4 : 0 ?>
        <?php foreach ($excerpt as $l => $line): ?>
            <tr>
                <td class="excerpt-number" data-number="<?= $lineno + $l ?>"></td>
                <td class="excerpt-line"><?= $line ?></td>
            </tr>
        <?php endforeach; ?>
        </table>

        <div id="stack-args-<?= $i ?>" style="display: none;">
            <pre><?= h(implode("\n", $params)) ?></pre>
        </div>
    </div>
<?php endforeach; ?>
