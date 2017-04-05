<?php
   /**
    * The template for displaying user profile
    *
    */
   
   get_header(); 
   global $wp_query;
   ?>
<?php 
   // Get content width and sidebar position
   $content_class = basel_get_content_class();
   
   ?>
<?php /*
   <div class="site-content <?php echo esc_attr( $content_class ); ?>" role="main">
*/ ?>
<div class="site-content col-sm-12" role="main">
   <?php 
      $user = get_user_by('login', $wp_query->query_vars['user']);
      if($user){
      
      	$dokan_profile = get_user_meta( $user->data->ID, 'dokan_profile_settings', true );
      	$ext_profile = get_user_meta( $user->data->ID, 'ktt_extended_profile', true );
      	$ext_shop = get_user_meta( $user->data->ID, 'ktt_extended_settings', true );
      	
      	/*
      	print_r($ext_profile);
      	print_r($ext_shop);
      	
      	print_r($dokan_profile);
      	*/
      	
      }else{
      	echo 'No user found!';
      }
      
      ?>
   <?php
      // @todo move hacks to backend functions
      
      if(!empty($ext_profile['education'])) {
      	$educationTranslations = array(
      	 '1' => translate('Basic education', 'ktt'),
      	 '2' => translate('Secondary education', 'ktt'),
      	 '3' => translate('Vocational education', 'ktt'),
      	 '4' => translate('Higher education', 'ktt')
      	);
      	$ext_profile['education_title'] = $educationTranslations[ $ext_profile['education'] ];
      }
      
      if(!empty($ext_shop['company_type'])) {
       $companyTypeTranslations = array(
        '1' => translate('FIE', 'ktt'),
        '2' => translate('OÜ', 'ktt'),
        '3' => translate('AS', 'ktt')
       );
       $ext_shop['company_type_title'] = $companyTypeTranslations[ $ext_shop['company_type'] ];
      }
      
      ?>
   <style>
      .ec-user-profile{
      }
      .ec-user-info{
      }
      .user-hero {
      background-size: cover;
      min-height: 300px;
      position: relative;
      }
      .user-hero .float-right{
      width: 80%;
      float: right;
      clear: both;
      }
      .user-hero .bottom-bar {
      position: absolute;
      bottom: 0;
      width: 100%;
      }
      .user-hero h1 {
      color: #ef7f27;
      font-weight: bold;
      }
      .user-hero p {
      font-weight: bold;
      font-size: 18px;
      }
      .shop-rating{
      float:left;
      }
      .shop-buttons{
      float:right;
      }
   </style>
   <div class="row">
      <div class="col-sm-3 ec-user-profile">
         <img class="profile-img" src="#" title="pildi array puudub">
         <h5 class="widget-title">
            Eesnimi ja Perekonnanimi
            <?php echo $user->first_name ?>
         </h5>
         <ul>
            <?php if(!empty($ext_profile['gender'])): ?>
            <li><?php echo $ext_profile['gender']; ?></li>
            <?php endif; ?>
            <li>
               <?php
                  $tmpAddr = array();
                  	if(!empty($ext_profile['country'])) $tmpAddr[] = $ext_profile['country'];
                  	if(!empty($ext_profile['state'])) $tmpAddr[] = $ext_profile['state'];
                  	if(!empty($ext_profile['city'])) $tmpAddr[] = $ext_profile['city'];
                  	if(!empty($ext_profile['address'])) $tmpAddr[] = $ext_profile['address'];
                  	echo implode(', ', $tmpAddr);
                  ?>
            </li>
            <?php if(!empty($ext_profile['mobile'])): ?>
            <li><?php echo $ext_profile['mobile']; ?></li>
            <?php endif; ?>
            <li><?php echo $user->user_email ?></li>
         </ul>
         <div class="expanded button-group">
            <a class="button" href="#">View CV</a>
            <a class="button" href="#">View Shop</a>
         </div>
      </div>
      <div class="col-sm-9 ec-user-info">
         <div class="user-hero" style="background-image: url(https://estoniancrafts.client.creativemeka.ee/wp-content/uploads/2017/03/tootelehe-slide.jpg)">
            <div class="float-right">
               <?php if(!empty($dokan_profile['store_name'])): ?>
               <h1 class="store_name"><?php echo $dokan_profile['store_name']; ?></h1>
               <?php endif; ?>
               <?php if(!empty($ext_shop['description'])): ?>
               <p class="ext_shop"><?php echo $ext_shop['description']; ?> <a href="#">View Shop</a></p>
               <?php endif; ?>
            </div>
            <div class="bottom-bar">
               <div class="shop-buttons">
                  <a class="button" href="#">following</a>
                  <a class="button" href="#">Products</a>
                  <a class="button" href="#">Reviews</a>
               </div>
            </div>
         </div>
         <h3 class="title">Shop <a href="#">see more</a></h3>
         <div class="items">
            <div class="col-sm-2"><a href="#"><img src="#"> item</a></div>
            <div class="col-sm-2"><a href="#"><img src="#"> item</a></div>
            <div class="col-sm-2"><a href="#"><img src="#"> item</a></div>
            <div class="col-sm-2"><a href="#"><img src="#"> item</a></div>
            <div class="col-sm-2"><a href="#"><img src="#"> item</a></div>
            <div class="col-sm-2"><a href="#"><img src="#"> item</a></div>
         </div>
         <h3 class="title about-title">About</h3>
         <?php if(!empty($ext_shop['description'])): ?>
         <p class="about-text"><?php echo $ext_shop['description']; ?></p>
         <?php endif; ?>
         <h3 class="title">Education history</h3>
         <ul>
            <li>
               <?php if(!empty($ext_profile['education_school'])): ?>
               <b><?php echo $ext_profile['education_school']; ?></b><br>
               <?php endif; ?>
               <?php if(!empty($ext_profile['education_title'])): ?>
               <?php echo $ext_profile['education_title']; ?><br>
               <?php endif; ?>
               <?php
                  $tmpEduYrs = array();
                  	if(!empty($ext_profile['education_start'])) $tmpEduYrs[] = $ext_profile['education_start'];
                  	if(!empty($ext_profile['education_end'])) $tmpEduYrs[] = $ext_profile['education_end'];
                  	echo implode('-', $tmpEduYrs);
                  ?>
            </li>
         </ul>
         <h3 class="title">Work education</h3>
         <ul>
            <?php foreach ($ext_profile['work_exp'] as $work_exp): ?>
            <li>
               <?php if(!empty($work_exp['name'])): ?>
               <b><?php echo $work_exp['name']; ?></b><br>
               <?php endif; ?>
               <?php if(!empty($work_exp['field'])): ?>
               <?php echo $work_exp['field']; ?><br>
               <?php endif; ?>
               <?php
                  $tmpWorkYrs = array();
                  	if(!empty($work_exp['start'])) $tmpWorkYrs[] = $work_exp['start'];
                  	if(!empty($work_exp['end'])) $tmpWorkYrs[] = $work_exp['end'];
                  	echo implode('-', $tmpWorkYrs);
                  ?>
            </li>
            <?php endforeach; ?>
         </ul>
      </div>
   </div>
   <div style="background:pink; width: 33.3333%; padding:15px; float: left;">
      <strong>ext_profile array VÄLJUNDID</strong>
      <ul>
         <li>mobile: <?php echo $ext_profile['mobile']; ?></li>
         <li>skype: <?php echo $ext_profile['skype']; ?></li>
         <li>gender: <?php echo $ext_profile['gender']; ?></li>
         <li>dob: <?php echo $ext_profile['dob']; ?></li>
         <li>workyears: <?php echo $ext_profile['workyears']; ?></li>
         <li>video: <?php echo $ext_profile['video']; ?></li>
         <li>description: <?php echo $ext_profile['description']; ?></li>
         <li>education: <?php echo $ext_profile['education']; ?></li>
         <li>education_school: <?php echo $ext_profile['education_school']; ?></li>
         <li>education_start: <?php echo $ext_profile['education_start']; ?></li>
         <li>education_end: <?php echo $ext_profile['education_end']; ?></li>
         <li>country: <?php echo $ext_profile['country']; ?></li>
         <li>state: <?php echo $ext_profile['state']; ?></li>
         <li>city: <?php echo $ext_profile['city']; ?></li>
         <li>address: <?php echo $ext_profile['address']; ?></li>
         <hr>
         <li>
            org
            <ul>
               <li>name: <?php echo $ext_profile['org'][0]['name']; ?></li>
               <li>link: <?php echo $ext_profile['org'][0]['link']; ?></li>
               <li>start: <?php echo $ext_profile['org'][0]['start']; ?></li>
               <li>end: <?php echo $ext_profile['org'][0]['end']; ?></li>
            </ul>
         </li>
         <hr>
         <li>
            work_exp
            <ul>
               <li>name: <?php echo $ext_profile['work_exp'][0]['name']; ?></li>
               <li>field: <?php echo $ext_profile['work_exp'][0]['field']; ?></li>
               <li>start: <?php echo $ext_profile['work_exp'][0]['start']; ?></li>
               <li>end: <?php echo $ext_profile['work_exp'][0]['end']; ?></li>
            </ul>
         </li>
         <hr>
         <li>
            certificates
            <ul>
               <li>name: <?php echo $ext_profile['certificates'][0]['name']; ?></li>
               <li>auth: <?php echo $ext_profile['certificates'][0]['auth']; ?></li>
               <li>start: <?php echo $ext_profile['certificates'][0]['start']; ?></li>
               <li>end: <?php echo $ext_profile['certificates'][0]['end']; ?></li>
               <li>link: <?php echo $ext_profile['certificates'][0]['link']; ?></li>
               <li>file: <?php echo $ext_profile['certificates'][0]['file']; ?></li>
            </ul>
         </li>
      </ul>
      <h1>$ext_profile</h1>
      <pre>
  <?php print_r($ext_profile) ?>
 </pre>
      <pre>
 	<?php print_r($user); ?>
 </pre>
   </div>
   <div style="background:yellow; width: 33.3333%; padding:15px; float: left;">
      <strong>ext_shop array VÄLJUNDID</strong>
      <ul>
         <li>company_name: <?php echo $ext_shop['company_name']; ?></li>
         <li>company_nr: <?php echo $ext_shop['company_nr']; ?></li>
         <li>company_type: <?php echo $ext_shop['company_type']; ?></li>
         <li>description: <?php echo $ext_shop['description']; ?></li>
         <hr>
         <li>
            media
            <ul>
               media:
               <?php $taitsteebpulli = $ext_shop['media'][0]; ?>
               <?php foreach ($ext_shop['media'] as $url): ?>
               <li><?php echo $url; ?></li>
               <?php endforeach; ?>
            </ul>
         </li>
         <hr>
         <li>
            address
            <ul>
               <li>country: <?php echo $ext_shop['address'][0]['country']; ?></li>
               <li>state: <?php echo $ext_shop['address'][0]['state']; ?></li>
               <li>city: <?php echo $ext_shop['address'][0]['city']; ?></li>
               <li>address: <?php echo $ext_shop['address'][0]['address']; ?></li>
               <li>email: <?php echo $ext_shop['address'][0]['email']; ?></li>
               <li>phone: <?php echo $ext_shop['address'][0]['phone']; ?></li>
            </ul>
         </li>
      </ul>
      <h1>$ext_shop</h1>
      <pre>
  <?php print_r($ext_shop) ?>
 </pre>
   </div>
   <div style="background:orange; width: 33.3333%; padding:15px; float: left;">
      <strong>dokan_profile array VÄLJUNDID</strong>
      <ul>
         <li>store_name: <?php echo $dokan_profile['store_name']; ?></li>
         <hr>
         <li>
            social
            <ul>
               <li>fb: <?php echo $dokan_profile['social']['fb']; ?></li>
               <li>gplus: <?php echo $dokan_profile['social']['gplus']; ?></li>
               <li>twitter: <?php echo $dokan_profile['social']['twitter']; ?></li>
               <li>linkedin: <?php echo $dokan_profile['social']['linkedin']; ?></li>
               <li>youtube: <?php echo $dokan_profile['social']['youtube']; ?></li>
               <li>instagram: <?php echo $dokan_profile['social']['instagram']; ?></li>
               <li>flickr: <?php echo $dokan_profile['social']['flickr']; ?></li>
            </ul>
         </li>
         <hr>
         <li>payment</li>
         <hr>
         <li>phone: <?php echo $dokan_profile['phone']; ?></li>
         <li>show_email: <?php echo $dokan_profile['show_email']; ?></li>
         <li>phone: <?php echo $dokan_profile['phone']; ?></li>
         <hr>
         <li>
            address:
            <ul>
               <li>street_1: <?php echo $dokan_profile['address']['street_1']; ?></li>
               <li>street_2: <?php echo $dokan_profile['address']['street_2']; ?></li>
               <li>city: <?php echo $dokan_profile['address']['city']; ?></li>
               <li>zip: <?php echo $dokan_profile['address']['zip']; ?></li>
               <li>country: <?php echo $dokan_profile['address']['country']; ?></li>
               <li>state: <?php echo $dokan_profile['address']['state']; ?></li>
            </ul>
         </li>
         <hr>
         <li>location: <?php echo $dokan_profile['location']; ?></li>
         <li>banner: <?php echo $dokan_profile['banner']; ?></li>
         <hr>
         <li>
            profile_completion
            <ul>
               <li>store_name: <?php echo $dokan_profile['profile_completion']['store_name']; ?></li>
               <li>Bank: <?php echo $dokan_profile['profile_completion']['Bank']; ?></li>
               <li>next_todo: <?php echo $dokan_profile['profile_completion']['next_todo']; ?></li>
               <li>progress: <?php echo $dokan_profile['profile_completion']['progress']; ?></li>
            </ul>
         </li>
         <hr>
         <li>store_ppp: <?php echo $dokan_profile['store_ppp']; ?></li>
         <li>find_address: <?php echo $dokan_profile['find_address']; ?></li>
         <li>show_min_order_discount: <?php echo $dokan_profile['show_min_order_discount']; ?></li>
         <li>setting_minimum_order_amount: <?php echo $dokan_profile['setting_minimum_order_amount']; ?></li>
         <li>setting_order_percentage: <?php echo $dokan_profile['setting_order_percentage']; ?></li>
         <li>show_more_ptab: <?php echo $dokan_profile['show_more_ptab']; ?></li>
         <li>gravatar: <?php echo $dokan_profile['gravatar']; ?></li>
         <li>enable_tnc: <?php echo $dokan_profile['enable_tnc']; ?></li>
         <li>store_tnc: <?php echo $dokan_profile['store_tnc']; ?></li>
      </ul>
      </ul>
      <h1>$dokan_profile</h1>
      <pre>
  <?php print_r($dokan_profile) ?>
 </pre>
   </div>
</div>
<!-- .site-content -->
<?php /* <?php get_sidebar(); ?> */ ?>
<?php get_footer(); ?>