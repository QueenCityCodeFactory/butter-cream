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
 * @link          https://git.willetts.com/packages/referer Referer Component
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace ButterCream\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Routing\Router;

/**
 * Referer Component Class
 *
 * Helps get the user back to where the want to be
 *
 */
class RefererComponent extends Component
{

    /**
     * Default config
     * - `ignored` - Array of URLs to ignore
     * @var array
     */
    protected $_defaultConfig = [
        'ignored' => []
    ];

    /**
     * Main execution method. Handles setting the referer
     *
     * @param \Cake\Event\Event $event The startup event.
     * @return void
     */
    public function startup(Event $event): void
    {
        $this->setReferer();
    }

    /**
     * Store referer data in view referer variable
     *
     * @param sting $default default referer
     * @return void
     */
    public function setReferer($default = null): void
    {
        $request = $this->_registry->getController()->getRequest();
        if ($request->getData('Referer.url') === null) {
            $referer = $request->referer();

            if ($referer == '/' && !empty($default)) {
                $referer = $default;

                if (is_array($referer)) {
                    $referer = Router::url($referer);
                }
            }
        } else {
            $referer = $request->getData('Referer.url');
        }

        $referer = $this->normalizeUrl($referer);

        $this->getController()->set(compact('referer'));
    }

    /**
     * Get the current referer
     * @return string referring URL
     */
    public function getReferer(): string
    {
        $request = $this->_registry->getController()->getRequest();
        if ($request->getData('Referer.url')) {
            $referer = $request->getData('Referer.url');
        } else {
            $referer = $request->referer();
        }

        $referer = $this->normalizeUrl($referer);

        return $referer ?? '';
    }

    /**
     * Determine if the referer matches
     *
     * @param string $url The URL
     * @return bool true if the match, otherwise false
     */
    public function isMatch(string $url): bool
    {
        return $this->getReferer() === $this->normalizeUrl($url);
    }

    /**
     * Determine if the referer matches
     *
     * @param string $url The URL
     * @return void
     */
    public function ignore(string $url): void
    {
        $url = $this->normalizeUrl($url);
        $this->_config['ignored'][] = $url;
    }

    /**
     * This function strips the host and protocol out of a URL if the host and url match
     *
     * @param string|null $url url to normalize
     * @return string normalized $url
     */
    public function normalizeUrl($url = null): ?string
    {
        if (is_array($url)) {
            $url = Router::url($url);
        }
        $baseUrl = Router::url('/', true);
        $baseUri = parse_url($baseUrl);

        $uri = isset($url) ? parse_url($url) : null;

        if (isset($uri['host']) && isset($baseUri['host']) && $baseUri['host'] == $uri['host']) {
            $url = urldecode((!empty($uri['path']) ? $uri['path'] : '') . (!empty($uri['query']) ? '?' . $uri['query'] : ''));
        }

        return $url;
    }

    /**
     * Redirect to url stored in Data.referer or default $url
     *
     * @param mixed $url the url to redirect to
     * @param int $status http status code, default is null
     * @param bool $exit calling php exit or not after redirect, default is true
     * @return mixed
     */
    public function redirect($url, int $status = 302): ?Response
    {
        $referer = $this->getReferer();

        if (in_array($referer, $this->_config['ignored'])) {
            $referer = null;
        }

        if (strlen($referer) == 0 || $referer == '/') {
            return $this->getController()->redirect($url, $status);
        } else {
            return $this->getController()->redirect($referer, $status);
        }
    }
}
