<?php echo $header; ?>

<div class="content">
    <div><p><?php echo $error_failure; ?></p></div>
</div>

<div class="buttons">
    <div class="right">
        <a id="checkout" class="button"><span><?php echo $button_goto_checkout; ?></span></a>
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