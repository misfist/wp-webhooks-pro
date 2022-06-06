<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_webhooks_Triggers_received_http_request' ) ) :

 /**
  * Load the received_http_request trigger
  *
  * @since 5.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_webhooks_Triggers_received_http_request {

	public function get_details(){

		$translation_ident = "action-received_http_request-description";

		$parameter = array(
			'custom_construct' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The data that was sent along with the HTTP call that was made to the receivable URL.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'HTTP request received',
			'webhook_slug' => 'received_http_request',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpwhpro/webhooks/add_webhook_actions',
				),
			),
			'steps' => array(
				WPWHPRO()->helpers->translate( 'Add your URL to this trigger.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Go into the settings for your added URL and copy the receivable URL (The dynamically created URL).', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Place the receivable URL from whereevr you want to send data to it based on your requirements.', $translation_ident ),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'The receivable URL accepts content types such as JSON, form data, or XML.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => false,
			'data' => array(
				'wpwhpro_webhooks_return_full_request' => array(
					'id'		  => 'wpwhpro_webhooks_return_full_request',
					'type'		=> 'checkbox',
					'label'	   => WPWHPRO()->helpers->translate( 'Send full request', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Send the full, validated request instead of the payload (body) data only. This gives you access to header, cookies, response type and much more.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'received_http_request',
			'name'			  => WPWHPRO()->helpers->translate( 'HTTP request received', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a HTTP request was received', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => sprintf( WPWHPRO()->helpers->translate( 'This webhook fires as soon as a request was received on the receivable URL within the "Webhooks" integration.', $translation_ident ), WPWHPRO()->settings->get_page_title() ),
			'description'	   => $description,
			'integration'	   => 'webhooks',
			'receivable_url'	=> true,
			'premium'		   => true,
		);

	}

	public function execute( $return_data, $response_body, $trigger_url_name ){

		$translation_ident = "action-received_http_request-description";

		if( $trigger_url_name !== null ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'received_http_request', $trigger_url_name );
			if( ! empty( $webhooks ) ){
				$webhooks = array( $webhooks );
			} else {
				$return_data['msg'] = WPWHPRO()->helpers->translate( 'We could not locate a callable trigger URL.', $translation_ident );
				return $return_data;
			}
		} else {
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'received_http_request' );
		}
		

		$payload = $response_body['content'];

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_webhooks_return_full_request' && ! empty( $settings_data ) ){
					$payload = $response_body;
				  }
	  
				}
			}

			if( $is_valid ){

				$webhook_response = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload, array( 'blocking' => true ) );

				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = $webhook_response;
				} else {
					$response_data_array[] = $webhook_response;
				}
			}

		}

		$return_data['success'] = true;
		$return_data['data'] = ( count( $response_data_array ) > 1 ) ? $response_data_array : reset( $response_data_array );

		do_action( 'wpwhpro/webhooks/trigger_received_http_request', $return_data, $response_body, $trigger_url_name, $response_data_array );

		return $return_data;
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'custom_construct' => 'The data that was sent to the receivable data URL. Or the full request array.',
		);

		return $data;
	}

  }

endif; // End if class_exists check.