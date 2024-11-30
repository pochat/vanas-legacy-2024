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
 * About View
 */
class FaqBookViewAbout extends JView
{
        /**
         * About view display method
         * @return void
         */
        function display($tpl = null) {		
			
			  $this->addToolbar();		
				parent::display($tpl);
				}
				
				protected function addToolbar()
				{
				JToolBarHelper::title(JText::_('About: FAQ Book'), 'info.png');
				
				// Settings Icon in Top Bar
				JToolBarHelper::preferences('com_faqbook', '600', '800');
				}

}