<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;
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
class CdiGridCrud extends AbstractHelper implements ServiceLocatorAwareInterface {

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

    public function __invoke() {


        $view = '  <script>';

        $view .= 'function cdiDeleteRecord(objectId){
        if(confirm("¿Esta seguro que desea eliminar el registro?")){
            cdiPost({crudAction: "delete", crudId: objectId});
      
        }
    }';

        $view .= 'function cdiEditRecord(objectId){
            cdiPost({crudAction: "edit", crudId: objectId});
    }';
        
                $view .= 'function cdiViewRecord(objectId){
            cdiPost({crudAction: "view", crudId: objectId});
    }';
        
        
           $view .= 'function cdiAddRecord(){
            cdiPost({crudAction: "add"});
    }';




        $view .= 'function cdiPost(params) {
            var goto = window.location.href;  
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", goto);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}';

        $view .= '</script>';


        return $view;
    }

}

?>
