<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Description of RenderGrid
 *
 * @author cincarnato
 */
class CdiGridCrud extends AbstractHelper  {


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
