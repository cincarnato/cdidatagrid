<?php

namespace CdiDataGrid\Exception;

class EntityManagerNoSetException extends \RuntimeException implements
    ExceptionInterface
{
    
     /**
     * @var string
     */
    protected $message = 'No entity manager set. Set by ->setEm($em)';
}
