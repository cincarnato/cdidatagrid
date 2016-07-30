<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CdiDataGrid\DataGrid\Column\InterfaceColumn;

/**
 * @author cincarnato
 */
class CdiGridBtnAdd extends AbstractHelper {

    /**
     * Invoke helper
     *
     * Proxies to {@link render()}.
     *
     * @param  InterfaceColumn $column
     * @param  array $data
     * @return string
     */
    public function __invoke($name, $class, $value,$onclick) {
        return $this->render($name, $class, $value,$onclick);
    }

    /**
     * Render a Btn
     *
     * @param  string $name
     * @param  string $class
     * @param  string $value
     * * @param  string onclick
     * @return string
     */
    public function render($name, $class, $value,$onclick) {

        $output = "<a id='.$name.' name='.$name.' class='" . $class . "' onclick='".$onclick."'>" . $value . "</a>";

        return $output;
    }

}

?>
