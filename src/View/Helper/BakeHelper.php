<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Bake\View\Helper\BakeHelper as Helper;

/**
 * Bake helper
 */
class BakeHelper extends Helper
{

    /**
     * Get field accessibility data.
     *
     * @param string[]|false|null $fields Fields list.
     * @param string[]|null $primaryKey Primary key.
     * @return string[]
     */
    public function getFieldAccessibility($fields = null, $primaryKey = null): array
    {
        $accessible = [];

        if (!isset($fields) || $fields !== false) {
            if (!empty($fields)) {
                foreach ($fields as $field) {
                    $accessible[$field] = 'true';
                }
            } elseif (!empty($primaryKey)) {
                $accessible['*'] = 'true';
                foreach ($primaryKey as $field) {
                    $accessible[$field] = 'false';
                }
            }

            // Add this to all Entities otherwise the referer can cause the entity to be dirty
            $accessible['Referer'] = 'false';
        }

        return $accessible;
    }
}
