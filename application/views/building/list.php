<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>All Buildings</h2>
        <div class="row">
            <ul class="list-group">
                <?php foreach ($buildings as $building): ?>
                    <li class="list-group-item">
                        <div class="pull-right">
                            <a href="<?php echo site_url('/building/upgrade/' . $building->building_id); ?>" class="btn btn-warning">Build Level <?php echo ((int)$building->level + 1); ?></a>
                        </div>
                        <h4 class="list-group-item-heading"><?php echo html_escape($building->name); ?></h4>
                        <p class="list-group-item-text">Level: <?php echo $building->level; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/scripts.js'); ?>"></script>
<script>
    $(function(){
        getPlanetResources('<?php echo site_url('/resources'); ?>');
    });
</script>