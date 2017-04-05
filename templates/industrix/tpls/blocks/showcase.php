<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div class="container">
	<div class="th-box showcase">
		<div class="row">
			<?php if ($this->countModules('showcase-left')) : ?>
			<div class="col-md-7">
			
			<!-- SLIDESHOW -->
			<div class="showcase-left <?php $this->_c('showcase-left') ?>">
				<jdoc:include type="modules" name="<?php $this->_p('showcase-left') ?>" style="raw" />
			</div>
			<!-- //SLIDESHOW -->
			
			</div>
			<?php endif ?>
			<?php if ($this->countModules('showcase-right')) : ?>
			<div class="col-md-5">
			
			<!-- SLIDESHOW -->
			<div class="showcase-right <?php $this->_c('showcase-right') ?>">
				<jdoc:include type="modules" name="<?php $this->_p('showcase-right') ?>" style="raw" />
			</div>
			<!-- //SLIDESHOW -->
			
			</div>
			<?php endif ?>

		</div>
	</div>
</div>