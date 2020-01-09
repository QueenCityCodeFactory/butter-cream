<?php
namespace ButterCream\Shell\Task;

use Bake\Shell\Task\ModelTask as CakeModelTask;
use Cake\Console\ConsoleIo;
use Cake\Utility\Inflector;

class ModelTask extends CakeModelTask
{

    /**
     * tasks
     *
     * @var array
     */
    public $tasks = [
        'Bake.Fixture',
        'ButterCream.BakeTemplate',
        'Bake.Test'
    ];

    /**
     * Constructs this Shell instance.
     *
     * @param \Cake\Console\ConsoleIo|null $io An io instance.
     * @link https://book.cakephp.org/3.0/en/console-and-shells.html#Shell
     */
    public function __construct(ConsoleIo $io = null)
    {
        parent::__construct($io);

        // Need to unset the original BakeTemplate so we can use the one from ButterCream
        unset($this->tasks['Bake.BakeTemplate']);
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addOption('callbacks', [
            'boolean' => true,
            'help' => 'Stub out callback methods (beforeMarshal, beforeSave)'
        ]);

        return $parser;
    }

    /**
     * Bake a table class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     * @return string|null
     */
    public function bakeTable($model, array $data = [])
    {
        $data += [
            'callbacks' => !empty($this->params['callbacks'])
        ];

        return parent::bakeTable($model, $data);
    }

    /**
     * Find belongsTo relations and add them to the associations list.
     *
     * @param \Cake\ORM\Table $model Database\Table instance of table being generated.
     * @param array $associations Array of in progress associations
     * @return array Associations with belongsTo added in.
     */
    public function findBelongsTo($model, array $associations)
    {
        $schema = $model->getSchema();
        foreach ($schema->columns() as $fieldName) {
            if (!preg_match('/^.+_id$/', $fieldName)) { // Don't need to skip if primary key
                continue;
            }

            if ($fieldName === 'parent_id') {
                $className = ($this->plugin) ? $this->plugin . '.' . $model->getAlias() : $model->getAlias();
                $assoc = [
                    'alias' => 'Parent' . $model->getAlias(),
                    'className' => $className,
                    'foreignKey' => $fieldName
                ];
            } else {
                $tmpModelName = $this->_modelNameFromKey($fieldName);
                if (!in_array(Inflector::tableize($tmpModelName), $this->_tables)) {
                    $found = $this->findTableReferencedBy($schema, $fieldName);
                    if ($found) {
                        $tmpModelName = Inflector::camelize($found);
                    }
                }
                $assoc = [
                    'alias' => $tmpModelName,
                    'foreignKey' => $fieldName
                ];
                if ($schema->getColumn($fieldName)['null'] === false) {
                    $assoc['joinType'] = 'INNER';
                }
            }

            if ($this->plugin && empty($assoc['className'])) {
                $assoc['className'] = $this->plugin . '.' . $assoc['alias'];
            }
            $associations['belongsTo'][] = $assoc;
        }

        return $associations;
    }
}
