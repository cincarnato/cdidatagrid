<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CdiDataGrid\DataGrid\Column\InterfaceColumn;

/**
 * @author cincarnato
 */
class CdiGridFieldLongText extends AbstractHelper {


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
     * Render a Field from the provided $column and $data
     *
     * @param  InterfaceColumn $column
     * @param  array $data
     * @return string
     */
    public function render(InterfaceColumn $column, array $data) {

        return substr(nl2br($data[$column->getName()]), 0, $column->getLength())."..." ;
    }

}

?>