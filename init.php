<?php

class SharpPlugin {

    private $wp_login_php;



    public function __construct() {
        $this->define_constants();
        $this->init();
    }

	/**
	 * init function 
	 * @return void
	 */
    protected function init() 
	{
		global $wp_version;

		if ( version_compare( $wp_version, '4.0-RC1-src', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notices_incompatible' ) );
			add_action( 'network_admin_notices', array( $this, 'admin_notices_incompatible' ) );

			return;
		}


		if ( is_multisite() && ! function_exists( 'is_plugin_active_for_network' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active_for_network( 'rename-wp-login/rename-wp-login.php' ) ) {
			deactivate_plugins( SHARPLOGIN_BASENAME );
			add_action( 'network_admin_notices', array( $this, 'admin_notices_plugin_conflict' ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		if ( is_plugin_active( 'rename-wp-login/rename-wp-login.php' ) ) {
			deactivate_plugins( SHARPLOGIN_BASENAME );
			add_action( 'admin_notices', array( $this, 'admin_notices_plugin_conflict' ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		if ( is_multisite() && is_plugin_active_for_network( SHARPLOGIN_BASENAME ) ) {
			add_action( 'wpmu_options', array( $this, 'wpmu_options' ) );
			add_action( 'update_wpmu_options', array( $this, 'update_wpmu_options' ) );

			add_filter( 'network_admin_plugin_action_links_' . SHARPLOGIN_BASENAME, array(
				$this,
				'plugin_action_links'
			) );
		}
		add_action('admin_menu', array($this, 'sharplogin_menu_page_func'));
        add_action( 'admin_init', array($this, 'sharplogin_settings_func') );
		add_action( 'admin_init', array($this, 'sl_login_attempts_settings_func') );
        add_action( 'login_enqueue_scripts', array($this, 'sharplogin_logo') );
        add_action('login_head', array($this, 'sharplogin_background_image') );
        add_action('login_footer',array($this, 'sharplogin_page_footer'));

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 9999 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'network_admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
		add_action( 'setup_theme', array( $this, 'setup_theme' ), 1 );

		add_filter( 'plugin_action_links_' . SHARPLOGIN_BASENAME, array( $this, 'plugin_action_links' ) );
		add_filter( 'site_url', array( $this, 'site_url' ), 10, 4 );
		add_filter( 'network_site_url', array( $this, 'network_site_url' ), 10, 3 );
		add_filter( 'wp_redirect', array( $this, 'wp_redirect' ), 10, 2 );
		add_filter( 'site_option_welcome_email', array( $this, 'welcome_email' ) );

		remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// add_action( 'admin_menu', array( $this, 'sharplogin_hide_login_menu_page' ) );
		add_action( 'admin_init', array( $this, 'sharplogin_template_redirect' ) );

		add_action( 'template_redirect', array( $this, 'sharplogin_redirect_page_email_notif_woocommerce' ) );
		add_filter( 'login_url', array( $this, 'login_url' ), 10, 3 );


		add_action( 'wp_login_failed', array($this, 'sharplogin_failed'), 10, 1 );
		add_filter( 'authenticate', array($this, 'check_attempted_login') , 30, 3 ); 
		add_action( 'login_errors', array($this, 'sharplogin_attempts_count') );

		add_action( 'admin_enqueue_scripts', array($this, 'load_wp_media_files')  );
		
		
	}

	/**
	 * Define constants
	 */
    public function define_constants() 
    {
        define('SHARPLOGIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
    }

	/**
	 * Initialize the plugin
	 */
    public function sharplogin_menu_page_func() 
    {
        add_menu_page(
            'Sharp Login', 
            'Sharp Login', 
            'manage_options', 
            'sharplogin', 
            array($this, 'sharplogin_menu_page_content_func'), 
            'dashicons-admin-network',
            3
        );
    }

	/**
	 * add menu page content
	 */
    public function sharplogin_menu_page_content_func() 
    {
        $settings = get_option( 'sharplogin_settings' );
		$login_logo = isset($settings['login_logo'])?esc_url( $settings['login_logo'] ):"";
		// get $active_tab variable from url
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general' ;
		if($active_tab == 'advanced'){
			$login_attempt = get_option( 'sl_login_attempts_settings');
			$sl_login_enabled = isset($login_attempt['sl_login_attempts_enabled'])?$login_attempt['sl_login_attempts_enabled']:'';
			$sl_login_attempts_max_attempts = isset($login_attempt['sl_login_attempts_max_attempts'])?$login_attempt['sl_login_attempts_max_attempts']:'';
			$sl_login_attempts_lockout_time = isset($login_attempt['sl_login_attempts_lockout_time'])?$login_attempt['sl_login_attempts_lockout_time']:'';
			$sl_login_attempts_lockout_time_unit = isset($login_attempt['sl_login_attempts_lockout_time_unit'])?$login_attempt['sl_login_attempts_lockout_time_unit']:'';
			$sl_login_attempts_lockout_message = isset($login_attempt['sl_login_attempts_lockout_message'])?$login_attempt['sl_login_attempts_lockout_message']:'';
			
		}

        include(SHARPLOGIN_PLUGIN_DIR . 'templates/sharplogin_menu_page.php');
    }

	/**
	 * settings function
	 * @return array and view
	 */
    public function sharplogin_settings_func()
    {   
        register_setting('sharplogin_settingss', 'sharplogin_settings');
		register_setting('sharplogin_settingss', 'sharplogin_page');
    }

	public function sl_login_attempts_settings_func()
	{
		register_setting('sl_login_attempts_settings', 'sl_login_attempts_settings');
	}

	/**
	 * add logo to login page with this function
	 */
    function sharplogin_logo() 
    {
        $settings = get_option( 'sharplogin_settings' );
        if (isset($settings['login_logo'])) {
        $img_url = $settings['login_logo'];
        if(isset($img_url) && $img_url != '') {
			$img_url = esc_attr( $settings['login_logo']);
        ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url(<?php echo $img_url;?>);
            }
        </style>
        <?php
         } 
        }
    
    }


	/**
	 * add background image to login page with this function
	 */
    function sharplogin_background_image() 
    {
        $settings = get_option( 'sharplogin_settings' );
        if(isset($settings['login_bg_img'])) {
            echo '<style type="text/css">
                body.login{
                background-image: url( "'.$settings['login_bg_img'].'" )!important;
                background-size: cover;
            }
            </style>';
        }
    }

	/**
	 * add footer to login screen
	 */
    function sharplogin_page_footer() 
    { ?>
        <p style="text-align: right;">
        <a style="color: black;" target="__blank" href="https://www.sharpedge.io">Powered by SharpLogin</a>
        </p>
        <?php 
    }
        
	/**
	 * admin notices for incompatible plugins
	 */
    public function admin_notices_incompatible() {
		echo '<div class="error notice is-dismissible"><p>' . __( 'Please upgrade to the latest version of WordPress to activate' ) . ' <strong>' . __( 'SharpLogin' ) . '</strong>.</p></div>';
	}

	/**
	 * admin notices for conflict plugins
	 */
	public function admin_notices_plugin_conflict() {
		echo '<div class="error notice is-dismissible"><p>' . __( 'SharLogin could not be activated because you already have Rename wp-login.php active. Please uninstall rename wp-login.php to use SharpLogin' ) . '</p></div>';
	}

	/**
	 * Plugin activation
	 */
	public static function activate() {
		//add_option( 'sharplogin_redirect', '1' );

		do_action( 'sharplogin_hide_login_activate' );
	}

	/**
	 * wpmu options.
	 * @return array
	 */
	public function wpmu_options() 
	{
		$out = '';

		$out .= '<h3>' . __( 'SharpLogin' ) . '</h3>';
		$out .= '<p>' . __( 'This option allows you to set a networkwide default, which can be overridden by individual sites. Simply go to to the site’s permalink settings to change the url.' ) . '</p>';
		//$out .= '<p>' . sprintf( __( 'Need help? Try the <a href="%1$s" target="_blank">support forum</a>. This plugin is kindly brought to you by <a href="%2$s" target="_blank">WPServeur</a>' ), 'http://wordpress.org/support/plugin/sharplogin-hide-login/', 'https://www.wpserveur.net/?refwps=14&campaign=wpshidelogin' ) . '</p>';
		$out .= '<table class="form-table">';
		$out .= '<tr valign="top">';
		$out .= '<th scope="row"><label for="sharplogin_page">' . __( 'Networkwide default' ) . '</label></th>';
		$out .= '<td><input id="sharplogin_page" type="text" name="sharplogin_page" value="' . esc_attr( get_site_option( 'sharplogin_page' ) ) . '"></td>';
		$out .= '</tr>';
		$out .= '</table>';

		echo $out;

	}

	/**
	 * update wpmu options.
	 */
	public function update_wpmu_options() 
	{
		if ( check_admin_referer( 'siteoptions' ) ) {
			if ( ( $sharplogin_page = sanitize_title_with_dashes( $_POST['sharplogin_page'] ) )
			     && strpos( $sharplogin_page, 'wp-login' ) === false
			     && ! in_array( $sharplogin_page, $this->forbidden_slugs() ) ) {

				update_site_option( 'sharplogin_page', $sharplogin_page );

			}
		}
	}

	

	/**
	 * remove trailing slash from site url
	 */
	private function sharplogin_use_trailing_slashes() 
	{

		return ( '/' === substr( get_option( 'permalink_structure' ), - 1, 1 ) );

	}

	/**
	 * user trailing slash in url
	 */
	private function user_trailingslashit( $string ) 
	{

		return $this->sharplogin_use_trailing_slashes() ? trailingslashit( $string ) : untrailingslashit( $string );

	}

	/**
	 * wp template loader for login page
	 */
	private function sharplogin_wp_template_loader() 
	{

		global $pagenow;

		$pagenow = 'index.php';

		if ( ! defined( 'WP_USE_THEMES' ) ) {

			define( 'WP_USE_THEMES', true );

		}

		wp();

		if ( $_SERVER['REQUEST_URI'] === $this->user_trailingslashit( str_repeat( '-/', 10 ) ) ) {

			$_SERVER['REQUEST_URI'] = $this->user_trailingslashit( '/wp-login-php/' );

		}

		require_once( ABSPATH . WPINC . '/template-loader.php' );

		die;

    }

	/**
	 * admin init hook
	 */
	function admin_init() 
	{

		register_setting( 'general', 'sharplogin_page', 'sanitize_title_with_dashes' );

		if ( get_option( 'sharplogin_redirect' ) ) {

			delete_option( 'sharplogin_redirect' );

			if ( is_multisite()
			     && is_super_admin()
			     && is_plugin_active_for_network( SHARPLOGIN_BASENAME ) ) {

				$redirect = network_admin_url( 'settings.php#sharplogin_settings' );

			} else {

				$redirect = admin_url( '/admin.php?page=sharplogin' );

			}

			wp_safe_redirect( $redirect );
			die();

		}

	}

	/**
	 * admin notices hook
	 */
	public function admin_notices() 
	{

		global $pagenow;

		$out = '';

		if ( ! is_network_admin()
		     && $pagenow === 'wp-admin/admin.php?page=sharplogin'
		     && isset( $_GET['settings-updated'] )
		     && ! isset( $_GET['page'] ) ) {

			echo '<div class="updated notice is-dismissible"><p>' . sprintf( __( 'Your login page is now here: <strong><a href="%1$s">%2$s</a></strong>. Bookmark this page!' ), $this->new_login_url(), $this->new_login_url() ) . '</p></div>';

		}

	}

	/**
	 * plugin action link to go to settings page
	 */
	public function plugin_action_links( $links ) 
	{

		if ( is_network_admin()
		     && is_plugin_active_for_network( SHARPLOGIN_BASENAME ) ) {

			array_unshift( $links, '<a href="' . network_admin_url( 'settings.php#sharplogin_settings' ) . '">' . __( 'Settings' ) . '</a>' );

		} elseif ( ! is_network_admin() ) {

			array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=sharplogin' ) . '">' . __( 'Settings' ) . '</a>' );

		}

		return $links;

	}

	/** 
	 * check if plugin is loaded
	 * @return boolean
	 */
    public function plugins_loaded() 
	{

		global $pagenow;

		if ( ! is_multisite()
		     && ( strpos( $_SERVER['REQUEST_URI'], 'wp-signup' ) !== false
		          || strpos( $_SERVER['REQUEST_URI'], 'wp-activate' ) !== false ) && apply_filters( 'sharplogin_hide_login_signup_enable', false ) === false ) {

			wp_die( __( 'This feature is not enabled.' ) );

		}

		$request = parse_url( $_SERVER['REQUEST_URI'] );

		if ( ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-login.php' ) !== false
		       || untrailingslashit( $request['path'] ) === site_url( 'wp-login', 'relative' ) )
		     && ! is_admin() ) {
				 $this->wp_login_php = true;
				 
				 $_SERVER['REQUEST_URI'] = $this->user_trailingslashit( '/' . str_repeat( '-/', 10 ) );
				 
			$pagenow = 'index.php';

		} elseif ( untrailingslashit( $request['path'] ) === home_url( $this->new_login_slug(), 'relative' )
		           || ( ! get_option( 'permalink_structure' )
		                && isset( $_GET[ $this->new_login_slug() ] )
		                && empty( $_GET[ $this->new_login_slug() ] ) ) ) {

			$pagenow = 'wp-login.php';

		} elseif ( ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-register.php' ) !== false
		             || untrailingslashit( $request['path'] ) === site_url( 'wp-register', 'relative' ) )
		           && ! is_admin() ) {

			$this->wp_login_php = true;

			$_SERVER['REQUEST_URI'] = $this->user_trailingslashit( '/' . str_repeat( '-/', 10 ) );

			$pagenow = 'index.php';
		}

	}

    public function setup_theme() 
	{
		global $pagenow;

		if ( ! is_user_logged_in() && 'customize.php' === $pagenow ) {
			wp_die( __( 'This has been disabled' ), 403 );
		}
	}

	public function wp_loaded() 
	{

		global $pagenow;
		$request = parse_url( $_SERVER['REQUEST_URI'] );
		
		if ( is_admin() && ! is_user_logged_in() && ! defined( 'DOING_AJAX' ) && $pagenow !== 'admin-post.php' && ( isset( $_GET ) && empty( $_GET['adminhash'] ) && $request['path'] !== '/wp-admin/options.php' ) ) {
			wp_safe_redirect( home_url( '/404' ) );
			die();
		}
		
		if ( $pagenow === 'wp-login.php'
		&& $request['path'] !== $this->user_trailingslashit( $request['path'] )
		&& get_option( 'permalink_structure' ) ) {
			
			wp_safe_redirect( $this->user_trailingslashit( $this->new_login_url() )
			. ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );
			
			die;
			
		} elseif ( $this->wp_login_php ) {

			if ( ( $referer = wp_get_referer() )
			     && strpos( $referer, 'wp-activate.php' ) !== false
			     && ( $referer = parse_url( $referer ) )
			     && ! empty( $referer['query'] ) ) {

				parse_str( $referer['query'], $referer );

				if ( ! empty( $referer['key'] )
				     && ( $result = wpmu_activate_signup( $referer['key'] ) )
				     && is_wp_error( $result )
				     && ( $result->get_error_code() === 'already_active'
				          || $result->get_error_code() === 'blog_taken' ) ) {

					wp_safe_redirect( $this->new_login_url()
					                  . ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );

					die;

				}

			}

			$this->sharplogin_wp_template_loader();

		} elseif ( $pagenow === 'wp-login.php' ) {
			global $error, $interim_login, $action, $user_login;

			if ( is_user_logged_in() && ! isset( $_REQUEST['action'] ) ) {
				wp_safe_redirect( admin_url() );
				die();
			}

			@require_once ABSPATH . 'wp-login.php';

			die;

		}

	}

    public function site_url( $url, $path, $scheme, $blog_id ) 
	{

		return $this->filter_wp_login_php( $url, $scheme );

	}

	public function network_site_url( $url, $path, $scheme ) 
	{

		return $this->filter_wp_login_php( $url, $scheme );

	}

	public function wp_redirect( $location, $status ) 
	{

		return $this->filter_wp_login_php( $location );

	}

	public function filter_wp_login_php( $url, $scheme = null ) 
	{

		if ( strpos( $url, 'wp-login.php' ) !== false ) {

			if ( is_ssl() ) {

				$scheme = 'https';

			}

			$args = explode( '?', $url );

			if ( isset( $args[1] ) ) {

				parse_str( $args[1], $args );

				if ( isset( $args['login'] ) ) {
					$args['login'] = rawurlencode( $args['login'] );
				}

				$url = add_query_arg( $args, $this->new_login_url( $scheme ) );

			} else {

				$url = $this->new_login_url( $scheme );

			}

		}

		return $url;

	}

    public function welcome_email( $value ) 
	{

		return $value = str_replace( 'wp-login.php', trailingslashit( get_site_option( 'sharplogin_page' ) ), $value );

	}

	public function forbidden_slugs() 
	{

		$wp = new WP;

		return array_merge( $wp->public_query_vars, $wp->private_query_vars );

	}

    /**
	 * Load scripts
	 */
	public function admin_enqueue_scripts( $hook ) 
	{
		if ( 'options-general.php' != $hook ) {
			return false;
		}

		wp_enqueue_style( 'plugin-install' );

		wp_enqueue_script( 'plugin-install' );
		wp_enqueue_script( 'updates' );
		add_thickbox();
	}

    public function settings_page() 
	{
		_e( 'SharpLogin' );
	}

	public function sharplogin_template_redirect() 
	{
		if ( ! empty( $_GET ) && isset( $_GET['page'] ) && 'sharplogin_settings' === $_GET['page'] ) {
			wp_redirect( admin_url( 'admin.php?page=sharplogin' ) );
			exit();
		}
	}
    
    private function new_login_slug() 
	{
		if ( $slug = get_option( 'sharplogin_page' ) ) {
			return $slug;
		} else if ( ( is_multisite() && is_plugin_active_for_network( SHARPLOGIN_BASENAME ) && ( $slug = get_site_option( 'sharplogin_page' ) ) ) ) {
			return $slug;
		} else if ( $slug = 'login' ) {
			return $slug;
		}
	}

    public function new_login_url( $scheme = null ) 
	{

		if ( get_option( 'permalink_structure' ) ) {

			return $this->user_trailingslashit( home_url( '/', $scheme ) . $this->new_login_slug() );

		} else {

			return home_url( '/', $scheme ) . '?' . $this->new_login_slug();

		}

	}

    /**
	 * Update redirect for Woocommerce email notification
	 */
	public function sharplogin_redirect_page_email_notif_woocommerce() 
	{

		if ( ! class_exists( 'WC_Form_Handler' ) ) {
			return false;
		}

		if ( ! empty( $_GET ) && isset( $_GET['action'] ) && 'rp' === $_GET['action'] && isset( $_GET['key'] ) && isset( $_GET['login'] ) ) {
			wp_redirect( $this->new_login_url() );
			exit();
		}
	}

    public function login_url( $login_url, $redirect, $force_reauth ) 
	{

		if ( $force_reauth === false ) {
			return $login_url;
		}

		if ( empty( $redirect ) ) {
			return $login_url;
		}

		$redirect = explode( '?', $redirect );

		if ( $redirect[0] === admin_url( 'options.php' ) ) {
			$login_url = admin_url();
		}

		return $login_url;
	}


	/**
	 * create function to limit login attempts
	 */

	public function check_attempted_login( $user, $username, $password ) 
	{
		$sl_login_attempt_settings = get_option( 'sl_login_attempts_settings' );
		if(!empty($sl_login_attempt_settings['sl_login_attempts_enabled'])){
			if ( get_transient( 'attempted_login' ) ) {
				$datas = get_transient( 'attempted_login' );
				$max_tries = !empty($sl_login_attempt_settings['sl_login_attempts_max_attempts'])? $sl_login_attempt_settings['sl_login_attempts_max_attempts']: 3;
				if ( $datas['tried'] >= $max_tries ) {
					$until = get_option( '_transient_timeout_' . 'attempted_login' );
					$time = $this->sl_time_to_go( $until );
		
					return new WP_Error( 'too_many_tried',  sprintf( __( '<strong>ERROR</strong>: You have reached authentication limit, you will be able to try again in %1$s.' ) , $time ) );
				}
			}
		}
	
		return $user;
	}
	
	// show login attemp fails on login screen
	public function sl_time_to_go( $until ) 
	{
		$time = $until - time();
		$time = $time / 60;
		$time = round( $time );
		return $time;
	}

	/**
	 * login attempts count 
	 * @return $message
	 */
	public function sharplogin_attempts_count() 
	{
		$attempt = 0;
		$sl_login_attempt_settings = get_option( 'sl_login_attempts_settings' );

		if(!empty($sl_login_attempt_settings['sl_login_attempts_enabled'])){
			if ( get_transient( 'attempted_login' ) ) {
				$datas = get_transient( 'attempted_login' );
				$tried = $datas['tried'];
				$until = get_option( '_transient_timeout_' . 'attempted_login' );
				$time = $this->sl_time_to_go( $until );
				$time_unit = !empty($sl_login_attempt_settings['sl_login_attempts_lockout_time_unit'])? $sl_login_attempt_settings['sl_login_attempts_lockout_time_unit']: 'minutes';
				$max_tries = !empty($sl_login_attempt_settings['sl_login_attempts_max_attempts'])? $sl_login_attempt_settings['sl_login_attempts_max_attempts']: 3;
				if($tried >= $max_tries){
					$message = sprintf( __( '<strong>ERROR</strong>: You have reached authentication limit, you will be able to try again in %1$s %2$s' ) , $time, $time_unit );
				}else{
					$message = sprintf( __( '<strong>ERROR</strong>: You have %1$s attempts left.' ) , $max_tries - $tried );
				}
				
				return  $message;
			}
		}
	}
	

	/**
	 * login failed check if user is blocked
	 * @param  [type] $user [description]
	 */
	public function sharplogin_failed( $username ) 
	{
		$sl_login_attempt_settings = get_option( 'sl_login_attempts_settings' );

		if(!empty($sl_login_attempt_settings['sl_login_attempts_enabled'])){
			$max_tries = !empty($sl_login_attempt_settings['sl_login_attempts_max_attempts'])? $sl_login_attempt_settings['sl_login_attempts_max_attempts']: 5;
			$time_unit = !empty($sl_login_attempt_settings['sl_login_attempts_lockout_time_unit'])? $sl_login_attempt_settings['sl_login_attempts_lockout_time_unit']: 'minutes';
			$lockout_time = !empty($sl_login_attempt_settings['sl_login_attempts_lockout_time'])? $sl_login_attempt_settings['sl_login_attempts_lockout_time']: 5;

			if($time_unit == 'minutes'){
				$time = $lockout_time * 60;
			}elseif($time_unit == 'hours'){
				$time = $lockout_time * 60 * 60;
			}elseif($time_unit == 'days'){
				$time = $lockout_time * 60 * 60 * 24;
			}

			

			if ( get_transient( 'attempted_login' ) ) {
				$datas = get_transient( 'attempted_login' );
				$datas['tried']++;
		
				if ( $datas['tried'] <= $max_tries )
					set_transient( 'attempted_login', $datas , $time );
			} else {
				$datas = array(
					'tried'     => 1
				);
				set_transient( 'attempted_login', $datas , $time );
			}
		}
	}

	
	function load_wp_media_files( $page ) {
	  // change to the $page where you want to enqueue the script
	//   if( $page == 'options-general.php' ) {
		// Enqueue WordPress media scripts
		wp_enqueue_media();
		// Enqueue custom script that will interact with wp.media
		wp_enqueue_script( 'sharplogin_script', plugin_dir_url( __FILE__ ) . '/assets/js/sharplogin_script.js', array(), '1.0' );
	//   }
	}

	// public function sharplogin_enqueue_admin_script() {
		
	// }
	
}