<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RenderGrid
 *
 * @author cincarnato
 */
class Clink extends AbstractHelper implements ServiceLocatorAwareInterface {

    /**
     * Set the service locator. 
     * 
     * @param ServiceLocatorInterface $serviceLocator 
     * @return CustomHelper 
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the service locator. 
     * 
     * @return \Zend\ServiceManager\ServiceLocatorInterface 
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function __invoke($clink, $data) {
        foreach ($clink as $value) {
            if($value["path"]){
                    $output.= $value["path"];
            }
            
            if($value["data"]){
                if($data[$value["data"]]){
                    $output.= $data[$value["data"]]->getId();
                }
            }
        
        }

        return $output;
    }

}

?>
