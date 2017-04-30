<?php

class WP_Job_Offer
{
    
    const FIELD_USER = 'job_offers_user_id';
    const FIELD_NAME = 'job_offers_name';
    const FIELD_EMAIL = 'job_offers_email';
    const FIELD_PHONE = 'job_offers_phone';
    const FIELD_PRICE = 'job_offers_price';
    const FIELD_MESSAGE = 'job_offers_message';
    const FIELD_TERMS = 'job_offers_terms';
    
    const NAME = 'job_offers';
    
    protected $type;
    
    public static function getFields()
    {
        return array(
            static::FIELD_USER,
            static::FIELD_NAME,
            static::FIELD_EMAIL,
            static::FIELD_PHONE,
            static::FIELD_PRICE,
            static::FIELD_MESSAGE,
            static::FIELD_TERMS
        );
    }
    
    public static function getFormFields()
    {
        return array(
            static::FIELD_USER => array(
                'type' => 'hidden',
                'label' => 'User id',
                'required' => true
            ),
            static::FIELD_NAME => array(
                'type' => 'text',
                'label' => __('Name', 'wp-job-manager'),
                'required' => true
            ),
            static::FIELD_EMAIL => array(
                'type' => 'text',
                'label' => __('E-mail', 'wp-job-manager'),
                'required' => true
            ),
            static::FIELD_PHONE => array(
                'type' => 'text',
                'label' => __('Phone', 'wp-job-manager'),
                'required' => true
            ),
            static::FIELD_PRICE => array(
                'type' => 'text',
                'label' => __('Price', 'wp-job-manager'),
                'required' => true
            ),
            static::FIELD_MESSAGE => array(
                'type' => 'textarea',
                'label' => __('Message', 'wp-job-manager'),
                'required' => false
            ),
            static::FIELD_TERMS => array(
                'type' => 'checkbox2',
                'label' => __('Terms', 'wp-job-manager'),
                'required' => true
            )
        );
    }
    
    public function __construct()
    {
        $post = get_post();
        $postTerms = wp_get_post_terms($post->ID, 'job_listing_type');
        $postType = 'procurement';
        if(is_array($postTerms)) {
            $postType = $postTerms[0]->slug;
        }
        
        $this->type = $postType;
    }
    
    public function getCheapestPriceRow()
    {
        global $wpdb;
        
        $post = get_post();
        
        $table_name = $wpdb->prefix . self::NAME;
        $ids = $this->getPostMeta($post->ID);
        if(!is_array($ids)) {
            return false;
        }
        
        $idsStr = implode(',', $ids);
        $priceField = str_replace(static::NAME . '_', '', static::FIELD_PRICE);
        $sql = "SELECT * FROM $table_name WHERE id in ($idsStr) ORDER BY $priceField ASC LIMIT 1";
        $results = $wpdb->get_results( $sql, OBJECT );
        
        $countSql = "SELECT count(id) AS count FROM $table_name WHERE id in ($idsStr)";
        $count = $wpdb->get_results( $countSql, OBJECT );
        
        if(count($results) > 0) {
            $result = $results[0];
            $result->count = $count[0]->count;
            
            return $result;
        }
        
        return false;
    }
    
    public function getFormData()
    {
        $defaultData = $this->getDefaultData();
        $postData = $this->getPostData();
        $data = array_merge($defaultData, $postData);
        
        return $data;
    }

    public function getDefaultData()
    {
        $user = wp_get_current_user();
        
        if($user->ID == 0) {
            return array(
                static::FIELD_USER => 0,
                static::FIELD_NAME => '',
                static::FIELD_EMAIL => ''
            );
        }

        return array(
            static::FIELD_USER => $user->ID,
            static::FIELD_NAME => $user->data->user_nicename,
            static::FIELD_EMAIL => $user->data->user_email
        );
    }
    
    public function getPostData()
    {
        $fields = static::getFields();
        $data = array();
        
        foreach($fields AS $field) {
            if(array_key_exists($field, $_POST)) {
                $value = $_POST[$field];
                if($field === static::FIELD_PRICE) {
                    $value = trim($value);
                    $value = preg_replace("/[^0-9,.]/", "", $value);
                    $value = str_replace(",", ".", $value);
                } else if($field === static::FIELD_PHONE) {
                    $value = trim($value);
                    $value = preg_replace("/[^0-9]/", "", $value);
                }
                $data[$field] = $value;
            }
        }
        
        return $data;
    }

