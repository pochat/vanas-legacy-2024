<?php
/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureBranding extends GantryFeature {
    var $_feature_name = 'branding';

	function render($position="") {
	    ob_start();
	    ?>
	    <div class="rt-block">
			<div id="developed-by">
				<a href="http://www.vanas.ca/" title="vanas.ca" id="rocket"></a>
			</div>
		</div>
		<?php
	    return ob_get_clean();
	}
}