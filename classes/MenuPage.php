<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 10.12.18
 * Time: 16:53
 */

namespace Palasthotel\ProcessLog;


/**
 * @property \Palasthotel\ProcessLog\Database database
 * @property \Palasthotel\ProcessLog\Plugin plugin
 */
class MenuPage {

	const API_HANDLE = "process-log-api";

	const APP_HANDLE = "process-log-app";

	const STYLE_HANDLE = "process-log-app-style";


	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->database = $plugin->database;
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {
		add_management_page(
			__("Process Logs", Plugin::DOMAIN),
			__("Process Logs", Plugin::DOMAIN),
			"manage_options",
			"process_logs",
			array( $this, 'render' )
		);
	}

	public function render() {

		wp_enqueue_script(
			self::API_HANDLE,
			$this->plugin->url . "/js/api.js",
			array( "jquery" ),
			1,
			true
		);
		wp_localize_script(
			self::API_HANDLE,
			"ProcessLogAPI",
			array(
				'ajaxurl' => $this->plugin->ajax->ajaxurl,
			)
		);
		wp_enqueue_script(
			self::APP_HANDLE,
			$this->plugin->url . "/js/menu-page.js",
			array( self::API_HANDLE, "jquery" ),
			1,
			true
		);
		wp_localize_script(
			self::APP_HANDLE,
			"ProcessLogApp",
			array(
				'selectors' => array(
					"root" => "#process-log-table-body",
					"button_load_more" => "#process-log-load-more"
				),
				'i18n'    => array(
					"affected_user" => __( "Affected user", Plugin::DOMAIN ),
					"affected_post" => __( "Affected post", Plugin::DOMAIN ),
					"affected_term" => __( "Affected term", Plugin::DOMAIN ),
					"affected_comment" => __( "Affected comment", Plugin::DOMAIN ),
					"load_more_loading" => __("Loading more logs", Plugin::DOMAIN),
					"load_more_loading_again" => __("Give me a second... I'm on it", Plugin::DOMAIN),
					"load_more_done" => __("No more logs to load 🏖", Plugin::DOMAIN),
				),
			)
		);
		wp_enqueue_style(
			self::STYLE_HANDLE,
			$this->plugin->url . "/css/menu-page.css"
		);

		?>
		<div class="wrap process-log">
			<h2><?php _e("Process logs", Plugin::DOMAIN); ?></h2>
			<table class="widefat">
				<thead>
				<tr>
					<th scope="col" title="Process ID">
						<?php _e("PID", Plugin::DOMAIN); ?>
					</th>
					<th scope="col">
						<?php _e( 'Created', Plugin::DOMAIN ); ?>
					</th>
					<th scope="col">
						<?php _e("Active User", Plugin::DOMAIN); ?>
					</th>
					<th scope="col"><?php
						_e("Logs", Plugin::DOMAIN); ?>
					</th>
					<th scope="col">
						<?php _e( 'URL', Plugin::DOMAIN ); ?>
					</th>
				</tr>
				</thead>
				<tbody id="process-log-table-body"></tbody>
			</table>
			<button id="process-log-load-more" class="button button-primary"><?php _e("Load more", Plugin::DOMAIN); ?></button>
		</div>
		<?php
	}
}