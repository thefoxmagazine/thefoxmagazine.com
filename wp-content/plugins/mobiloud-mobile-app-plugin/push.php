<?php
include_once dirname( __FILE__ ) . '/notification_categories.php';

//function that sets the last notified post
function ml_set_post_id_as_notified( $postID ) {
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	$wpdb->insert(
		$table_name,
		array(
			'time'    => current_time( "timestamp" ),
			'post_id' => $postID,
		)
	);
}

function ml_is_notified( $post_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	$num        = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE post_id = %d", $post_id ) );

	return $num > 0;
}

function ml_post_published_notification( $new_status, $old_status = null, $post = null ) {
	if ( $old_status === null || $post === null ) {
		return;
	}
	if ( ml_is_notified( $post->ID ) || ! ml_check_post_notification_required( $post->ID ) || $post->post_type != 'post' ) {
		return;
	}

	if ( ( $new_status == 'publish' ) && ( $old_status != 'publish' ) ) { // only send push if it's a new publish

		$alert             = $post->post_title;
		$custom_properties = array( 'post_id' => $post->ID );

		//tags
		$tags = array();
		//subscriptions
		// if(ml_subscriptions_enable()) {
		// 	$tags[] = "all";
		// 	$capabilities = ml_subscriptions_post_capabilities($post);
		// 	foreach($capabilities as $c) {
		// 		$tags[] = $c;
		// 	}
		// } else {
		$tags[]     = "all";
		$categories = wp_get_post_categories( $post->ID );
		foreach ( $categories as $c ) {
			if ( $c != null ) {
				$tags[] = $c;
			}
		}

		// ml_send_notification($alert, true,NULL,$custom_properties,$tags,$post_id);
		ml_send_notification( $alert, true, null, $custom_properties, null, strval( $post->ID ) );

	}
}

function ml_pb_post_published_notification_future( $post ) {
	ml_pb_post_published_notification( 'publish', 'future', $post, true );
}

function ml_pb_post_published_notification( $new_status, $old_status, $post ) {

	if ( ml_is_notified( $post->ID ) || ! ml_check_post_notification_required( $post->ID ) ) {
		return;
	}

	$push_types = Mobiloud::get_option( "ml_push_post_types", "post" );
	if ( strlen( $push_types ) > 0 ) {
		$push_types = explode( ",", $push_types );

		if ( $new_status == 'publish' && $old_status != 'publish' && in_array( $post->post_type, $push_types ) ) {  // only send push if it's a new publish
			$payload = array(
				'post_id' => strval( $post->ID ),
			);

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
			if ( is_array( $image ) ) {
				$payload['featured_image'] = $image[0];
			}
			$tags       = ml_get_post_tag_ids( $post->ID );
			$tags[]     = 'all';
			$tagNames   = ml_get_post_tags( $post->ID );
			$tagNames[] = 'all';
			$data       = array(
				'platform' => array( 0, 1 ),
				'msg'      => strip_tags( trim( $post->post_title ) ),
				'sound'    => 'default',
				'badge'    => '+1',
				'notags'   => true,
				'tags'     => $tags,
				'payload'  => $payload,
				"chunk"    => 2000,
				"rate"     => 60
			);
			ml_pb_send_batch_notification( $data, $tagNames );
		}
	}
}


//true if the notification was sent successfully
//false if there was an error
function ml_send_notification( $alert, $sound = true, $badge = null, $custom_properties = null, $tags = null, $remote_identifier = null ) {
	global $ml_api_key, $ml_secret_key;

	//push notification only when api key is set
	if ( ( $ml_api_key == null || strlen( $ml_api_key ) < 5 ) &&
	( $ml_secret_key == null || strlen( $ml_secret_key ) < 5 )
	) {
		return false;
	}
	$notification = array( 'alert' => strip_tags( $alert ) );
	if ( $sound ) {
		$notification['sound'] = $sound;
	}
	if ( $badge ) {
		$notification['badge'] = $badge;
	}
	if ( $custom_properties ) {
		$notification['custom_properties'] = $custom_properties;
	}
	if ( $tags ) {
		$notification['tags'] = $tags;
	}

	$parameters = array(
		'api_key'      => $ml_api_key,
		'api_secret'   => $ml_secret_key,
		'notification' => $notification,
	);

	//postID
	if ( $remote_identifier ) {
		$parameters['remote_identifier'] = "$remote_identifier";
	}

	$request = new WP_Http;
	$headers = array( 'Content-Type: application/json' );
	$result  = $request->request( MOBILOUD_PUSH_API_PUBLISH_URL,
		array( 'method' => 'POST', 'timeout' => 10, 'body' => $parameters, 'headers' => $headers ) );

	return false;
}

