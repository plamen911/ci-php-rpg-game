<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content span=8 offset=2">
    <div class="well">
        <?php echo form_open('/planet/create', 'class="form-horizontal" name="create"'); ?>
            <fieldset>
                <legend>Get Resource</legend>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="planet_id">Planet</label>
                    <div class="col-sm-4">
                        <select id="planet_id" name="planet_id" class="form-control">
                            <option value="1">Earth</option>
                            <option value="1">Venus</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="resource_id">Source</label>
                    <div class="col-sm-4">
                        <select id="resource_id" name="resource_id" class="form-control">
                            <option value="1">Metal Mine</option>
                            <option value="2">Mineral Mine</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-4">
                        <h3>Income: 3000</h3>
                        <a class="btn btn-primary btn-block btn-lg" href="#">+10</a>
                        <p class="help-block">Multiple click the button above to get more income.</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-4">
                        <a class="btn btn-default" href="<?php echo site_url('/planet/list'); ?>">Cancel</a>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>