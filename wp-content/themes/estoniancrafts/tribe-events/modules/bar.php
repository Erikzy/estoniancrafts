<?php
/**
 * Events Navigation Bar Module Template
 * Renders our events navigation bar used across our views
 *
 * $filters and $views variables are loaded in and coming from
 * the show funcion in: lib/Bar.php
 *
 * Override this template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/modules/bar.php
 *
 * @package  TribeEventsCalendar
 * @version  4.3.5
 */
?>

<?php

$filters = tribe_events_get_filters();
$views   = tribe_events_get_views();

$current_url = tribe_events_get_current_filter_url();

$tribe_events_calendar_options = get_option( 'tribe_events_calendar_options' , array('eventsSlug' => 'events'));
?>

<?php do_action( 'tribe_events_bar_before_template' ) ?>
<div id="tribe-events-bar">
    <?php

        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);

        $begin = new DateTime();

        $interval = new DateInterval("P1M"); // 1 month
        $period = new DatePeriod($begin, $interval, 11);

        $selection = date('Y-m');
        if (isset($_GET['tribe-bar-date']) && strlen($_GET['tribe-bar-date']) == 7) {
            $selection = trim($_GET['tribe-bar-date']);
        } elseif (isset($uri_segments[2]) && strlen($uri_segments[2]) == 7) {
            $selection = $uri_segments[2];
        }

        foreach($period as $dt){
            echo '<div class="date-dot'.($dt->format("Y-m") == $selection ? ' active' : '').'"><a href="/'.$tribe_events_calendar_options['eventsSlug'].'/foto/?action=tribe_photo&tribe_paged=1&tribe_event_display=photo&tribe-bar-date='.$dt->format("Y-m").'" data-month="'.$dt->format("Y-m").'">'.mysql2date( 'M', $dt->format("Y-m-d H:i:s")).'</a></div>';
        }
    ?>

	<form id="tribe-bar-form" class="tribe-clearfix" name="tribe-bar-form" method="post" action="<?php echo esc_attr( $current_url ); ?>">

		<!-- Mobile Filters Toggle -->

		<div id="tribe-bar-collapse-toggle" <?php if ( count( $views ) == 1 ) { ?> class="tribe-bar-collapse-toggle-full-width"<?php } ?>>
			<?php printf( esc_html__( 'Find %s', 'the-events-calendar' ), tribe_get_event_label_plural() ); ?><span class="tribe-bar-toggle-arrow"></span>
		</div>

		<!-- Views -->
		<?php if ( count( $views ) > 1 ) { ?>
			<div id="tribe-bar-views">
				<div class="tribe-bar-views-inner tribe-clearfix">
					<h3 class="tribe-events-visuallyhidden"><?php esc_html_e( 'Event Views Navigation', 'the-events-calendar' ) ?></h3>
					<label><?php esc_html_e( 'View As', 'the-events-calendar' ); ?></label>
					<select class="tribe-bar-views-select tribe-no-param" name="tribe-bar-view">
						<?php foreach ( $views as $view ) : ?>
							<option <?php echo tribe_is_view( $view['displaying'] ) ? 'selected' : 'tribe-inactive' ?> value="<?php echo esc_attr( $view['url'] ); ?>" data-view="<?php echo esc_attr( $view['displaying'] ); ?>">
								<?php echo $view['anchor']; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<!-- .tribe-bar-views-inner -->
			</div><!-- .tribe-bar-views -->
		<?php } // if ( count( $views ) > 1 ) ?>

		<?php if ( ! empty( $filters ) ) { ?>
            <?php
            $filters = array_reverse($filters, true);
            ?>
			<div class="tribe-bar-filters">
				<div class="tribe-bar-filters-inner tribe-clearfix">
					<?php foreach ( $filters as $filter ) : ?>
						<div class="<?php echo esc_attr( $filter['name'] ) ?>-filter">
							<label class="label-<?php echo esc_attr( $filter['name'] ) ?>" for="<?php echo esc_attr( $filter['name'] ) ?>"><?php echo $filter['caption'] ?></label>
							<?php echo $filter['html'] ?>
						</div>
					<?php endforeach; ?>
					<div class="tribe-bar-submit">
						<input class="tribe-events-button tribe-no-param" type="submit" name="submit-bar" value="<?php printf( esc_attr__( 'Find %s', 'the-events-calendar' ), tribe_get_event_label_plural() ); ?>" />
					</div>
					<!-- .tribe-bar-submit -->
				</div>
				<!-- .tribe-bar-filters-inner -->
			</div><!-- .tribe-bar-filters -->
		<?php } // if ( !empty( $filters ) ) ?>

	</form>
	<!-- #tribe-bar-form -->

</div><!-- #tribe-events-bar -->

<?php if (is_user_logged_in()) : ?>
    <?php $tribe_community_events_options = get_option( 'tribe_community_events_options' , array('communityRewriteSlug' => 'community')); ?>
    <a href="/<?=$tribe_events_calendar_options['eventsSlug']?>/<?=$tribe_community_events_options['communityRewriteSlug']?>/add" class="btn btn-color-primary btn-lg btn-block">Lisa enda üritus <i class="icon-plus icons"></i></a>
<?php endif; ?>

<?php
do_action( 'tribe_events_bar_after_template' );
