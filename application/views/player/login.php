<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content span=8 offset=2">
    <div class="well">
        <?php echo form_open('/login', 'class="form-horizontal" name="authenticate"'); ?>
            <fieldset>
                <legend>Player Login</legend>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="username">Username</label>
                    <div class="col-sm-4 ">
                        <input type="text" class="form-control" id="username" placeholder="Username" name="username" value="<?php echo set_value('username'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="password">Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-4">
                        <a class="btn btn-default" href="/">Cancel</a>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<script>
    $(function () {
       $('#username').focus();
    });
</script>