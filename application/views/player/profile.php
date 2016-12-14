<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content span=8 offset=2">
    <div class="well">
        <?php echo form_open('/profile', 'class="form-horizontal" name="profile"'); ?>
            <fieldset>
                <legend>Profile</legend>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="username">Username</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="username" placeholder="Your username" name="username" value="<?php echo html_escape($player->username); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="email">Email</label>
                    <div class="col-sm-4">
                        <input type="email" class="form-control" id="email" placeholder="Your email" name="email" value="<?php echo html_escape($player->email); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="password">Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="password" placeholder="Enter a password" name="password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="password_confirm">Confirm password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="password_confirm" placeholder="Confirm your password" name="password_confirm">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="full_name">Full Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="full_name" placeholder="Your full name" name="full_name" value="<?php echo html_escape($player->full_name); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-4">
                        <a class="btn btn-default" href="<?php echo site_url('planet/list'); ?>">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<script>
    $(function () {
        window.setTimeout(function () {
            $('#password, #password_confirm').val('')
        }, 100);
    });
</script>