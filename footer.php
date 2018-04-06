<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>

		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'storefront_before_footer' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		
			<div class="u-sizeFull">
				<div class="Footer-channels u-displayFlex u-flexDirectionRow u-paddingTop">
					<div class="u-paddingVertical u-maxSize--container u-sizeFull u-alignCenterBox u-displayFlex u-flexSwitchReverse--mobile u-flexDirectionRow u-paddingBottom--inter">
						<div class="Newsletter u-sizeFull u-displayFlex u-flexSwitchReverse--mobile u-flexDirectionRow">
							<h3 class="u-sizeFull u-marginBottom--inter--half u-alignCenter Newsletter-title">Receba as nossas novidades no seu e-mail</h3>
							<?php get_template_part('template-parts/forms/form','newsletter-footer');?>
						</div>
						<div class="SocialMedia u-sizeFull u-displayFlex u-flexJustifyContentCenter">
							<ul class="SocialMedia-items u-displayFlex u-flexDirectionRow">
								<li class="SocialMedia-items-item SocialMedia-items-item--facebook">
									<a class="SocialMedia-items-item-link is-animating" href="https://www.facebook.com/dogmastorebr/" target="_blank"></a>
								</li>
								<li class="SocialMedia-items-item SocialMedia-items-item--instagram">
									<a class="SocialMedia-items-item-link is-animating" href="https://www.instagram.com/dogma.store/" target="_blank"></a>
								</li>
							</ul>
						</div>	
					</div>
							
				</div><!-- .Footer-channels -->

				<div class="Footer-navigations">
					
						<?php
						/**
						 * Functions hooked in to storefront_footer action
						 *
						 * @hooked storefront_footer_channels - 10
						 * @hooked storefront_footer_widgets - 10
						 * @hooked storefront_credit         - 20
						 */
						do_action( 'storefront_footer' ); ?>

				</div>

			</div><!-- .sizeFull -->
		
	</footer><!-- #colophon -->

	<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() . '/assets/js/main.min.js'; ?>"></script>

<?php wp_footer(); ?>


</body>
</html>
