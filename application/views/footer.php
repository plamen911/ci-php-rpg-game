<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	        </div><!-- #main -->
	    </div><!-- .row -->
	</div><!-- .container -->

    <footer>
        <div class="container modal-footer">
            <p>&copy; 2016 - Plamen Markov</p>
        </div>
    </footer>


<?php if (isset($_SESSION['username']) && true === $_SESSION['logged_in']) : ?>
<script src="<?php echo base_url('assets/js/scripts.js'); ?>"></script>
<script>
    $(function () {
        getPlanetResources('<?php echo site_url('/resources'); ?>');
    });
</script>
<?php endif; ?>
</body>
</html>