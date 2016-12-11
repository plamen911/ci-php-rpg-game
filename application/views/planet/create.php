<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content span=8 offset=2">
    <div class="well">
        <?php echo form_open('/planet/create', 'class="form-horizontal" name="create"'); ?>
            <fieldset>
                <legend>New Planet</legend>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="name">Planet Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="name" placeholder="Enter planet name" name="name" value="<?php echo set_value('name'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-4">
                        <a class="btn btn-default" href="<?php echo site_url('/planet/list'); ?>">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>