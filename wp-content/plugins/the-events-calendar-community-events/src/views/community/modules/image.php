<?php
/**
 * Event Submission Form Image Uploader Block
 * Renders the image upload field in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/image.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @version 4.4.2
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$upload_error = tribe( 'community.main' )->max_file_size_exceeded();
$size_format = size_format( wp_max_upload_size() );
$image_label = sprintf( esc_html__( '%s Image', 'tribe-events-community' ), tribe_get_event_label_singular() );
?>

<?php do_action( 'tribe_events_community_before_the_featured_image' ); ?>

<div id="event_image_uploader" class="tribe-events-community-details eventForm bubble">
	<div class="tribe_sectionheader">
		<h4 tabindex="0"><?php tribe_community_events_field_label( 'featured_image', $image_label ); ?></h4>
	</div>
	<div class="tribe-image-upload-area">
		<div class="note">
			<p><?php esc_html_e( 'Images that are not png, jpg, or gif will not be uploaded.', 'tribe-events-community' );?></p>
			<p><?php echo esc_html( sprintf( __( 'Images may not exceed %1$s in size.', 'tribe-events-community' ), $size_format ) ); ?></p>
		</div>

		<?php if ( get_post() && has_post_thumbnail() ) { ?>
			<div class="tribe-community-events-preview-image">
				<?php the_post_thumbnail( 'medium' ); ?>
				<?php tribe_community_events_form_image_delete(); ?>
			</div>
		<?php }	?>

		<div class="form-controls">
			<label for="EventImage" class="screen-reader-text <?php echo esc_attr( $upload_error ? 'error' : '' ); ?>">
				<?php esc_html_e( 'Event Image', 'tribe-events-community' );?>
			</label>

			<div class="choose-file button"><?php esc_html_e( 'Choose File', 'tribe-events-community' );?></div>

			<label for="uploadFile" class="screen-reader-text">
				<?php esc_html_e( 'Upload File', 'tribe-events-community' ); ?>
			</label>
			<input id="uploadFile" class="uploadFile" placeholder="" disabled="disabled" />

			<input id="EventImage" class="EventImage" type="file" name="event_image">

		</div>
	</div>
</div>

<?php
do_action( 'tribe_events_community_after_the_featured_image' );
