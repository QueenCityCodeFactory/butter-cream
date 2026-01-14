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
     * @param array<string>|false|null $fields Fields list.
     * @param array<string>|null $primaryKey Primary key.
     * @return array<string>
     */
    public function getFieldAccessibility(array|false|null $fields = null, ?array $primaryKey = null): array
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
