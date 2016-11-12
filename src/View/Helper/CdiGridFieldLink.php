<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use CdiDataGrid\Column\ColumnInterface;

/**
 * @author cincarnato
 */
class CdiGridFieldLink extends AbstractHelper {


    /**
     * Invoke helper
     *
     * Proxies to {@link render()}.
     *
     * @param  ColumnInterface $column
     * @param  array $data
     * @return string
     */
    public function __invoke(ColumnInterface $column, array $data) {



        return $this->render($column, $data);
    }

    /**
     * Render a Field from the provided $column and $data
     *
     * @param  ColumnInterface $column
     * @param  array $data
     * @return string
     */
    public function render(ColumnInterface $column, array $data) {

        return '<a target="_blank" href="'.$data[$column->getName()].'">LINK</a>';
    }

}

?>
