<?php
/**
 * Plugin Name: Dynamic Pricing Table
 * Description: Create customizable pricing tables with shortcode support
 * Version: 1.2
 * Author: Rahees Ahmed
 * Author URI: https://github.com/raheesahmed
 * Plugin URI:https://github.com/raheesahmed/dynamic-pricing-table
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DynamicPricingTable
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_shortcode('dynamic_pricing_table', array($this, 'render_pricing_table'));
    }

    public function add_plugin_page()
    {
        add_menu_page(
            'Dynamic Pricing Table',
            'Pricing Tables',
            'manage_options',
            'dynamic-pricing-table',
            array($this, 'create_admin_page'),
            'dashicons-grid-view',
            56
        );
    }

    public function register_settings()
    {
        register_setting('dynamic_pricing_table_options', 'dynamic_pricing_table_data', array($this, 'sanitize_pricing_data'));
    }

    public function sanitize_pricing_data($input)
    {
        // Implement proper sanitization here
        return $input;
    }

    public function create_admin_page()
    {
        ?>
        <div class="wrap dynamic-pricing-table-admin">
            <h1><i class="dashicons dashicons-grid-view"></i> Dynamic Pricing Table</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('dynamic_pricing_table_options');
                do_settings_sections('dynamic_pricing_table_options');
                ?>
                <div id="template-selector">
                    <h3>Choose a Template</h3>
                    <select id="template-select">
                        <option value="">Select a template</option>
                        <option value="default">Default</option>
                        <option value="modern">Modern</option>
                        <option value="minimalist">Minimalist</option>
                        <option value="colorful">Colorful</option>
                        <option value="dark">Dark Mode</option>
                    </select>
                    <button type="button" id="create-from-template" class="button">Create New Table</button>
                </div>
                <div id="pricing-tables-container">
                    <!-- Pricing tables will be dynamically added here -->
                </div>
                <button type="button" id="add-pricing-table" class="button button-primary">
                    <i class="dashicons dashicons-plus-alt"></i> Add Pricing Table
                </button>
                <?php submit_button('Save All Tables'); ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_admin_assets($hook)
    {
        if ('toplevel_page_dynamic-pricing-table' !== $hook) {
            return;
        }
        wp_enqueue_style('dynamic-pricing-table-admin-style', plugins_url('css/style.css', __FILE__));
        wp_enqueue_script('dynamic-pricing-table-admin-script', plugins_url('js/script.js', __FILE__), array('jquery', 'wp-color-picker'), '1.2', true);
        wp_enqueue_style('wp-color-picker');
        wp_localize_script('dynamic-pricing-table-admin-script', 'dynamicPricingTableData', array(
            'existing_data' => get_option('dynamic_pricing_table_data', array()),
            'templates' => $this->get_templates()
        ));
    }

    private function get_templates()
    {
        return array(
            'default' => array(
                'name' => 'Default',
                'bg_color' => '#ffffff',
                'text_color' => '#000000',
                'border_radius' => '0',
                'tier_bg_color' => '#f8f9fa',
                'tier_text_color' => '#212529',
                'button_bg_color' => '#007bff',
                'button_text_color' => '#ffffff',
                'display_style' => 'column',
            ),
            'modern' => array(
                'name' => 'Modern',
                'bg_color' => '#f8f9fa',
                'text_color' => '#212529',
                'border_radius' => '10',
                'tier_bg_color' => '#ffffff',
                'tier_text_color' => '#495057',
                'button_bg_color' => '#6c63ff',
                'button_text_color' => '#ffffff',
                'display_style' => 'column',
            ),
            'minimalist' => array(
                'name' => 'Minimalist',
                'bg_color' => '#ffffff',
                'text_color' => '#333333',
                'border_radius' => '5',
                'tier_bg_color' => '#f1f3f5',
                'tier_text_color' => '#495057',
                'button_bg_color' => '#212529',
                'button_text_color' => '#ffffff',
                'display_style' => 'vertical',
            ),
            'colorful' => array(
                'name' => 'Colorful',
                'bg_color' => '#ffd166',
                'text_color' => '#073b4c',
                'border_radius' => '15',
                'tier_bg_color' => '#ffffff',
                'tier_text_color' => '#073b4c',
                'button_bg_color' => '#06d6a0',
                'button_text_color' => '#ffffff',
                'display_style' => 'column',
            ),
            'dark' => array(
                'name' => 'Dark Mode',
                'bg_color' => '#212529',
                'text_color' => '#f8f9fa',
                'border_radius' => '8',
                'tier_bg_color' => '#343a40',
                'tier_text_color' => '#f8f9fa',
                'button_bg_color' => '#ffd166',
                'button_text_color' => '#212529',
                'display_style' => 'column',
            ),
        );
    }

    public function enqueue_frontend_assets()
    {
        wp_enqueue_style('dynamic-pricing-table-style', plugins_url('css/style.css', __FILE__));
    }

    public function render_pricing_table($atts)
    {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);

        $table_id = $atts['id'];
        $pricing_tables = get_option('dynamic_pricing_table_data', array());

        if (!isset($pricing_tables[$table_id])) {
            return '';
        }

        $table = $pricing_tables[$table_id];
        $display_style = isset($table['display_style']) ? $table['display_style'] : 'column';
        $output = '<div class="dynamic-pricing-table ' . esc_attr($display_style) . '" style="background-color: ' . esc_attr($table['bg_color']) . '; color: ' . esc_attr($table['text_color']) . ';">';
        $output .= '<h2>' . esc_html($table['title']) . '</h2>';
        $output .= '<div class="pricing-tiers">';

        foreach ($table['tiers'] as $tier) {
            $output .= '<div class="pricing-tier" style="background-color: ' . esc_attr($table['tier_bg_color']) . '; color: ' . esc_attr($table['tier_text_color']) . '; border-radius: ' . esc_attr($table['border_radius']) . 'px;">';
            $output .= '<h3>' . esc_html($tier['name']) . '</h3>';
            $output .= '<div class="price">' . esc_html($tier['price']) . '</div>';
            $output .= '<ul class="features">';
            $features = explode("\n", $tier['features']);
            foreach ($features as $feature) {
                $output .= '<li>' . esc_html(trim($feature)) . '</li>';
            }
            $output .= '</ul>';
            $output .= '<a href="#" class="pricing-button" style="background-color: ' . esc_attr($table['button_bg_color']) . '; color: ' . esc_attr($table['button_text_color']) . ';">' . esc_html($tier['button_text']) . '</a>';
            $output .= '</div>';
        }

        $output .= '</div></div>';

        return $output;
    }
}

$dynamic_pricing_table = new DynamicPricingTable();