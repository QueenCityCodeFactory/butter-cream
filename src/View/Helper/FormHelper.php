<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\FormHelper as Helper;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\View;

/**
 * Form Helper
 */
class FormHelper extends Helper
{

    /**
     * Returns an HTML FORM element.
     *
     * @param mixed $context The context for which the form is being defined.
     *   Can be a ContextInterface instance, ORM entity, ORM resultset, or an
     *   array of meta data. You can use `null` to make a context-less form.
     * @param array $options An array of html attributes and options.
     * @return string An formatted opening FORM tag.
     */
    public function create($context = null, array $options = []): string
    {
        $options += ['novalidate' => true];

        $noreferer = $options['noreferer'] ?? null;
        unset($options['noreferer']);

        $out = parent::create($context, $options);

        if (empty($noreferer)) {
            $out .= parent::hidden('Referer.url', ['value' => $this->_View->get('referer')]);
        }

        return $out;
    }

    /**
     * Creates an HTML link, but access the URL using the method you specify
     * (defaults to POST). Requires javascript to be enabled in browser.
     *
     * This method creates a `<form>` element. If you want to use this method inside of an
     * existing form, you must use the `block` option so that the new form is being set to
     * a view block that can be rendered outside of the main form.
     *
     * If all you are looking for is a button to submit your form, then you should use
     * `FormHelper::button()` or `FormHelper::submit()` instead.
     *
     * ### Options:
     *
     * - `data` - Array with key/value to pass in input hidden
     * - `method` - Request method to use. Set to 'delete' to simulate
     *   HTTP/1.1 DELETE request. Defaults to 'post'.
     * - `confirm` - Confirm message to show. Form execution will only continue if confirmed then.
     * - `block` - Set to true to append form to view block "postLink" or provide
     *   custom block name.
     * - Other options are the same of HtmlHelper::link() method.
     * - The option `onclick` will be replaced.
     *
     * @param string $title The content to be wrapped by <a> tags.
     * @param string|array|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array $options Array of HTML attributes.
     * @return string An `<a />` element.
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#creating-standalone-buttons-and-post-links
     */
    public function postLink(string $title, $url = null, array $options = []): string
    {
        $options += ['block' => null, 'confirm' => null];

        $requestMethod = 'POST';
        if (!empty($options['method'])) {
            $requestMethod = strtoupper($options['method']);
            unset($options['method']);
        }

        $confirmText = $options['confirm'];
        unset($options['confirm']);

        $formName = str_replace('.', '', uniqid('post_', true));
        $formOptions = [
            'name' => $formName,
            'style' => 'display:none;',
            'method' => 'post',
        ];
        if (isset($options['target'])) {
            $formOptions['target'] = $options['target'];
            unset($options['target']);
        }
        $templater = $this->templater();

        $restoreAction = $this->_lastAction;
        $this->_lastAction($url);
        $restoreFormProtector = $this->formProtector;

        $action = $templater->formatAttributes([
            'action' => $this->Url->build($url),
            'escape' => false
        ]);

        $out = $this->formatTemplate('formStart', [
            'attrs' => $templater->formatAttributes($formOptions) . $action
        ]);
        $out .= $this->hidden('_method', [
            'value' => $requestMethod,
            'secure' => static::SECURE_SKIP
        ]);
        $out .= $this->_csrfField();

        $formTokenData = $this->_View->getRequest()->getAttribute('formTokenData');
        if ($formTokenData !== null) {
            $this->formProtector = $this->createFormProtector($formTokenData);
        }

        $fields = [];
        if (isset($options['data']) && is_array($options['data'])) {
            foreach (Hash::flatten($options['data']) as $key => $value) {
                $fields[$key] = $value;
                $out .= $this->hidden($key, ['value' => $value, 'secure' => static::SECURE_SKIP]);
            }
            unset($options['data']);
        }
        $out .= $this->secure($fields);
        $out .= $this->formatTemplate('formEnd', []);

        $this->_lastAction = $restoreAction;
        $this->formProtector = $restoreFormProtector;

        if ($options['block']) {
            if ($options['block'] === true) {
                $options['block'] = __FUNCTION__;
            }
            $this->_View->append($options['block'], $out);
            $out = '';
        }
        unset($options['block']);

        $url = '#';
        $options += ['data-form-name' => $formName];
        if (!empty($options['class'])) {
            $options['class'] .= ' modal-confirm';
        } else {
            $options += ['class' => 'modal-confirm'];
        }

        if ($confirmText) {
            $options += ['data-modal' => 1, 'data-modal-message' => $confirmText];
        } else {
            $options += ['data-modal' => 0];
        }

        $out .= $this->Html->link($title, $url, $options);

        return $out;
    }

