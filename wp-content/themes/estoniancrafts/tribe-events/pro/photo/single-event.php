<?php
/**
 * Photo View Single Event
 * This file contains one event in the photo view
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/photo/single_event.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
} ?>

<?php

global $post;

?>

<div class="tribe-events-photo-event-wrap">
    <?php echo tribe_event_featured_image( null, 'medium' ); ?>

    <div class="tribe-events-event-details tribe-clearfix">

        <!-- Event Title -->
        <?php do_action( 'tribe_events_before_the_event_title' ); ?>
        <h2 class="tribe-events-list-event-title">
            <a class="tribe-event-url" href="<?php echo esc_url( tribe_get_event_link() ); ?>" title="<?php the_title() ?>" rel="bookmark">
                <?php the_title(); ?>
            </a>
        </h2>
        <?php do_action( 'tribe_events_after_the_event_title' ); ?>

        <!-- Event Content -->
        <?php do_action( 'tribe_events_before_the_content' ); ?>
        <div class="tribe-events-list-photo-description tribe-events-content">
            <?php echo tribe_events_get_the_excerpt() ?>
        </div>
        <?php do_action( 'tribe_events_after_the_content' ) ?>

        <div class="tribe_events_more_button_wrapper dokan-store-caption">
            <a href="<?php echo esc_url( tribe_get_event_link() ); ?>" title="<?php the_title() ?>" rel="bookmark" class="button add-btn orange-black-button medium-orange-button ">More</a>
        </div>

    </div><!-- /.tribe-events-event-details -->

</div><!-- /.tribe-events-photo-event-wrap -->
