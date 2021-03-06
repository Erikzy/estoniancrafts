<?php

if (!class_exists("LoginCommon")) {

    class LoginCommon {

       public static function login($identityCode, $firstName, $lastName, $email) {
            //$userName = "EST" . $identityCode;
			$userName = mb_strtolower($firstName).'.'.mb_strtolower($lastName);
			$user = WP_User::get_data_by('login', $userName);
			if($user){
				$increment = 0;
				while($user){
					$increment++;
					$userName = mb_strtolower($firstName).'.'.mb_strtolower($lastName).'.'.$increment;
					$user = WP_User::get_data_by('login', $userName);
				}	
			
			}
			
			
			
			
            if (strlen($identityCode) == 11) {
                //Otsime üles sisselogitud inimese või tekitame, kui teda varem polnud
                $user = LoginCommon::getUser($identityCode);
                if (($user == NULL) and ( NULL == username_exists($userName))) {
                    $regHash = sha1($identityCode.$firstName.$lastName.time());
					
					
										
                    $user_id = LoginCommon::createUser($userName, $firstName, $lastName, $email, $identityCode, $regHash);
                    $myaccount_page_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
                    // $myaccount_page_url .= '?reghash='.$regHash;
               		// $myaccount_page_url .= '/edit-account';
               
               		$redirect_url = redirect_to_user_appropriate_home();
            		wp_set_auth_cookie($user_id);
            		header("Location: ".$redirect_url);	
                } else {
                    $user_id = $user->userid;
                }
            } else {
                //At least some form of error handling
                echo "ERROR: Idcode not received from the login. Please try again";
                echo "$identityCode, $firstName, $lastName, $email";
                die();
            }
     		
     		
     		$redirect_url = redirect_to_user_appropriate_home();
            wp_set_auth_cookie($user_id);
            header("Location: ".$redirect_url);	
            
            
        }

       private static function createUser($userName, $firstName, $lastName, $email, $identityCode, $regHash = '') {
           global $wpdb;
           $current_user = wp_get_current_user();

           if (0 == $current_user->ID) {
// generate new user
               $user_data = array(
                   'user_pass' => wp_generate_password(64, true),
                   'user_login' => $userName,
                   'display_name' => "$firstName $lastName",
                   'first_name' => $firstName,
                   'last_name' => $lastName,
                   'user_email' => $email,
                   'role' => 'customer'// Use default role or another role, e.g. 'editor'
               );
               $user_id = wp_insert_user($user_data);
               do_action( 'ec_user_register', $new_user->ID );
               
//               $user_id = 0;
               $wpdb->insert($wpdb->prefix . "idcard_users",
                   array(
                       'firstname' => $firstName,
                       'lastname' => $lastName,
                       'identitycode' => $identityCode,
                       'userid' => $user_id,
                       'reghash' => $regHash,
                       'created_at' => current_time('mysql')
                   )
               );
           } else {
               $user_id = $current_user->ID;
               $user_id = wp_update_user( array( 'ID' => $user_id, 'first_name' => $firstName, 'last_name' => $lastName  ) );
               
               $wpdb->insert($wpdb->prefix . "idcard_users",
                   array(
                       'firstname'      => $firstName,
                       'lastname'       => $lastName,
                       'identitycode'   => $identityCode,
                       'userid'         => $user_id,
                       'created_at'     => current_time('mysql')
                   )
               );
           }


           return $user_id;
       }

        private static function getUser($identityCode) {
            global $wpdb;
            $user = $wpdb->get_row(
                $wpdb->prepare(
                    "select * from $wpdb->prefix" . "idcard_users WHERE identitycode=%s", $identityCode
                )
            );
            return $user;
        }

    }

}

