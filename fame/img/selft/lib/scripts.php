		<!--================================================== -->

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<!--<script data-pace-options='{ "restartOnRequestAfter": true }' src="<?php echo PATH_N_COM_JS; ?>/plugin/pace/pace.min.js"></script>-->
		<script data-pace-options='{ "restartOnRequestAfter": true }' src="<?php echo PATH_SELF_JS; ?>/plugin/pace/pace.min.js"></script>
    
    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<!-- IMPORTANT: APP CONFIG -->
		<!--<script src="<?php echo PATH_ADM; ?>/bootstrap/js/app.config.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/app.config.js"></script>

		<!-- JQuery 2.0.2 and JQuery-UI 1.10.3 -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/libs/jquery-2.0.2.min.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/libs/jquery-2.0.2.min.js"></script>
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/libs/jquery-ui-1.10.3.min.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/libs/jquery-ui-1.10.3.min.js"></script>

		<!-- BOOTSTRAP JS -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/bootstrap/bootstrap.min.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/bootstrap/bootstrap.min.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/plugin/notification/SmartNotification.min.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/plugin/smartwidgets/jarvis.widget.min.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/smartwidgets/jarvis.widget.min.js"></script>

		<!-- browser msie issue fix -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/plugin/msie-fix/jquery.mb.browser.min.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/msie-fix/jquery.mb.browser.min.js"></script>

		<!-- FastClick: For mobile devices -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/plugin/fastclick/fastclick.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/fastclick/fastclick.js"></script>

		<!-- EASY PIE CHARTS -->
		<!--<script src="<?php echo PATH_N_COM_JS; ?>/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

		<!-- ImageLoaded -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/imagesloaded/imagesloaded.pkgd.min.js"></script>
		
		<!-- Packery -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/packery/packery.pkgd.min.js"></script>

		<!-- Countdown -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/jquery.countdown/jquery.plugin.min.js"></script>
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/jquery.countdown/jquery.countdown.min.js"></script>

		<!-- Clock -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/jquery-clock/jqClock.min.js"></script>

		<!-- HTML entities @url: https://github.com/mathiasbynens/he -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/he/he.js"></script>

		<!-- Flowplayer flash -->		
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/flowplayer_flash/flowplayer-3.2.13.min.js"></script>

		<!-- Node -->
		<script src="http://campus.vanas.ca:3000/socket.io/socket.io.js"></script>
		<script src="<?php echo PATH_SELF_JS; ?>/node.inc.js"></script>

		<!-- JQUERY SELECT2 INPUT -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/select2/select2.min.js"></script>
    
    <!-- MAIN APP JS FILE -->
		<script src="<?php echo PATH_SELF_JS; ?>/app.min.js"></script>

		<!-- app.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/app.js"></script>

		<!-- vanas.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/vanas.js"></script>

		<!-- board.inc.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/board.inc.js"></script>
		
		<!-- messages.inc.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/messages.inc.js"></script>

		<!-- desktop.inc.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/desktop.inc.js"></script> 

    <!-- PAGE RELATED PLUGIN(S) -->
		<!--<script src="<?php echo PATH_SELF_JS; ?>/plugin/datatables/jquery.dataTables.min.js"></script>
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/datatables/dataTables.colVis.min.js"></script>
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/datatables/dataTables.tableTools.min.js"></script>
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/datatables/dataTables.bootstrap.min.js"></script>
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/datatable-responsive/datatables.responsive.min.js"></script>-->
    
    <!-- PAGE RELATED PLUGIN(S) DATETABLES -->
    <script src="<?php echo PATH_SELF_JS; ?>/plugin/datatables/jquery.dataTables-cust.min.js"></script>
    <script src="<?php echo PATH_SELF_JS; ?>/plugin/datatables/DT_bootstrap.js"></script>

		<script type="text/javascript">
		// DO NOT REMOVE : GLOBAL FUNCTIONS!
		$(document).ready(function() {
			pageSetUp();
		})
		</script>