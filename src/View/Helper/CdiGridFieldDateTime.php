<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;

use CdiDataGrid\Column\ColumnInterface;

/**
 * @author cincarnato
 */
class CdiGridFieldDateTime extends AbstractHelper {


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

        if (is_a($data[$column->getName()], "\datetime")){
            return $data[$column->getName()]->format($column->getFormatDatetime());
        }
        
        return '';
                                  
    }

}

?>
