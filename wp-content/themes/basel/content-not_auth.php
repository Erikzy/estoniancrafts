	<article id="post-0" class="post no-results not-found">
		<header class="entry-header">
			<h1 class="entry-title"><?php esc_html_e( 'Not authorized', 'ktt' ); ?></h1>
		</header>

		<div class="entry-content">
			<p><?php esc_html_e( 'Apologies, but you are not authorized to view this post.', 'ktt' ); ?></p>
			
			<?php if( is_user_logged_in() ){
				_e( "Make sure your email is added to the post's authorized email list.", 'ktt' );
			}else{
				_e( "You need to log in first.", 'ktt' );
			}
			?>

		</div><!-- .entry-content -->
	</article><!-- #post-0 -->
