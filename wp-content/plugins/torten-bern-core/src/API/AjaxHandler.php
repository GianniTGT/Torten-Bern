<?php
namespace TortenBern\API;

class AjaxHandler {
    public function register(): void {
        add_action('wp_ajax_tb_generate_twint_qr', [$this,'generate_twint_qr']);
        add_action('wp_ajax_nopriv_tb_generate_twint_qr', [$this,'generate_twint_qr']);
    }

    /**
     * Returns a simple Twint QR instruction or a placeholder image URL for the given order total.
     * In production you'd create a real TWINT payment request via a provider.
     */
    public function generate_twint_qr(){
        check_ajax_referer('tb_core','nonce');
        $amount = floatval($_POST['amount'] ?? 0);
        if ($amount <= 0) wp_send_json_error(['message'=>'Invalid amount']);

        // For demo: generate a simple QR using Google Chart API encoding a Twint instruction (placeholder)
        $text = 'TWINT Torten Bern | Betrag: ' . number_format($amount,2) . ' CHF | Verwendungszweck: Bestellung';
        $qr = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . rawurlencode($text);

        wp_send_json_success(['qr'=>$qr,'instruction'=>'Scanne den QR mit Twint und überweise den Betrag manuell. Anschliessend bestätige die Bestellung im Admin.']);
    }
}
