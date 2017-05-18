<<<<<<< HEAD
<?php
/*
        
        <div class="container">
            <div class="row header margin-0-10">
                <div class="col-md-5 text-right<?=(isset($video) AND !empty($video->products)) ? '' : ' visible-lg'?>">
                    
                    <? if(isset($video) AND !empty($video->products)) : ?>
                        
                        <div id="carousel-product" class="box-shadow carousel slide visible-lg" data-ride="carousel">
                            <div class="carousel-inner">
                                <? $i = 0; foreach($video->products AS $product) : ?>
                                    <div class="item product-container text-center <?=$i ? '' : ' active'?>" style="background-image: url('<?=strlen($product->product_image_url) ? $product->product_image_url : '/img/at-logo.png'?>')">
                                        <div class="pad-small">
                                            <a href="http://arvutitark.ee/est/TOOTEKATALOOG/Arvutitark-TV/<?=url_title($product->name,'-')?>-<?=$product->product_id?>" class="btn btn-info col-md-12" target="_blank">
                                                Telli toode e-poest!
                                            </a>
                                            <h5><em><strong><?=$product->name?></strong></em></h5>
                                        </div>
                                    </div>
                                    
                                <? $i++; endforeach; ?>
                                
                            </div>
                            <? if(count($video->products) > 1) : ?>
                                <a class="left carousel-control" href="#carousel-product" data-slide="prev">
                                    <div class="control-arrow">
                                        <div class="corner left"></div>
                                        <img src="/img/arrow-left.png" alt="<">
                                    </div>
                                </a>
                                <a class="right carousel-control" href="#carousel-product" data-slide="next">
                                    <div class="control-arrow">
                                        <div class="corner right"></div>
                                        <img src="/img/arrow-right.png" alt=">">
                                    </div>
                                </a>
                            <? endif; ?>
                        </div>
                    
                    
                    <? elseif(0) : ?>
                        <div id="header-video">
                            <a href="<?=$headerUrl?>" style="display:block;width:480px;height:290px;" id="player"></a>
                            <script language="JavaScript">flowplayer("player", "/play.swf", {
                                clip: {
                                    autoPlay: false,
                                    autoBuffering: true
                                }
                            });</script>
                        </div>
                    <? else : ?>
                        <div id="header-video">
                            <a href="/video/<?=$headerVideo->id?>-<?=$headerVideo->slug?>">
                                <div class="video-img">
                                    <img src="/img/play-icon.png" alt="" class="play-icon">
                                    <img src="<?=$headerVideo->youtube_thumbnail_url?>" alt="Vaata" class="img-responsive">
                                </div>
                            </a>
                        </div>
                    <? endif; ?>
                </div>
            </div>
            
            <div class="row box-shadow" id="header-nav">
                <div class="col-md-3 first-nav">
                    <div class="corner left"></div>
                    <div class="pad-small no-right no-left"><h2 class="no-top no-bot"><img alt="Kategooriad" src="/img/categories-icon.png">&nbsp;&nbsp;&nbsp; Kategooriad</h2></div>
                </div>
                <div class="col-md-9">
                    <div class="corner right"></div>
                    <div class="pad-small">
                        <a href="/"><h2 class="no-top no-bot"><img alt="Play" src="/img/videos-icon.png">&nbsp;&nbsp;&nbsp; <?=(isset($categoy_name) AND $this->uri->segment(2) != '20-saadete-arhiiv') ? $categoy_name : 'Viimased videod'?></h2></a>
                        <a href="/kategooria/20-saadete-arhiiv"><h2 class="no-top no-bot"><img alt="Play" src="/img/videos-icon.png">&nbsp;&nbsp;&nbsp; Saadete arhiiv</h2></a>
                    </div>
                </div>
            </div>
            
            <div class="row box-shadow<?= isset($video) ? ' medium-bg' : ''?>" id="main-container">
                <div class="col-md-3" id="left-nav">
                    <nav>
                        <ul class="list-unstyled no-bot">
                            <? foreach($videoGroups AS $group) : ?>
                                <? if($group->is_hidden) continue; ?>
                                <li><a<?= $this->uri->segment(2) == $group->id.'-'.$group->slug ? ' class="active"' : '' ?> href="/kategooria/<?=$group->id?>-<?=$group->slug?>"><?=$group->title?> <span class="badge pull-right"><?=$group->total_videos?></span><div class="separator"></div></a></li>
                            <? endforeach; ?>
                            
                        </ul>
                    </nav>
                </div>
*/?><!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Arvutitark TV - <?=$this->config->item('video_types')[$active_type]?></title>
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/stylesheets/app-all.css" />
    <link rel="stylesheet" href="/stylesheets/app<?=$active_type?>.css" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
    <script src="/bower_components/modernizr/modernizr.js"></script>
  </head>
  <body class="col3">
  <div class="preheader">
    <div class="row">
      <div class="large-12 columns">
       
      </div>
    </div>
  </div>
  <header>
    <div class="row logorow">
      <div class="large-12 medium-12 columns"><a href="/"><img src="/img/at-logo-white-tv.png" alt=""></a>
      </div>
    </div>
    <div class="row collapse">
      <div class="large-12 columns">
         <nav class="top-bar" data-topbar role="navigation">
          <ul class="title-area">
            <li class="name">
              <h1><a href="#"></a></h1>
            </li>
             <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
            <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
          </ul>

          <section class="top-bar-section">
            <!-- Right Nav Section -->
            <ul class="right">
                <? foreach ($this->config->item('video_types') as $key => $value) : ?>
                    <li class="color<?=$key?><?=$active_type == $key ? ' active' : ''?>"><a href="/<?=$this->config->item('video_type_urls')[$key]?>"><?=$value?></a></li>
                <? endforeach; ?>
            </ul>
          </section>
        </nav>
      </div>
    </div>
  </header>

  <section class="section-type-<?=$active_type?>">
  
  <? if (!$this->uri->segment(2)) : ?>
      <div class="row">
        <div class="medium-6 large-8 columns">
            <div class="card">
                <div class="header padding">
                    <? if ($active_type == 2) : ?>
                        <h2>Viimane episood</h2>
                    <? else : ?>
                        <h2>Videod</h2>
                    <? endif; ?>
                </div>
                <div class="body">
                    <? if ($active_type == 2) : ?>
                        <div class="flex-video youtube widescreen">
                            <a href="/<?=$active_type_url?>/video/<?=$headerVideo->id?>-<?=$headerVideo->slug?>">
                                <div class="video-img">
                                    <!-- <img src="/img/play-icon.png" alt="" class="play-icon"> -->
                                    <img src="<?=$headerVideo->youtube_thumbnail_url?>" alt="Vaata" class="img-responsive">
                                </div>
                            </a>

                            <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/7CBs4tTpZWA?rel=0" frameborder="0" allowfullscreen></iframe> -->
                        </div>
                    <? else : ?>
                        
                    <? endif; ?>
                </div>
                <? if ($active_type == 2) : ?>
                    <div class="footer">
                        <p>TallinnTVs 16.12.14 eetris olnud Arvutitarga teise hooaja 15. saade. Täna saates: *Kosmos IMAX* *midagi uut Kingstonilt * *Konsoolide ajalugu 2* *Mängu loos Gamestar.ee vahendusel*</p>
                    </div>
                <? endif; ?>
            </div>
        </div>
        <div class="medium-6 large-4 columns description">
            <?=$this->config->item('video_type_text')[$active_type]?>
            <br>
            <a href="/<?=$active_type_url?>/kategooria" class="button expand">Kõik videod&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a>
        </div>
    </div>
