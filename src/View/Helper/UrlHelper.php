<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Cake\Routing\Router;
use Cake\View\Helper\UrlHelper as Helper;

/**
 * UrlHelper Class
 */
class UrlHelper extends Helper
{
    /**
     * Returns a URL based on provided parameters.
     *
     * ### Options:
     *
     * - `escape`: If false, the URL will be returned unescaped, do only use if it is manually
     *    escaped afterwards before being displayed.
     * - `fullBase`: If true, the full base URL will be prepended to the result
     *
     * @param string|array|null $url Either a relative string URL like `/products/view/23` or
     *    an array of URL parameters. Using an array for URLs will allow you to leverage
     *    the reverse routing features of CakePHP.
     * @param array $options Array of options.
     * @return string Full translated URL with base path.
     */
    public function build($url = null, array $options = []): string
    {
        $defaults = [
            'fullBase' => false,
            'escape' => false,
        ];
        $options += $defaults;

        return parent::build($url, $options);
    }

    /**
     * Normalizes two url/routes and returns if they are considered equal
     *
     * @param  array|string $url The first url
     * @param  array|string $url2 The second url
     * @return bool Are they the same URL???
     */
    public function isEqual($url, $url2): bool
    {
        $url = Router::parse(Router::url($url));
        $url2 = Router::parse(Router::url($url2));

        $urlParts = [
            'prefix' => $url['prefix'] ?? null,
            'plugin' => $url['plugin'] ?? null,
            'controller' => $url['controller'] ?? null,
            'action' => $url['action'] ?? null,
        ];

        $url2Parts = [
            'prefix' => $url2['prefix'] ?? null,
            'plugin' => $url2['plugin'] ?? null,
            'controller' => $url2['controller'] ?? null,
            'action' => $url2['action'] ?? null,
        ];

        return $urlParts === $url2Parts;
    }
}
