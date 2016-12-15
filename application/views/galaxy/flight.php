<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>My Ships</h2>
        <p class="help-block">Chose how many of each unit type to send to <?php echo get_planet_name($defender_planet); ?>.</p>
        <div class="row">
            <?php echo form_open('/galaxy/flight/' . $defender_planet_id); ?>
                <ul class="list-group">
                    <?php foreach ($ships as $ship): ?>
                        <li class="list-group-item">
                            <h4 class="list-group-item-heading"><?php echo html_escape($ship->name); ?></h4>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <p class="list-group-item-text"><strong>Ships: <?php echo $ship->qty; ?></strong></p>
                                    <?php foreach ($ship->resources as $resource) : ?>
                                        <p class="list-group-item-text"><?php echo html_escape($resource->name); ?>: <?php echo $resource->amount; ?></p>
                                    <?php endforeach; ?>
                                    <?php foreach ($ship->buildings as $building) : ?>
                                        <p class="list-group-item-text"><?php echo html_escape($building->name); ?>: <?php echo $building->level; ?> Level</p>
                                    <?php endforeach; ?>
                                    <p class="list-group-item-text">Build Time (per unit): <?php echo $ship->cost_time; ?> sec.</p>
                                    <p class="list-group-item-text">Damage: <?php echo $ship->damage; ?></p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label text-right" style="margin-top: 12px;">Qty:</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" name="amount_<?php echo $ship->ship_id; ?>" value="<?php echo $ship->qty; ?>" min="0" max="<?php echo $ship->qty; ?>" required>
                                            <p class="help-block">max. <?php echo $ship->qty; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="form-group">
                    <a class="btn btn-default" href="<?php echo site_url('galaxy/map'); ?>">Cancel</a>
                    <button type="submit" class="btn btn-warning">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
