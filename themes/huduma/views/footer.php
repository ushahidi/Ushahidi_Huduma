			</div>
		</div>
		<!-- / main body -->

	</div>
	<!-- / wrapper -->

	<div id="footer">
        <div id="footer-holder">
            <div id="footer-main">
                <div id="footer-text">
                    <?php if ($site_copyright_statement != ''): ?>
                    <p><?php echo $site_copyright_statement; ?></p>
                    <?php endif; ?>
                </div>
                <div class="footer-credits">
                    Powered by the &nbsp;<a href="http://www.ushahidi.com/"><img src="<?php echo url::base(); ?>/media/img/footer-logo.png" alt="Ushahidi" style="vertical-align:middle" /></a>&nbsp; Platform
                </div>
            </div>
        </div>


	</div>
	<!-- / footer -->

	<?php echo $ushahidi_stats; ?>
	<?php echo $google_analytics; ?>

	<!-- Task Scheduler -->
	<img src="<?php echo url::base(); ?>media/img/spacer.gif" alt="" height="1" width="1" border="0" onload="runScheduler(this)" />

	<?php
	// Action::main_footer - Add items before the </body> tag
	Event::run('ushahidi_action.main_footer');
	?>
</body>
</html>
