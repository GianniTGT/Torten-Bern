(function($){
    $(function(){
        console.log('Torten Bern core loaded');

        // Example: request Twint QR for the cart total on the thankyou page if user chooses Twint
        $('#tb-get-twint-qr').on('click', function(e){
            e.preventDefault();
            var amount = $(this).data('amount');
            $.post(tbCore.ajaxUrl, { action:'tb_generate_twint_qr', nonce:tbCore.nonce, amount:amount }, function(resp){
                if (resp.success){
                    $('#tb-twint-area').html('<img src="'+resp.data.qr+'" alt="Twint QR"/> <p>'+resp.data.instruction+'</p>');
                } else {
                    alert('QR konnte nicht generiert werden');
                }
            });
        });
    });
})(jQuery);