function ml_pb_send_batch_notification( $data, $tagNames = array() ) {
	$data['msg'] = stripslashes( $data['msg'] );
	$json_data   = json_encode( $data );

	$headers = array(
		'X-PUSHBOTS-APPID'  => get_option( 'ml_pb_app_id' ),
		'X-PUSHBOTS-SECRET' => get_option( 'ml_pb_secret_key' ),
		'Content-Type'      => 'application/json',
		'Content-Length'    => strlen( $json_data )
	);
	$url     = MOBILOUD_PB_URL . '/push/all';

	$request = new WP_Http;
	$result  = $request->post( $url, array(
		'timeout'   => 10,
		'headers'   => $headers,
		'sslverify' => false,
		'body'      => $json_data
	) );
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	$values = array(
		'time'    => current_time( "timestamp" ),
		'post_id' => isset( $data['payload']['post_id'] ) ? $data['payload']['post_id'] : null,
		'url' => isset( $data['payload']['url'] ) ? $data['payload']['url'] : null,
		'msg'     => $data['msg'],
		'android' => is_array( $data['platform'] ) && in_array( 1, $data['platform'] ) ? 'Y' : 'N',
		'ios'     => is_array( $data['platform'] ) && in_array( 0, $data['platform'] ) ? 'Y' : 'N',
		'tags'    => count( $tagNames ) > 0 ? implode( ",", $tagNames ) : ''
	);

	$wpdb->insert(
		$table_name,
		$values
	);
	if (!empty($wpdb->last_error) && (false !== stripos($wpdb->last_error, 'unknown column'))) {
		Mobiloud::run_db_update_notifications();
		$wpdb->insert(
			$table_name,
			$values
		);
	}
}

function ml_registered_devices() {
	$request      = new WP_Http;
	$headers      = array(
		'X-PUSHBOTS-APPID'  => get_option( 'ml_pb_app_id' ),
		'X-PUSHBOTS-SECRET' => get_option( 'ml_pb_secret_key' ),
		'Content-Type'      => 'application/json',
		'Content-Length'    => 0
	);
	$url          = MOBILOUD_PB_URL . '/deviceToken/all';
	$result       = $request->get( $url, array(
		'timeout'   => 10,
		'headers'   => $headers,
		'sslverify' => false
	) );
	$responseCode = null;
	if ( $result instanceof WP_Error ) {
		$responseCode = null;
	} elseif ( is_array( $result ) && isset( $result['response']['code'] ) ) {
		$responseCode = $result['response']['code'];
	}
	if ( $responseCode === 200 ) {
		$body = json_decode( $result['body'] );

		return $body;
	} else {
		return null;
	}
}

