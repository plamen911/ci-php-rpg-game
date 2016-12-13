<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>All Ships</h2>
        <div class="row">
            <ul class="list-group">
                <?php foreach ($ships as $ship): ?>
                    <li class="list-group-item">
                        <h4 class="list-group-item-heading"><?php echo html_escape($ship->name); ?></h4>
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
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
                            <div class="col-lg-6 col-md-6">
                                <?php echo form_open('/ship/upgrade/' . $ship->ship_id, 'class="form-inline" method="get"'); ?>
                                    <div class="form-group">
                                        <label>Qty:</label>
                                        <input type="number" class="form-control" name="amount" value="0">
                                    </div>
                                    <button type="submit" class="btn btn-warning">Order</button>
                                </form>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
