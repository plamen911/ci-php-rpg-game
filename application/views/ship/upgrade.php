<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container body-content">
    <div class="well">
        <h2>Upgrading Building Level...</h2>
        <h3>Time Remaining: <span id="timediv"></span></h3>
    </div>
</div>

<script src="<?php echo base_url('assets/js/countdown.js'); ?>"></script>
<script>
    $(function () {
        runCountdown({
            // endTime: 'December 12 2016 14:56:59 GMT+0200',
            endTime: '<?php echo $finishes_on; ?>',
            redirectUrl: '<?php echo site_url('building/list'); ?>'
        })
    });
</script>

