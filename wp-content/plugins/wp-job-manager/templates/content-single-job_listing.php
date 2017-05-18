<?php global $post; ?>
<div class="single_job_listing" itemscope itemtype="http://schema.org/JobPosting">
    <meta itemprop="title" content="<?php echo esc_attr($post->post_title); ?>" />

    <?php if (get_option('job_manager_hide_expired_content', 1) && 'expired' === $post->post_status) : ?>
        <div class="job-manager-info"><?php _e('This listing has expired.', 'wp-job-manager'); ?></div>
    <?php else : ?>
        <?php
        /**
         * single_job_listing_start hook
         *
         * @hooked job_listing_meta_display - 20
         * @hooked job_listing_company_display - 30
         */
        do_action('single_job_listing_start');
        
        
        $postTerms = wp_get_post_terms($post->ID, 'job_listing_type');
        $postType = 'procurement';
        if(is_array($postTerms)) {
            $postType = $postTerms[0]->slug;
        }
        
        
        
        ?>
        
        <div class='row'>
            <div class="col2">
                <div class='image'><?php echo get_the_job_image(); ?></div>
            </div>

            <div class='col2'>
                <div class='col2'>
                    
                    <?php if(($upperPrice = get_the_job_upper_price()) && !empty($upperPrice)): ?>
                    <div class='upper-price'>
                        <span class='label2'><?php echo __(( $postType == 'procurement' ? 'Upper price' : 'Inital price'), 'wp-job-manager'); ?>:</span>
                        <span class='text'><?php echo $upperPrice; ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if($postType == 'offer'): ?>
                    <div class='deadline'>
                        <span class='label2'><?php echo __('Start time', 'wp-job-manager'); ?>:</span>
                        <span class='text'><?php echo get_the_job_deadline(); ?></span>
                    </div>
                    <?php endif; ?>

                    <div class='deadline'>
                        <span class='label2'><?php echo __('Deadline', 'wp-job-manager'); ?>:</span>
                        <span class='text'><?php echo get_the_job_deadline(); ?></span>
                    </div>

                    <?php if($postType == 'offer'): ?>
                    <div class='deadline'>
                        <span class='label2'><?php echo __('Delivery time', 'wp-job-manager'); ?>:</span>
                        <span class='text'><?php echo get_the_job_deadline(); ?></span>
                    </div>
                    <?php endif; ?>
                    
                </div>
                <div class='col2'>
                    <?php echo get_the_job_author_info(); ?>
                </div>
            </div>
        </div>

        <div class="job_description" itemprop="description">
            <?php echo apply_filters('the_job_description', get_the_content()); ?>
        </div>

        <div class="job_description">
            <?php echo apply_filters('the_job_payment', get_the_job_payment()); ?>
        </div>

        <div class="job_description">
            <?php echo apply_filters('the_job_terms', get_the_job_terms()); ?>
        </div>
        
        <div class='job_images'>
            <?php get_the_job_image(null, 'company_images'); ?>
        </div>
        
        <div class='job_files'>
            <h3><?php echo __('Files', 'wp-job-manager'); ?></h3>
            <?php get_the_job_files(); ?>
        </div>
        
        <?php get_job_manager_template('job-offer.php'); ?>
        
        <?php if (false && candidates_can_apply()) : ?>
            <?php get_job_manager_template('job-application.php'); ?>
        <?php endif; ?>

        <?php
        /**
         * single_job_listing_end hook
         */
        do_action('single_job_listing_end');
        ?>
    <?php endif; ?>
</div>
