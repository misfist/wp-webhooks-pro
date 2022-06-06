<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_complete_lessons' ) ) :

	/**
	 * Load the ld_complete_lessons action
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_complete_lessons {

	public function get_details(){

		$translation_ident = "action-ld_complete_lessons-content";

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The user id (or user email) of the user you want to set the lesson to completed.', $translation_ident ) ),
				'course_id'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The id of the course you want to set the lesson to completed.', $translation_ident ) ),
				'lesson_ids'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Add the lesson IDs of the lessons you want to set to complete. This argument accepts the value "all" to set all lessons as incomplete, a single lesson id, or a comma-separated string of lesson IDs.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the value 'all' to set all lessons as completed, a single lesson id, as well as multiple lesson ids, separated by commas (Multiple lesson ids will set all the lessons to completed for the given course of the specified user):", $translation_ident ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['lesson_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_complete_lessons</strong> action was fired.", $translation_ident ); ?>
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
			'msg' => 'The lessons have been successfully completed.',
			'data' => 
			array (
			  'user_id' => 1,
			  'lesson_ids' => 'all',
			  'lessons_completed' => 
			  array (
				'success' => true,
				'lessons' => 
				array (
				  8055 => 
				  array (
					'user_id' => 1,
					'course_id' => 8053,
					'lesson_id' => 8055,
					'response' => true,
					'completed_topics' => 
					array (
					  'success' => true,
					  'topics' => 
					  array (
						8075 => 
						array (
						  'user_id' => 1,
						  'course_id' => 8053,
						  'lesson_id' => 8055,
						  'topic_id' => 8075,
						  'response' => true,
						),
					  ),
					),
				  ),
				  8057 => 
				  array (
					'user_id' => 1,
					'course_id' => 8053,
					'lesson_id' => 8057,
					'response' => true,
					'completed_topics' => 
					array (
					  'success' => false,
					  'topics' => 
					  array (
					  ),
					),
				  ),
				),
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Complete lessons',
			'webhook_slug' => 'ld_complete_lessons',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user_id</strong> argument. You can either set it to the user id or the user email of which you want to complete the lessons for.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>course_id</strong> argument to the id of the course you want to complete for the given user.', $translation_ident ),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'If you do not set the <strong>lesson_ids</strong> argument (or you set its value to "all"), we will mark all lessons for the given course and user as completed.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Completing a lesson will also complete its topics for the specified user.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'ld_complete_lessons', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Complete lessons', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'complete one or multiple lessons', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Complete one or multiple lessons of a course for a user within Learndash.', $translation_ident ),
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
					'lesson_ids' => 0,
					'lessons_completed' => array(),
				),
			);

			$user_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$course_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_id' ) );
			$lesson_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lesson_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) || empty( $course_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user_id and course_id arguments.", 'action-ld_complete_lessons-error' );
				return $return_args;
			}

			if( empty( $lesson_ids ) ){
				$lesson_ids = 'all';
			}

			if( is_numeric( $user_id ) ){
				$user_id = intval( $user_id );
			} elseif( ! empty( $user_id ) && is_email( $user_id ) ) {
				$user_data = get_user_by( 'email', $user_id );
				if( ! empty( $user_data ) && isset( $user_data->ID ) ){
					$user_id = $user_data->ID;
				}
			}

			$validated_lesson_ids = array();
			if( $lesson_ids === 'all' ){
				$validated_lesson_ids = 'all';
			} else {
				$validated_lesson_ids = array_map( "trim", explode( ',', $lesson_ids ) );
			}

			$lessons_completed = $ld_helpers->complete_lessons( $user_id, $course_id, $validated_lesson_ids );

			if( $lessons_completed ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The lessons have been successfully completed.", 'action-ld_complete_lessons-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['lesson_ids'] = $validated_lesson_ids;
				$return_args['data']['lessons_completed'] = $lessons_completed;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The lessons could not be fully completed.", 'action-ld_complete_lessons-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['lesson_ids'] = $validated_lesson_ids;
				$return_args['data']['lessons_completed'] = $lessons_completed;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.