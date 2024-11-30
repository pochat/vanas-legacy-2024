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
 * Dashboard View
 */
class FaqBookViewDashboard extends JView
{
        /**
         * Dashboard view display method
         * @return void
         */
        function display($tpl = null) {
	
				JHTML::_('behavior.tooltip', '.hasTip');
				jimport('joomla.html.pane');
				$pane	=& JPane::getInstance('sliders');
				
				$this->assignRef( 'pane'		, $pane );
	
				JToolBarHelper::title( "Dashboard: FAQ Book", "faqbook.png");		
		
		    // Settings Icon in Top Bar
				JToolBarHelper::preferences('com_faqbook', '600', '800');
		
				parent::display($tpl);
				}

}