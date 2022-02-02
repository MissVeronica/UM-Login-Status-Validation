<?php

remove_action( 'um_submit_form_errors_hook_logincheck', 'um_submit_form_errors_hook_logincheck', 9999 );
add_action( 'um_submit_form_errors_hook_logincheck', 'my_um_submit_form_errors_hook_logincheck', 9999, 1 );
add_shortcode( 'reject_login_log', 'reject_login_log_shortcode' );

function reject_login_log_shortcode( $atts ) {

    if( current_user_can( 'administrator' )) {

        $log = get_option( 'um_reject_login_log' );

        ob_start();
        echo '<h4>' . __( 'Reject Login Log', 'ultimate-member' ) . '</h4>';
        
        if( !empty( $log )) {

            $log = array_reverse( $log );

            echo '<div style="display: table-row;">';
            echo '<div style="display: table-cell;">' . __( 'Date', 'ultimate-member' ) . '</div>';
            echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . __( 'Rejected', 'ultimate-member' ) . '</div>';
            echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . __( 'User ID', 'ultimate-member' ) . '</div>';
            echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . __( 'Username', 'ultimate-member' ) . '</div>';
            echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . __( 'Role Title', 'ultimate-member' ) . '</div>';
            echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . __( 'Registration Date', 'ultimate-member' ) . '</div>';

            if( !empty( $atts ) && isset( ( $atts['meta_keys']))) {

                $meta_keys = explode( ',', $atts['meta_keys'] );
                foreach( $meta_keys as $key => $meta_key ) {
                    $meta_keys[$key] = sanitize_key( $meta_key );
                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . esc_attr( $meta_keys[$key] ) . '</div>';
                }

            } else $meta_keys = false;

            echo '</div>';

            $time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

            foreach( $log as $items ) {

                echo '<div style="display: table-row;">';
                echo '<div style="display: table-cell;">';
                echo esc_attr( date_i18n(  $time_format, $items[0] )) . '</div>';
                echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . esc_attr( $items[2] ) . '</div>';
                
                if( !empty( $items[1] ) && !is_numeric( $items[1] )) {
                    $user = get_user_by( 'login', $items[1] );
                    if( $user ) {
                        $items[1] = $user->ID;
                    }
                }

                if( is_numeric( $items[1] ) && intval( $items[1] ) > 0 ) {

                    um_fetch_user( $items[1] );
                    $user_link = UM()->user()->get_profile_link( $items[1] );
                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . esc_attr( $items[1] ) . '</div>';
                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;"><a href="' . esc_url( $user_link ) . '">';
                    echo esc_attr( um_user( 'user_login' )) . '</a></div>';                    
                    $role_name = UM()->roles()->get_role_name( UM()->roles()->get_editable_priority_user_role( $items[1] ));
                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . esc_attr( $role_name ) . '</div>';
                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . esc_attr( um_user( 'user_registered' )) . '</div>';

                    if( $meta_keys ) {
                        
                        foreach( $meta_keys as $meta_key ) {                            
                            
                            $meta_value = maybe_unserialize( um_user( $meta_key ));
                            if( is_array( $meta_value )) $meta_value = implode( ',', $meta_value );

                            echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . esc_attr( $meta_value ) . '</div>';
                        }
                    }

                } else {

                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">-</div>';
                    if( empty( $items[1] )) $items[1] = '-';
                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">' . esc_attr( $items[1] ) . '</div>';
                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">-</div>';
                    echo '<div style="display: table-cell; padding:0px 0px 0px 10px;">-</div>';
                }

                echo '</div>';
            }
        } else echo '<div>' . __( 'No Posts', 'ultimate-member' ) . '</div>';
    } else echo '<div>' . __( 'This is not possible for security reasons.', 'ultimate-member' ) . '</div>';

    $output = ob_get_contents();
    ob_end_clean();
    
    return $output;
}


function my_um_reject_login_log( $user_id, $error ) {

    $log = get_option( 'um_reject_login_log' );
    if( empty( $log )) $log = array();

    $log[] = array( current_time( 'timestamp' ), sanitize_key( $user_id ), sanitize_key( $error ));

    if( count( $log ) > 30 ) array_shift( $log );
    update_option( 'um_reject_login_log', $log );
}

function my_um_submit_form_errors_hook_logincheck( $args ) {

	if ( is_user_logged_in() ) {
		wp_logout();
	}

	$user_id = ( isset( UM()->login()->auth_id ) ) ? UM()->login()->auth_id : '';
    
    if( empty( $user_id ) || !is_numeric( $user_id )) {

        if( UM()->form()->errors ) {

            if( array_key_exists( 'user_password', UM()->form()->errors )) { 

                if( array_key_exists( 'incorrect_password', UM()->form()->errors )) $error_code = 'incorrect_password';
                else $error_code = 'user_password';
                $username = $_REQUEST['username-' . $_REQUEST['form_id']];
                my_um_reject_login_log( $username, $error_code);
                return;
            }
            
            my_um_reject_login_log( $user_id, array_key_first( UM()->form()->errors ));
            return;
        }

        my_um_reject_login_log( $user_id, 'forbidden' );
        um_reset_user();
        exit( wp_redirect( add_query_arg( 'err', esc_attr( 'forbidden' ), UM()->permalinks()->get_current_url() ) ) );
    }
    
    UM()->user()->remove_cache( $user_id );
    um_fetch_user( $user_id );

	$status = um_user( 'account_status' );
    if( !$status ) $status = 'status_false';

	switch ( $status ) {

	    case 'inactive':
		case 'awaiting_admin_review':
		case 'awaiting_email_confirmation':
		case 'rejected':
                            my_um_reject_login_log( $user_id, $status );
                            um_reset_user();
                            exit( wp_redirect( add_query_arg( 'err', esc_attr( $status ), UM()->permalinks()->get_current_url() ) ) );
                            break;

        case 'approved':    break;

        default:            
                            $login_status_validation = apply_filters( 'um_login_status_validation', false, $status, $user_id );
                            if( !$login_status_validation ) {
                                my_um_reject_login_log( $user_id, $status );
                                um_reset_user();
                                exit( wp_redirect( add_query_arg( 'err', esc_attr( $status ), UM()->permalinks()->get_current_url() ) ) );
                            }
                            break;
	}

	if ( isset( $args['form_id'] ) && $args['form_id'] == UM()->shortcodes()->core_login_form() && UM()->form()->errors && ! isset( $_POST[ UM()->honeypot ] ) ) {        
        my_um_reject_login_log( $user_id, array_key_first( UM()->form()->errors ));
        exit( wp_redirect( um_get_core_page( 'login' ) ) );
	}
}
