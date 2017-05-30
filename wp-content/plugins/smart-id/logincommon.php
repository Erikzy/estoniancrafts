<?php

if (!class_exists("LoginCommon")) {

    class LoginCommon {

       public static function login($identityCode, $firstName, $lastName, $email) {
            $userName = "EST" . $identityCode;

            if (strlen($identityCode) == 11) {
                //Otsime üles sisselogitud inimese või tekitame, kui teda varem polnud
                $user = LoginCommon::getUser($identityCode);
                if (($user == NULL) and ( NULL == username_exists($userName))) {
                    $user_id = LoginCommon::createUser($userName, $firstName, $lastName, $email, $identityCode);
                } else {
                    $user_id = $user->userid;
                }
            } else {
                //At least some form of error handling
                echo "ERROR: Idcode not received from the login. Please try again";
                echo "$identityCode, $firstName, $lastName, $email";
                die();
            }

            wp_set_auth_cookie($user_id);
        }

       private static function createUser($userName, $firstName, $lastName, $email, $identityCode) {
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
                   'role' => get_option('default_role') // Use default role or another role, e.g. 'editor'
               );
               $user_id = wp_insert_user($user_data);
               $wpdb->insert($wpdb->prefix . "idcard_users",
                   array(
                       'firstname' => $firstName,
                       'lastname' => $lastName,
                       'identitycode' => $identityCode,
                       'userid' => $user_id,
                       'created_at' => current_time('mysql')
                   )
               );
           } else {
               $user_id = $current_user->ID;
               $wpdb->insert($wpdb->prefix . "idcard_users",
                   array(
                       'firstname'      => $current_user->user_firstname,
                       'lastname'       => $current_user->user_lastname,
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
