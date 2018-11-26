<?php

class DP_Snow_Widget extends WP_Widget {

	/**
	 * URL Base for retrieving the remote API feeds
	 *
	 * @access private
	 * @var string
	 */
	private $remote_feed_base = 'https://delivery.digitalfirstmedia.com/xql/';

	/**
	 * Sets how long the transients should exist for.
	 *
	 * @access public
	 * @var int
	 */
	private $transient_expiration = HOUR_IN_SECONDS;

	/**
	 * DP_Snow_Widget constructor.
	 *
	 * Extends FM_Widget constructor which extends the WP_Widget constructor
	 * @see WP_Widget
	 */
	function __construct() {
		parent::__construct( 'dp_snow_widget', __( 'Snow Widget', 'site-denverpost' ), array(
			'description',
			__( 'Here is the denver post snow widget', 'site-denverpost' )
		) );
	}

	/**
	 * Handles the front end output of the actual widget
	 *
	 * @param array $args
	 * @param array $instance
	 * @access public
	 * @return void
	 */
	public function widget( $args, $instance ) {
        echo $args['before_widget'];
        echo $args['before_title'] . 'Colorado Snow Report' . $args['after_title'];
		?>
		<script type="text/javascript"
				src="<?php echo get_stylesheet_directory_uri() . '/snow-widget/snowwidget.min.js'; ?>"></script>
		<div class="snowwidget-wrapper">
			<div class="snowwidget-container">
				<div class="snowwidget-headline">
					<div class="snowwidget-headline-label snowwidget-section-label">
						<i class="snowwidget-tagline-icon icon-weather-snow"></i>
						<a href="https://www.coloradoski.com/snow-report?utm_source=Colorado%20Ski%20Country%20USA's%20name%20in%20the%20Snow%20Report&utm_medium=Snow%20Report&utm_campaign=Snow%20Report">Colorado Ski Country USA</a>
					</div>
				</div>

				<div class="snowwidget-pane-container">
					<div class="snowwidget-pane snowwidget-pane-newsnow">
						<?php $new_snow_list = $this->get_remote_forecast( 'snocountry_report' ); ?>
						<?php if ( false !== $new_snow_list && ! empty( $new_snow_list['snocountry']['items'] ) ): ?>
							<ul class="snowwidget-pane-list snowwidget-pane-newsnow">
								<?php uasort( $new_snow_list['snocountry']['items'], function( $a, $b ) {
									return strcasecmp( $a['resortName'], $b['resortName'] );
								} ); ?>
								<?php foreach ( $new_snow_list['snocountry']['items'] as $item ): ?>
									<?php
									$default_text = 'N/A';
									$resort_name  = ( ! empty( $item['resortName'] ) ) ? $item['resortName'] : $default_text;
									$resort_link  = ( ! empty( $item['webSiteLink'] ) ) ? $item['webSiteLink'] : $default_text;
									$new_snow     = ( ! empty( $item['newSnowMin'] ) ) ? $item['newSnowMin'] : $default_text;
									$snow_base    = ( ! empty( $item['avgBaseDepthMin'] ) ) ? $item['avgBaseDepthMin'] : $default_text;
									$lifts_open   = ( ! empty( $item['openDownHillLifts'] ) ) ? $item['openDownHillLifts'] : '0';
									?>
									<li class="snowwidget-pane-list-item">
										<h2 class="snowwidget-pane-list-label">
											<a href="<?php echo esc_url( $resort_link ); ?>"><?php echo esc_html( $resort_name ); ?></a>
										</h2>
										<div class="snowwidget-pane-data snowwidget-pane-newsnow-data">
											<i class="snowwidget-pane-data-icon icon-weather-snow"></i>
											<p class="snowwidget-pane-data-text">
												<?php echo sprintf( 'New (24 hr): %s" | Base: %s"', esc_html( $new_snow ), esc_html( $snow_base ) ); ?>
												<br>
												<?php echo sprintf( 'Open: %s', esc_html( $lifts_open ) ); ?>
											</p>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
					<div class="snowwidget-pane snowwidget-pane-forecast">
						<div id="openSnowImg" class="openSnowImg">
							<a href="https://opensnow.com" target="_blank">
								<img alt="" src="<?php echo get_stylesheet_directory_uri(). '/snow-widget/opensnow.png'; ?>"/>
							</a>
						</div>
						<?php $forecasts = $this->get_remote_forecast( 'snow_forecast' ); ?>
						<?php if ( false !== $forecasts && ! empty( $forecasts['locations']['location'] ) ): ?>
							<ul class="snowwidget-pane-list snowwidget-pane-forecast">
								<?php foreach ( $forecasts['locations']['location'] as $forecast ): ?>
									<?php
									$forecast_name    = ( ! empty( $forecast['meta']['name'] ) ) ? $forecast['meta']['name'] : '';
									$future_forecasts = ( ! empty( $forecast['forecast']['period'] ) ) ? $forecast['forecast']['period'] : array();
									?>
									<li class="snowwidget-pane-list-item">
										<h2 class="snowwidget-pane-list-label"><?php echo esc_html( $forecast_name ); ?></h2>
										<?php if ( ! empty( $future_forecasts ) ): ?>
											<?php foreach ( $future_forecasts as $future_forecast ): ?>
												<?php
												$day_of_week   = ( ! empty( $future_forecast['dow'] ) ) ? substr( $future_forecast['dow'], 0, 3 ) : '';
												$forecast_date = ( ! empty( $future_forecast['date'] ) ) ? substr( $future_forecast['date'], 5, 10 ) : '';
												$day_snow      = ( ! empty( $future_forecast['day']['snow'] ) ) ? $future_forecast['day']['snow'] : '0';
												$night_snow    = ( ! empty( $future_forecast['night']['snow'] ) ) ? $future_forecast['night']['snow'] : '0';
												?>
												<div class="snowwidget-pane-data snowwidget-pane-forecast-data">
													<div
														class="snowwidget-pane-data-item snowwidget-pane-forecast-data-item">
														<div><?php echo esc_html( $day_of_week ); ?></div>
														<div><?php echo esc_html( $forecast_date ); ?></div>
													</div>
													<div
														class="snowwidget-pane-data-item snowwidget-pane-forecast-data-item">
														<div>Day</div>
														<div><?php echo esc_html( $day_snow ); ?>"</div>
													</div>
													<div
														class="snowwidget-pane-data-item snowwidget-pane-forecast-data-item">
														<div>Night</div>
														<div><?php echo esc_html( $night_snow ); ?>"</div>
													</div>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div> <!-- END .snowwidget-panes -->

				<div class="snowwidget-tabs">
					<div class="snowwidget-tabs-btn snowwidget-tabs-btn-newsnow"
						 data-pane="snowwidget-pane-newsnow">
						<i class="snowwidget-tab-icon icon-weather-snow"></i>
						<span class="snowwidget-tab-text">New Snow</span>
					</div>
					<div class="snowwidget-tabs-btn snowwidget-tabs-btn-forecast is-active" data-pane="snowwidget-pane-forecast">
						<i class="snowwidget-tab-icon icon-thermometer"></i>
						<span class="snowwidget-tab-text">Forecast</span>
					</div>
				</div> <!-- END .snowwidget-tabs -->

				<div class="snowwidget-footer">
					<div id="div-gpt-ad-dp_snow_widget_bottom">
						<script>
							googletag.cmd.push(function()
								{ googletag.defineSlot('/8013/denverpost.com', [300,80],'div-gpt-ad-dp_snow_widget_bottom').setTargeting('kv',['dp_snow_widget']).addService(googletag.pubads()); googletag.enableServices(); googletag.display('div-gpt-ad-dp_snow_widget_bottom'); }
							);
						</script>
					</div>
				</div> <!-- END .snowwidget-footer -->
			</div> <!-- END .snowwidget-container -->
		</div> <!-- END .snowwidget-wrapper -->
		<?php
		echo $args['after_widget'];
	}

