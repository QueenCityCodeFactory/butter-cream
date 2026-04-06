<?php
declare(strict_types=1);

/**
 * QueenCityCodeFactory(tm) : Web application developers (http://queencitycodefactory.com)
 * Copyright (c) Queen City Code Factory, Inc. (http://queencitycodefactory.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Queen City Code Factory, Inc. (http://queencitycodefactory.com)
 * @link          https://github.com/QueenCityCodeFactory/butter-cream
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace ButterCream\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Routing\Router;

/**
 * Referer Component Class
 *
 * Tracks the referring URL across requests and provides smart redirection.
 * Automatically stores the referer on startup and makes it available to views.
 */
class RefererComponent extends Component
{
    /**
     * Default config
     * - `ignored` - Array of URLs to ignore when redirecting
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'ignored' => [],
    ];

    /**
     * Main execution method. Handles setting the referer
     *
     * @param \Cake\Event\EventInterface<\Cake\Controller\Component> $event The startup event.
     * @return void
     */
    public function startup(EventInterface $event): void
    {
        $this->setReferer();
    }

    /**
     * Store referer data in view referer variable
     *
     * @param array<string, mixed>|string|null $default default referer
     * @return void
     */
    public function setReferer(string|array|null $default = null): void
    {
        $request = $this->getController()->getRequest();
        if ($request->getData('Referer.url') === null) {
            $referer = $request->referer();

            if ($referer === '/' && !empty($default)) {
                $referer = is_array($default) ? Router::url($default) : $default;
            }
        } else {
            $referer = $request->getData('Referer.url');
        }

        $referer = $this->normalizeUrl($referer);

        $this->getController()->set(compact('referer'));
    }

    /**
     * Get the current referer
     *
     * @return string referring URL
     */
    public function getReferer(): string
    {
        $request = $this->getController()->getRequest();
        if ($request->getData('Referer.url')) {
            $referer = $request->getData('Referer.url');
        } else {
            $referer = $request->referer();
        }

        return $this->normalizeUrl($referer) ?? '';
    }

    /**
     * Determine if the referer matches the given URL
     *
     * @param string $url The URL to compare against
     * @return bool true if they match, otherwise false
     */
    public function isMatch(string $url): bool
    {
        return $this->getReferer() === $this->normalizeUrl($url);
    }

    /**
     * Add a URL to the ignore list
     *
     * @param string $url The URL to ignore
     * @return void
     */
    public function ignore(string $url): void
    {
        $ignored = $this->getConfig('ignored', []);
        $ignored[] = $this->normalizeUrl($url);
        $this->setConfig('ignored', $ignored);
    }

    /**
     * Strips the host and protocol from a URL if the host matches the application's base URL.
     *
     * @param string|null $url URL to normalize
     * @return string|null Normalized URL
     */
    public function normalizeUrl(?string $url = null): ?string
    {
        if ($url === null) {
            return null;
        }

        $baseUrl = Router::url('/', true);
        /** @var array<string, string> $baseUri */
        $baseUri = parse_url($baseUrl);
        /** @var array<string, string> $uri */
        $uri = parse_url($url);

        if (isset($uri['host'], $baseUri['host']) && $baseUri['host'] === $uri['host']) {
            $url = urldecode(
                ($uri['path'] ?? '') . (!empty($uri['query']) ? '?' . $uri['query'] : ''),
            );
        }

        return $url;
    }

    /**
     * Redirect to the stored referer or the default URL
     *
     * @param array<string, mixed>|string $url The fallback URL to redirect to
     * @param int $status HTTP status code
     * @return \Cake\Http\Response|null
     */
    public function redirect(array|string $url, int $status = 302): ?Response
    {
        $referer = $this->getReferer();
        $ignored = $this->getConfig('ignored', []);

        if (in_array($referer, $ignored, true) || $referer === '' || $referer === '/') {
            return $this->getController()->redirect($url, $status);
        }

        return $this->getController()->redirect($referer, $status);
    }
}
