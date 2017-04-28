<?php

if (!defined('ABSPATH')) {
    exit;
}

class EH_CRM_Cron_Setup {

    function __construct() {
        add_filter('cron_schedules', array($this, 'crawler_schedule_time'));
        add_action('init', array($this, 'crawler_schedule_init'));
        add_action('crm_email_crawler', array($this, 'start_crm_email_crawler'));
    }

    function crawler_schedule_time($schedules) {
        $schedules['crm_crawler_interval'] = array(
            'interval' => 60,
            'display' => "Every 60 Seconds"
        );
        return $schedules;
    }

    function crawler_schedule_init() {
        if (!wp_next_scheduled('crm_email_crawler')) {
            wp_schedule_event(time(), 'crm_crawler_interval', 'crm_email_crawler');
        }
    }

    function start_crm_email_crawler() {
        ini_set('max_execution_time', 60);
        if (eh_crm_get_settingsmeta(0, "oauth_activation") == "activated") {
            $oauth = new EH_CRM_OAuth();
            if ($oauth->refresh_accesstoken()) {
                $this->message_search();
            }
        }
        if (eh_crm_get_settingsmeta(0, "imap_activation") == "activated") {
            $this->email_imap();
        }
    }
    
    function email_imap() {
        $server_url = eh_crm_get_settingsmeta('0', "imap_server_url");
        $server_port = eh_crm_get_settingsmeta('0', "imap_server_port");
        $server_email = eh_crm_get_settingsmeta('0', "imap_server_email");
        $server_email_pwd = eh_crm_get_settingsmeta('0', "imap_server_email_pwd");
        $upload = wp_upload_dir();
        $mailbox = new PhpImap\Mailbox("{".$server_url.":".$server_port."/imap/ssl/novalidate-cert}INBOX", $server_email, $server_email_pwd, $upload['path']);
        $mailsIds = $mailbox->searchMailbox('UNSEEN');
        if($mailsIds) {
            $end_mail = count($mailsIds);
            foreach ($mailsIds as $key => $value) {
                $mail = $mailbox->getMail($mailsIds[$key]);  
                $message_data = array();
                $message_data['email'] = $mail->fromAddress;
                $message_data['cc'] = array_keys($mail->cc);
                $message_data['bcc'] = array_keys($mail->bcc);
                $message_data['date_time'] = gmdate("M d, Y h:i:s A", strtotime($mail->date));
                $message_data['subject'] = $mail->subject;
                $array = explode("\n", $mail->textPlain);
                $content_output = array();
                $i=0;
                $remove =0;
                foreach ($array as $arr) {
                    if (!(preg_match('/^>/', $arr)) && !(preg_match('/^On Mon,|^On Tue,|^On Wed,|^On Thu,|^On Fri,|^On Sat,|^On Sun,|^wrote:|^On /', $arr))) {
                        array_push($content_output, $arr);
                    }
                    else
                    {
                        $remove = $i;
                    }
                    $i++;
                }
                if($remove != 0)
                {
                    unset($content_output[$remove-1]);
                }
                $content = implode("\n", $content_output);
                $message_data['content'] = preg_replace('/\n(\s*\n){2,}/', "\n\n", $content);
                $mail_attachments = $mail->getAttachments();
                $attachment = array();
                if (!empty($mail_attachments)) {
                    foreach ($mail_attachments as $single) {
                        $temp_array = array
                        (
                            'path' => $upload['path'] . '/' . $single->name,
                            'url' => $upload['url'] . '/' . $single->name
                        );
                        array_push($attachment, $temp_array);
                    }
                    $message_data['attachments'] = $attachment;
                }
                $this->match_insert($message_data);
            }
        }
    }
    
