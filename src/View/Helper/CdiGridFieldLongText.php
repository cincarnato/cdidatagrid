<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use CdiDataGrid\Column\ColumnInterface;

/**
 * @author cincarnato
 */
class CdiGridFieldLongText extends AbstractHelper {


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

        return substr(nl2br($data[$column->getName()]), 0, $column->getLength())."..." ;
    }

}

?>
