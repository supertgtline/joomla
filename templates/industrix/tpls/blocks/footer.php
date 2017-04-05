<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>


<!-- FOOTER -->
<footer id="t3-footer" class="container t3-footer">

	<div class="th-box">

	<?php if ($this->countModules('client')) : ?>
		<div class="row"> 
			<div class="col-md-12">
			<!-- Client -->
			<div class="client-logo <?php $this->_c('client') ?>">
				<jdoc:include type="modules" name="<?php $this->_p('client') ?>" style="Xhtml" />
			</div>
			<!-- //Client -->
			</div>
		</div>
	<?php endif ?>


	<?php if ($this->checkSpotlight('footer-sl', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6')) : ?>
		<!-- FOOTER SPOTLIGHT -->
		<div class="th-footer">
			<?php $this->spotlight('footer-sl', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6') ?>
		</div>
		<!-- //FOOTER SPOTLIGHT -->
	<?php endif ?>

	<section class="t3-copyright">
		<div class="">
			<div class="row">
				<div class="col-md-6 col-sm-12">
				<div class="<?php echo $this->getParam('t3-rmvlogo', 1) ? 'col-md-8' : 'col-md-12' ?> copyright <?php $this->_c('footer') ?>">
					<jdoc:include type="modules" name="<?php $this->_p('footer') ?>" />
				</div>
				</div>

				
					<?php if ($this->countModules('footer-right')) : ?>
					<div class="col-md-6 col-sm-12">
					
					<!-- SLIDESHOW -->
					<div class="footer-right <?php $this->_c('footer-right') ?>">
						<jdoc:include type="modules" name="<?php $this->_p('footer-right') ?>" style="raw" />
					</div>
					<!-- //SLIDESHOW -->
					
					</div>
					<?php endif ?>


				

			</div>
		</div>
	</section>
</div>
</footer>
<!-- //FOOTER -->

<!-- BACK TOP TOP BUTTON -->
<div id="back-to-top" data-spy="affix" data-offset-top="10" class="back-to-top hidden-xs hidden-sm ">
  <button class="btn btn-primary" title="Back to Top"><i class="fa fa-chevron-up"></i></button>
</div>

<script type="text/javascript">
(function($) {
	// Back to top
	$('#back-to-top').on('click', function(){
		$("html, body").animate({scrollTop: 0}, 500);
		return false;
	});
})(jQuery);
</script>
<!-- BACK TO TOP BUTTON -->