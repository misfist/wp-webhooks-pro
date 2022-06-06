<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_remove_group_access' ) ) :

	/**
	 * Load the ld_remove_group_access action
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_remove_group_access {

	public function get_details(){

		$translation_ident = "action-ld_remove_group_access-content";

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The user id (or user email) of the user you want to remove the group access from.', $translation_ident ) ),
				'group_ids'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add the group IDs of the groups you want to remove the access for. This argument accepts the value "all" to remove access to all groups of the user, a single group id, or a comma-separated string of group IDs.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the value 'all' to set all groups as completed, a single group id, as well as multiple group ids, separated by commas (Multiple group ids will set all the groups to completed for the given course of the specified user):", $translation_ident ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['group_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_remove_group_access</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The group access has been successfully removed.',
			'data' => 
			array (
			  'user_id' => 1,
			  'group_ids' => '8080',
			  'removed_access' => 
			  array (
				8080 => 
				array (
				  'user_id' => 1,
				  'group_id' => 8080,
				  'response' => true,
				),
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Remove group access',
			'webhook_slug' => 'ld_remove_group_access',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user_id</strong> argument. You can either set it to the user id or the user email of which you want to grant the group access for.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>group_id</strong> argument to the id of the group you want to grant access to the given user.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'ld_remove_group_access', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Remove group access', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'remove group access from a user', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Remove group access for a user within Learndash.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'learndash',
			'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$ld_helpers = WPWHPRO()->integrations->get_helper( 'learndash', 'ld_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'group_ids' => 0,
					'removed_access' => false,
				),
			);

			$user_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$group_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'group_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) || empty( $group_ids ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user_id and group_ids arguments.", 'action-ld_remove_group_access-error' );
				return $return_args;
			}

			if( is_numeric( $user_id ) ){
				$user_id = intval( $user_id );
			} elseif( ! empty( $user_id ) && is_email( $user_id ) ) {
				$user_data = get_user_by( 'email', $user_id );
				if( ! empty( $user_data ) && isset( $user_data->ID ) ){
					$user_id = $user_data->ID;
				}
			}

			$removed_access = array();
			if( $group_ids === 'all' ){
				$user_groups = learndash_get_users_group_ids( $user_id );
			} else {
				$user_groups_array = array_map( "trim", explode( ',', $group_ids ) );
				$user_groups = array();
				foreach( $user_groups_array as $sugk => $sugv ){
					$user_groups[ $sugk ] = intval( $sugv );
				}
			}

			foreach( $user_groups as $group_id ){

				if( ! is_numeric( $group_id ) ){
					continue;
				}

				$removed_access[ $group_id ] = array(
					'user_id' => $user_id,
					'group_id' => $group_id,
					'response' => ld_update_group_access( $user_id, $group_id, true ),
				);
			}

			if( ! empty( $removed_access ) ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The group access has been successfully removed.", 'action-ld_remove_group_access-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['group_ids'] = $group_ids;
				$return_args['data']['removed_access'] = $removed_access;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "No group access has been removed for the given user within Learndash.", 'action-ld_remove_group_access-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['group_ids'] = $group_ids;
				$return_args['data']['removed_access'] = $removed_access;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.