    /**
     * Delete Button for Index Pages
     *
     * @param string|int $primaryKey The primary key
     * @param array $options The standard options for links
     * @return string HTML Link wrapped in a form
     */
    public function deleteBtn($primaryKey, array $options = []): string
    {
        $options += [
            'escape' => false,
            'title' => 'Delete',
            'class' => 'btn btn-outline-danger btn-sq-xs btn-xs'
        ];

        return $this->postLink($this->Html->icon('trash'), ['action' => 'delete', $primaryKey], $options);
    }

    /**
     * Creates a `<button>` tag. It is preset with save icon font awesome class and title for the button.
     *
     * @param string $title The button's caption. Not automatically HTML encoded
     * @param array $options Array of options and HTML attributes.
     * @return string A HTML button tag.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::button
     */
    public function saveButton($title = null, array $options = []): string
    {
        if (empty($title)) {
            $title = $this->Html->icon('save') . ' Save';
        }
        $options += [
            'class' => 'btn btn-success'
        ];

        return parent::button($title, $options);
    }

    /**
     * Creates an `a` tag not a true button. It is preset with font awesome class and title for the button.
     *
     * @param string $title The button's caption. Not automatically HTML encoded
     * @param array $options Array of options and HTML attributes.
     * @return string A HTML button tag.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::button
     */
    public function cancelButton($title = null, array $options = []): string
    {
        if (empty($title)) {
            $title = $this->Html->icon('arrow-circle-left') . ' Cancel';
        }
        $options += [
            'class' => 'btn btn-danger',
            'escape' => false,
            'confirm' => 'Are you sure you want to cancel?'
        ];

        return $this->Html->link($title, $this->_View->get('referer'), $options);
    }

    /**
     * Creates an `button` tag. It is preset with font awesome class and title for the button.
     *
     * @param string $title The button's caption. Not automatically HTML encoded
     * @param array $options Array of options and HTML attributes.
     * @return string A HTML button tag.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::button
     */
    public function backButton(string $title = '<em class="fa fa-arrow-circle-left"></em> Back', array $options = []): string
    {
        if (empty($title)) {
            $title = $this->Html->icon('arrow-circle-left') . ' Back';
        }
        $options += [
            'class' => 'btn btn-info',
            'escape' => false,
            'confirm' => 'Are you sure you want go back to the previous page? Your changes on the current page will not be saved!'
        ];

        return $this->Html->link($title, $this->_View->get('referer'), $options);
    }

    /**
     * Creates an `a` tag not a true button. It is preset with font awesome class and title for the button.
     *
     * @param string $title The button's caption. Not automatically HTML encoded
     * @param array $options Array of options and HTML attributes.
     * @return string A HTML button tag.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::button
     */
    public function continueButton(string $title = 'Continue <em class="fa fa-arrow-circle-right"></em>', array $options = []): string
    {
        if (empty($title)) {
            $title = 'Continue ' . $this->Html->icon('arrow-circle-right');
        }
        $options += ['class' => 'btn btn-primary continue-button'];

        return parent::button($title, $options);
    }

