<h3><?php _e( 'Custom Payment Settings', 'woocommerce-custom-payment-gateway' ); ?></h3>
<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">
			<table class="form-table">
				<?php $this->generate_settings_html();?>
			</table><!--/.form-table-->
		</div>
		<div id="postbox-container-1" class="postbox-container">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox " id="wpruby_support">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><i class="fa fa-question-circle"></i>&nbsp;&nbsp;Plugin Support</span></h3>
					<div class="inside">
						<div class="support-widget">
							<p>
								<img style="width:100%;" src="https://wpruby.com/wp-content/uploads/2016/03/wpruby_logo_with_ruby_color-300x88.png">
								<br/>
								Got a Question, Idea, Problem or Praise?</p>
							<ul>
								<li>» <a href="https://wpruby.com/submit-ticket/" target="_blank">Support Request</a></li>
								<li>» <a href="https://wpruby.com/knowledgebase-category/woocommerce-custom-payment-gateway/" target="_blank">Documentation and Common issues.</a></li>
								<li>» <a href="https://wpruby.com/plugins/" target="_blank">Our Plugins Shop</a></li>
							</ul>

						</div>
					</div>
				</div>

				<div class="postbox rss-postbox" id="wpruby_rss">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><i class="fa fa-wordpress"></i>&nbsp;&nbsp;WPRuby Blog</span></h3>
					<div class="inside">
						<div class="rss-widget">
							<?php
							try{
								wp_widget_rss_output(array(
									'url' => 'https://wpruby.com/feed/',
									'title' => 'WPRuby Blog',
									'items' => 3,
									'show_summary' => 0,
									'show_author' => 0,
									'show_date' => 1,
								));
							} catch (Exception $e) {
								echo 'Remote calls are disabled!!';
							}
							?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<div class="clear"></div>