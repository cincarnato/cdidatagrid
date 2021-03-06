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
class CdiGridCrudAjax extends AbstractHelper implements ServiceLocatorAwareInterface {

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

    public function __invoke($gridId,$url) {


        $view = '  <script type="text/javascript">';

        $view .= 'function cdiDeleteRecord(objectId){
        if(confirm("¿Esta seguro que desea eliminar el registro?")){
            cdiPost({crudAction: "delete", crudId: objectId});
      
        }
    }';

        $view .= 'function cdiEditRecord(objectId){
                    cdiPost({crudAction: "edit", crudId: objectId});
                }';

        $view .= 'function cdiListRecords(){
                    cdiPost();
                }'; 

        $view .= 'function cdiViewRecord(objectId){
                    cdiPost({crudAction: "view", crudId: objectId});
                }';


        $view .= 'function cdiAddRecord(){
                    cdiPost({crudAction: "add"});
                }';


        $view .= 'function cdiPagination(url) {
                    $.get(url).done(function (data) {
                    $("#'.$gridId.'").html(data);
                    });
                }';

        $view .= 'function cdiFilter() {
                      $.get("' . $url . '", $("#GridFormFilters").serialize()).done(function (data) {
                          $("#'.$gridId.'").html(data);
                      });
                  }';
        
          $view .= 'function cdiForm(fname) {
                      $.post("' . $url . '", $("#"+fname).serialize()).done(function (data) {
                          $("#'.$gridId.'").html(data);
                      });
                  }';
        
        $view .= 'function cdiOrder(url) {
                      $.get(url).done(function (data) {
                      $("#'.$gridId.'").html(data);
                      });
                  }';
        
        
        $view .= 'function cdiPost(params) {
                    var url = "' . $url . '";  
                    $.post(url,params).done(function (data) {
                    $("#'.$gridId.'").html(data);
                    });
                 }';

        $view .= '</script>';


        return $view;
    }

}

?>
