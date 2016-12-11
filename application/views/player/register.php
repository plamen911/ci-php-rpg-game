<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content span=8 offset=2">
    <div class="well">
        <?php echo form_open('/register', 'class="form-horizontal" name="register"'); ?>
            <fieldset>
                <legend>Register</legend>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="username">Username</label>
                    <div class="col-sm-4 ">
                        <input type="text" class="form-control" id="username" placeholder="Enter a username" name="username" value="<?php echo set_value('username'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="email">Email</label>
                    <div class="col-sm-4 ">
                        <input type="email" class="form-control" id="email" placeholder="Enter your email" name="email" value="<?php echo set_value('email'); ?>">
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
                    <div class="col-sm-4 col-sm-offset-4">
                        <a class="btn btn-default" href="/">Cancel</a>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>