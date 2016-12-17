<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>Battle Report</h2>
        <h3><?php echo $stats[0]; ?></h3>
        <div class="row">
            <ul class="list-group">
                <?php foreach ($stats as $stat): ?>
                    <li class="list-group-item">
                        <h4 class="list-group-item-heading"><?php echo html_escape($stat); ?></h4>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="btn btn-default" href="<?php echo site_url('ship/list'); ?>">Done</a>
        </div>
    </div>
</div>

