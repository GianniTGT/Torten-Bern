<?php
namespace TortenBern\UI;

class Shortcodes {
    public function register(): void {
        add_shortcode('tb_order_button', [$this,'order_button']);
    }

    public function order_button($atts){
        $atts = shortcode_atts(['text'=>'Jetzt bestellen'],$atts);
        return '<a class="tb-order-button" href="' . esc_url(wc_get_cart_url()) . '">' . esc_html($atts['text']) . '</a>';
    }
}
