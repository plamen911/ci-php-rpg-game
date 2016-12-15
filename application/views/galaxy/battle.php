<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>Battle on <?php echo get_planet_name($defender_planet); ?>: <?php echo html_escape($attacker_player->username); ?> vs. <?php echo html_escape($defender_player->username); ?></h2>
        <h3>Time Remaining: <span id="timediv"></span></h3>
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo base_url('assets/img/space-battle.gif'); ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h3>Attacker (<?php echo html_escape($attacker_player->username); ?>)</h3>
                <ul class="list-group">
                    <?php foreach ($attacker_ships as $ship): ?>
                        <li class="list-group-item">
                            <h4 class="list-group-item-heading"><?php echo html_escape($ship->name); ?></h4>
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <p class="list-group-item-text"><strong>Ships: <?php echo $ship->qty; ?></strong></p>
                                    <p class="list-group-item-text">Damage: <?php echo $ship->damage; ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Defender (<?php echo html_escape($defender_player->username); ?>)</h3>
                <ul class="list-group">
                    <?php foreach ($defender_ships as $ship): ?>
                        <li class="list-group-item">
                            <h4 class="list-group-item-heading"><?php echo html_escape($ship->name); ?></h4>
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <p class="list-group-item-text"><strong>Ships: <?php echo $ship->qty; ?></strong></p>
                                    <p class="list-group-item-text">Damage: <?php echo $ship->damage; ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <a href="#" class="btn btn-warning">Battle Report</a>
    </div>
</div>