=======
<?php
/*
        
        <div class="container">
            <div class="row header margin-0-10">
                <div class="col-md-5 text-right<?=(isset($video) AND !empty($video->products)) ? '' : ' visible-lg'?>">
                    
                    <? if(isset($video) AND !empty($video->products)) : ?>
                        
                        <div id="carousel-product" class="box-shadow carousel slide visible-lg" data-ride="carousel">
                            <div class="carousel-inner">
                                <? $i = 0; foreach($video->products AS $product) : ?>
                                    <div class="item product-container text-center <?=$i ? '' : ' active'?>" style="background-image: url('<?=strlen($product->product_image_url) ? $product->product_image_url : '/img/at-logo.png'?>')">
                                        <div class="pad-small">
                                            <a href="http://arvutitark.ee/est/TOOTEKATALOOG/Arvutitark-TV/<?=url_title($product->name,'-')?>-<?=$product->product_id?>" class="btn btn-info col-md-12" target="_blank">
                                                Telli toode e-poest!
                                            </a>
                                            <h5><em><strong><?=$product->name?></strong></em></h5>
                                        </div>
                                    </div>
                                    
                                <? $i++; endforeach; ?>
                                
                            </div>
                            <? if(count($video->products) > 1) : ?>
                                <a class="left carousel-control" href="#carousel-product" data-slide="prev">
                                    <div class="control-arrow">
                                        <div class="corner left"></div>
                                        <img src="/img/arrow-left.png" alt="<">
                                    </div>
                                </a>
                                <a class="right carousel-control" href="#carousel-product" data-slide="next">
                                    <div class="control-arrow">
                                        <div class="corner right"></div>
                                        <img src="/img/arrow-right.png" alt=">">
                                    </div>
                                </a>
                            <? endif; ?>
                        </div>
                    
                    
                    <? elseif(0) : ?>
                        <div id="header-video">
                            <a href="<?=$headerUrl?>" style="display:block;width:480px;height:290px;" id="player"></a>
                            <script language="JavaScript">flowplayer("player", "/play.swf", {
                                clip: {
                                    autoPlay: false,
                                    autoBuffering: true
                                }
                            });</script>
                        </div>
                    <? else : ?>
                        <div id="header-video">
                            <a href="/video/<?=$headerVideo->id?>-<?=$headerVideo->slug?>">
                                <div class="video-img">
                                    <img src="/img/play-icon.png" alt="" class="play-icon">
                                    <img src="<?=$headerVideo->youtube_thumbnail_url?>" alt="Vaata" class="img-responsive">
                                </div>
                            </a>
                        </div>
                    <? endif; ?>
                </div>
            </div>
            
            <div class="row box-shadow" id="header-nav">
                <div class="col-md-3 first-nav">
                    <div class="corner left"></div>
                    <div class="pad-small no-right no-left"><h2 class="no-top no-bot"><img alt="Kategooriad" src="/img/categories-icon.png">&nbsp;&nbsp;&nbsp; Kategooriad</h2></div>
                </div>
                <div class="col-md-9">
                    <div class="corner right"></div>
                    <div class="pad-small">
                        <a href="/"><h2 class="no-top no-bot"><img alt="Play" src="/img/videos-icon.png">&nbsp;&nbsp;&nbsp; <?=(isset($categoy_name) AND $this->uri->segment(2) != '20-saadete-arhiiv') ? $categoy_name : 'Viimased videod'?></h2></a>
                        <a href="/kategooria/20-saadete-arhiiv"><h2 class="no-top no-bot"><img alt="Play" src="/img/videos-icon.png">&nbsp;&nbsp;&nbsp; Saadete arhiiv</h2></a>
                    </div>
                </div>
            </div>
            
            <div class="row box-shadow<?= isset($video) ? ' medium-bg' : ''?>" id="main-container">
                <div class="col-md-3" id="left-nav">
                    <nav>
                        <ul class="list-unstyled no-bot">
                            <? foreach($videoGroups AS $group) : ?>
                                <? if($group->is_hidden) continue; ?>
                                <li><a<?= $this->uri->segment(2) == $group->id.'-'.$group->slug ? ' class="active"' : '' ?> href="/kategooria/<?=$group->id?>-<?=$group->slug?>"><?=$group->title?> <span class="badge pull-right"><?=$group->total_videos?></span><div class="separator"></div></a></li>
                            <? endforeach; ?>
                            
                        </ul>
                    </nav>
                </div>
*/?><!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Arvutitark TV - <?=$this->config->item('video_types')[$active_type]?></title>
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/stylesheets/app-all.css" />
    <link rel="stylesheet" href="/stylesheets/app<?=$active_type?>.css" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
    <script src="/bower_components/modernizr/modernizr.js"></script>
  </head>
  <body class="col3">
  <div class="preheader">
    <div class="row">
      <div class="large-12 columns">
       
      </div>
    </div>
  </div>
  <header>
    <div class="row logorow">
      <div class="large-12 medium-12 columns"><a href="/"><img src="/img/at-logo-white-tv.png" alt=""></a>
      </div>
    </div>
    <div class="row collapse">
      <div class="large-12 columns">
         <nav class="top-bar" data-topbar role="navigation">
          <ul class="title-area">
            <li class="name">
              <h1><a href="#"></a></h1>
            </li>
             <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
            <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
          </ul>

          <section class="top-bar-section">
            <!-- Right Nav Section -->
            <ul class="right">
                <? foreach ($this->config->item('video_types') as $key => $value) : ?>
                    <li class="color<?=$key?><?=$active_type == $key ? ' active' : ''?>"><a href="/<?=$this->config->item('video_type_urls')[$key]?>"><?=$value?></a></li>
                <? endforeach; ?>
            </ul>
          </section>
        </nav>
      </div>
    </div>
  </header>

  <section class="section-type-<?=$active_type?>">
  
  <? if (!$this->uri->segment(2)) : ?>
      <div class="row">
        <div class="medium-6 large-8 columns">
            <div class="card">
                <div class="header padding">
                    <? if ($active_type == 2) : ?>
                        <h2>Viimane episood</h2>
                    <? else : ?>
                        <h2>Videod</h2>
                    <? endif; ?>
                </div>
                <div class="body">
                    <? if ($active_type == 2) : ?>
                        <div class="flex-video youtube widescreen">
                            <a href="/<?=$active_type_url?>/video/<?=$headerVideo->id?>-<?=$headerVideo->slug?>">
                                <div class="video-img">
                                    <!-- <img src="/img/play-icon.png" alt="" class="play-icon"> -->
                                    <img src="<?=$headerVideo->youtube_thumbnail_url?>" alt="Vaata" class="img-responsive">
                                </div>
                            </a>

                            <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/7CBs4tTpZWA?rel=0" frameborder="0" allowfullscreen></iframe> -->
                        </div>
                    <? else : ?>
                        
                    <? endif; ?>
                </div>
                <? if ($active_type == 2) : ?>
                    <div class="footer">
                        <p>TallinnTVs 16.12.14 eetris olnud Arvutitarga teise hooaja 15. saade. Täna saates: *Kosmos IMAX* *midagi uut Kingstonilt * *Konsoolide ajalugu 2* *Mängu loos Gamestar.ee vahendusel*</p>
                    </div>
                <? endif; ?>
            </div>
        </div>
        <div class="medium-6 large-4 columns description">
            <?=$this->config->item('video_type_text')[$active_type]?>
            <br>
            <a href="/<?=$active_type_url?>/kategooria" class="button expand">Kõik videod&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a>
        </div>
    </div>
>>>>>>> d76b2e7ccde0dbc4308b5918fbd215fcea94b08d
<? endif; ?>