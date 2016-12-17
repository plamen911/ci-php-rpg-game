<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>Galaxy Map</h2>
        <div class="row">
            <ul class="list-group">
                <?php foreach ($planets as $planet): ?>
                    <?php $isMe = $player_id == $planet->player_id; ?>
                    <li class="list-group-item<?php echo (($isMe) ? ' list-group-item-info' : ''); ?>">
                        <?php if (!$isMe) : ?>
                            <div class="pull-right">
                                <a href="<?php echo site_url('/galaxy/flight/' . $planet->planet_id); ?>" class="btn btn-warning">Attack</a>
                            </div>
                        <?php endif; ?>
                        <h4 class="list-group-item-heading"><?php echo html_escape($planet->username); ?>,
                            <?php echo get_planet_name($planet); ?> <?php if ($isMe) : ?>(Me)<?php endif; ?>
                        </h4>
                        <?php if (!$isMe) : ?>
                            <p class="list-group-item-text">Coordinates: [<?php echo $planet->x; ?>:<?php echo $planet->y; ?>], Distance: <?php echo $planet->distance; ?> LYA</p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
