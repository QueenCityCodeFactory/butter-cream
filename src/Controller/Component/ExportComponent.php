<?php
declare(strict_types=1);

namespace ButterCream\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Response;

/**
 * ExportComponent — Convenience methods for exporting query results.
 *
 * Supports CSV download from any controller action. Pairs with the
 * CakeSpreadsheet plugin that is already a dependency.
 *
 * ### Usage
 *
 * ```php
 * // In controller initialize()
 * $this->loadComponent('ButterCream.Export');
 *
 * // In action
 * if ($this->request->getQuery('export') === 'csv') {
 *     return $this->Export->csv($query, 'users.csv');
 * }
 * ```
 */
class ExportComponent extends Component
{
    /**
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'delimiter' => ',',
        'enclosure' => '"',
        'bom' => true,
    ];

    /**
     * Export a query as a CSV download.
     *
     * @param iterable $data Query, ResultSet, or array of rows.
     * @param string $filename Download filename.
     * @param array<string> $columns Column names to include. Empty = all.
     * @return \Cake\Http\Response
     */
    public function csv(iterable $data, string $filename = 'export.csv', array $columns = []): Response
    {
        $response = $this->getController()->getResponse();

        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Unable to open temp stream for CSV export.');
        }

        // BOM for Excel UTF-8 compatibility
        if ($this->getConfig('bom')) {
            fwrite($stream, "\xEF\xBB\xBF");
        }

        $delimiter = $this->getConfig('delimiter');
        $enclosure = $this->getConfig('enclosure');
        $headerWritten = false;

        foreach ($data as $row) {
            if (is_object($row) && method_exists($row, 'toArray')) {
                $row = $row->toArray();
            }
            $row = (array)$row;

            // Filter virtual/object properties — keep only scalar values
            $row = array_filter($row, fn($v) => is_scalar($v) || $v === null);

            if (!empty($columns)) {
                $row = array_intersect_key($row, array_flip($columns));
            }

            if (!$headerWritten) {
                $headers = array_map(
                    fn($key) => \Cake\Utility\Inflector::humanize((string)$key),
                    array_keys($row),
                );
                fputcsv($stream, $headers, $delimiter, $enclosure);
                $headerWritten = true;
            }

            fputcsv($stream, array_values($row), $delimiter, $enclosure);
        }

        rewind($stream);
        $csvContent = stream_get_contents($stream);
        fclose($stream);

        return $response
            ->withType('csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withStringBody($csvContent !== false ? $csvContent : '');
    }
}