	/**
	 * get_remote_forecast
	 *
	 * Retrieves the remote data, either from the transient, or from the API and then stores it in the transient.
	 *
	 * @access private
	 * @param string $source
	 * @return array|bool $forecast
	 */
	private function get_remote_forecast( $source ) {

		// First check to see if we can get it from the transient
		if ( false === ( $forecast = get_transient( 'dfm_' . $source ) ) ) {

			// Build the URL for the remote request.
			$build_url = add_query_arg( array(
				'format' => 'noxslt',
				'source' => $source,
				'json'   => 'true',
			), $this->remote_feed_base );
			
			// Retrieve the data from the API.
			$remote_data = wp_remote_get(
				$build_url, // This stops working when you run it through esc_url() ¯\_(ツ)_/¯
				'',
				3,
				3,
				20,
				array( 'headers' => array( 'content-type' => 'application/json' ) )
			);

			// Store it to the transient if there's something there, otherwise return false.
			if ( ! empty( $remote_data['body'] ) ) {
				$forecast = json_decode( $remote_data['body'], true );
				set_transient( 'dfm_' . $source, $forecast, $this->transient_expiration + rand( 0, 180 ) );
			} else {
				$forecast = false;
			}

		}

		return $forecast;

	}

}

// Register the widget
add_action( 'widgets_init', function () {
	register_widget( 'DP_Snow_Widget' );
} );