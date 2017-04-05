<?php
/**
 * Student
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( isset($_GET['edit']) ){

	require_once('student-edit.php');
	exit;

}

$args = array(
	'post_type' => 'student_product',
);
$query = new WP_Query($args);

if ( $query->have_posts() ) : ?>

<div class="dokan-dashboard-content dokan-product-listing">
	<article class="dokan-product-listing-area">

		<div class="product-listing-top dokan-clearfix" style="padding-bottom:1em;">
                <span class="dokan-add-product-link">
                    <a href="<?= site_url() ?>/my-account/student/?action=new" class="dokan-btn dokan-btn-theme dokan-right"><i class="fa fa-briefcase">&nbsp;</i> Add new product</a>
                </span>
            </div>

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
				<tr>                  
			        <td data-title="Name">
			            <p><a href="<?= site_url() ?>/my-account/student/?edit=<?php the_ID() ?>"><?php the_title() ?></a></p>

			            <div class="row-actions">
			                <span class="edit"><a href="<?= site_url() ?>/my-account/student/?edit=<?php the_ID() ?>">Edit</a> | </span>
			                <span class="delete"><a onclick="return confirm('Are you sure?');" href="<?= site_url() ?>/my-account/student/?delete=<?php the_ID() ?>">Delete Permanently</a> | </span>
			                <span class="view"><a href="<?php the_permalink() ?>" rel="permalink">View</a></span>
			            </div>
			        </td>
			        <td data-title="Name">
			            aaro@mail.ee, miki@mail.ee
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
	</article>
</div>

<?php else : ?>

<?php endif; ?>
<?php wp_reset_query(); ?>

<?php get_footer(); ?>
