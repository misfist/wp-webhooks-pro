<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_restrict_content_pro_Triggers_rcp_membership_activated_free' ) ) :

 /**
  * Load the rcp_membership_activated_free trigger
  *
  * @since 4.3.6
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_restrict_content_pro_Triggers_rcp_membership_activated_free {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'rcp_membership_post_activate',
				'callback' => array( $this, 'rcp_membership_activated_free_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-rcp_membership_activated_free-description";

		$parameter = array(
			'membership_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the membership.', $translation_ident ) ),
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The user id.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the user.', $translation_ident ) ),
			'membership' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the membership.', $translation_ident ) ),
			'membership_level' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the membership level.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Free membership activated',
			'webhook_slug' => 'rcp_membership_activated_free',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'rcp_membership_post_activate',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific membership levels only. To do that, simply specify the membership levels within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_restrict_content_pro_trigger_on_selected_levels' => array(
					'id'		  => 'wpwhpro_restrict_content_pro_trigger_on_selected_levels',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'restrict-content-pro',
							'helper' => 'rcp_helpers',
							'function' => 'get_query_levels',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected membership levels', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the membership levels you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'rcp_membership_activated_free',
			'name'			  => WPWHPRO()->helpers->translate( 'Free membership activated', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a free membership was activated', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a free membership was activated within Restrict Content Pro.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'restrict-content-pro',
			'premium'		   => false,
		);

	}

	public function rcp_membership_activated_free_callback( $membership_id, $membership ){

		if( empty( $membership ) || $membership->is_paid() ){
            return;
        }

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'rcp_membership_activated_free' );
		$response_data_array = array();
		$rcp_helpers = WPWHPRO()->integrations->get_helper( 'restrict-content-pro', 'rcp_helpers' );

		$payload = $rcp_helpers->build_payload( $membership );

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_restrict_content_pro_trigger_on_selected_levels' && ! empty( $settings_data ) ){
					if( ! in_array( $payload['membership_level']['id'], $settings_data ) ){
					  $is_valid = false;
					}
				  }
	  
				}
			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_rcp_membership_activated_free', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'membership_id' => '12',
			'user_id' => 144,
			'user' => 
			array (
				'data' => 
				array (
				  'ID' => '113',
				  'user_login' => 'demouser',
				  'user_pass' => '$P$BNHFPR8znhTIMV1dpceF7aUTUcqSrU/',
				  'user_nicename' => 'Demo User',
				  'user_email' => 'demouser@demo.test',
				  'user_url' => '',
				  'user_registered' => '2019-11-14 18:06:50',
				  'user_activation_key' => '',
				  'user_status' => '0',
				  'display_name' => 'demouser',
				  'spam' => '0',
				  'deleted' => '0',
				),
				'ID' => 113,
				'caps' => 
				array (
				  'subscriber' => true,
				),
				'cap_key' => 'zipf_capabilities',
				'roles' => 
				array (
				  0 => 'subscriber',
				),
				'allcaps' => 
				array (
				  'read' => true,
				  'level_0' => true,
				  'read_private_locations' => true,
				  'read_private_events' => true,
				  'manage_resumes' => true,
				  'subscriber' => true,
				),
				'filter' => NULL,
			),
			'membership' => 
			array (
			  'customer_id' => '10',
			  'customer' => 
			  array (
				'id' => '10',
				'user_id' => '144',
				'date_registered' => 'February 21, 2022',
				'email_verification_status' => 'none',
				'last_login' => '',
				'ips' => 
				array (
				),
				'has_trialed' => false,
				'notes' => '',
				'is_pending_verification' => false,
				'has_active_membership' => true,
				'has_paid_membership' => false,
				'lifetime_value' => 0,
			  ),
			  'membership_level_name' => 'Demo Level Free',
			  'currency' => 'USD',
			  'initial_amount' => '0.00',
			  'recurring_amount' => '0.00',
			  'biling_cycle_formatted' => 'Free',
			  'status' => 'active',
			  'expiration_date' => 'none',
			  'expiration_time' => false,
			  'created_date' => 'February 21, 2022',
			  'activated_date' => '2022-02-21 00:00:00',
			  'trial_end_date' => NULL,
			  'renewed_date' => NULL,
			  'cancellation_date' => NULL,
			  'times_billed' => 0,
			  'maximum_renewals' => '0',
			  'gateway' => 'manual',
			  'gateway_customer_id' => 'demo-gateway-customer-id',
			  'gateway_subscription_id' => 'demo-gateway-subscription-id',
			  'subscription_key' => '',
			  'get_upgraded_from' => '0',
			  'was_upgrade' => false,
			  'payment_plan_completed_date' => NULL,
			  'notes' => 'February 21, 2022 11:28:15 - Membership activated.',
			  'signup_method' => 'manual',
			  'prorate_credit_amount' => 0,
			  'payments' => 
			  array (
			  ),
			  'card_details' => 
			  array (
			  ),
			),
			'membership_level' => 
			array (
			  'id' => 1,
			  'name' => 'Demo Level Free',
			  'description' => '',
			  'is_lifetime' => true,
			  'duration' => 0,
			  'duration_unit' => 'day',
			  'has_trial' => false,
			  'trial_duration' => 0,
			  'trial_duration_unit' => 'day',
			  'get_price' => 0,
			  'is_free' => true,
			  'fee' => 0,
			  'renewals' => 0,
			  'access_level' => 0,
			  'status' => 'active',
			  'role' => 'lms_manager',
			  'get_date_created' => NULL,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.