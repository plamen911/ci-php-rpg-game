<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>Galaxy Map</h2>
        <div class="row">
            <ul class="list-group">
                <?php foreach ($players as $player): ?>
                    <?php $isMe = $player_id == $player->player_id;  ?>
                    <li class="list-group-item<?php echo (($isMe) ? ' list-group-item-info' : ''); ?>">
                        <?php if (!$isMe) : ?>
                            <div class="pull-right">
                                <a href="<?php echo site_url('/building/upgrade/'); ?>" class="btn btn-warning">Attack</a>
                            </div>
                        <?php endif; ?>
                        <h4 class="list-group-item-heading"><?php echo html_escape($player->username); ?></h4>
                        <p class="list-group-item-text">Coordinates: <?php echo $player->x; ?>:<?php echo $player->y; ?>
                            <?php if (!$isMe) : ?>/ Distance: <?php echo $player->distance; ?> LYA<?php endif; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