    function message_search() {
        $access_token = eh_crm_get_settingsmeta(0, "oauth_accesstoken");
        $search = 'after:' . eh_crm_get_settingsmeta(0, 'oauth_last_requested') . ' before:' . time();
        $url = 'https://www.googleapis.com/gmail/v1/users/me/messages?q=in:inbox ' . $search;
        $url .= '&v=2';
        $url .= '&oauth_token=' . $access_token;
        $response = wp_safe_remote_get($url);
        $result = $response['response'];
        if ($result['code'] == 200 && $result['message'] == 'OK') {
            eh_crm_update_settingsmeta(0, "oauth_last_requested", time());
            $body = json_decode($response['body']);
            if ($body->resultSizeEstimate != 0) {
                foreach ($body->messages as $key=>$message) {
                    $message_url = 'https://www.googleapis.com/gmail/v1/users/me/messages/' . $message->id;
                    $constant_url = '?v=2&oauth_token=' . $access_token;
                    $request_url = $message_url . $constant_url;
                    $req_res = wp_safe_remote_get($request_url);
                    $message_body = json_decode($req_res['body']);
                    $payload = $message_body->payload;
                    $parts = $payload->parts;
                    $header = $payload->headers;
                    $message_data = array();
                    foreach ($header as $single) {
                        if ($single->name == 'From') {
                            preg_match('~<(.*?)>~', $single->value, $output);
                            $message_data['email'] = $output[1];
                        }
                        if ($single->name == 'Date') {
                            $date = strtotime($single->value);
                            $message_data['date_time'] = gmdate("M d, Y h:i:s A", $date);
                        }
                        if ($single->name == 'Subject') {
                            $message_data['subject'] = $single->value;
                        }
                        if ($single->name == 'Cc') {
                            $cc_ex = explode(",", $single->value);
                            $cc = array();
                            foreach ($cc_ex as $cc_value) {
                                array_push($cc,$this->get_string_between($cc_value,"\u003c", "\u003e"));
                            }
                            $message_data['cc'] = $cc;
                        }
                        if ($single->name == 'Bcc') {
                            $bcc_ex = explode(",", $single->value);
                            $bcc = array();
                            foreach ($bcc_ex as $bcc_value) {
                                array_push($bcc,$this->get_string_between($bcc_value,"\u003c", "\u003e"));
                            }
                            $message_data['bcc'] = $bcc;
                        }
                    }
                    $parsed = $this->parts_parser($parts, $message->id);
                    $array = explode("\n", $parsed['content']);
                    $content_output = array();
                    $i=0;
                    $remove =0;
                    foreach ($array as $arr) {
                        if (!(preg_match('/^>/', $arr)) && !(preg_match('/^On Mon,|^On Tue,|^On Wed,|^On Thu,|^On Fri,|^On Sat,|^On Sun,|^wrote:|^On /', $arr))) {
                            array_push($content_output, $arr);
                        }
                        else
                        {
                            $remove = $i;
                        }
                        $i++;
                    }
                    if($remove != 0)
                    {
                        unset($content_output[$remove-1]);
                    }
                    $content = implode("\n", $content_output);
                    $message_data['content'] = preg_replace('/\n(\s*\n){2,}/', "\n\n", $content);
                    if (isset($parsed['attachments'])) {
                        $message_data['attachments'] = $parsed['attachments'];
                    }
                    $this->match_insert($message_data);
                }
            }
        }
    }
    
    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) 
        {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
    
    function match_insert($data) {
        $ticket_id = 'new';
        if (preg_match('/Ticket \[(.*?)\] :/', $data['subject'], $output) == 1) {
            $ticket_id = $output[1];
        }
        if ($ticket_id != 'new') {
            $email_validate = eh_crm_get_ticket_value_count("ticket_email", $data['email'], false, "ticket_id", $ticket_id);
            if (!empty($email_validate)) {
                $parent = eh_crm_get_ticket(array("ticket_id" => $ticket_id));
                eh_crm_update_ticketmeta($ticket_id, "ticket_cc", (isset($data['cc']))?$data['cc']:array());
                eh_crm_update_ticketmeta($ticket_id, "ticket_bcc", (isset($data['bcc']))?$data['bcc']:array());
                eh_crm_update_ticketmeta($ticket_id, "ticket_label", 'label_LL01');
                $user = get_user_by("email", $data['email']);
                $author = 0;
                if($user)
                {
                    $author = $user->ID;
                }
                $category = 'raiser_reply';
                $child = array(
                    'ticket_author' => $author,
                    'ticket_email' => $data['email'],
                    'ticket_title' => $parent[0]['ticket_title'],
                    'ticket_content' => str_replace("\n", '<br/>', $data['content']),
                    'ticket_category' => $category,
                    'ticket_parent' => $ticket_id);
                if (isset($data['attachments'])) {
                    $child_meta = array();
                    $attach_path = array();
                    $attach_url = array();
                    foreach ($data['attachments'] as $attach) {
                        array_push($attach_url, $attach['url']);
                        array_push($attach_path, $attach['path']);
                    }
                    $child_meta["ticket_attachment"] = $attach_url;
                    $child_meta["ticket_attachment_path"] = $attach_path;
                    eh_crm_insert_ticket($child, $child_meta);
                } else {
                    eh_crm_insert_ticket($child);
                }
            }
            else
            {
                $user = get_user_by("email", $data['email']);
                $author = 0;
                if($user)
                {
                    $author = $user->ID;
                }
                $email = $data['email'];
                $title = $data['subject'];
                $desc = str_replace("\n", '<br/>', $data['content']);
                $args = array(
                    'ticket_author' => $author,
                    'ticket_email' => $email,
                    'ticket_title' => $title,
                    'ticket_content' => $desc,
                    'ticket_category' => 'raiser_reply',
                    'ticket_vendor' => ''
                );
                $meta = array();
                $default_assignee = eh_crm_get_settingsmeta('0', "default_assignee");
                $assignee = array();
                switch ($default_assignee) {
                    case "no_assignee":
                        break;
                    default:
                        array_push($assignee, $default_assignee);
                        break;
                }
                $meta['ticket_assignee'] = $assignee;
                $meta['ticket_tags'] = array();
                $default_label = eh_crm_get_settingsmeta('0', "default_label");
                if(eh_crm_get_settings(array('slug'=>$default_label)))
                {
                    $meta['ticket_label'] = $default_label;
                }
                if (isset($data['attachments'])) {
                    $attach_path = array();
                    $attach_url = array();
                    foreach ($data['attachments'] as $attach) {
                        array_push($attach_url, $attach['url']);
                        array_push($attach_path, $attach['path']);
                    }
                    $meta["ticket_attachment"] = $attach_url;
                    $meta["ticket_attachment_path"] = $attach_path;
                }
                $meta["ticket_cc"] = (isset($data['cc']))?$data['cc']:array();
                $meta["ticket_bcc"] = (isset($data['bcc']))?$data['bcc']:array();
                $meta['ticket_source'] = "EMail";
                eh_crm_insert_ticket($args, $meta);
            }
        } else {
            $user = get_user_by("email", $data['email']);
            $author = 0;
            if($user)
            {
                $author = $user->ID;
            }
            $email = $data['email'];
            $title = $data['subject'];
            $desc = str_replace("\n", '<br/>', $data['content']);
            $args = array(
                'ticket_author' => $author,
                'ticket_email' => $email,
                'ticket_title' => $title,
                'ticket_content' => $desc,
                'ticket_category' => 'raiser_reply',
                'ticket_vendor' => ''
            );
            $meta = array();
            $default_assignee = eh_crm_get_settingsmeta('0', "default_assignee");
            $assignee = array();
            switch ($default_assignee) {
                case "no_assignee":
                    break;
                default:
                    array_push($assignee, $default_assignee);
                    break;
            }
            $meta['ticket_assignee'] = $assignee;
            $meta['ticket_tags'] = array();
            $default_label = eh_crm_get_settingsmeta('0', "default_label");
            if(eh_crm_get_settings(array('slug'=>$default_label)))
            {
                $meta['ticket_label'] = $default_label;
            }
            if (isset($data['attachments'])) {
                $attach_path = array();
                $attach_url = array();
                foreach ($data['attachments'] as $attach) {
                    array_push($attach_url, $attach['url']);
                    array_push($attach_path, $attach['path']);
                }
                $meta["ticket_attachment"] = $attach_url;
                $meta["ticket_attachment_path"] = $attach_path;
            }
            $meta["ticket_cc"] = (isset($data['cc']))?$data['cc']:array();
            $meta["ticket_bcc"] = (isset($data['bcc']))?$data['bcc']:array();
            $meta['ticket_source'] = "EMail";
            eh_crm_insert_ticket($args, $meta);
        }
    }

    function parts_parser($parts, $message_id) {
        $parsed = array();
        $attachment =array();
        foreach ($parts as $part) {
            switch ($part->mimeType) {
                case "text/plain":
                    $part_body = $part->body;
                    $parsed['content'] = $this->decodeBody($part_body->data);
                    break;
                case "multipart/alternative":
                    $slice = $part->parts[0];
                    if ($slice->mimeType == "text/plain") {
                        $slice_body = $slice->body;
                        $parsed['content'] = $this->decodeBody($slice_body->data);
                    }
                    break;
                case "text/html":
                    break;
                default:
                    $part_body = $part->body;
                    array_push($attachment, $this->get_attachment($message_id, $part_body->attachmentId, $part->filename));
                    break;
            }
        }
        if(!empty($attachment))
        {
            $parsed['attachments'] = $attachment;
        }
        return $parsed;
    }

    function get_attachment($message_id, $attachment_id, $filename) {
        $access_token = eh_crm_get_settingsmeta(0, "oauth_accesstoken");
        $message_url = 'https://www.googleapis.com/gmail/v1/users/me/messages/' . $message_id;
        $constant_url = '?v=2&oauth_token=' . $access_token;
        $request_url = $message_url . '/attachments/' . $attachment_id . $constant_url;
        $attdata = json_decode(file_get_contents($request_url));
        $attachment = $this->decodeBody($attdata->data);
        $upload = wp_upload_dir();
        $file = time() . '_' . $filename;
        $file_name = $upload['path'] . '/' . $file;
        $ifp = fopen($file_name, "w");
        fwrite($ifp, $attachment);
        fclose($ifp);
        $data = array
            (
            'path' => $upload['path'] . '/' . $file,
            'url' => $upload['url'] . '/' . $file
        );
        return $data;
    }

    function decodeBody($body) {
        $rawData = $body;
        $sanitizedData = strtr($rawData, '-_', '+/');
        $decodedMessage = base64_decode($sanitizedData);
        if (!$decodedMessage) {
            $decodedMessage = FALSE;
        }
        return $decodedMessage;
    }

    function crawler_schedule_terminate() {
        wp_clear_scheduled_hook('crm_email_crawler');
    }

}
