<?php
/*
Plugin Name: Configure Viewport Ninjas
Plugin URI: http://www.CustomWPNinjas.com/
Description: A viewport controls how a webpage is displayed on a mobile device. Without a viewport, mobile devices will render the page at a typical desktop screen width, scaled to fit the screen. Setting a viewport gives control over the page's width and scaling on different devices. 
Version: 1.0.1
Author: CustomWPNinjas
Author URI: http://www.CustomWPNinjas.com/
Contributor: Ishan Kukadia
Tested up to: 4.1
Text Domain: cvn

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

// Constants
define('CVN_PLUGIN_URL', plugins_url() . '/configure-viewport-ninjas');
define('CVN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('CVN_PLUGIN_BASE', plugin_basename( __FILE__ ) );

global $cvn_settings, $send_mail_status;


if (!class_exists('configure_viewport_ninjas')){
	class configure_viewport_ninjas{
		/*
		*  Construct
		*
		*  @description: 
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		
		function __construct() {
			// Actions
			add_action( 'init', array($this, 'cvn_get_settings') );
			add_action( 'admin_menu', array($this, 'cvn_admin_menu' ) );
			add_action( 'admin_head', array($this, 'cvn_admin_head') );
			add_action( 'wp_head', array($this, 'cvn_meta_output') );

			// Filters
			add_filter('plugin_action_links_' . CVN_PLUGIN_BASE , array($this, 'cvn_settings_link') );
		}

		public function cvn_admin_menu(){
			$page_title = 'Configure Viewport Ninjas';
			$menu_title = 'Configure Viewport';
			$capability = 'manage_options';
			$menu_slug = 'configure-viewport-ninjas';
			$icon_url = 'dashicons-hammer';

			add_menu_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'cvn_admin_menu_content'), $icon_url);
		}

		public function cvn_settings_link($actions, $file) {
			$actions['settings'] = '<a href="admin.php?page=configure-viewport-ninjas">Configure</a>';
			return $actions; 
		}

		public function cvn_admin_menu_content(){
			global $cvn_settings, $send_mail_status;
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}
			if (isset($_POST)){
				$this->cvn_save_settings($_POST);
			}
			?>

			<div class="wrap NinjaWrap"><h2>Configure Viewport Ninjas</h2>
				<div class="banner-img">
					<div class="img"><a href="http://wphostingninjas.com/" target="_blank"><img src="<?php echo CVN_PLUGIN_URL; ?>/images/wphostingninjas-banner.jpg" /></a></div>
				</div>

				<form id="cvn-meta" method="post" action="">
					<table class="form-table">
						<tbody>
							<tr>
								<th>Viewport</th>
								<td>
									<?php 
									$checked_f = ($cvn_settings['meta_view'] == 'fixed') ? 'checked="checked"' : ''; 
									$checked_r = ($cvn_settings['meta_view'] == 'responsive') ? 'checked="checked"' : '';
									?>
									<input type="radio" <?php echo $checked_f ;?> id="cvn_fixed" name="cvn_settings[meta_view]" value="fixed"> <label for="cvn_fixed">Fixed Width</label><br />
									<input type="radio" <?php echo $checked_r ;?> id="cvn_responsive" name="cvn_settings[meta_view]" value="responsive"> <label for="cvn_responsive">Responsive</label>
								</td>
							</tr>
							<tr class="cvn_fixed" >
								<th>Width<br /><small>(Numerals Only)</small></th>
								<td>
									<?php $meta_fixed_value = ($cvn_settings['meta_fixed'] != 'fixed') ? $cvn_settings['meta_fixed'] : ''; ?>
									<input type="number" step="0.5" name="cvn_settings[meta_fixed]" value="<?php echo $meta_fixed_value; ?>" />
									<small>Recommended Ranges from 320 to 1024 are best</small>
								</td>
							</tr>
						</tbody>						
					</table>
					<?php wp_nonce_field('cvn_post','cvn_nonce'); ?>
					<?php submit_button('Update' ); ?>
				</form>
				<div class="cvn_help_text">
					<h4>Fixed-Width Viewport</h4>
					<p>The viewport can be set to a specific width, such as width=320 or width=1024. While discouraged, this can be a useful stopgap to ensure pages with fixed dimensions display as expected.</p>

					<h4>Responsive Viewport</h4>
					<p>Using the meta viewport value width=device-width instructs the page to match the screen's width in device independent pixels. This allows the page to reflow content to match different screen sizes.</p>
					<br />
					<p>Some browsers, including iOS and Windows Phone, will keep the page's width constant when rotating to landscape mode, and zoom rather than reflow to fill the screen. Adding the attribute initial-scale=1 instructs browsers to establish a 1:1 relationship between CSS pixels and device independent pixels regardless of device orientation, and allows the page to take advantage of the full landscape width.</p>
					<p>vs Left: An iPhone 5 rotating width=device-width, resulting in a landscape width of 320px. Right: An iPhone 5 rotating width=device-width, initial-scale=1, resulting in a landscape width of 568px.</p>

					<p>Pages must be designed to work at different widths to use a responsive viewport. Contact us <a href="http://www.customwpninjas.com/contact-us">for responsive designs</a>.</p>
				</div>
				<div class="banner-img">
					<div class="img">
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="E9STJGRYXRH24">
							<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
							<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form>
					</div>
				</div>
				<div class="banner-img">
					<div class="img"><a href="http://customwpninjas.com/" target="_blank"><img src="<?php echo CVN_PLUGIN_URL; ?>/images/customwpninjas-banner.jpg" /></a></div>
				</div>
			</div>
			<div class="NinjaForm">
				<div class="contact-form-wrap">
					<div class="contact-form-title">Subscribe To Our FREE SEO News Letter</div>
					<div class="contact-form-content">
						<form action="https://madmimi.com/signups/subscribe/97451" id="mad_mimi_signup_form" method="post" target="_blank">
						   <p>Name:<br/><input data-required-field="This field is required" id="signup_name" name="signup[name]" type="text"/></p>
						   <p>Email:<br/><input data-invalid-email="This field is invalid" data-required-field="This field is required" id="signup_email" name="signup[email]" placeholder="you@example.com" type="text"/></p>
						   <p><input data-choose-list="&#8593; Choose a list" data-default-text="Subscribe" data-invalid-text="&#8593; You forgot some required fields" data-submitting-text="Sending..." id="webform_submit_button" name="commit" type="submit" value="Subscribe"/></p>
						</form>
					</div>
					<hr />
					<div class="contact-form-title">Ask A Ninja</div>
					<?php
						echo $send_mail_status;
					?>
					<div class="contact-form-content">
						<form method="post">
							<p>Name:<br /><input type="text" name="NinjaForm_name" value="" /></p>
							<p>Email:<br /><input type="text" name="NinjaForm_email" value="" /></p>
							<p>Subject:<br /><input type="text" name="NinjaForm_sub" value="" /></p>
							<p>Message:<br /><textarea name="NinjaForm_msg"></textarea></p>
							<p><input type="submit" name="NinjaForm_submit" value="Submit" /></p>
							Not sure how to improve your site? Want help to increase your site's performance from a Ninja? Ask us a question and we will be glad to help!
						</form>
					</div>
				</div>
			</div>

			<?php
		}

		public function cvn_save_settings($post){
			global $cvn_settings, $send_mail_status;
			
			if (isset($post['cvn_settings'])){
				if ($post['cvn_settings'] == $cvn_settings){
					if ($post['cvn_settings']['meta_view'] == 'fixed' && $post['cvn_settings']['meta_fixed'] == ''){
						$this->cvn_show_messages('Please specify the width.', 1);
						return;
					} else {
						$this->cvn_show_messages('Settings saved.');
						return;
					}
				}

				if (isset( $post['cvn_nonce'] ) && wp_verify_nonce( $post['cvn_nonce'], 'cvn_post' ) ){
					if ( update_option( 'cvn_settings', $post['cvn_settings'] ) ){
						if ($post['cvn_settings']['meta_view'] == 'fixed' && $post['cvn_settings']['meta_fixed'] == ''){
							$this->cvn_show_messages('Please specify the width.', 1);
							$this->cvn_get_settings();
						} else {
							$this->cvn_get_settings();
							$this->cvn_show_messages('Settings saved.');
						}
					} else {
						$this->cvn_show_messages('Error updating your settings. Please try again.', 1);
					}
				}
			}

			if (isset($post['NinjaForm_submit'])) {
	
				$message  = 'URL : ' . site_url() . '<br />';
				$message .= 'Name : ' . $post['NinjaForm_name'] . '<br />';
				$message .= 'Email : ' . $post['NinjaForm_email'] . '<br />';
				$message .= 'Subject : ' . $post['NinjaForm_sub'] . '<br />';
				$message .= 'Message : ' . $post['NinjaForm_msg'] . '<br />';
				
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				
				if (!empty($post['NinjaForm_email'])){
					// if(mail ( 'work@rawnuke.com' , 'Msg from GZip Ninja Speed Plugin' , $message, $headers )) {
					if(mail ( 'wordpress@kukadia.in' , 'Msg from Configure Viewport Ninjas Plugin' , $message, $headers )) {
						$send_mail_status = '<div class="contact-form-msg-success">Thank you for contacting us.<br />We will get back to you soon.</div>';
					} else {
						$send_mail_status = '<div class="contact-form-msg-error">Error sending details.</div>';
					}
				} else {
					$send_mail_status = '<div class="contact-form-msg-error">Some or all fields are empty.</div>';
				}
			}
		}

		public function cvn_get_settings(){
			global $cvn_settings;

			$cvn_settings = get_option('cvn_settings');
		}

		public function cvn_show_messages($message, $errormsg = false){
			if ($errormsg) {
				echo '<div id="message" class="error">';
			}
			else {
				echo '<div id="message" class="updated fade">';
			}

			echo "<p><strong>$message</strong></p></div>";
		}

		public function cvn_meta_output(){
			global $cvn_settings;

			if ($cvn_settings['meta_view'] == 'fixed'){
				$meta_output = '<meta name="viewport" content="width='.$cvn_settings['meta_fixed'].'px">';
			} elseif ($cvn_settings['meta_view'] == 'responsive'){
				$meta_output = '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
			}

			if (!is_admin()){
				$return = '<!-- Configure Viewport Ninjas Start -->';
				$return .= $meta_output;
				$return .= '<!-- Configure Viewport Ninjas End -->';
				echo $return;
			}
		}


		public function cvn_admin_head(){
			?>
				<style>
					.cvn_fixed{ display: none; }
					.NinjaWrap{
						float: left;
						width: 70%;
					}
					.NinjaForm{
						float: right;
						width: 25%;
					}
					.contact-form-wrap {
						right:0;
						position: fixed;
						background: #FFFFFF;
						padding: 10px;
						top: 50px;
						border: 3px solid #000000;
						border-right: 0 none;
						width: 250px;
					}
					.contact-form-wrap hr{
						border: 1px solid #000000;
					}
					.contact-form-title {
						font-size: 18px;
						line-height: 20px;
						font-weight: bold;
					}
					.contact-form-content p{
						margin: 5px 0;
					}
					.contact-form-content input[type=text]{
						width: 250px;
					}
					.contact-form-content textarea{
						width: 250px;
						height: 50px;
					}
					.contact-form-msg-success, .settings-form-msg-success {
						color: #00FF00;
						padding: 5px 0;
					}
					.contact-form-msg-error, .settings-form-msg-error {
						color: #FF0000;
					}
					.banner-img {
					    overflow: hidden;
					    padding: 20px 0;
					}
					.banner-img .img {
					    display: block;
					    margin: 0 auto;
					    text-align: center;
					}
				</style>
		 		<script type="text/javascript">
				(function($){
					$(document).ready(function(){
						$('#cvn_fixed').click(function(){
							$('.cvn_fixed').show();
						});
						$('#cvn_responsive').click(function(){
							$('.cvn_fixed').hide();
						});
						if($('#cvn_fixed').is(':checked')) { $('.cvn_fixed').show(); }
					});
				})(jQuery);
		 		</script>
		 	<?php
		}

		
	}

	$cvn = new configure_viewport_ninjas;

}