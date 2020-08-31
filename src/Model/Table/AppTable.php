<?php
declare(strict_types=1);

namespace ButterCream\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Table;

/**
 * App Table
 */
class AppTable extends Table
{
    /**
     * Weird Encoding from Word or Other Applications that need replaced
     *
     * @var array
     */
    protected $encodingsToReplace = [
        "\xC2\xAB", // « (U+00AB) in UTF-8
        "\xC2\xBB", // » (U+00BB) in UTF-8
        "\xE2\x80\x98", // ‘ (U+2018) in UTF-8
        "\xE2\x80\x99", // ’ (U+2019) in UTF-8
        "\xE2\x80\x9A", // ‚ (U+201A) in UTF-8
        "\xE2\x80\x9B", // ‛ (U+201B) in UTF-8
        "\xE2\x80\x9C", // “ (U+201C) in UTF-8
        "\xE2\x80\x9D", // ” (U+201D) in UTF-8
        "\xE2\x80\x9E", // „ (U+201E) in UTF-8
        "\xE2\x80\x9F", // ‟ (U+201F) in UTF-8
        "\xE2\x80\xB9", // ‹ (U+2039) in UTF-8
        "\xE2\x80\xBA", // › (U+203A) in UTF-8
        "\xE2\x80\x93", // – (U+2013) in UTF-8
        "\xE2\x80\x94", // — (U+2014) in UTF-8
        "\xE2\x80\xA6", // … (U+2026) in UTF-8
    ];

    /**
     * List of Replacements for Weird encodings
     *
     * @var array
     */
    protected $encodingReplacements = [
        '<<',
        '>>',
        "'",
        "'",
        "'",
        "'",
        '"',
        '"',
        '"',
        '"',
        '<',
        '>',
        '-',
        '-',
        '...',
    ];

    /**
     * Before Marshal Callback
     *
     * @param \Cake\Event\EventInterface $event The beforeMarshal event that was fired
     * @param \ArrayObject $data ArrayObject instance.
     * @param \ArrayObject $options ArrayObject instance.
     * @return void
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        $data = $this->cleanData($data);
    }

    /**
     * Recursive Function to Clean Up submitted form Fields
     *
     * @param \ArrayObject $data The data being marshalled
     * @return \ArrayObject The cleaned up data
     */
    protected function cleanData($data)
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = str_replace(
                    $this->encodingsToReplace,
                    $this->encodingReplacements,
                    trim($value, " \t\0\x0B") // Don't remove \n or \r
                );
            } elseif (is_array($value)) {
                $data[$key] = $this->cleanData(new ArrayObject($value));
            }
        }

        return $data;
    }

    /**
     * Modifies the entity before it is saved so that translated fields are persisted
     * in the database too.
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // place global before save here
    }

    /**
     * AfterSave Callback
     *
     * @param \Cake\Event\EventInterface $event The afterDelete event that was fired.
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject $options The options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        // place global after save here
    }

    /**
     * Event fired after the record has been deleted
     *
     * @param \Cake\Event\EventInterface $event The beforeDelete event that was fired.
     * @param \Cake\ORM\Entity $entity The entity
     * @param \ArrayObject $options The options
     * @return void
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // place global before delete here
    }
}
