<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>Battle on <?php echo get_planet_name($defender_planet); ?>: <?php echo html_escape($attacker_player->username); ?> vs. <?php echo html_escape($defender_player->username); ?></h2>
        <h3>Time Remaining: <span id="timediv"></span></h3>
        <div class="row">
            <div class="col-md-6">
                <h3>Attacker (<?php echo (($attacker_player->id == $player_id) ? 'Me' : $attacker_player->username); ?>) Units</h3>
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
                <h3>Defender (<?php echo (($defender_player->id == $player_id) ? 'Me' : $defender_player->username); ?>) Units</h3>
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
        <!--<div class="row">
            <div class="col-md-12">
                <img src="<?php /*echo base_url('assets/img/space-battle.gif'); */?>" class="img-responsive">
            </div>
        </div>-->
        <!--<hr>
        <div class="row">
            <div class="col-md-12">
                <a href="<?php /*echo site_url('galaxy/battle-report/' . $flight_id); */?>" class="btn btn-warning">Battle Report</a>
                <p class="help-block">Click to see how many units both sides left with after the battle.</p>
            </div>
        </div>-->
    </div>
</div>

<script src="<?php echo base_url('assets/js/countdown.js'); ?>"></script>
<script>
    $(function () {
        runCountdown({
            // endTime: 'December 12 2016 14:56:59 GMT+0200',
            endTime: '<?php echo $battle_end_on; ?>',
            redirectUrl: '<?php echo site_url('galaxy/battle-report/' . $flight_id); ?>'
        });
    });
</script>


