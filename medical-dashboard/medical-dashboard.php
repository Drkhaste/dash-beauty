<?php
/**
 * Plugin Name: Medical Dashboard Pro
 * Plugin URI:  https://example.com
 * Description: داشبورد وردپرس را با تم سبز پزشکی مدرن، راست‌چین و زیبا می‌کند
 * Version:     1.0.0
 * Author:      Medical Dashboard Pro
 * Text Domain: medical-dashboard
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MDP_VERSION', '1.0.0' );
define( 'MDP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MDP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

class Medical_Dashboard_Pro {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
        add_action( 'login_enqueue_scripts',  [ $this, 'enqueue_login_styles' ] );
        add_filter( 'admin_body_class',        [ $this, 'add_body_class' ] );
        add_action( 'admin_head',              [ $this, 'inject_inline_svg_icons' ] );
        add_action( 'wp_dashboard_setup',      [ $this, 'customize_dashboard_widgets' ] );
        add_filter( 'admin_footer_text',       [ $this, 'custom_footer_text' ] );
        add_action( 'admin_menu',              [ $this, 'add_settings_menu' ] );
        add_action( 'admin_init',              [ $this, 'register_settings' ] );
    }

    public function enqueue_styles( $hook ) {
        wp_enqueue_style(
            'medical-dashboard-pro',
            MDP_PLUGIN_URL . 'assets/css/admin-style.css',
            [],
            MDP_VERSION
        );

        wp_add_inline_style( 'medical-dashboard-pro', $this->get_dynamic_css() );

        $custom_css = get_option( 'mdp_custom_css' );
        if ( ! empty( $custom_css ) ) {
            wp_add_inline_style( 'medical-dashboard-pro', $custom_css );
        }
    }

    public function enqueue_login_styles() {
        wp_enqueue_style(
            'medical-dashboard-login',
            MDP_PLUGIN_URL . 'assets/css/login-style.css',
            [],
            MDP_VERSION
        );
    }

    public function add_body_class( $classes ) {
        $classes .= ' mdp-active mdp-rtl-theme';
        return $classes;
    }

    public function inject_inline_svg_icons() {
        // No external dependencies — SVG icons are inline
    }

    public function customize_dashboard_widgets() {
        // Optionally remove default widgets that clutter the dashboard
        remove_meta_box( 'dashboard_primary',       'dashboard', 'side' );
        remove_meta_box( 'dashboard_quick_press',   'dashboard', 'side' );
    }

    public function custom_footer_text( $text ) {
        return '<span style="font-family: inherit; direction: rtl; unicode-bidi: embed;">❤️ طراحی‌شده با دقت پزشکی &mdash; Medical Dashboard Pro</span>';
    }

    public function add_settings_menu() {
        add_menu_page(
            'تنظیمات مدیکال داشبورد',
            'مدیکال داشبورد',
            'manage_options',
            'medical-dashboard-settings',
            [ $this, 'render_settings_page' ],
            'dashicons-heart',
            60
        );
    }

    public function register_settings() {
        register_setting( 'mdp_settings_group', 'mdp_custom_css' );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap mdp-settings-wrap" style="direction: rtl;">
            <h1>تنظیمات مدیکال داشبورد</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'mdp_settings_group' );
                do_settings_sections( 'mdp_settings_group' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">سی‌اس‌اس سفارشی (Custom CSS)</th>
                        <td>
                            <textarea name="mdp_custom_css" rows="15" cols="70" style="width: 100%; font-family: monospace; direction: ltr;"><?php echo esc_textarea( get_option( 'mdp_custom_css' ) ); ?></textarea>
                            <p class="description">کدهای CSS خود را اینجا وارد کنید تا در داشبورد اعمال شوند.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
        <?php
    }

    private function get_dynamic_css() {
        $primary   = '#10b981';
        $primary_d = '#059669';
        $primary_l = '#d1fae5';
        $accent    = '#06d6a0';

        return "
        :root {
            --mdp-primary:    {$primary};
            --mdp-primary-d:  {$primary_d};
            --mdp-primary-l:  {$primary_l};
            --mdp-accent:     {$accent};
        }
        ";
    }
}

Medical_Dashboard_Pro::get_instance();
