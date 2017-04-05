<?php
/**
 * Student-edit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( isset($_POST['lb_student_save']) ){

	$id 	 = (int)$_GET['edit']; // TODO: check if current user owns the post!
	$title   = sanitize_text_field($_POST['lb_student_title']);
	$content = wp_kses_post($_POST['lb_student_content']);

	if(strlen($title) < 3){
    	lbStudent::$errors[] = __( 'Please enter product title', 'ktt' );
	}
	if(strlen($content) < 3){
    	lbStudent::$errors[] = __( 'Please enter product content', 'ktt' );
	}

	if( count(lbStudent::$errors) == 0 ){
		$post_array = [
			'ID' => $id, 
	        'post_content' => $content,
	        'post_title' => $title,
	        'post_status' => 'publish',
	        'post_type' => 'student_product',
	        'comment_status' => 'open',
	        'ping_status' => 'closed'
		];

		$insert = wp_insert_post( $post_array );

		if( !$insert){
    		lbStudent::$errors[] = __( 'Something went wrong. Try again', 'ktt' );
		}

	}

}

$student_post = new stdClass;
$student_post->ID = 0;
$student_post->post_title = '';
$student_post->post_content = '';

if( isset($_GET['edit']) ){
	
	$student_post = get_post( (int)$_GET['edit'] );

}


?>

<?php if ( lbStudent::$errors ) { ?>
    <div class="dokan-alert dokan-alert-danger">
        <a class="dokan-close" data-dismiss="alert">&times;</a>

        <?php foreach ( lbStudent::$errors as $error) { ?>
            <strong><?php _e( 'Error!', 'ktt' ); ?></strong> <?php echo $error ?>.<br>
        <?php } ?>
    </div>
<?php } ?>

<div class="dokan-dashboard-content dokan-product-listing">
	<article class="dokan-product-listing-area">
			
		<form method="post">

			<p class="form-row form-row-wide">
	            <label for="post_title"><?php _e('Post name', 'ktt') ?> <span class="required">*</span></label>
	            <input type="text" class="input-text" name="lb_student_title" id="post_title" value="<?= $student_post->post_title ?>">
	        </p>

			<p class="form-row form-row-wide">

				<?php wp_editor( $student_post->post_content, 'lb_student_content', array('quicktags' => false, 'media_buttons' => true, 'editor_class' => 'post_excerpta') ); ?>

	        </p>

	        <p>
	        	<input type="hidden" name="lb_student_id" value="<?= $student_post->ID ?>">	
	        	<input type="submit" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" name="lb_student_save" value="<?php _e('Save post', 'ktt') ?>">	
	        </p>
        </form>
	</article>
</div>

<?php get_footer(); ?>
