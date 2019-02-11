<?php
use Cake\Routing\Router;
use Cake\Error\Debugger;

$this->layout = 'dev_error';

$this->assign('title', 'Missing Route');
$this->assign('templateName', 'missing_route.ctp');

$attributes = $error->getAttributes();

$this->start('subheading');
?>
    <strong>Error: </strong>
    <?= h($error->getMessage()); ?>
<?php $this->end() ?>

<?php $this->start('file') ?>
<p>None of the currently connected routes match the provided parameters.
Add a matching route to <?= 'config' . DIRECTORY_SEPARATOR . 'routes.php' ?></p>

<?php if (!empty($attributes['context'])): ?>
<p>The passed context was:</p>
<pre>
<?= h(Debugger::exportVar($attributes['context'])); ?>
</pre>
<?php endif; ?>

<h3>Connected Routes</h3>
<table cellspacing="0" cellpadding="0">
<tr><th>Template</th><th>Defaults</th><th>Options</th></tr>
<?php
foreach (Router::routes() as $route):
    echo '<tr>';
    printf(
        '<td width="25%%">%s</td><td>%s</td><td width="20%%">%s</td>',
        h($route->template),
        h(Debugger::exportVar($route->defaults)),
        h(Debugger::exportVar($route->options))
    );
    echo '</tr>';
endforeach;
?>
</table>
<?php $this->end() ?>
