<?php
/**
 * Event Submission Form Metabox For Custom Fields
 * This is used to add a metabox to the event submission form to allow for custom
 * field input for user submitted events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/custom.php
 *
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @author Modern Tribe Inc.
 * @version 4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$customFields = tribe_get_option( 'custom-fields' );

if ( empty( $customFields ) || ! is_array( $customFields ) ) {
	return;
}
?>

<!-- Custom -->
<div id="event_custom" class="tribe-events-community-details eventForm bubble event-custom-fields">
	<div class="tribe_sectionheader">
		<h4 class="tribe-custom-fields"><?php esc_html_e( 'Additional Fields', 'tribe-events-community' ); ?></h4>
	</div>
	
	<?php foreach ( $customFields as $customField ) :
	$val = '';
	global $post;
	if ( isset( $post->ID ) && get_post_meta( get_the_ID(), $customField['name'], true ) ) {
		$val = get_post_meta( get_the_ID(), $customField['name'], true );
	}
	$val = apply_filters( 'tribe_community_custom_field_value', $val, $customField['name'], get_the_ID() );
	$field_id = 'tribe_custom_'.sanitize_title( $customField['label'] );
	?>
	<table class="tribe-community-event-details" role="presentation">
		<colgroup>
			<col style="width:33%">
			<col style="width:67%">
		</colgroup>
		<tr>
			<td>
				<?php tribe_community_events_field_label( $customField['name'], sprintf( _x( '%s:', 'custom field label', 'tribe-events-community' ), $customField['label'] ) ); ?>
			</td>
			<td>
				<?php
				$options = explode( "\n", $customField['values'] );
				if ( $customField['type'] == 'text' ) {
					?>
					<input
						type="text"
						id="<?php echo esc_attr( $field_id ); ?>"
						name="<?php echo esc_attr( $customField['name'] ); ?>"
						value="<?php echo esc_attr( $val ); ?>"
					>
					<?php
				} elseif ( $customField['type'] == 'url' ) {
					?>
					<input
						type="url"
						id="<?php echo esc_attr( $field_id ); ?>"
						name="<?php echo esc_attr( $customField['name'] ); ?>"
						value="<?php echo esc_attr( $val ); ?>"
					>
					<?php
				} elseif ( 'radio' === $customField['type'] ) {
					?>
					<label>
						<input
							type="radio"
							name="<?php echo esc_attr( $customField['name'] ) ?>"
							value="" <?php checked( trim( $val ), '' ) ?>
						>
						<?php esc_html_e( 'None', 'tribe-events-community' ); ?>
					</label>
					<?php foreach ( $options as $option ) { ?>
						<label>
							<input
								type="radio"
								name="<?php echo esc_attr( stripslashes( $customField['name'] ) ); ?>"
								value="<?php echo esc_attr( trim( $option ) ); ?>"
								<?php checked( esc_attr( trim( $val ) ), esc_attr( trim( $option ) ) ); ?>
							>
							<?php echo esc_html( stripslashes( $option ) ); ?>
						</label>
						<?php
					}
				} elseif ( $customField['type'] == 'checkbox' ) {
					foreach ( $options as $option ) {
						$values = ! is_array( $val ) ? explode( '|', $val ) : $val;
						?>
						<label>
							<input
								type="checkbox"
								value="<?php echo esc_attr( trim( $option ) ); ?>"
								<?php checked( in_array( esc_attr( trim( $option ) ), $values ) ) ?>
								name="<?php echo esc_html( stripslashes( $customField['name'] ) ); ?>[]"
							>
							<?php echo esc_html( stripslashes( $option ) ); ?>
						</label>
						<?php
					}
				} elseif ( $customField['type'] == 'dropdown' ) { ?>
					<select name="<?php echo esc_attr( $customField['name'] ); ?>">
						<option
							value=""
							<?php selected( trim( $val ), '' ) ?>
						>
							<?php esc_html_e( 'None', 'tribe-events-community' ); ?>
						</option>
						<?php
						$options = explode( "\n", $customField['values'] );
						foreach ( $options as $option ) {
							?>
							<option
								value="<?php echo esc_attr( trim( $option ) ); ?>"
								<?php selected( esc_attr( trim( $val ) ), esc_attr( trim( $option ) ) ); ?>
							>
								<?php echo esc_html( stripslashes( $option ) ); ?>
							</option>
							<?php
						}
						?>
					</select>
					<?php
				} elseif ( $customField['type'] == 'textarea' ) {
					?>
					<textarea id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $customField['name'] ); ?>"><?php echo esc_textarea( stripslashes( $val ) ); ?></textarea>
					<?php
				}
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div><!-- #event-meta -->