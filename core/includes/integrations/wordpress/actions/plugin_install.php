<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_plugin_install' ) ) :

	/**
	 * Load the plugin_install action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_plugin_install {

		public function is_active(){

			//Backwards compatibility for the "Manage Plugins" integration
			if( defined( 'WPWHPRO_MNGPL_PLUGIN_NAME' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

			$translation_ident = "action-plugin_install-description";

			$parameter = array(
				'plugin_slug'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(string) The plugin slug of the plugin you want to install. Please check the description for further details.', $translation_ident ) ),
				'plugin_download'	 => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The download URL of the plugin version you want to install. Please check the description for further details.', $translation_ident ) ),
				'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks Pro fires this webhook. Please check the description for further details.', $translation_ident ) ),
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This argument contains the slug of a plugin. This isually is the folder name of the plugin, followed by a slash and the plugin file name + file extension. Down below is an example.", $translation_ident ); ?>
<pre>wpwh-comments/wpwh-plugin-file.php</pre>
<?php echo WPWHPRO()->helpers->translate( "The above slug is defined based on the plugin setup. <strong>wpwh-comments</strong> is the name of the plugin folder. <strong>wpwh-plugin-file.php</strong> is the file name of the plugin file within the folder (The file where you defined your plugin details within the comment).", $translation_ident ); ?>
<?php
			$parameter['plugin_slug']['description'] = ob_get_clean();

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This argument contains the download URL to the ZIP file of your plugin. In case your plugin is hosted on WordPress.org, you can use the URL down below.", $translation_ident ); ?>
<pre>https://downloads.wordpress.org/plugin/wp-webhooks-comments.latest-stable.zip</pre>
<?php echo WPWHPRO()->helpers->translate( "The URL above adds the latest version of the plugin wp-webhooks-comments. If you want to customize it, just change the slug from <strong>wp-webhooks-comments</strong> to your WordPress slug.", $translation_ident ); ?>
<?php
			$parameter['plugin_download']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>plugin_install</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $plugin_slug, $check, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$plugin_slug</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The currently given slug (+ filename) of the plugin. (The given data from the plugin_slug argument)", $translation_ident ); ?>
	</li>
	<li>
		<strong>$check</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "True if the plugin was successfully installed, false if not.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response the the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Plugin successfully installed.',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Install a plugin',
				'webhook_slug' => 'plugin_install',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'The second argument is <strong>plugin_slug</strong>, which contains the slug of the given plugin.', $translation_ident ),
					WPWHPRO()->helpers->translate( 'It is also required to define the <strong>plugin_download</strong> argument. You need to set the plugin ZIP file here. This file will be the one you install.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'plugin_install',
				'name'			  => WPWHPRO()->helpers->translate( 'Install plugin', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'install a plugin', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to install a plugin within your WordPress website.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$plugin_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'plugin_helpers' );
			$return_args = array(
				'success' => false
			);

			$plugin_slug	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'plugin_slug' );
			$plugin_download	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'plugin_download' );
			$do_action	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $plugin_slug ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the plugin_slug to continue.", 'action-plugin_install-failure' );
				return $return_args;
			}
			
			if( empty( $plugin_download ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the plugin_download to continue.", 'action-plugin_install-failure' );
				return $return_args;
			}

			$check = $plugin_helpers->install( $plugin_slug, $plugin_download, array( 'prevent_outputs' => true ) );
			if( $check ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Plugin successfully installed.", 'action-plugin_install-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The plugin was not installed.", 'action-plugin_install-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $plugin_slug, $check, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.