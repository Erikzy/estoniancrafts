<?php
/**
 * Student-edit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$student_post = new stdClass;
$student_post->ID = 0;
$student_post->post_title = '';
$student_post->post_content = '';

$shared_emails = [''];

if( isset($_POST['lb_student_save']) ){

	if( !check_admin_referer( 'edit_student_post_'.(int)$_POST['lb_student_id'] ) ){

		lbStudent::$errors[] = __( "Wp_nonce is not valid.", 'ktt' );

	}

	if( isset($_POST['lb_student_id']) && $_POST['lb_student_id'] != 0 ){

		$student_p = lbStudent::can_edit_post($_POST['lb_student_id']);
		if ( $student_p )  {
			$student_post->ID = $student_p->ID;
		}else{
			lbStudent::$errors[] = __( "Can't modify the post with that ID", 'ktt' );
		}

	}

	$student_post->post_title   = sanitize_text_field($_POST['lb_student_title']);
	$student_post->post_content = wp_kses_post($_POST['lb_student_content']);

	if(strlen($student_post->post_title) < 3){
    	lbStudent::$errors[] = __( 'Please enter product title', 'ktt' );
	}
	if(strlen($student_post->post_content) < 3){
    	lbStudent::$errors[] = __( 'Please enter product content', 'ktt' );
	}

	if( count(lbStudent::$errors) == 0 ){
		$post_array = [
			'ID' => $student_post->ID, 
	        'post_content' => $student_post->post_content,
	        'post_title' => $student_post->post_title,
	        'post_status' => 'publish',
	        'post_type' => 'student_product',
	        'comment_status' => 'open',
	        'ping_status' => 'closed'
		];

		$insert_id = wp_insert_post( $post_array );

		if( !$insert_id){
    		lbStudent::$errors[] = __( 'Something went wrong. Try again', 'ktt' );
		}else{
			$student_post->ID = $insert_id;

			lbStudent::share_post($student_post->ID, $_POST['_shared_email']);

		}

	}

}

if( isset($_GET['edit']) ){

	$student_p = lbStudent::can_edit_post($_GET['edit']);

	if ( $student_p )  {
		$student_post = $student_p;

		$emails = get_post_meta($student_post->ID, '_shared_emails', true);
		if( is_array($emails)  && count($emails) ){
		    $shared_emails = $emails;
		}

	}else{
		lbStudent::$errors[] = __( "Can't modify the post with that ID", 'ktt' );
	}

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
                <h3><?php _e( 'Invite users to access post', 'ktt' ); ?></h3>
            </p>

            <p>
                <div class="lb-elastic-container">
                    <div class="lb-elastic-elements">
                        <?php
                        
                        foreach($shared_emails as $email){

                        ?>
                            <div class="lb-elastic-element lb-input-margins">

                                <div class="dokan-form-group">

                                	<input type="email" name="_shared_email[]" value="<?= $email ?>" class="dokan-form-control" placeholder="<?php _e("Share access with email", 'ktt') ?>">

                                </div>

                            </div>

                        <?php 
                        }
                        ?>

                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                </div>
            </p>
	        <p>
	        	<?php wp_nonce_field( 'edit_student_post_'.$student_post->ID ); ?>
	        	<input type="hidden" name="lb_student_id" value="<?= $student_post->ID ?>">	
	        	<input type="submit" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" name="lb_student_save" value="<?php _e('Save post', 'ktt') ?>">	
	        </p>
        </form>
	</article>
</div>

<?php get_footer(); ?>
