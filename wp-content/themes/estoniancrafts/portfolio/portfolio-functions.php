<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
//ob_clean();
ob_start();

function get_portfolio_pictures( $portfolio )
{
    if ( !$portfolio->post_content ) {
        return [];
    }

    $spids = explode(',', $portfolio->post_content);
    $dbids = array_map(function ($id) { return sprintf("ID = %s", $id); }, $spids);

    global $wpdb;
    $dbdescriptions = $wpdb->get_results("SELECT ID, post_content FROM `ktt_posts` WHERE " . implode(' OR ', $dbids), OBJECT_K);

    $pictures = [];
    foreach ($spids as $id) {
        $url = wp_get_attachment_url((int)$id);
        $pictures[] = [
            'picture' => (int)$id,
            'description' => $dbdescriptions[(int)$id]->post_content,
            'url' => $url
        ];
    }

    return $pictures;
}

function add_edit_portfolio()
{
    if( !is_user_logged_in() ) {
        wp_redirect( home_url('/my-account') );
    }
    $current_user = wp_get_current_user();

    $id = isset($_GET['id']) ? esc_attr($_GET['id']) : null ;
    if( $id !== null ) {
        $portfolio = get_post($id);
        // check if this post belong to the user
    } else {
        $portfolio = new WP_Post((object)[]);
        $portfolio->post_author = $current_user->ID;
        $portfolio->post_type = 'portfolio';
    }

    $errors = [];
    if(isset($_POST['gallery_submit'])  && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' )) {

        // catch post data
        $rawPictures = isset($_POST['pictures']) && is_array($_POST['pictures'])? $_POST['pictures'] : [];
        unset($_POST['pictures']);
        $pictures = [];
        foreach ($rawPictures as $raw) {
            if (array_key_exists('picture', $raw) && 
                array_key_exists('description', $raw) &&
                ($picture = $raw['picture']) &&
                ($description = $raw['description'])
            ) {
                $pictures[] = [
                    'picture' => (int)$picture,
                    'description' => $description
                ];
            }
        }
        $title = isset($_POST['post_title']) ? $_POST['post_title'] : null;

        if (!$title) {
            $errors['title'] = __('Portfolio title needed', 'ktt');
        }

        if (!count($errors)) {
            $pids = array_map(function ($picture) { return $picture['picture']; }, $pictures);
            $portfolio->post_content = implode(',', $pids);
            $portfolio->post_title = $title;

            // save picture descriptions
            global $wpdb;
            $prepp = array_map(function ($picture) { return sprintf("(%s,'%s')", $picture['picture'], $picture['description']); }, $pictures);
            $psql = sprintf("
                INSERT INTO `ktt_posts` (ID, post_content) 
                    VALUES %s 
                ON DUPLICATE KEY 
                    UPDATE ID = VALUES(ID), 
                        post_content = VALUES(post_content);", 
                implode(',', $prepp)
            );
            $wpdb->query($psql);

            if ($portfolio->ID) {
                wp_update_post($portfolio);
            } else {
                $portfolio->ID = wp_insert_post($portfolio);
                wp_redirect(home_url('/my-account/portfolio/edit?id='.$portfolio->ID));
            }
        }

    }

    // populate post pictures
    if (!count($errors)) {
        $pictures = get_portfolio_pictures($portfolio);
    } else if (!isset($pictures)) {
        $pictures = [];
    }

    ob_start();
    include(locate_template('templates/myaccount/portfolio_edit.php'));
    return ob_get_clean();
}
add_shortcode('add_edit_portfolio', 'add_edit_portfolio');

function ec_portfolio_list()
{
    if( !is_user_logged_in() ) {
        wp_redirect( home_url('/my-account') );
    }
    $current_user = wp_get_current_user();

    // get portfolios
    $portfolios = get_posts([
        'post_author' => $current_user->ID,
        'post_type' => 'portfolio'
    ]);

    ob_start();
    include(locate_template('templates/myaccount/portfolio_list.php'));
    return ob_get_clean();
}
add_shortcode('portfolio_list', 'ec_portfolio_list');

/*page design*/
function my_enqueue_media_lib_uploader() {
        //Core media script
        wp_enqueue_media();
}
add_action('wp_enqueue_scripts',   'my_enqueue_media_lib_uploader');


