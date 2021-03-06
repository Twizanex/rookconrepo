<?php include_once('../include.php');
if(basename($_SERVER['SCRIPT_FILENAME']) == 'add_ticket_maps.php') {
	ob_clean();
}
error_reporting(0);
if($_GET['map_action'] == 'pickup_delivery') {
	$strict_view = strictview_visible_function($dbc, 'ticket');
	$tile_security = get_security($dbc, ($_GET['tile_name'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile_name']));
	if($strict_view > 0) {
		$tile_security['edit'] = 0;
		$tile_security['config'] = 0;
	}
	if(!isset($ticketid)) {
		$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	}
	$start = $destination = '';
	$waypoints = [];
	$stop_list = [];
	$stops = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 ORDER BY `sort`");
	while($row = mysqli_fetch_assoc($stops)) {
		$start = empty($start) ? $row['address'].','.$row['city'].','.$row['postal_code'] : $start;
		$waypoints[] = $row['address'].','.$row['city'].','.$row['postal_code'];
		if($row['city'] != '') {
			$stop_list[] = $row['address'].','.$row['city'].','.$row['postal_code'];
		}
		$destination = $row['address'].','.$row['city'].','.$row['postal_code'];
	}
	unset($waypoints[count($waypoints) - 1]);
	unset($waypoints[0]);
	if(!empty($start)) { ?>
		<iframe class="google-map" src="https://www.google.com/maps/embed/v1/directions?key=<?= EMBED_MAPS_KEY ?>&origin=<?= urlencode($start) ?><?= count($waypoints) > 0 ? '&waypoints='.urlencode(implode('|',$waypoints)) : '' ?>&destination=<?= urlencode($destination) ?>&mode=driving" allowfullscreen>
		</iframe>
		<?php $excess_km_serviceid = get_config($dbc, 'delivery_km_service');
		if($excess_km_serviceid > 0) {
			$max_km = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `max_km` FROM `contacts_cost` LEFT JOIN `tickets` ON `tickets`.`businessid`=`contacts_cost`.`contactid` WHERE `ticketid`='$ticketid'"))['max_km'];
			if($max_km > 0) {
				$kms = $excess_km = 0;
				foreach($stop_list as $end) {
					if($start != $end) {
						//Send request and receive json data
						$data = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=".urlencode($start)."&destinations=".urlencode($end)."&language=en-EN&sensor=false"));
						echo "<!--From: ".$data->origin_addresses[0];
						echo " To: ".$data->destination_addresses[0];
						foreach($data->rows[0]->elements as $road) {
							echo " ETA: ".($road->distance->value / 1000)." KM @ ".time_decimal2time($road->duration->value / 60 / 60);
							$kms += $road->distance->value / 1000;
						}
						echo "http://maps.googleapis.com/maps/api/distancematrix/json?origins=".urlencode($start)."&destinations=".urlencode($end)."&language=en-EN&sensor=false-->";
					}
					
					// Set the next start as the previous end
					$start = $end;
				}
				
				if($kms > $max_km) {
					$excess_km = round($kms - $max_km);
				} ?>
				<script>
				var excess_km = <?= $excess_km ?>;
				var max_km = '<?= $max_km ?>';
				var total_km = <?= $kms ?>;
				var current_excess = 0;
				$('[name=serviceid]').filter(function() { return this.value == '<?= $excess_km_serviceid ?>'; }).each(function() {
					current_excess = $(this).closest('.multi-block').find('[name=service_qty]').first().val();
				});
				if(ticket_excess_confirm && excess_km != current_excess && excess_km > 0 && confirm('The estimated distance of <?= $total_km ?> is over the <?= BUSINESS_CAT ?> allowed KM of <?= $max_km ?> by <?= $excess_km ?>. Would you like to update the Extra KM quantity to this amount?')) {
					setSave();
					if($('[name=serviceid]').filter(function() { return this.value == '<?= $excess_km_serviceid ?>'; }).length == 0) {
						debugger;
						if($('[name=serviceid]').filter(function() { return this.value == ''; }).length == 1) {
							$('[name=serviceid]').filter(function() { return this.value == ''; }).val('<?= $excess_km_serviceid ?>').trigger('change.select2');
						} else {
							addMulti($('[name=serviceid]').get(0));
							$('[name=serviceid]').filter(function() { return this.value == ''; }).val('<?= $excess_km_serviceid ?>').trigger('change.select2');
						}
					}
					$('[name=serviceid]').filter(function() { return this.value == '<?= $excess_km_serviceid ?>'; }).each(function() {
						$(this).closest('.multi-block').find('[name=service_qty]').first().val(excess_km);
						$(this).closest('.multi-block').find('[name=service_qty]').first().change();
					});
				} else if(ticket_excess_confirm && excess_km != current_excess && excess_km > 0) {
					ticket_excess_confirm = false;
				}
				</script>
			<?php }
		}
	}
	else {
		echo "No Address provided, cannot display driving directions.";
	}
} ?>