function ml_registered_devices_count() {
	$request  = new WP_Http;
	$headers  = array(
		'X-PUSHBOTS-APPID'  => get_option( 'ml_pb_app_id' ),
		'X-PUSHBOTS-SECRET' => get_option( 'ml_pb_secret_key' ),
		'platform'          => 0
	);
	$url      = MOBILOUD_PB_URL . '/deviceToken/count';
	$result   = $request->get( $url, array(
		'timeout'   => 10,
		'headers'   => $headers,
		'sslverify' => false
	) );
	$iosCount = null;

	if ( $result instanceof WP_Error ) {
		$iosCount = Mobiloud::get_option('ml_count_ios', 0);
	} elseif ( isset( $result['body'] ) && isset( $result[ 'response' ] ) && isset( $result[ 'response' ][ 'code' ] )  && ( 200 == $result[ 'response' ][ 'code' ] ) ) {
		$responseJson = json_decode( $result['body'] );
		$iosCount     = ( isset( $responseJson->count ) ? $responseJson->count : 0 );
		if (!empty( $responseJson->count )) {
			Mobiloud::set_option('ml_count_ios', $iosCount);
		} else {
			$iosCount = Mobiloud::get_option('ml_count_ios', 0);
		}
	} else {
		$iosCount = Mobiloud::get_option('ml_count_ios', 0);
	}

	$request      = new WP_Http;
	$headers      = array(
		'X-PUSHBOTS-APPID'  => get_option( 'ml_pb_app_id' ),
		'X-PUSHBOTS-SECRET' => get_option( 'ml_pb_secret_key' ),
		'platform'          => 1
	);
	$url          = MOBILOUD_PB_URL . '/deviceToken/count';
	$result       = $request->get( $url, array(
		'timeout'   => 10,
		'headers'   => $headers,
		'sslverify' => false
	) );
	$androidCount = null;
	if ( $result instanceof WP_Error ) {
		$androidCount = Mobiloud::get_option('ml_count_android', 0);;
	} elseif ( isset( $result['body'] ) && isset( $result[ 'response' ] ) && isset( $result[ 'response' ][ 'code' ] )  && ( 200 == $result[ 'response' ][ 'code' ] ) ) {
		$responseJson = json_decode( $result['body'] );
		$androidCount = ( isset( $responseJson->count ) ? $responseJson->count : 0 );
		if (!empty( $responseJson->count )) {
			Mobiloud::set_option('ml_count_android', $androidCount);
		} else {
			$androidCount = Mobiloud::get_option('ml_count_android', 0);
		}
	} else {
		$androidCount = Mobiloud::get_option('ml_count_android', 0);
	}

	return array( 'ios' => $iosCount, 'android' => $androidCount );
}

function ml_notifications( $limit = null ) {
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	$sql        = "SELECT * FROM $table_name ORDER BY time DESC";
	if ( $limit != null ) {
		$sql .= " LIMIT " . $limit;
	}

	return $wpdb->get_results( $sql );
}

function ml_get_notification_by( $filter = array() ) {
	global $wpdb;
	$table_name = $wpdb->prefix . "mobiloud_notifications";
	$sql        = "
	SELECT * FROM " . $table_name . "
	WHERE
	msg = '" . $wpdb->escape( $filter['msg'] ) . "'
	";
	if ( $filter['post_id'] != null ) {
		$sql .= " AND post_id = " . $wpdb->escape( $filter['post_id'] );
	}
	if ( $filter['url'] != null ) {
		$sql .= " AND url = '" . $wpdb->escape( $filter['url'] ) . "'";
	}
	$sql .= " AND android = '" . $wpdb->escape( $filter['android'] ) . "'";
	$sql .= " AND ios = '" . $wpdb->escape( $filter['ios'] ) . "'";

	$results = $wpdb->get_results( $sql );

	return $results;
}

function ml_get_post_tags( $postId ) {
	$post_categories = wp_get_post_categories( $postId );
	$tags            = array();

	foreach ( $post_categories as $c ) {
		$cat    = get_category( $c );
		$tags[] = $cat->slug;
	}

	return $tags;
}

function ml_get_post_tag_ids( $postId ) {
	$post_categories = wp_get_post_categories( $postId );
	$tags            = array();
	foreach ( $post_categories as $c ) {
		$tags[] = $c;
	}

	return $tags;
}

function ml_check_post_notification_required( $postId ) {
	$notification_categories = ml_get_push_notification_categories();
	if ( is_array( $notification_categories ) && count( $notification_categories ) > 0 ) {
		$post_categories = wp_get_post_categories( $postId );
		$found           = false;
		if ( is_array( $post_categories ) && count( $post_categories ) > 0 ) {
			foreach ( $post_categories as $post_category_id ) {
				foreach ( $notification_categories as $notification_category ) {
					if ( $notification_category->cat_ID == $post_category_id ) {
						$found = true;
					}
				}
			}
		}

		return $found;
	}

	return true;
}

?>
