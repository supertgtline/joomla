<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="module <?php echo $params->get('moduleclass_sfx'); ?>">
	<small><?php echo $lineone; ?> Designed by <a href="http://www.themehippo.com/" title="Visit themehippo.com!" <?php echo method_exists('T3', 'isHome') && T3::isHome() ? '' : 'rel="nofollow"' ?>>themehippo.com</a>.</small>
	<small><?php echo JText::_( 'MOD_FOOTER_LINE2' ); ?></small>
</div>