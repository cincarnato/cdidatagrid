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
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

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
