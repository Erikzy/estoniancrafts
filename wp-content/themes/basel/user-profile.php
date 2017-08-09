<?php
   /**
    * The template for displaying user profile
    *
    */
   
   get_header(); 
?>
<div class="site-content col-sm-12" role="main">
<?php 
	$page = apply_filters('ec_get_page_personal_profile', null);
   
	if($page->user)
	{
		$user = $page->user;
//		ec_debug_to_console('$page', $page);
	}
	else
	{
		echo 'No user found!';
	}      
?>
	<div id="dokan-secondary" class="dokan-clearfix dokan-w3 dokan-store-sidebar ec-user-profile" role="complementary" style="margin-right:3%;">

		<?php // Avatar ?>
		<?php if($page->avatar_url): ?>
			<div class="profile-image">
				<img src="<?= $page->avatar_url ?>" title="<?= $user->first_name ?> <?= $user->last_name ?>" />
			</div>
		<?php endif; ?>

		<?php // Name ?>
		<h5 class="widget-title">
			<?php echo $page->name ?>
		</h5>

		<?php // User meta ?>
		<ul class="user-fields">
			<?php if($page->gender): ?>
				<li><?php echo $page->gender ?></li>
			<?php endif; ?>
			<?php if($page->address): ?>
			<li>
				<?= $page->address->get_address_as_string() ?>
			</li>
			<?php endif; ?>
			<?php if($page->phone): ?>
				<li><?php echo $page->phone ?></li>
			<?php endif; ?>
			<?php if($page->email): ?>
				<li><?php echo $page->email ?></li>
			<?php endif; ?>
		</ul>

		<?php // Social media ?>
		<ul class="social-nav">
			<?php if(($link = isset($page->sm_links['facebook']) ? $page->sm_links['facebook'] : null)): ?>
				<li class="facebook"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-facebook fa-lg"></i></a></li>
			<?php endif; ?>
			<?php if(($link = isset($page->sm_links['googleplus']) ? $page->sm_links['googleplus'] : null)): ?>
				<li class="twitter"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-twitter fa-lg"></i></a></li>
			<?php endif; ?>
			<?php if(($link = isset($page->sm_links['twitter']) ? $page->sm_links['twitter'] : null)): ?>
				<li class="instagram"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-instagram fa-lg"></i></a></li>
			<?php endif; ?>
			<?php if(($link = isset($page->sm_links['linkedin']) ? $page->sm_links['linkedin'] : null)): ?>
				<li class="linkedin"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-linkedin fa-lg"></i></a></li>
			<?php endif; ?>
			<?php if(($link = isset($page->sm_links['youtube']) ? $page->sm_links['youtube'] : null)): ?>
				<li class="youtube"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-youtube fa-lg"></i></a></li>
			<?php endif; ?>
			<?php if(($link = isset($page->sm_links['instagram']) ? $page->sm_links['instagram'] : null)): ?>
				<li class="instagram"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-instagram fa-lg"></i></a></li>
			<?php endif; ?>
			<?php if(($link = isset($page->sm_links['flickr']) ? $page->sm_links['flickr'] : null)): ?>
				<li class="flickr"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-flickr fa-lg"></i></a></li>
			<?php endif; ?>
		</ul>

		<?php // Contact us button ?>
		<?php if($page->contact_us_url): ?>
		<div class="expanded button-group">
			<a class="button" href="<?= $page->contact_us_url ?>"><?= __('Võta ühendust', 'ktt') ?></a>
		</div>
		<?php endif; ?>

	</div>
	<div id="dokan-primary" class="dokan-single-store dokan-w8 ec-user-info">
		<div id="dokan-content" class="store-page-wrap woocommerce" role="main">

			<?php if ($page->custom_content) : ?>
				<?= $page->custom_content ?>
			<?php else : ?> 

				<?php // Banner ?>
				<?php if(($banner = $page->banner) && $banner->image_url): ?>
				<div class="user-hero profile-info-summery-wrapper dokan-clearfix">
					<img src="<?= $banner->image_url ?>" title="<?= $banner->title ?>" />
				</div>
	<?php /* Sven: old vrsion with text on the image
				<div class="user-hero profile-info-summery-wrapper dokan-clearfix" <?php if($banner->image_url): ?> style="background-image: url(<?= $banner->image_url ?>)"<?php endif; ?>>
					<div class="class-effect">
						<div class="float-right">
							<?php if($banner->title): ?>
								<h1 class="store_name"><?= $banner->title ?></h1>
							<?php endif; ?>
							<?php if($banner->description): ?>
								<p class="ext_shop">
									<?= $banner->description ?>
									<?php if($banner->link_url): ?>
										<br /><a class="par-link" href="<?= $banner->link_url ?>"><?= __('View Shop', 'ktt') ?></a>
									<?php endif; ?>
								</p>
							<?php endif; ?>
						</div>
						<div class="bottom-bar">
							<div class="shop-buttons">
								<a class="button" href="#">Products</a>
							</div>
						</div>
					</div>
				</div>
	*/ ?>
				<?php endif; ?>

				<?php // Stores ?>
				<?php if(!empty($page->stores)): ?>
					<?php foreach($page->stores as $store): ?>
						<h3 class="title">
							<?= $store->title ?>
							<?php if($store->public_url): ?>
								<a href="<?= $store->public_url ?>"><?= __('see more', 'ktt' ) ?></a>
							<?php endif; ?>
						</h3>
						<?php if(!empty($store->featured_products)): ?>
							<div class="items">
								<?php foreach($store->featured_products as $product): ?>
									<?php // ec_debug_to_console($product) ?>
									<div class="item col-sm-2">
										<a href="<?= $product->public_url ?>">
											<?php if($product->image): ?>
												<img src="<?= $product->image->url ?>">
											<?php endif; ?>
											<?= $product->title ?>
										</a>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>

	            <?php //portfolio ?>
	            <?php if (count($page->portfolios)) : ?>
	            	<h3 class="title"><?= __('Portfolio', 'ktt') ?></h3>
	            	<ul class="dokan-seller-wrap">
	            	<?php foreach ($page->portfolios as $portfolio) : ?>
	            		<li class="dokan-single-seller">
			                <div class="dokan-store-thumbnail">

			                    <div class="dokan-store-banner-wrap">
			                        <a href="<?= ec_get_portfolio_url($portfolio, $user) ?>">
			                            <?php if ( false ) {
			                                $banner_url = wp_get_attachment_image_src( $banner_id, $image_size );
			                                ?>
			                                <img class="dokan-store-img" src="<?php echo esc_url( $banner_url[0] ); ?>" alt="<?php echo esc_attr( $store_name ); ?>">
			                            <?php } else { ?>
			                                <img class="dokan-store-img" src="<?php echo dokan_get_no_seller_image(); ?>" alt="<?php _e( 'No Image', 'dokan' ); ?>">
			                            <?php } ?>
			                        </a>
			                    </div>
			                    <div class="dokan-store-caption">
			                        <h3><a href="<?= ec_get_portfolio_url($portfolio, $user) ?>"><?= $portfolio->post_title ?></a></h3>
			                        <p><a class="dokan-btn dokan-btn-theme" href="<?= ec_get_portfolio_url($portfolio, $user) ?>"><?php _e( 'View portfolio', 'ktt' ); ?></a></p>
			                    </div> <!-- .caption -->
			                </div> <!-- .thumbnail -->
			            </li> <!-- .single-seller -->
	            	<?php endforeach; ?>
	            	</ul>
	            <?php endif; ?>

	            <?php // indu poop code ?>
	            <?php if(get_post_meta($page->user->data->ID, 'portfolio_gallery')): ?>
	            <h3 class="title"><?= __('Portfolio', 'ktt') ?></h3>
	            <?php 
	                $gallery_content = get_post_meta($page->user->data->ID, 'portfolio_gallery', true);
	                if(!empty($gallery_content)):
		                preg_match('/\[gallery.*ids=.(.*).\]/', $gallery_content, $ids);
		                $images_id = explode(",", $ids[1]); ?>
			            <ul class="row">
			                <?php foreach($images_id as $gallry) : ?>
				                <li class="col-md-2">
				                    <?php $attchment = get_post( intval(preg_replace('/[^0-9]+/', '', $gallry), 10) ); ?>
				                    <img src="<?= wp_get_attachment_url( $attchment->ID ); ?>" ><span><?= $attchment->post_content; ?></span>
				                </li>
			                <?php endforeach; ?>
			            </ul>
		            <?php endif; ?>
	            <div class="clear"></div>
	            <?php endif; ?>
	            
				<?php // About ?>
				<?php if($page->description): ?>
					<h3 class="title about-title"><?= __('About', 'ktt') ?></h3>
					<p class="about-text"><?= $page->description ?></p>
				<?php endif; ?>

				<?php // Education history ?>
				<?php if(!empty($page->education_history)): ?>
					<h3 class="title"><?= __('Education history', 'ktt') ?></h3>
					<ul class="user-education">
						<?php foreach($page->education_history as $education): ?>
							<li>
								<?php if($education->school_name): ?>
									<strong><?= $education->school_name ?></strong><br>
								<?php endif; ?>
								<?php if($education->title): ?>
									<?= $education->title ?><br>
								<?php endif; ?>
								<?php if($education->start_date): ?>
									<?= $education->start_date ?> -
									<?= !empty($education->end_date) ? $education->end_date : '...' ?>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php // Word history ?>
				<?php if(!empty($page->work_history)): ?>
					<h3 class="title"><?= __('Work experience', 'ktt') ?></h3>
					<ul class="work-education">
						<?php foreach($page->work_history as $job): ?>
							<li>
								<?php if(!empty($job->company_name)): ?>
									<strong><?= $job->company_name ?></strong><br>
								<?php endif; ?>
								<?php if(!empty($job->job_title)): ?>
									<?= $job->job_title ?><br>
								<?php endif; ?>
								<?= $job->start_date ?> -
								<?= !empty($job->end_date) ? $job->end_date : '...' ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php // Certificates ?>
				<?php if(!empty($page->certificates)): ?>
					<h3 class="title"><?= __('Certificates', 'ktt') ?></h3>
					<ul class="work-education">
						<?php foreach($page->certificates as $certificate): ?>
							<li>
								<?php if(!empty($certificate->title)): ?>
									<strong><?= $certificate->title ?></strong>
								<?php endif; ?>
								<?php if(!empty($certificate->authority_title)): ?>
									<br /><?= $certificate->authority_title ?>
								<?php endif; ?>
								<br />
								<?= $certificate->start_date ?> -
								<?= !empty($certificate->end_date) ? $certificate->end_date : '...' ?>
								<?php if($certificate->file_url): ?>
									<br />
									<a href="<?= $certificate->file_url ?>" target="_blank"><?= __('Download file', 'ktt') ?></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
            
           


           <?php endif; ?>

		</div>
	</div>
</div>
<!-- .site-content -->

<?php /* <?php get_sidebar(); ?> */ ?>
<?php get_footer(); ?>



