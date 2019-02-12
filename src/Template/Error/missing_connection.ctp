<?php
$this->assign('templateName', 'missing_connection.ctp');
$this->assign('title', 'Missing Database Connection');


$this->start('subheading'); ?>
A Database connection using was missing or unable to connect.
<br>
<?php
if (isset($reason)):
    echo sprintf('The database server returned this error: %s', h($reason));
endif;
$this->end();

$this->start('file');
echo $this->element('auto_table_warning');
$this->end();
