<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_fusion_Actions_wpfs_add_tags' ) ) :

	/**
	 * Load the wpfs_add_tags action
	 *
	 * @since 4.3.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_fusion_Actions_wpfs_add_tags {

	public function get_details(){

		$translation_ident = "action-wpfs_add_tags-content";

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', $translation_ident ) ),
				'tags'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Add the tags you want to add to the user. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple tags to the user, you can either comma-separate them like <code>2,3,12,44</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  23,
  3,
  44
}</pre>
		<?php
		$parameter['tags']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wpfs_add_tags</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $user_id, $validated_tags ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$user_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The id of the user.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$validated_tags</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array of the tags that have been removed from the user.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'Tags have been added to the given user.',
			'data' => 
			array (
			  'user_id' => 155,
			  'tags' => 
			  array (
				0 => 3,
				1 => 1,
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Add tags',
			'webhook_slug' => 'wpfs_add_tags',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user</strong> argument. Please set it to the user id or user email of the user you want to add the tags to.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>tags</strong> argument. This argument accepts a comma-separated list of tag ids, as well as a JSON with each id on a separate line. Please see the argument definition for further information.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'wpfs_add_tags', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Add tags', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'add one or multiple tags', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Add one or multiple tags to a user within WP Fusion.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-fusion',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'tags' => '',
				)
			);

			$user		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$tags		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user argument to either the user id or user email.", 'action-wpfs_add_tags-error' );
				return $return_args;
			}

			if( empty( $tags ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the tags argument.", 'action-wpfs_add_tags-error' );
				return $return_args;
			}

			$validated_tags = array();
			if( WPWHPRO()->helpers->is_json( $tags ) ){
                $validated_tags = json_decode( $tags, true );
            } else {
				$validated_tags = explode( ',', $tags );
			}

            if( ! is_array( $validated_tags ) && ! empty( $validated_tags ) ){
                $validated_tags = array( $validated_tags );
            }

			foreach( $validated_tags as $tk => $tv ){
				$validated_tags[ $tk ] = intval( $tv );
			}

            $user_id = 0;

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user id.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

            $user_tags = wp_fusion()->user->get_tags( $user_id );

			foreach( $validated_tags as $tag ){
				if( ! in_array( $tag, $user_tags ) ){
					wp_fusion()->user->apply_tags( array( $tag ), $user_id );
				}
			}
			
			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "Tags have been added to the given user.", 'action-wpfs_add_tags-success' );
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['tags'] = $validated_tags;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $user_id, $validated_tags );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.