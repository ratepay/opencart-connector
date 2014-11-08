<div class="content">
    <div><p><?php echo $logo; ?></p></div>
    <div id="ratepay_error" style="color:red; margin-bottom:10px"></div>
</div>

<div class="buttons">
    <div class="right">
        <a id="checkout" class="button"><span><?php echo $button_confirm; ?></span></a>
    </div>
</div>

<script type="text/javascript"><!--
    var runningCheckout = false;
    $('a#checkout').click(function(event) {

        // we don't accept multiple confirmations of one onder
        if(runningCheckout){
            event.preventDefault();
            return false;
        }
        runningCheckout = true;

        var url = 'http://paypage.dev/paypage/payment/show/lang/de/token/<?php echo $ratepay_token; ?>';

        $(location).attr('href',url);
    });
//--></script>