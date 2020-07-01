 <?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Cake\View\Helper;

/**
 * Gravatar Helper
 */
class GravatarHelper extends Helper
{

    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html'];

    /**
     * Takes and email address and options and returns a gravatar
     *
     * This function takes in a users email address and options and then provides either a gravatar image html tag
     * or an image tag with the specified default options.
     *
     * @param string $email The gravatar email address.
     * @param array $options An array specify overrides to the default options
     * - size: The width and height of the profile (150 default)
     * - default: The default gravatar image (mm default) [List Here](http://en.gravatar.com/site/implement/images/)
     * - class: The css class of the image tag (gravatar default)
     * @return string The HTML IMG tag for the gravatar
     */
    public function image(string $email, array $options = []): string
    {
        $options += [
            'size' => 150,
            'default' => 'mm',
            'class' => 'rounded gravatar'
        ];

        $gravatar = "https://www.gravatar.com/avatar/%s?s=%s&d=%s";
        $email = md5(strtolower(trim($email)));
        $url = sprintf($gravatar, $email, $options['size'], $options['default']);
        unset($options['size']);
        unset($options['default']);

        return $this->Html->image($url, $options);
    }
}
