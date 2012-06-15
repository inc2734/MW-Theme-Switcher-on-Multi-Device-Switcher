<?php
/**
 * Plugin Name: MW Theme Switcher on Multi Device Switcher
 * Plugin URI: http://2inc.org
 * Description: <a href="http://wordpress.org/extend/plugins/multi-device-switcher/">Multi Device Switcher</a> を使用中の場合、フッターにPC<=>モバイルの切り替えボタンを表示するプラグインです。
 * Version: 0.1
 * Author: kitajima takashi
 * Author URI: http://2inc.org
 */
class mw_theme_switcher_on_multi_device_switcher {

	const NAME = 'mw_theme_switcher_on_multi_device_switcher';
	const FLG = 'viewmode';

	/**
	 * __construct
	 */
	public function __construct() {
		if ( class_exists( 'Multi_Device_Switcher' ) ) {
			add_action( 'plugins_loaded', array( $this, '_mobile_switcher' ) );
			add_action( 'init', array( $this, '_set_theme_cookie' ) );
			add_action( 'wp_footer', array( $this, '_render_switcher' ), 11 );
		}
	}

	/**
	 * _set_theme_cookie
	 * cookieをセット
	 */
	public function _set_theme_cookie() {
		if ( isset( $_GET[self::FLG] ) ) {
			$viewmode = $_GET[self::FLG];
			if ( $viewmode === 'pc' || $viewmode === 'mb' ) {
				setcookie( self::FLG, $viewmode, null, '/' );
				$requestUri = $_SERVER['REQUEST_URI'];
				$requestUri = preg_replace( '/^(.+?)(\?.*)$/', '$1', $requestUri );
				$args = $_GET;
				unset( $args[self::FLG] );
				if ( !empty( $args ) ) {
					$args = '?'.http_build_query( $args );
					$requestUri = $requestUri.$args;
				}
				wp_redirect( esc_attr( $requestUri ) );
				exit;
			}
		}
	}

	/**
	 * _mobile_switcher
	 * Multi_Device_Switcher関数をremove
	 */
	public function _mobile_switcher() {
		if ( !empty( $_COOKIE[self::FLG] ) ) {
			$viewmode = $_COOKIE[self::FLG];
			if ( $viewmode === 'pc' ) {
				global $multi_device_switcher;
				remove_filter( 'stylesheet', array( $multi_device_switcher, 'get_stylesheet' ) );
				remove_filter( 'template', array( $multi_device_switcher, 'get_template' ) );
			}
		}
	}

	/**
	 * _render_switcher
	 * ボタン表示
	 */
	public function _render_switcher( $content ) {
		global $multi_device_switcher;
		$theme = $multi_device_switcher->get_device_theme();
		if ( !empty( $multi_device_switcher->device ) && !empty( $theme ) && $theme != 'None' ) :
		?>
		<div class="renderSwitcher">
			<ul>
				<li class="pc"><a href="?<?php echo self::FLG; ?>=pc">PC表示</a></li><!--
				--><li class="mobile"><a href="?<?php echo self::FLG; ?>=mb">モバイル表示</a></li>
			</ul>
		<!-- end .renderSwitcher --></div>
		<?php
		endif;
	}
}
$mw_theme_switcher_on_multi_device_switcher = new mw_theme_switcher_on_multi_device_switcher();
