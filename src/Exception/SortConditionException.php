<?php

namespace CdiDataGrid\Exception;

class SortConditionException extends \RuntimeException implements
    ExceptionInterface
{
    
     /**
     * @var string
     */
    protected $message = 'Condition can be ASC or DESC';
}
