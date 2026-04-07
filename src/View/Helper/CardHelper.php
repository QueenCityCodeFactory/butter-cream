<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * CardHelper — Programmatic Bootstrap 5 card builder.
 *
 * ```
 * echo $this->Card->create(['header' => 'Users']);
 * echo $tableContent;
 * echo $this->Card->end(['footer' => $paginationHtml]);
 * ```
 */
class CardHelper extends Helper
{
    use OptionsAwareTrait;
    use StringTemplateTrait;

    /**
     * @var list<string>
     */
    public array $helpers = ['Html' => ['className' => 'ButterCream.Html']];

    /**
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'templates' => [
            'cardStart' => '<div{{attrs}}>',
            'cardEnd' => '</div>',
            'cardHeader' => '<div{{attrs}}>{{content}}</div>',
            'cardHeaderWithButtons' => '<div class="row justify-content-between"><div class="col-auto">{{title}}</div><div class="col-auto card-header-buttons">{{buttons}}</div></div>',
            'cardBody' => '<div{{attrs}}>{{content}}</div>',
            'cardFooter' => '<div{{attrs}}>{{content}}</div>',
            'tableResponsiveStart' => '<div class="table-responsive">',
            'tableResponsiveEnd' => '</div>',
        ],
    ];

    /**
     * Track whether we are inside an open card.
     *
     * @var bool
     */
    protected bool $_open = false;

    /**
     * Open a card. Returns the opening `<div class="card">` plus optional header.
     *
     * ### Options
     *
     * - `header` (string|null)   — Header text / HTML.
     * - `headerButtons` (string) — Buttons HTML to render inside the header (right-aligned).
     * - `headerClass` (array)    — Extra classes for card-header div.
     * - `class` (array|string)   — Extra classes for the card wrapper.
     * - `tableResponsive` (bool) — Wrap card body area in `<div class="table-responsive">`. Default false.
     *
     * @param array<string, mixed> $options Card options.
     * @return string Opening card HTML.
     */
    public function create(array $options = []): string
    {
        $options += [
            'header' => null,
            'headerButtons' => null,
            'headerClass' => [],
            'tableResponsive' => false,
        ];

        $this->_open = true;
        $out = '';

        // Card wrapper
        $wrapperOptions = array_diff_key($options, array_flip([
            'header', 'headerButtons', 'headerClass', 'tableResponsive',
        ]));
        $wrapperOptions = $this->injectClasses(['card'], $wrapperOptions);
        $out .= $this->formatTemplate('cardStart', [
            'attrs' => $this->templater()->formatAttributes($wrapperOptions),
        ]);

        // Header
        if ($options['header'] !== null) {
            $out .= $this->_renderHeader(
                $options['header'],
                $options['headerButtons'],
                $options['headerClass'],
            );
        }

        // Table-responsive wrapper
        if ($options['tableResponsive']) {
            $out .= $this->formatTemplate('tableResponsiveStart', []);
        }

        return $out;
    }

    /**
     * Render a standalone card body block.
     *
     * @param string $content Body content.
     * @param array<string, mixed> $options Extra HTML attributes.
     * @return string
     */
    public function body(string $content, array $options = []): string
    {
        $options = $this->injectClasses(['card-body'], $options);

        return $this->formatTemplate('cardBody', [
            'attrs' => $this->templater()->formatAttributes($options),
            'content' => $content,
        ]);
    }

    /**
     * Close the card. Optionally renders a card footer.
     *
     * ### Options
     *
     * - `footer` (string|null) — Footer HTML.
     * - `footerClass` (array)  — Extra classes for card-footer.
     * - `tableResponsive` (bool) — Close a table-responsive wrapper. Default false.
     *
     * @param array<string, mixed> $options Footer options.
     * @return string Closing card HTML.
     */
    public function end(array $options = []): string
    {
        $options += [
            'footer' => null,
            'footerClass' => [],
            'tableResponsive' => false,
        ];

        $out = '';

        if ($options['tableResponsive']) {
            $out .= $this->formatTemplate('tableResponsiveEnd', []);
        }

        if ($options['footer'] !== null) {
            $footerAttrs = $this->injectClasses(
                array_merge(['card-footer'], (array)$options['footerClass']),
                [],
            );
            $out .= $this->formatTemplate('cardFooter', [
                'attrs' => $this->templater()->formatAttributes($footerAttrs),
                'content' => $options['footer'],
            ]);
        }

        $out .= $this->formatTemplate('cardEnd', []);
        $this->_open = false;

        return $out;
    }

    /**
     * Render a complete self-contained card.
     *
     * @param string $body Card body content.
     * @param array<string, mixed> $options Same as create() + end() options.
     * @return string Complete card HTML.
     */
    public function render(string $body, array $options = []): string
    {
        $footer = $options['footer'] ?? null;
        $footerClass = $options['footerClass'] ?? [];
        $tableResponsive = $options['tableResponsive'] ?? false;
        unset($options['footer'], $options['footerClass']);

        $out = $this->create($options);
        if (!$tableResponsive) {
            $out .= $this->body($body);
        } else {
            $out .= $body;
        }
        $out .= $this->end([
            'footer' => $footer,
            'footerClass' => $footerClass,
            'tableResponsive' => $tableResponsive,
        ]);

        return $out;
    }

    /**
     * Build the card-header markup with optional right-aligned buttons.
     *
     * @param string $title Header title / HTML.
     * @param string|null $buttons Buttons HTML.
     * @param array<string> $extraClasses Extra CSS classes.
     * @return string
     */
    protected function _renderHeader(string $title, ?string $buttons = null, array $extraClasses = []): string
    {
        $classes = array_merge(['card-header'], $extraClasses);

        if ($buttons !== null) {
            $content = $this->formatTemplate('cardHeaderWithButtons', [
                'title' => $title,
                'buttons' => $buttons,
            ]);
        } else {
            $content = $title;
        }

        $attrs = $this->injectClasses($classes, []);

        return $this->formatTemplate('cardHeader', [
            'attrs' => $this->templater()->formatAttributes($attrs),
            'content' => $content,
        ]);
    }
}
