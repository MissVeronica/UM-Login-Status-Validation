<?php

remove_action( 'um_submit_form_errors_hook_logincheck', 'um_submit_form_errors_hook_logincheck', 9999 );
add_action( 'um_submit_form_errors_hook_logincheck', 'ibiza69_submit_form_errors_hook_logincheck', 10, 1 );

function ibiza69_submit_form_errors_hook_logincheck( $args ) {

	if ( is_user_logged_in() ) {
		wp_logout();
	}

	$user_id = ( isset( UM()->login()->auth_id ) ) ? UM()->login()->auth_id : '';
    
    if( empty( $user_id ) || !is_numeric( $user_id )) {
        um_reset_user();
        exit( wp_redirect( add_query_arg( 'err', esc_attr( 'forbidden' ), UM()->permalinks()->get_current_url() ) ) );
    }
    
    UM()->user()->remove_cache( $user_id );
    um_fetch_user( $user_id );

	$status = um_user( 'account_status' );

	switch ( $status ) {

	    case 'inactive':
		case 'awaiting_admin_review':
		case 'awaiting_email_confirmation':
		case 'rejected':
                            um_reset_user();
                            exit( wp_redirect( add_query_arg( 'err', esc_attr( $status ), UM()->permalinks()->get_current_url() ) ) );
                            break;

        case 'approved':    break;

        default:            
                            $login_status_validation = apply_filters( 'um_login_status_validation', false, $status, $user_id );
                            if( !$login_status_validation ) {
                                um_reset_user();
                                exit( wp_redirect( add_query_arg( 'err', esc_attr( $status ), UM()->permalinks()->get_current_url() ) ) );
                            }
                            break;
	}

	if ( isset( $args['form_id'] ) && $args['form_id'] == UM()->shortcodes()->core_login_form() && UM()->form()->errors && ! isset( $_POST[ UM()->honeypot ] ) ) {
        exit( wp_redirect( um_get_core_page( 'login' ) ) );
	}
}
