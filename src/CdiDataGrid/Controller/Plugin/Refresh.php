<?php

/**
* TITLE
*
* Description
*
* @author Cristian Incarnato <cristian.cdi@gmail.com>
*
* @package Paquete
*/

namespace CdiDatagrid\Controller\Plugin;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
/**
 * Easily export data to downloadable CSV files.
 */
class Refresh extends AbstractPlugin
{

	/**
	 *
	 * @return 
	 */
	public function __invoke()
	{
            return $this->getController()->getAction();
	}
	
}