    /**
     * Creates an `button` tag. It is preset with font awesome class and title for the button.
     *
     * @param string $title The button's caption. Not automatically HTML encoded
     * @param array $options Array of options and HTML attributes.
     * @return string A HTML button tag.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::button
     */
    public function confirmButton($title = null, array $options = []): string
    {
        $options += [
            'class' => 'btn btn-primary confirm-button',
            'confirm' => null
        ];

        $confirmTitle = null;
        $confirmText = null;
        if (is_array($options['confirm'])) {
            $confirmTitle = isset($options['confirm']['title']) ? $options['confirm']['title'] : null;
            $confirmText = isset($options['confirm']['text']) ? $options['confirm']['text'] : null;
        } else {
            $confirmText = $options['confirm'];
        }

        unset($options['confirm']);

        if ($confirmText && isset($options['data-form-name'])) {
            $options += [
                'data-modal' => 1,
                'data-modal-message' => $confirmText
            ];
            if ($confirmTitle) {
                $options['data-original-title'] = $confirmTitle;
            }
            if (!empty($options['class'])) {
                $options['class'] .= ' modal-confirm';
            } else {
                $options += ['class' => 'modal-confirm'];
            }
        }

        return parent::button($title, $options);
    }

    /**
     * Returns a formatted error message for given form field, '' if no errors.
     *
     * Uses the `error`, `errorList` and `errorItem` templates. The `errorList` and
     * `errorItem` templates are used to format multiple error messages per field.
     *
     * ### Options:
     *
     * - `escape` boolean - Whether or not to html escape the contents of the error.
     *
     * @param string $field A field name, like "modelname.fieldname"
     * @param string|array|null $text Error message as string or array of messages. If an array,
     *   it should be a hash of key names => messages.
     * @param array $options See above.
     * @return string Formatted errors or ''.
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#displaying-and-checking-errors
     */
    public function error(string $field, $text = null, array $options = []): string
    {
        if (isset($text['escape']) && $text['escape'] === false) {
            $options['escape'] = false;
        }

        return parent::error($field, $text, $options);
    }

    /**
     * Creates a Tempus Dominus Bootstrap 4 DateTimePicker. Using this helper requires Bootstrap 4,
     * the tempusdominus datetimepicker javascript, and a fetch for the 'script' block i.e. <?= $this->fetch('script') ?>
     * somewhere in your view. If using another view block called 'script' ->start('script') will clear the contents inserted
     * by this helper. To override the JS this helper outputs, set options['script'] false and add your JS to the view your picker
     * is on. If you only intend to override the datetimepicker's object literal parameters, just pass your options into
     * options['datetimepicker']['options']. If a placeholder is passed, any value will be wiped out - the datetimepicker will not have
     * a value and show a placeholder. If frontEndTimezoneConversion is not used, you're responsible for adjusting the time
     * read from the database before it gets to this helper. The helper will still give the guessed timezone abbreviation based on
     * the timezone given by the browser. If you'd like to override this, add that functionality to this helper.
     *
     * @param string $fieldName A field name, like "modelname.fieldname"
     * @param array $options Array of options and HTML attributes
     * @return string A form-group div containing the datetimepicker
     */
    public function dateTimePicker(string $fieldName, array $options = []): string
    {
        $domId = $this->_domId($fieldName);

        $options += [
            'type' => 'text',
            'target' => $domId . '-datetimepicker',
            'prepend' => '<em class="fa fa-calendar"></em>',
            'placeholder' => false,
            'frontEndTimezoneConversion' => false,
            'script' => true,
            'datetimepicker' => [
                'options' => [
                    'timezone: moment.tz.guess()',
                    'format: "MM/DD/YYYY hh:mm a z"',
                    'icons: { time: "fas fa-clock" }'
                ]
            ],
            'class' => ['datetimepicker-input']
        ];

        $target = $options['target'];
        unset($options['target']);
        $placeholder = $options['placeholder'];
        unset($options['placeholder']);
        $frontEndTimezoneConversion = $options['frontEndTimezoneConversion'];
        unset($options['frontEndTimezoneConversion']);
        $script = $options['script'];
        unset($options['script']);
        $datetimepicker = $options['datetimepicker'];
        unset($options['datetimepicker']);

        $options += [
            'templates' => [
                'inputGroupAddon' => '<div class="{{class}}" data-target="#' . $target . '" data-toggle="datetimepicker">{{content}}</div>',
                'inputGroupContainer' => '<div{{attrs}} id="' . $target . '" data-target-input="nearest">{{prepend}}{{content}}{{append}}</div>'
            ],
            'data-target' => '#' . $target,
            'data-toggle' => 'datetimepicker'
        ];

        $placeholderJs = '';
        if (!empty($placeholder)) {
            $placeholderJs .= "\n\t$(\"#" . $domId . "\").attr('placeholder', '" . $placeholder . "');\n";
        }

        $timezone = 'moment.tz.guess()';
        $conversionJs = '';
        if ($frontEndTimezoneConversion === true) {
            $timezone = '\'UTC\'';
            $conversionJs = "\tif ($(\"#" . $domId . "\").val()) {";
            $conversionJs .= "\n\t\t$(\"#" . $target . "\").datetimepicker('date', $(\"#" . $target . "\").datetimepicker('viewDate').tz(moment.tz.guess()));";
            $conversionJs .= "\n\t}";
            $conversionJs .= "\n\tmoment.tz.setDefault(moment.tz.guess());\n";
        }

        if ($script === true) {
            // This will load into a block called 'script' by default
            $this->Html->scriptBlock(
                "$(function() {\n\tmoment.tz.setDefault(" . $timezone . ");\n\t$(\"#" . $target . "\").datetimepicker({\n\t\t" . join(",\n\t\t", $datetimepicker['options']) . "\n\t});\n" . $conversionJs . $placeholderJs . '});',
                ['block' => true]
            );
        }

        return $this->control($fieldName, $options);
    }

