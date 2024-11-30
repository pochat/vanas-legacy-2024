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
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * FAQ Book Component Controller
 */
class FaqBookController extends JController {

			function display() {
        // Make sure we have a default view
        if( !JRequest::getVar( 'view' )) {
            JRequest::setVar('view', 'faqbook' );
        }
        parent::display();
    }

}