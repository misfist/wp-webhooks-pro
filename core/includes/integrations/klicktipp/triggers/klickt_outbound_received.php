<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Triggers_klickt_outbound_received' ) ) :

 /**
  * Load the klickt_outbound_received trigger
  *
  * @since 5.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_klicktipp_Triggers_klickt_outbound_received {

	public function get_details(){

		$translation_ident = "action-klickt_outbound_received-description";

		$parameter = array(
			'custom_construct' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The data that was sent along with the HTTP call that was made to the receivable URL from within KlickTipp.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'KlickTipp outbound request received',
			'webhook_slug' => 'klickt_outbound_received',
			'post_delay' => false,
			'steps' => array(
				WPWHPRO()->helpers->translate( 'Add a URL to this trigger on which you want to receive the KlickTipp data.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Go into the settings for your added URL and copy the receivable URL (The dynamically created URL).', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Head into KlickTipp and select the "Webhooks by KlickTipp" app along with the "POST" or "Custom Request" action.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Place the receivable URL there and send data based on your requirements.', $translation_ident ),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'To receive this data on the receivable URL, please use the "Webhooks by KlickTipp" app within KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'The receivable URL accepts content types such as JSON, form data, or XML.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => false,
			'data' => array(
				'wpwhpro_klicktipp_return_full_request' => array(
					'id'		  => 'wpwhpro_klicktipp_return_full_request',
					'type'		=> 'checkbox',
					'label'	   => WPWHPRO()->helpers->translate( 'Send full request', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Send the full, validated request instead of the payload (body) data only. This gives you access to header, cookies, response type and much more.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'klickt_outbound_received',
			'name'			  => WPWHPRO()->helpers->translate( 'KlickTipp outbound request received', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a KlickTipp outbound request was received', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => sprintf( WPWHPRO()->helpers->translate( 'This webhook fires as soon as a request was received from the "Webhooks by KlickTipp" app of KlickTipp with the action "POST" or "Custom Request".', $translation_ident ), WPWHPRO()->settings->get_page_title() ),
			'description'	   => $description,
			'integration'	   => 'klicktipp',
			'receivable_url'	=> true,
			'premium'		   => true,
		);

	}

	public function execute( $return_data, $response_body, $trigger_url_name ){

		$translation_ident = "action-klickt_outbound_received-description";

		if( $trigger_url_name !== null ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'klickt_outbound_received', $trigger_url_name );
			if( ! empty( $webhooks ) ){
				$webhooks = array( $webhooks );
			} else {
				$return_data['msg'] = WPWHPRO()->helpers->translate( 'We could not locate a callable trigger URL.', $translation_ident );
				return $return_data;
			}
		} else {
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'klickt_outbound_received' );
		}
		

		$payload = $response_body['content'];

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_klicktipp_return_full_request' && ! empty( $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_klickt_outbound_received', $return_data, $response_body, $trigger_url_name, $response_data_array );

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