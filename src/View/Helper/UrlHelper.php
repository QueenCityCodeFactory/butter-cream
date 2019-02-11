<?php
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
     * @param string|array $url Either a relative string url like `/products/view/23` or
     *    an array of URL parameters. Using an array for URLs will allow you to leverage
     *    the reverse routing features of CakePHP.
     * @param bool $full If true, the full base URL will be prepended to the result
     * @return string Full translated URL with base path.
     */
    public function build($url = null, $full = false)
    {
        return Router::url($url, $full);
    }

    /**
     * Normalizes two url/routes and returns if they are considered equal
     *
     * @param  array|string $url The first url
     * @param  array|string $url2 The second url
     * @return bool Are they the same URL???
     */
    public function isEqual($url, $url2)
    {
        $url = Router::parse(Router::url($url));
        $url2 = Router::parse(Router::url($url2));

        $urlParts = [
            'prefix' => isset($url['prefix']) ? $url['prefix'] : null,
            'plugin' => isset($url['plugin']) ? $url['plugin'] : null,
            'controller' => isset($url['controller']) ? $url['controller'] : null,
            'action' => isset($url['action']) ? $url['action'] : null,
        ];

        $url2Parts = [
            'prefix' => isset($url2['prefix']) ? $url2['prefix'] : null,
            'plugin' => isset($url2['plugin']) ? $url2['plugin'] : null,
            'controller' => isset($url2['controller']) ? $url2['controller'] : null,
            'action' => isset($url2['action']) ? $url2['action'] : null,
        ];

        return $urlParts === $url2Parts;
    }
}
