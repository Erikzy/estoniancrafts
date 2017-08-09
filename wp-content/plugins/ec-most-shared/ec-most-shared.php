<?php
/*
Plugin Name: Top Shared Facebook Posts
Plugin URI: https://www.estoniancrafts.com/
Description: Displays most shared posts in facebook
Author: Urmas Luuk
*/

require_once( dirname( __FILE__ ) .'/libs/Facebook/autoload.php');

add_action("wp", "ec_most_shared_post");
function ec_most_shared_post()
{
    try {
        if(is_single() && !is_attachment())
        {
            global $post;

            $last_update = get_post_meta($post->ID, "ec_msp_last_update", true);

            if($last_update)
            {
                if(time() - 21600 > $last_update)
                {
                    msp_update($post->ID);
                }
            }
            else
            {
                msp_update($post->ID);
            }
        }
    } catch (Exception $e) {}
}



function msp_update($id)
{
    $config = array('app_id' => get_option("_facebook_app_id"), 'app_secret'=> get_option("_facebook_app_secret"));
    $connect = new \Facebook\Facebook($config);
    $url = get_permalink($id);
    $result = $connect->get('?ids=' . $url . '&fields=engagement', $connect->getApp()->getAccessToken());

    $body = $result->getDecodedBody();
    if (sizeof($body) > 0) {
        $engagement = current($body)['engagement'];

        $reactionCount = $engagement['reaction_count'];
        $shareCount = $engagement['share_count'];

        $total = $reactionCount + $shareCount;

        update_post_meta($id, "ec_msp_fb_share_count", $total);
        update_post_meta($id, "ec_msp_last_update", time());
    }
}

function getPostSharesCount($postId)
{
    $count = get_post_meta($postId, "ec_msp_fb_share_count", true);
    return $count == null ? 0 : $count;
}