    public function validate()
    {
        $errors = array();
        $post = $this->getPostData();
        if (!empty($post)) {
            $deadline = get_the_job_deadline();
            $deadline = strtotime($deadline);
            $now = strtotime(date('Y-m-d H:i:s'));
            $now = strtotime($now);
            if($now > $deadline) {
                $errors[static::FIELD_PRICE] = __('The deadline is over', 'wp-job-manager');
            }
            if (!is_email($post[static::FIELD_EMAIL])) {
                $errors[static::FIELD_EMAIL] = __('Invalid e-mail', 'wp-job-manager');
            }
            if (empty($post[static::FIELD_NAME])) {
                $errors[static::FIELD_NAME] = __('Name is empty', 'wp-job-manager');
            }
            if (empty($post[static::FIELD_PHONE])) {
                $errors[static::FIELD_PHONE] = __('Phone is empty', 'wp-job-manager');
            }
            if (empty($post[static::FIELD_PRICE])) {
                $errors[static::FIELD_PRICE] = __('Price is empty', 'wp-job-manager');
            } else if($upperPrice = get_the_job_upper_price()) {
                $upperPrice = preg_replace("/[^0-9,.]/", "", $upperPrice);
                $upperPrice = str_replace(",", ".", $upperPrice);
                $cheapest = $this->getCheapestPriceRow();
                if($this->type === 'procurement') {
                    if($upperPrice < $post[static::FIELD_PRICE] || 
                            ($cheapest && (int) $cheapest->price > 0 && $post[static::FIELD_PRICE] >= $cheapest->price)) {
                        $errors[static::FIELD_PRICE] = __('The price is too high', 'wp-job-manager');
                    }
                } else {
                    if($upperPrice > $post[static::FIELD_PRICE] || 
                            ($cheapest && (int) $cheapest->price > 0 && $post[static::FIELD_PRICE] <= $cheapest->price)) {
                        $errors[static::FIELD_PRICE] = __('The price is too low', 'wp-job-manager');
                    }
                }
            }
            if (empty($post[static::FIELD_TERMS])) {
                $errors[static::FIELD_TERMS] = __('Terms is not checked', 'wp-job-manager');
            }
            
            
        }

        return $errors;
    }

    public function submit()
    {
        $post = get_post();

        $this->createTable();

        $id = $this->insertRow();
        $this->addPostMeta($post->ID, $id);
        
        $this->updateDeadline();
    }
    
    protected function updateDeadline()
    {
        $deadline = get_the_job_deadline();
        $deadline = strtotime($deadline);
        $now = strtotime(date('Y-m-d H:i:s'));
        $now = strtotime($now);
        
        if($now + (60*5) > $deadline) {
            $deadline = $deadline + (60*5);
            $deadline = date("d.m.Y H:i:s", $deadline);
            update_the_job_deadline();
        }
    }

    public function addPostMeta($postId, $jobOfferId)
    {
        $data = array($jobOfferId);
        if (!add_post_meta($postId, self::NAME, $data, true)) {
            $meta = $this->getPostMeta($postId);
            $meta[] = $jobOfferId;
            $data = array_unique($meta);
            update_post_meta($postId, self::NAME, $data);
        }
    }
    
    public function getPostMeta($postId)
    {
        return get_post_meta($postId, self::NAME, true);
    }

    public function insertRow()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::NAME;
        $data = array();
        $fields = static::getFields();
        $post = $this->getPostData();
        
        foreach($fields AS $field) {
            if(in_array($field, array(static::FIELD_TERMS))) {
                continue;
            }
            $dbField = str_replace(static::NAME . '_', '', $field);
            $data[$dbField] = $post[$field];
        }
        $data['time'] = current_time('mysql');

        $wpdb->insert($table_name, $data);

        $lastId = $wpdb->insert_id;

        return $lastId;
    }

    public function createTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::NAME;

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            user_id int(11) NOT NULL,
            name varchar(255) DEFAULT '' NOT NULL,
            email varchar(255) DEFAULT '' NOT NULL,
            phone varchar(50) DEFAULT '' NOT NULL,
            price varchar(25) DEFAULT '' NOT NULL,
            message text NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
          ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($sql);
        }
    }
    
    protected $termsPage = null;
    public function getTermPage()
    {
        if($this->termsPage) {
            return $this->termsPage;
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'posts';
        $post_name = 'job-offer-terms';
        
        $page = $wpdb->get_row("SELECT post_name, guid FROM $table_name WHERE post_name = '" . $post_name . "'", 'ARRAY_A');
        $this->termsPage = $page;
        
        return $page;
    }
    
    public function createTermsPageIfNotExit()
    {
        if($this->getTermPage()) {
            return false;
        }
        
        $my_post = array(
            'post_author' => 1,
            'post_content' => '',
            'post_content_filtered' => '',
            'post_title' => 'Job Offer Terms',
            'post_excerpt' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => '',
            'ping_status' => '',
            'post_password' => '',
            'to_ping' =>  '',
            'pinged' => '',
            'post_parent' => 0,
            'menu_order' => 0,
            'guid' => '',
            'import_id' => 0,
            'context' => '',
        );
        
        wp_insert_post( $my_post );
    }
}
