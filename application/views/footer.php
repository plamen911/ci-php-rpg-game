<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	        </div><!-- #main -->
	    </div><!-- .row -->
	</div><!-- .container -->

    <footer>
        <div class="container modal-footer">
            <p>&copy; 2016 - Plamen Markov</p>
        </div>
    </footer>


<?php if (isset($_SESSION['logged_in']) && true === $_SESSION['logged_in']) : ?>
    <div id="game-message">
        <div style="padding: 5px;">
            <div id="inner-game-message" class="alert alert-warning"></div>
        </div>
    </div>

    <script src="<?php echo base_url('assets/js/game-scripts.js'); ?>"></script>
    <script>
        $(function () {
            getPlanetResources('<?php echo site_url('/resources'); ?>');
        });
    </script>
<?php endif; ?>
</body>
</html>