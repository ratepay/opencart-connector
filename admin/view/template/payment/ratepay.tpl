<?php echo $header; ?>
<?php if ($error_permission) { ?>
<div class="warning"><?php echo $error_permission; ?></div>
<?php } ?>

<div class="box">
<div class="left"></div>
<div class="right"></div>
<div class="heading">
    <h1 style=""><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
</div>
<div class="content">
    <!--general settings -->
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
            <tbody>
            <tr>
                <td>Version</td>
                <td><?php echo $ratepay_version; ?></td>
            </tr>
            <tr>
                <td><?php echo $ratepay_status_text; ?></td>
                <td><select name="ratepay_status">
                        <?php if ($ratepay_status) { ?>
                        <option value="1" selected="selected"><?php echo $ratepay_enabled; ?></option>
                        <option value="0"><?php echo $ratepay_disabled; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $ratepay_enabled; ?></option>
                        <option value="0" selected="selected"><?php echo $ratepay_disabled; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>

            <tr>
                <td>
                    <?php echo $ratepay_sandbox_text; ?>
                    <span class="help"><?php echo $ratepay_sandbox_help; ?></span>
                </td>
                <td><select name="ratepay_sandbox">
                        <?php if ($ratepay_sandbox) { ?>
                        <option value="1" selected="selected"><?php echo $ratepay_on; ?></option>
                        <option value="0"><?php echo $ratepay_off; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $ratepay_on; ?></option>
                        <option value="0" selected="selected"><?php echo $ratepay_off; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>

            <tr>
                <td>
                    <?php echo $ratepay_title_text; ?>
                    <span class="help"><?php echo $ratepay_title_help; ?></span>
                </td>
                <td><input type="text" name="ratepay_title" value="<?php echo $ratepay_title; ?>" size="40" /></td>
            </tr>

            <tr>
                <td><?php echo $ratepay_description_text; ?></td>
                <td><textarea rows="2" cols="30" name="ratepay_description"><?php echo $ratepay_description; ?></textarea></td>
            </tr>

            <tr>
                <td><?php echo $ratepay_profile_id_text; ?></td>
                <td><input type="text" name="ratepay_profile_id" value="<?php echo $ratepay_profile_id; ?>" size="40" /></td>
            </tr>

            <tr>
                <td><?php echo $ratepay_security_code_text; ?></td>
                <td><input type="text" name="ratepay_security_code" value="<?php echo $ratepay_security_code; ?>" size="40" /></td>
            </tr>

            <tr>
                <td>
                    <?php echo $ratepay_order_status_text; ?>
                    <span class="help"><?php echo $ratepay_order_status_help; ?></span>
                </td>
                <td>
                    <select name="ratepay_order_success_id">
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <?php if ($order_status['order_status_id'] == $ratepay_order_success_id) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>
                    <?php echo $ratepay_limits_text; ?>
                    <span class="help"><?php echo $ratepay_limits_help ?></span></td>
                <td>
                    <span class="help"><?php echo $ratepay_limit_min_text; ?></span>
                    <input name="ratepay_limit_min" type="text" value="<?php echo $ratepay_limit_min; ?>" size="2" />
                    <span class="help"><?php echo $ratepay_limit_max_text; ?></span>
                    <input name="ratepay_limit_max" type="text" value="<?php echo $ratepay_limit_max; ?>" size="2" />
                </td>
            </tr>

            <tr>
                <td><?php echo $ratepay_ala_text; ?></td>
                <td><select name="ratepay_ala">
                        <?php if ($ratepay_ala) { ?>
                        <option value="1" selected="selected"><?php echo $ratepay_allowed; ?></option>
                        <option value="0"><?php echo $ratepay_forbidden; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $ratepay_allowed; ?></option>
                        <option value="0" selected="selected"><?php echo $ratepay_forbidden; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>

            <tr>
                <td><?php echo $ratepay_b2b_text; ?></td>
                <td><select name="ratepay_b2b">
                        <?php if ($ratepay_b2b) { ?>
                        <option value="1" selected="selected"><?php echo $ratepay_allowed; ?></option>
                        <option value="0"><?php echo $ratepay_forbidden; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $ratepay_allowed; ?></option>
                        <option value="0" selected="selected"><?php echo $ratepay_forbidden; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>

            <tr>
                <td>
                    <?php echo $ratepay_pp_editable_text; ?>
                    <span class="help"><?php echo $ratepay_pp_editable_help; ?></span>
                </td>
                <td><select name="ratepay_pp_editable">
                        <?php if ($ratepay_pp_editable) { ?>
                        <option value="1" selected="selected"><?php echo $ratepay_allowed; ?></option>
                        <option value="0"><?php echo $ratepay_forbidden; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $ratepay_allowed; ?></option>
                        <option value="0" selected="selected"><?php echo $ratepay_forbidden; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>

            <tr>
                <td>
                    <?php echo $ratepay_pp_basket_text; ?>
                    <span class="help"><?php echo $ratepay_pp_basket_help; ?></span>
                </td>
                <td><select name="ratepay_pp_basket">
                        <?php if ($ratepay_pp_basket) { ?>
                        <option value="1" selected="selected"><?php echo $ratepay_on; ?></option>
                        <option value="0"><?php echo $ratepay_off; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $ratepay_on; ?></option>
                        <option value="0" selected="selected"><?php echo $ratepay_off; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>

            <tr>
                <td><?php echo $ratepay_privacy_policy_url_text; ?></td>
                <td><?php echo $ratepay_privacy_policy_url; ?></td>
            </tr>

            <tr>
                <td><?php echo $ratepay_sort_order_text; ?></td>
                <td><input type="text" name="ratepay_sort_order" value="<?php echo $ratepay_sort_order; ?>" size="1" /></td>
            </tr>

            </tbody>
        </table>

    </form>
</div>
</div>
<div style="height:100px"></div>
<script type="text/javascript"><!--
    $('#tab-invoice a').tabs();
    //--></script>
<?php echo $footer; ?>