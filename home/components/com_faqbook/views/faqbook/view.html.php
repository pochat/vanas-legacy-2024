<?php
/**
* @title   Minitek FAQ Book
* @version   1.5.2
* @copyright   Copyright (C) 2011-2012 Minitek, All rights reserved.
* @license   GNU General Public License version 2 or later.
* @author url   http://www.minitek.gr/
* @author email   info@minitek.gr
* @developer   Ioannis Maragos - minitek.gr
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML Index View class for the FAQ Book Component
 */
class FaqBookViewFaqBook extends JView
{
        // Overwriting JView display method
        function display($tpl = null) 
        {
				 				$app			= JFactory::getApplication();
								$params 		= &$app->getParams(); 
								$model			= &$this->getModel();
								$rows      	= &$this->get( 'Data');		
								
								// Get return from getIndexItemid
								$this->searchitemid = $this->get('SearchItemid');
								
								if (JRequest::getVar( 'Itemid', '' )) {
								  $url_itemid = JRequest::getVar( 'Itemid', '' );
								  if (!is_numeric($url_itemid)) {
								    die('Restricted access');
								  }
								}
													
                // Assign data to the view
                $this->assignRef('params',				$params);
								$this->assignRef('rows'      	, $rows);								
						
                // Check for errors.
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                // Display the view
                parent::display($tpl);
        }
}