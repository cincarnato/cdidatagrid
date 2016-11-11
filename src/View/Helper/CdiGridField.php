<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use CdiDataGrid\Column\InterfaceColumn;

/**
 * @author cincarnato
 */
class CdiGridField extends AbstractHelper {

    const DEFAULT_HELPER = 'CdiGridFieldText';

    /**
     * Instance map to view helper
     *
     * @var array
     */
    protected $typeMap = array(
        'string' => 'CdiGridFieldString',
        'text' => 'CdiGridFieldText',
        'boolean' => 'CdiGridFieldBoolean',
        'datetime' => 'CdiGridFieldDateTime',
        'link' => 'CdiGridFieldLink',
        'extra' => 'CdiGridFieldExtra',
        'longText' => 'CdiGridFieldLongText',
        'custom' => 'CdiGridFieldCustom',
    );

    /**
     * Default helper name
     *
     * @var string
     */
    protected $defaultHelper = self::DEFAULT_HELPER;

    /**
     * Invoke helper
     *
     * Proxies to {@link render()}.
     *
     * @param  InterfaceColumn $column
     * @param  array $data
     * @return string
     */
    public function __invoke(InterfaceColumn $column, array $data) {



        return $this->render($column, $data);
    }

    /**
     * Render an field
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  InterfaceColumn $column
     * @param  array $data
     * @return string
     */
    public function render(InterfaceColumn $column, array $data) {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }


        $renderedType = $this->renderType($column, $data);

        if ($renderedType !== null) {
            return $renderedType;
        }

        return $this->renderHelper($this->defaultHelper, $column, $data);
    }

    /**
     * Render element by type map
     *
     * @param ElementInterface $element
     * @return string|null
     */
    protected function renderType(InterfaceColumn $column, array $data) {
        if (isset($this->typeMap[$column->getType()])) {
            return $this->renderHelper($this->typeMap[$column->getType()], $column, $data);
        }
        return null;
    }

    /**
     * Render element by helper name
     *
     * @param string $name
     * @param ElementInterface $element
     * @return string
     */
    protected function renderHelper($name, InterfaceColumn $column, array $data) {
        $helper = $this->getView()->plugin($name);
        return $helper($column, $data);
    }

    /**
     * Set default helper name
     *
     * @param string $name
     * @return self
     */
    public function setDefaultHelper($name) {
        $this->defaultHelper = $name;
        return $this;
    }

    /**
     * Add form element type to plugin map
     *
     * @param string $type
     * @param string $plugin
     * @return self
     */
    public function addType($type, $plugin) {
        $this->typeMap[$type] = $plugin;

        return $this;
    }

}

?>