    /**
     * Generates a form control element complete with label and wrapper div.
     *
     * ### Options
     *
     * See each field type method for more information. Any options that are part of
     * $attributes or $options for the different **type** methods can be included in `$options` for control().
     * Additionally, any unknown keys that are not in the list below, or part of the selected type's options
     * will be treated as a regular HTML attribute for the generated input.
     *
     * - `type` - Force the type of widget you want. e.g. `type => 'select'`
     * - `label` - Either a string label, or an array of options for the label. See FormHelper::label().
     * - `options` - For widgets that take options e.g. radio, select.
     * - `error` - Control the error message that is produced. Set to `false` to disable any kind of error reporting
     *   (field error and error messages).
     * - `empty` - String or boolean to enable empty select box options.
     * - `nestedInput` - Used with checkbox and radio inputs. Set to false to render inputs outside of label
     *   elements. Can be set to true on any input to force the input inside the label. If you
     *   enable this option for radio buttons you will also need to modify the default `radioWrapper` template.
     * - `templates` - The templates you want to use for this input. Any templates will be merged on top of
     *   the already loaded templates. This option can either be a filename in /config that contains
     *   the templates you want to load, or an array of templates to use.
     * - `labelOptions` - Either `false` to disable label around nestedWidgets e.g. radio, multicheckbox or an array
     *   of attributes for the label tag. `selected` will be added to any classes e.g. `class => 'myclass'` where
     *   widget is checked
     * - `select2` - turn on/off select2
     *
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array $options Each type of input takes different options.
     * @return string Completed form widget.
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#creating-form-inputs
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function control(string $fieldName, array $options = []): string
    {
        $options += [
            'type' => null,
            'select2' => true
        ];

        if ($options['select2'] !== false && isset($options['options']) && is_array($options)) {
            if (empty($options['type'])) {
                $options['type'] = $this->_inputType($fieldName, $options);
            }

            if ($options['type'] == 'select') {
                $options += [
                    'class' => ['select2-input-field']
                ];
            }
        }

        unset($options['select2']);

        return parent::control($fieldName, $options);
    }
}
