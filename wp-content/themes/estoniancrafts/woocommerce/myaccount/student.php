<?php
/**
 * Student
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( isset($_GET['edit']) || ( isset($_GET['action']) && $_GET['action'] == 'new' ) ){

	require_once('student-edit.php');
	exit;

}

if( isset($_GET['delete']) && (int)$_GET['delete'] != 0 && lbStudent::can_edit_post((int)$_GET['delete']) ){

	if( check_admin_referer( 'delete-student-post_'.(int)$_GET['delete'] ) ){
		wp_delete_post( (int)$_GET['delete'], true );
	}

}

$args = array(
	'post_type' => 'student_product',
	'author' => get_current_user_id()
);
$query = new WP_Query($args);

?>

<div class="dokan-dashboard-content dokan-product-listing">
	<article class="dokan-product-listing-area">

		<?php if ( isset( $_GET['message'] ) && $_GET['message'] == 'success') { ?>
            <div class="dokan-message">
                <button type="button" class="dokan-close" data-dismiss="alert">&times;</button>
                <strong><?php _e( 'Success!', 'dokan' ); ?></strong> <?php _e( 'The product has been saved successfully.', 'dokan' ); ?>
            </div>
        <?php } ?>

		<div class="product-listing-top dokan-clearfix" style="padding-bottom:1em;">
            <span class="dokan-add-product-link">
                <a href="<?= site_url() ?>/my-account/student/?action=new" class="dokan-btn dokan-btn-theme dokan-right"><i class="fa fa-briefcase">&nbsp;</i> Add new product</a>
            </span>
        </div>

       	<?php if ( $query->have_posts() ) : ?>
		<table class="dokan-table dokan-table-striped product-listing-table">
			<thead>
			    <tr>
			        <th>Name</th>
			        <th>Shared</th>
			        <th>Date</th>
			    </tr>
			</thead>
			<tbody>
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php global $post; ?>
				<tr>                  
			        <td data-title="Name">
			            <p><a href="<?= site_url() ?>/my-account/student/?edit=<?php the_ID() ?>"><?php the_title() ?></a></p>

			            <div class="row-actions">
			                <span class="edit"><a href="<?= site_url() ?>/my-account/student/?edit=<?php the_ID() ?>">Edit</a> | </span>
			                <span class="delete"><a onclick="return confirm('Are you sure?');" href="<?= wp_nonce_url( site_url()."/my-account/student/?delete=".$post->ID, 'delete-student-post_'.$post->ID ) ?>">Delete Permanently</a> | </span>
			                <span class="view"><a href="<?php the_permalink() ?>" rel="permalink">View</a></span>
			            </div>
			        </td>
			        <td data-title="Name">
			        	<?php
		
							$emails = get_post_meta($post->ID, '_shared_emails', true);
							
			        	?>
			        </td>
			        
			        <td data-title="Date">
			            <?php the_time( get_option( 'date_format' ) ); ?>
			        </td>
			        <td class="diviader"></td>
			    </tr>
			<?php endwhile; wp_reset_postdata(); ?>
			<!-- show pagination here -->

		    </tbody>
		</table>
		<?php else : ?>

			<h4><?php _e("You haven't added any products yet.", "ktt"); ?></h4>

		<?php endif; ?>
	</article>
</div>

<?php wp_reset_query(); ?>

<?php get_footer(); ?>
