<?php echo $header; ?>

Leider nicht erfolgreich

<div class="buttons">
    <div class="right">
        <a id="checkout" class="button"><span><?php echo $ratepay_back_to_checkout; ?></span></a>
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

        var url = '<?php echo $button_checkout; ?>';

        $(location).attr('href',url);
    });
    //--></script>

<?php echo $footer; ?>