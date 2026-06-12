<?php
namespace TortenBern\Core;

use TortenBern\UI\Shortcodes;
use TortenBern\API\AjaxHandler;

class Plugin {
    private static $instance = null;

    public static function get_instance(): Plugin {
        if (null===self::$instance) self::$instance = new self();
        return self::$instance;
    }

    public function init(): void {
        // load textdomain
        load_plugin_textdomain('torten-bern-core', false, dirname(plugin_basename(__FILE__)) . '/../../languages');

        // enqueue
        add_action('wp_enqueue_scripts', [$this,'enqueue_assets']);

        // WooCommerce hooks
        add_action('woocommerce_after_order_notes', [$this,'checkout_custom_fields']);
        add_action('woocommerce_checkout_process', [$this,'checkout_process']);
        add_action('woocommerce_checkout_update_order_meta', [$this,'checkout_update_order_meta']);

        // shortcodes
        $shortcodes = new Shortcodes();
        $shortcodes->register();

        // AJAX
        $ajax = new AjaxHandler();
        $ajax->register();

        register_activation_hook(TB_CP_DIR . 'torten-bern-core.php', [$this,'on_activation']);
    }

    public function enqueue_assets(): void {
        wp_enqueue_style('tb-core', TB_CP_URL . 'assets/css/tb-core.css', [], TB_CP_VERSION);
        wp_enqueue_script('tb-core', TB_CP_URL . 'assets/js/tb-core.js', ['jquery'], TB_CP_VERSION, true);
        wp_localize_script('tb-core','tbCore', ['ajaxUrl'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('tb_core')]);
    }

    public function on_activation(): void {
        // create demo products if WooCommerce active
        if (class_exists('WooCommerce')) {
            // create simple demo cakes (only if none)
            if (0 === wp_count_posts('product')->publish) {
                $this->create_demo_products();
            }
        }
    }

    private function create_demo_products(): void {
        // create three products with variations simulated via attributes
        $products = [
            ['title'=>'Sahnetorte (Beeren)','price'=>45.00,'desc'=>'Leichte Sahnetorte mit frischen Beeren.'],
            ['title'=>'Fondant Torte (Geburtstag)','price'=>65.00,'desc'=>'Fondant-Torte individuell verziert.'],
            ['title'=>'Schokoladentorte (Premium)','price'=>55.00,'desc'=>'Reiche Schokoladentorte mit Ganache.'],
        ];

        foreach ($products as $p) {
            $post_id = wp_insert_post([ 'post_title'=>$p['title'], 'post_content'=>$p['desc'], 'post_status'=>'publish', 'post_type'=>'product' ]);
            if ($post_id) {
                update_post_meta($post_id,'_regular_price',number_format($p['price'],2,'.',''));
                update_post_meta($post_id,'_price',number_format($p['price'],2,'.',''));
                wp_set_object_terms($post_id,'simple','product_type');
                // set SKU
                update_post_meta($post_id,'_sku','TB-' . $post_id);
            }
        }
    }

    /** Checkout custom fields: delivery date, time, allergens, pickup/delivery, delivery fee handled later */
    public function checkout_custom_fields($checkout){
        echo '<div id="tb_checkout_fields"><h3>Lieferdetails & Allergien</h3>';
        woocommerce_form_field('tb_delivery_date', array('type'=>'date','class'=>array('form-row-wide'),'label'=>'Gewünschtes Datum','required'=>true,'min'=>date('Y-m-d')),
            $checkout->get_value('tb_delivery_date'));

        woocommerce_form_field('tb_delivery_time', array('type'=>'time','class'=>array('form-row-first'),'label'=>'Uhrzeit','required'=>false), $checkout->get_value('tb_delivery_time'));

        woocommerce_form_field('tb_is_delivery', array('type'=>'select','class'=>array('form-row-first'),'label'=>'Abholung oder Lieferung','options'=>array('pickup'=>'Abholung','delivery'=>'Lieferung')),
            $checkout->get_value('tb_is_delivery'));

        woocommerce_form_field('tb_allergies', array('type'=>'textarea','class'=>array('form-row-wide'),'label'=>'Allergene / Unverträglichkeiten (Bitte angeben)','required'=>false), $checkout->get_value('tb_allergies'));

        // Allergiker disclaimer checkbox
        woocommerce_form_field('tb_allergy_disclaimer', array('type'=>'checkbox','class'=>array('form-row-wide'),'label'=>'Ich bestätige, dass ich Allergene korrekt angegeben habe und halte den Verkäufer frei von Haftung.','required'=>true), $checkout->get_value('tb_allergy_disclaimer'));

        echo '</div>';
    }

    public function checkout_process(){
        if (!isset($_POST['tb_delivery_date']) || empty($_POST['tb_delivery_date'])) wc_add_notice('Bitte gewünschtes Lieferdatum angeben','error');
        if (!isset($_POST['tb_allergy_disclaimer'])) wc_add_notice('Bitte den Allergiker‑Disclaimer bestätigen.','error');

        // validate date not in past
        if (!empty($_POST['tb_delivery_date'])){
            $d = sanitize_text_field($_POST['tb_delivery_date']);
            if (strtotime($d) < strtotime(date('Y-m-d'))) wc_add_notice('Das Datum darf nicht in der Vergangenheit liegen.','error');
        }
    }

    public function checkout_update_order_meta($order_id){
        if (!empty($_POST['tb_delivery_date'])) update_post_meta($order_id,'tb_delivery_date',sanitize_text_field($_POST['tb_delivery_date']));
        if (!empty($_POST['tb_delivery_time'])) update_post_meta($order_id,'tb_delivery_time',sanitize_text_field($_POST['tb_delivery_time']));
        if (!empty($_POST['tb_is_delivery'])) update_post_meta($order_id,'tb_is_delivery',sanitize_text_field($_POST['tb_is_delivery']));
        if (!empty($_POST['tb_allergies'])) update_post_meta($order_id,'tb_allergies',sanitize_textarea_field($_POST['tb_allergies']));
        update_post_meta($order_id,'tb_allergy_disclaimer', isset($_POST['tb_allergy_disclaimer']) ? 'yes':'no');

        // If delivery chosen, add delivery fee as shipping (simple fixed rate)
        if (isset($_POST['tb_is_delivery']) && $_POST['tb_is_delivery']==='delivery'){
            update_post_meta($order_id,'tb_delivery_fee',10.00);
        } else {
            update_post_meta($order_id,'tb_delivery_fee',0.00);
        }
    }
}
