<?php
namespace ButterCream\Database\Type;

/**
 * Provides behavior for the JSON type
 */
class JsonArrayType extends JsonType
{

    /**
     * Do you want this to be an object or array - we want the array!
     * @var boolean
     */
    public $array = true;
}
