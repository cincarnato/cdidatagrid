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
class JsAbmAjaxModal extends AbstractHelper implements ServiceLocatorAwareInterface {

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

    public function __invoke($nameObject, $urlEdit, $urlSubmit) {
        
        $return = "<div class='modal fade' id='cdiModal'>
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                <h4 class='modal-title'><div id='cdiTitle'>Edicion</div></h4>
            </div>
            <div class='modal-body'>
                <div id='cdiAjaxContent'></div>
            </div>
        </div>
    </div>
</div>";

        $return .= "<script>

    function cdiGoEdit(id) {
        $.get('$urlEdit', {id: id})
                .done(function(data) {
            $('#cdiTitle').html('Edicion');
            $('#cdiAjaxContent').html(data);
            $('#cdiModal').modal('show');

        });
    }
    
    
        function cdiSubmitEdit() {
        var datastring = $('#$nameObject').serialize();
        $.post('$urlSubmit', datastring)
                .done(function(data) {
            $('#cdiAjaxContent').html(data);

            var patt = /Error/;

            if (!patt.test(data)) {
                setTimeout('refrescar()', 1000);
            }
        });
    }
    
    
      function refrescar()
    {
        window.location.reload();
    }
</script>  ";


        return $return;
    }

}

?>
