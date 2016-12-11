<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content span=8 offset=2">
    <div class="well">
        <?php echo form_open('/planet/edit/' . $planet->id, 'class="form-horizontal" name="create"'); ?>
            <fieldset>
                <legend>Edit Planet</legend>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="name">Planet Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="name" placeholder="Enter planet name" name="name" value="<?php echo html_escape($planet->name); ?>">
                        <p class="help-block">Coordinates: X = <?php echo $planet->x; ?>, Y = <?php echo $planet->y; ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-4">
                        <a class="btn btn-default" href="<?php echo site_url('/planet/list'); ?>">Cancel</a>
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>