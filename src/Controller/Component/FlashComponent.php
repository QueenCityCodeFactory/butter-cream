<?php
declare(strict_types=1);

namespace ButterCream\Controller\Component;

use Cake\Controller\Component\FlashComponent as Component;

/**
 * The CakePHP FlashComponent provides a way for you to write a flash variable
 * to the session from your controllers, to be rendered in a view with the
 * FlashHelper.
 *
 * @method void success(string $message, array $options = []) Set a message using "success" element
 * @method void error(string $message, array $options = []) Set a message using "error" element
 */
class FlashComponent extends Component
{
    /**
     * Used to set a session variable that can be used to output messages in the view.
     * If you make consecutive calls to this method, the messages will stack (if they are
     * set with the same flash key)
     *
     * In your controller: $this->Flash->set('This has been saved');
     *
     * ### Options:
     *
     * - `key` The key to set under the session's Flash key
     * - `element` The element used to render the flash message. Default to 'default'.
     * - `params` An array of variables to make available when using an element
     * - `clear` A bool stating if the current stack should be cleared to start a new one
     * - `escape` Set to false to allow templates to print out HTML content
     *
     * @param string|\Exception $message Message to be flashed. If an instance
     *   of \Exception the exception message will be used and code will be set
     *   in params.
     * @param array $options An array of options
     * @return void
     */
    public function set($message, array $options = []): void
    {
        $options += [
            'escape' => false,
        ];

        parent::set($message, $options);
    }
}
