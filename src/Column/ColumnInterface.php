<?php

namespace CdiDataGrid\Column;

/**
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
interface ColumnInterface {

    public function getName();

    public function setName($name);

    public function getType();

    public function getHidden();

    public function setHidden($hidden);

    public function getDisplayName();

    public function setDisplayName($name);
    //TODO...
}
