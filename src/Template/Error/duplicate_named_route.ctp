<?php
use Cake\Error\Debugger;

$this->assign('title', 'Duplicate Named Route');
$this->assign('templateName', 'duplicate_named_route.ctp');

$attributes = $error->getAttributes();

$this->start('subheading');
?>
    <strong>Error: </strong>
    <?= h($error->getMessage()); ?>
<?php $this->end() ?>

<?php $this->start('file') ?>
<p>Route names must be unique across your entire application.
The same <code>_name</code> option cannot be used twice,
even if the names occur in different routing scopes.
Remove duplicate route names in your route configuration.</p>

<?php if (!empty($attributes['context'])) : ?>
<p>The passed context was:</p>
<pre>
<?= Debugger::exportVar($attributes['context']); ?>
</pre>
<?php endif; ?>

<?php if (isset($attributes['duplicate'])): ?>
    <h3>Duplicate Route</h3>
    <table cellspacing="0" cellpadding="0">
    <tr><th>Template</th><th>Defaults</th><th>Options</th></tr>
    <?php
    $other = $attributes['duplicate'];
    echo '<tr>';
    printf(
        '<td width="25%%">%s</td><td>%s</td><td width="20%%">%s</td>',
        h($other->template),
        h(Debugger::exportVar($other->defaults)),
        h(Debugger::exportVar($other->options))
    );
    echo '</tr>';
    ?>
    </table>
<?php endif; ?>
<?php $this->end() ?>
