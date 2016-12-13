<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>Galaxy Map</h2>
        <div class="row">
            <ul class="list-group">
                <?php foreach ($players as $player): ?>
                    <li class="list-group-item">
                        <?php if ($player_id != $player->player_id) : ?>
                            <div class="pull-right">
                                <a href="<?php echo site_url('/building/upgrade/'); ?>" class="btn btn-warning">Attack</a>
                            </div>
                        <?php endif; ?>
                        <h4 class="list-group-item-heading"><?php echo html_escape($player->username); ?></h4>
                        <p class="list-group-item-text">Coordinates: <?php echo $player->x; ?>:<?php echo $player->y; ?> / Distance: 999 light years</p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
