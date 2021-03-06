<?php
$from_safety = '';
if (!empty($_GET['safetyid'])) {
	$from_safety = '../Incident Report/';
} ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_type" >
				<?= (strpos($value_config, ','."Type_DetailsLabel".',') !== FALSE ? 'Details of Staff/Member(s) Involved' : 'Type & Individuals') ?><span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_type" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group" <?= (strpos($hide_config, ',Type,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="company_name" class="col-sm-4 control-label">Type<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
				<select id="category" name="type" class="chosen-select-deselect form-control" width="380">
					<option value=''></option>
					<?php foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) {
						echo "<option ".($in_type == $type ? 'selected' : '')." value='$in_type'>$in_type</option>";
					} ?>
				</select>
			</div>
		  </div>

		  <?php if (strpos($value_config, ','."Completed By".',') !== FALSE) { ?>
			<?php foreach(explode(',',$completed_by) as $completed_id) { ?>
			  <div class="form-group patient" <?= (strpos($hide_config, ',Completed By,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="clientid" class="col-sm-4 control-label">Completed By<span class="text-red">*</span>:</label>
				<div class="col-sm-7">
					<select data-placeholder="Select Staff..." id="completed_by" name="completed_by[]" class="chosen-select-deselect form-control" width="380">
						<option value=""></option>
						<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
						foreach($staff_list as $id) {
							$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
							echo "<option ".($completed_id == $id ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
						} ?>
					</select>
				</div>
				<div class="col-sm-1">
					<img class="inline-img cursor-hand" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);">
				</div>
			  </div>
			<?php } ?>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Date of Happening".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Date of Happening,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Date of Happening:</label>
			<div class="col-sm-8">
				<input type="text" name="date_of_happening" class="form-control datepicker" value="<?php echo $date_of_happening; ?>">
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Date of Report".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Date of Report,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Date of Report:</label>
			<div class="col-sm-8">
				<input type="text" name="date_of_report" class="form-control datepicker" value="<?php echo $date_of_report; ?>">
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Program".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Program,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="programid" class="col-sm-4 control-label">Program<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Program..." id="programid" name="programid" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $program_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category='Programs' AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
					foreach($program_list as $id) {
						$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$id'"));
						echo "<option ".($programid == $id ? 'selected' : '')." value='". $id."'>".(empty($row['name']) ? decryptIt($row['first_name']).' '.decryptIt($row['last_name']) : decryptIt($row['name'])).'</option>';
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Project Type".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Project Type,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label"><?= PROJECT_NOUN ?> Type:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select <?= PROJECT_NOUN ?> Type..." id="project_type" name="project_type" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $project_tabs = get_config($dbc, 'project_tabs');
					if($project_tabs == '') {
						$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
					}
					$project_tabs = explode(',',$project_tabs);
					$project_vars = [];
					foreach($project_tabs as $item) {
						$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
					}
					foreach($project_tabs as $project_i => $project_tab) {
						echo "<option ".($project_type == $project_vars[$project_i] ? 'selected' : '')." value='".$project_vars[$project_i]."'>".$project_tab."</option>";
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Project".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Project,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label"><?= PROJECT_NOUN ?>:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select <?= PROJECT_NOUN ?>..." id="projectid" name="projectid" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $project_list = mysqli_fetch_all(mysqli_query($dbc,"SELECT * FROM `project` WHERE `deleted` = 0 ORDER BY `project_name`"),MYSQLI_ASSOC);
					foreach($project_list as $project) {
						echo "<option data-projecttype='".$project['projecttype']."' ".($projectid == $project['projectid'] ? 'selected' : '')." value='". $project['projectid']."'>".get_project_label($dbc, $project).'</option>';
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Ticket,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label"><?= TICKET_NOUN ?>:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select <?= TICKET_TILE ?>..." id="ticketid" name="ticketid" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $ticket_list = mysqli_fetch_all(mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted` = 0 AND (`status` NOT IN ('Done','Archive') OR `ticketid` = '".$ticketid."') ORDER BY `heading`"),MYSQLI_ASSOC);
					foreach($ticket_list as $ticket) {
						$project_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$ticket['projectid']."'"))['projecttype'];
						echo "<option data-projecttype='".$project_type."' data-projectid='".$ticket['projectid']."' ".($ticketid == $ticket['ticketid'] ? 'selected' : '')." value='". $ticket['ticketid']."' ".(!empty($projectid) && $projectid != $ticket['projectid'] ? 'style="display:none;"' : '').">".get_ticket_label($dbc, $ticket).'</option>';
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Client".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Client,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="clientid" class="col-sm-4 control-label">Client(s) Involved<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Clients..." multiple id="clientid" name="clientid[]" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $client_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category NOT IN (".STAFF_CATS.") AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
					foreach($client_list as $id) {
						$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$id'"));
						echo "<option ".(strpos(','.$clientid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".(empty($row['name']) ? decryptIt($row['first_name']).' '.decryptIt($row['last_name']) : decryptIt($row['name'])).'</option>';
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Member".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Member,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="memberid" class="col-sm-4 control-label">Member(s) Involved<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Members..." multiple id="memberid" name="memberid[]" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $member_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category='Members' AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
					foreach($member_list as $id) {
						$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name`, `businessid` FROM `contacts` WHERE `contactid`='$id'"));
						echo "<option data-businessid='".$row['businessid']."' ".(strpos(','.$memberid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".(empty($row['name']) ? decryptIt($row['first_name']).' '.decryptIt($row['last_name']) : decryptIt($row['name'])).'</option>';
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Equipment,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="clientid" class="col-sm-4 control-label">Equipment<span class="text-red"></span>:</label>
			<div class="col-sm-8">
				<select multiple data-placeholder="Select Equipment..." id="clientid" name="equipmentid[]" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $equip_query = mysqli_query($dbc, "SELECT `equipmentid`,`unit_number`,`make`,`model`,`licence_plate` FROM `equipment` WHERE `deleted`=0");
					while($equip_row = mysqli_fetch_array($equip_query)) {
						echo "<option ".(strpos(','.$equipmentid.',', ','.$equip_row['equipmentid'].',') !== FALSE ? 'selected' : '')." value='".$equip_row['equipmentid']."'>".$equip_row['make']." ".$equip_row['model']." Unit #".$equip_row['unit_number']." (Licence Plate ".$equip_row['licence_plate'].")</option>";
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Driver".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label"><?= (strpos($value_config, ','."Driver_WorkerLabel".',') !== FALSE ? 'Worker/ Operator' : 'Driver') ?><span class="text-red">*</span>:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Staff..." multiple id="contactid" name="contactid[]" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
					foreach($staff_list as $id) {
						$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
						echo "<option ".(strpos(','.$contactid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' (Licence #'.$row['license'].', Address: ';
						echo (!empty($row['address']) ? $row['address'] : (!empty($row['mailing_address']) ? $row['mailing_address'] : (!empty($row['business_address']) ? $row['business_address'] : $row['ship_to_address']))).')</option>';
					} ?>
				</select>
			</div>
		  </div>
		  <?php } else if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Staff,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label"><?= (strpos($value_config, ','."Staff_InvolvedLabel".',') !== FALSE ? 'Staff Involved' : 'Staff') ?><span class="text-red">*</span>:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Staff..." multiple id="contactid" name="contactid[]" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
					foreach($staff_list as $id) {
						$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
						echo "<option ".(strpos(','.$contactid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Workers Involved".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Workers Involved,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Workers Involved:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Workers..." multiple id="workerid" name="workerid[]" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
					foreach($staff_list as $id) {
						$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
						echo "<option ".(strpos(','.$workerid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
					} ?>
				</select>
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Others".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Others,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Others:</label>
			<div class="col-sm-8">
				<input type="text" name="other_names" class="form-control" value="<?= $other_names ?>">
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Other Driver".',') !== FALSE) { ?>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Other Driver's Name:</label>
			<div class="col-sm-8">
				<input type="text" name="other_driver_name" class="form-control" value="<?= $other_driver_name ?>">
			</div>
		  </div>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Other Driver's Address:</label>
			<div class="col-sm-8">
				<input type="text" name="other_driver_address" class="form-control" value="<?= $other_driver_address ?>">
			</div>
		  </div>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Other Driver's Licence #:</label>
			<div class="col-sm-8">
				<input type="text" name="other_driver_licence" class="form-control" value="<?= $other_driver_licence ?>">
			</div>
		  </div>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Other Driver's Insurance Company:</label>
			<div class="col-sm-8">
				<input type="text" name="other_driver_ins_company" class="form-control" value="<?= $other_driver_ins_company ?>">
			</div>
		  </div>
		  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Other Driver's Insurance Policy:</label>
			<div class="col-sm-8">
				<input type="text" name="other_driver_ins_policy" class="form-control" value="<?= $other_driver_ins_policy ?>">
			</div>
		  </div>
		  <?php } ?>

			<?php if (strpos($value_config, ','."Individuals Witnesses".',') !== FALSE) { ?>
				<h4>Witness(s) Statement</h4>

				  <div class="form-group">
					<label for="site_name" class="col-sm-4 control-label">Witness(s) Name / Phone Number:</label>
					<div class="col-sm-8">
					  <input type="text" name="witness_names" class="form-control" value="<?php echo $witness_names; ?>">
					</div>
				  </div>

				  <div class="form-group">
					<label for="site_name" class="col-sm-4 control-label">Witness(s) Statement:</label>
					<div class="col-sm-8">
					  <textarea name="ir5" rows="4" cols="50" class="form-control" ><?php echo $ir5; ?></textarea>
					</div>
				  </div>
			<?php } ?>

		</div>
	</div>
</div>

<!-- All -->
<?php if (strpos($value_config, ','."Description Accordion".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Description Accordion,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_desc" >
				Description<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_desc" class="panel-collapse collapse">
		<div class="panel-body">
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Date Of Incident:</label>
			<div class="col-sm-8">
			  <input type="text" name="incident_date_date" class="form-control datepicker" value="<?php echo $incident_date_date; ?>">
			</div>
			<label for="site_name" class="col-sm-4 control-label">Time Of Incident:</label>
			<div class="col-sm-8">
			  <input type="text" name="incident_date_time" class="form-control datetimepicker" value="<?php echo $incident_date_time; ?>">
			</div>
		  </div>

		<?php if (strpos($value_config, ','."Accident Report".',') !== FALSE) { ?>
		  <div class="form-group" <?= (strpos($hide_config, ',Accident Report,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Accident Report<br><em>(Who, What, Where, When, Why - Be As Descriptive As Possible)</em>:</label>
			<div class="col-sm-8">
			  <textarea name="ir1" rows="4" cols="50" class="form-control" ><?php echo $ir1; ?></textarea>
			</div>
		  </div>
		<?php } else if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
		  <div class="form-group" <?= (strpos($hide_config, ',Description,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Description Of Incident<br><em>(Who, What, Where, When, Why - Be As Descriptive As Possible)</em>:</label>
			<div class="col-sm-8">
			  <textarea name="ir1" rows="4" cols="50" class="form-control" ><?php echo $ir1; ?></textarea>
			</div>
		  </div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Location".',') !== FALSE) { ?>
		  <div class="form-group" <?= (strpos($hide_config, ',Location,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Location Of Incident:</label>
			<div class="col-sm-8">
				<?php $site_list = mysqli_query($dbc, "SELECT `site_name` FROM `contacts` WHERE `category`='Sites' AND `deleted`=0");
				if(mysqli_num_rows($site_list) > 0 && strpos($value_config, ','."Location Textbox".',') === FALSE) { ?>
					<select data-placeholder="Select a Site" name="location" class="form-control chosen-select-deselect"><option></option>
						<?php $other_location = true;
						while($site_row = mysqli_fetch_array($site_list)) {
							if($site_row['site_name'] == $location) {
								$other_location = false;
							} ?>
							<option <?= ($location == $site_row['site_name'] ? 'selected' : '') ?> value="<?= $site_row['site_name'] ?>"><?= $site_row['site_name'] ?></option>
						<?php }
						if($other_location) { ?>
							<option selected value="$location"><?= $location ?></option>
						<?php } ?>
					</select>
				<?php } else { ?>
					<input type="text" name="location" class="form-control" value="<?php echo $location; ?>">
				<?php } ?>
			</div>
		  </div>
		<?php } ?>

		</div>
	</div>
</div>
<?php } ?>

<!-- All -->
<?php if (strpos($value_config, ','."Record Equipment Or Property Damage".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Record Equipment Or Property Damage,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_rec" >
				Record Equipment Or Property Damage<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_rec" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Record Equipment Or Property Damage:<br /><em><?= strpos($value_config, ','."Equipment".',') !== FALSE ? "Specify Equipment Unit #" : '' ?></em></label>
			<div class="col-sm-8">
			  <textarea name="ir2" rows="4" cols="50" class="form-control" ><?php echo $ir2; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Action Taken".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Action Taken,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_follow" >
				Actions<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_follow" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Action taken:<br /><em>(what, where, &amp; by whom)</em></label>
			<div class="col-sm-8">
			  <textarea name="action_taken" rows="4" cols="50" class="form-control" ><?php echo $action_taken; ?></textarea>
			</div>
		  </div>
		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Record Of Injury Involved".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Record Of Injury Involved,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
				Record Of Injury Involved<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_7" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Record Of Injury Involved<br><em>(Description & Picture If Possible)</em>:</label>
			<div class="col-sm-8">
			  <textarea name="ir9" rows="4" cols="50" class="form-control" ><?php echo $ir9; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Determine Causes".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Determine Causes,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_12" >
				Determine Causes<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>
	<div id="collapse_12" class="panel-collapse collapse">
		<div class="panel-body">

			<?php if (strpos($value_config, ','."Direct Indirect Root Causes".',') !== FALSE) { ?>

			  <div class="form-group" <?= (strpos($hide_config, ',Direct Indirect Root Causes,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="site_name" class="col-sm-4 control-label">Determine Direct Causes:</label>
				<div class="col-sm-8">
				  <textarea name="ir13[]" rows="4" cols="50" class="form-control" ><?php echo explode('#*#',$ir13)[0]; ?></textarea>
				</div>
			  </div>

			  <div class="form-group" <?= (strpos($hide_config, ',Direct Indirect Root Causes,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="site_name" class="col-sm-4 control-label">Determine Indirect Causes:</label>
				<div class="col-sm-8">
				  <textarea name="ir13[]" rows="4" cols="50" class="form-control" ><?php echo explode('#*#',$ir13)[1]; ?></textarea>
				</div>
			  </div>

			  <div class="form-group" <?= (strpos($hide_config, ',Direct Indirect Root Causes,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="site_name" class="col-sm-4 control-label">Determine Root Causes:</label>
				<div class="col-sm-8">
				  <textarea name="ir13[]" rows="4" cols="50" class="form-control" ><?php echo explode('#*#',$ir13)[2]; ?></textarea>
				</div>
			  </div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Happening Lead Up".',') !== FALSE) { ?>
			  <div class="form-group" <?= (strpos($hide_config, ',Happening Lead Up,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="site_name" class="col-sm-4 control-label">Happening Lead Up:</label>
				<div class="col-sm-8">
				  <textarea name="happening_lead_up" rows="4" cols="50" class="form-control" ><?php echo $happening_lead_up; ?></textarea>
				</div>
			  </div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Happening Follow Up".',') !== FALSE) { ?>
			  <div class="form-group" <?= (strpos($hide_config, ',Happening Follow Up,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="site_name" class="col-sm-4 control-label">Happening Follow Up:</label>
				<div class="col-sm-8">
				  <textarea name="happening_follow_up" rows="4" cols="50" class="form-control" ><?php echo $happening_follow_up; ?></textarea>
				</div>
			  </div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Future Considerations".',') !== FALSE) { ?>
			  <div class="form-group" <?= (strpos($hide_config, ',Future Considerations,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="site_name" class="col-sm-4 control-label">Future Considerations:</label>
				<div class="col-sm-8">
				  <textarea name="future_considerations" rows="4" cols="50" class="form-control" ><?php echo $future_considerations; ?></textarea>
				</div>
			  </div>
			<?php } ?>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Supply Pictures".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Supply Pictures,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_13" >
				<?= (strpos($value_config, ','."Pictures_ProvideLabel".',') !== FALSE ? 'Provide Pictures' : 'Pictures') ?><span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_13" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
		  <script>
		  function add_file() {
			  var clone = $('[name="upload_document[]"]').last().clone();
			  clone.find('input').val('');
			  $('#add_picture').before(clone);
		  }
		  </script>
			<label for="file[]" class="col-sm-4 control-label">Supply Pictures:</label>
			<div class="col-sm-8">
			<?php if((!empty($_GET['incidentreportid']) || !empty($_GET['safetyid'])) && ($upload_document != '')) {
						$file_names = explode('#$#', $upload_document);
						echo '<ul>';
						foreach($file_names as $file_name) {
							if($file_name != '') {
								echo '<li><a href="'.$from_safety.'download/'.$file_name.'" target="_blank">'.$file_name.'</a></li>';
							}
						}
						echo '</ul>';
				?>
				<input type="hidden" name="upload_document_current" value="<?php echo $upload_document; ?>" />
				<input multiple name="upload_document[]" type="file" id="file" data-filename-placement="inside" class="form-control" />
			  <?php } else { ?>
				<input multiple name="upload_document[]" type="file" id="file" data-filename-placement="inside" class="form-control" />
			  <?php } ?>
			  <button class="btn brand-btn pull-right" id="add_picture" type="button" onclick="add_file(); return false;">Additional Pictures</button>

			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Recommendations".',') !== FALSE) { ?>
	<div class="panel panel-default" <?= (strpos($hide_config, ',Recommendations,') !== FALSE ? 'style="display:none;"' : '') ?>>
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_recommend" >
					Recommendations<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_recommend" class="panel-collapse collapse">
			<div class="panel-body">

			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Recommendations on how to correct or avoid recurrence of this type of accident or incident:</label>
				<div class="col-sm-8">
				  <textarea name="recommendations" rows="4" cols="50" class="form-control" ><?php echo $recommendations; ?></textarea>
				</div>
			  </div>
			</div>
		</div>
	</div>
<?php } ?>

<?php if (strpos($value_config, ','."Reporting Information".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Reporting Information,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_reporting" >
				Reporting Information<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_reporting" class="panel-collapse collapse">
		<div class="panel-body">
		
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Reported:</label>
			<div class="col-sm-8">
			  <?= get_contact($dbc, $reported_by).': '.$today_date ?><br />
				<?php if(!empty($_GET['incidentreportid']) || $sign != '') { ?>
					<img src="<?php echo $from_safety; ?>download/sign_<?php echo $incidentreportid; ?>_reporting.png" width="190" height="80" border="0" alt="">
					<input type="hidden" name="output" value='<?php echo $sign; ?>'>
				<?php } else { ?>
					<?php include ('../phpsign/sign.php'); ?>
				<?php } ?>
			</div>
		  </div>

		<?php if (strpos($value_config, ','."Comments".',') !== FALSE) { ?>
		  <div class="form-group" <?= (strpos($hide_config, ',Comments,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Comments:</label>
			<div class="col-sm-8">
			  <textarea name="comments" rows="4" cols="50" class="form-control" ><?php echo $comments; ?></textarea>
			</div>
		  </div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Supervisor Statement & Signoff".',') !== FALSE) { ?>
		  <div class="form-group" <?= (strpos($hide_config, ',Supervisor Statement & Signoff,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Supervisor Statement:</label>
			<div class="col-sm-8">
			  <textarea name="ir6" rows="4" cols="50" class="form-control" ><?php echo $ir6; ?></textarea>
			</div>
		  </div>

		  <?php if($supervisor_sign != '') { ?>
		  <div class="form-group" <?= (strpos($hide_config, ',Supervisor Statement & Signoff,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Supervisor Signature:</label>
			<div class="col-sm-8">
				<input type="hidden" name="supervisor" value="<?= $supervisor ?>">
			  <img src="<?php echo $from_safety; ?>download/sign_<?php echo $incidentreportid; ?>_supervisor.png" width="190" height="80" border="0" alt="">
			  <input type="hidden" name="sign2" value='<?= $supervisor_sign ?>'>
			</div>
			<label for="site_name" class="col-sm-4 control-label">Date:</label>
			<div class="col-sm-8">
			  <?php if(strpos($value_config, ','."Reporting Information_EditDates".',') !== FALSE) { ?>
				<input type="text" name="supervisor" class="form-control datepicker" value="<?= $supervisor ?>">
			  <?php } else {
			  	echo $supervisor;
			  } ?>
			</div>
		  </div>
		   <?php } else { ?>
			  <div class="form-group" <?= (strpos($hide_config, ',Supervisor Statement & Signoff,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="site_name" class="col-sm-4 control-label">Signature:</label>
				<div class="col-sm-8">
				  <?php include ('../phpsign/sign2.php'); ?>
				</div>
				<label for="site_name" class="col-sm-4 control-label">Date:</label>
				<div class="col-sm-8">
					<input type="text" name="supervisor" class="form-control datepicker">
				</div>
			  </div>
		   <?php } ?>
		<?php } ?>

		<?php if (strpos($value_config, ','."Coordinator Statement & Signoff".',') !== FALSE) { ?>
		  <div class="form-group" <?= (strpos($hide_config, ',Coordinator Statement & Signoff,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Coordinator Statement:</label>
			<div class="col-sm-8">
			  <textarea name="coordinator_comments" rows="4" cols="50" class="form-control" ><?php echo $ir6; ?></textarea>
			</div>
		  </div>

		  <?php if($coordinator_sign != '') { ?>
		  <div class="form-group" <?= (strpos($hide_config, ',Coordinator Statement & Signoff,') !== FALSE ? 'style="display:none;"' : '') ?>>
			<label for="site_name" class="col-sm-4 control-label">Coordinator Signature:</label>
			<div class="col-sm-8">
				<input type="hidden" name="coordinator" value="<?= $coordinator ?>">
			  <img src="<?php echo $from_safety; ?>download/sign_<?php echo $incidentreportid; ?>_coordinator.png" width="190" height="80" border="0" alt="">
			  <input type="hidden" name="sign3" value='<?= $coordinator_sign ?>'>
			</div>
			<label for="site_name" class="col-sm-4 control-label">Date:</label>
			<div class="col-sm-8">
			  <?php if(strpos($value_config, ','."Reporting Information_EditDates".',') !== FALSE) { ?>
				<input type="text" name="coordinator" class="form-control datepicker" value="<?= $coordinator ?>">
			  <?php } else {
			  	echo $coordinator;
			  } ?>
			</div>
		  </div>
		   <?php } else { ?>
			  <div class="form-group" <?= (strpos($hide_config, ',Coordinator Statement & Signoff,') !== FALSE ? 'style="display:none;"' : '') ?>>
				<label for="site_name" class="col-sm-4 control-label">Signature:</label>
				<div class="col-sm-8">
				  <?php include ('../phpsign/sign3.php'); ?>
				</div>
				<label for="site_name" class="col-sm-4 control-label">Date:</label>
				<div class="col-sm-8">
					<input type="text" name="coordinator" class="form-control datepicker">
				</div>
			  </div>
		   <?php } ?>
		<?php } ?>

		</div>
	</div>
</div>
<?php } ?>
	
<?php if (strpos($value_config, ','."Funder Contact".',') !== FALSE) { ?>
	<div class="panel panel-default" <?= (strpos($hide_config, ',Funder Contact,') !== FALSE ? 'style="display:none;"' : '') ?>>
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
					For Critical Incidents Only<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_1" class="panel-collapse collapse">
			<div class="panel-body">

			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Funder Contacted:</label>
				<div class="col-sm-8">
				  <input type="text" name="funder_name" class="form-control" value="<?= $funder_name ?>">
				</div>
			  </div>
			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Date Contacted:</label>
				<div class="col-sm-8">
				  <input type="text" name="funder_contacted" class="form-control datepicker" value="<?= $funder_contacted ?>">
				</div>
			  </div>

			</div>
		</div>
	</div>
<?php } ?>
	
<?php if (strpos($value_config, ','."Director Signature".',') !== FALSE) { ?>
	<div class="panel panel-default" <?= (strpos($hide_config, ',Director Signature,') !== FALSE ? 'style="display:none;"' : '') ?>>
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_director" >
					Director Signature<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_director" class="panel-collapse collapse">
			<div class="panel-body">

		  <?php if($director_sign != '') { ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Director Signature:</label>
			<div class="col-sm-8">
			<input type="hidden" name="director" value="<?= $director ?>">
			  <img src="<?php echo $from_safety; ?>download/sign_<?php echo $incidentreportid; ?>_director.png" width="190" height="80" border="0" alt="">
			  <input type="hidden" name="sign4" value='<?= $director_sign ?>'>
			</div>
			<label for="site_name" class="col-sm-4 control-label">Date:</label>
			<div class="col-sm-8">
			  <?php if(strpos($value_config, ','."Director Signature_EditDates".',') !== FALSE) { ?>
				<input type="text" name="director" class="form-control datepicker" value="<?= $director ?>">
			  <?php } else {
			  	echo $director;
			  } ?>
			</div>
		  </div>
		   <?php } else { ?>
			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Signature:</label>
				<div class="col-sm-8">
				  <?php include ('../phpsign/sign4.php'); ?>
				</div>
				<label for="site_name" class="col-sm-4 control-label">Date:</label>
				<div class="col-sm-8">
					<input type="text" name="director" class="form-control datepicker">
				</div>
			  </div>
		   <?php } ?>

			</div>
		</div>
	</div>
<?php } ?>

<?php if (strpos($value_config, ','."Record Cause Of Accident".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Record Cause Of Accident,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cause" >
				Record Cause Of Accident<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_cause" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Record Cause Of Accident:</label>
			<div class="col-sm-8">
			  <textarea name="ir3" rows="4" cols="50" class="form-control" ><?php echo $ir3; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Witness Statement".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Witness Statement,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
				<?= (strpos($value_config, ','."Witness_InterviewLabel".',') !== FALSE ? 'Interview Witness(s) (if required)' : 'Witness(s) Statement') ?><span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_3" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Witness(s) Name / Phone Number:</label>
			<div class="col-sm-8">
			  <input type="text" name="witness_names" class="form-control" value="<?php echo $witness_names; ?>">
			</div>
		  </div>

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Witness(s) Statement:</label>
			<div class="col-sm-8">
			  <textarea name="ir5" rows="4" cols="50" class="form-control" ><?php echo $ir5; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Taken Care".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Taken Care,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
				Medical Aid Provided<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_5" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Injured To Be Taken Care Of And Supervisors/Medical Aid Contacted:</label>
			<div class="col-sm-8">
			  <textarea name="ir7" rows="4" cols="50" class="form-control" ><?php echo $ir7; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Initial Actions Required".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Initial Actions Required,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
				Initial Actions Required<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_6" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Initial Actions Required<br><em>(Reporting, Medical Aid Required, Severity Of Injury)</em>:</label>
			<div class="col-sm-8">
			  <textarea name="ir8" rows="4" cols="50" class="form-control" ><?php echo $ir8; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Interview Witness(s)".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Interview Witness(s),') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
				<?= (strpos($value_config, ','."Interview_IfRequiredLabel".',') !== FALSE ? 'Interview Witness(s) (if required)' : 'Interview Witness(s)') ?><span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_9" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Witness(s) Name / Phone Number:</label>
			<div class="col-sm-8">
			  <input type="text" name="witness_names" class="form-control" value="<?php echo $witness_names; ?>">
			</div>
		  </div>

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Interview Witness(s):</label>
			<div class="col-sm-8">
			  <textarea name="ir10" rows="4" cols="50" class="form-control" ><?php echo $ir10; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Check Background Info".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Check Background Info,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_10" >
				Check Background Info<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_10" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Check Background Info<br><em>(Equipment, People, Conditions That Would Contribute To The Incident. Were Safety Procedures followed? Was all PPE worn at the time of incident?) </em> : </label>
			<div class="col-sm-8">
			  <textarea name="ir11" rows="4" cols="50" class="form-control" ><?php echo $ir11; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Timing".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Timing,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_11" >
				Timing<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_11" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Record Length Of Time Working With The Company<br><em>(Including Tickets And Training)</em>:</label>
			<div class="col-sm-8">
			  <textarea name="ir12" rows="4" cols="50" class="form-control" ><?php echo $ir12; ?></textarea>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Follow Up".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Follow Up,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_14" >
				Follow Up<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_14" class="panel-collapse collapse">
		<div class="panel-body">
		  
		  <?php if ($type == 'Critical Incident' || $type == 'Non-Critical Incicent') { ?>
		  <div class='form-group'>
		  <h4>Action and follow-up (<b>please indicate who was contacted, when and by whom</b>):</h4>
		  <div class="col-sm-3"></div><div class="col-sm-3">Who Was Contacted</div><div class="col-sm-3">When</div><div class="col-sm-3">By Whom</div>
		  <?php $follow_up_titles = explode('#*#',$follow_up_title);
		  $follow_up_names = explode('#*#',$follow_up_name);
		  $follow_up_dates = explode('#*#',$follow_up_date);
		  $follow_up_who_list = explode('#*#',$follow_up_who);
		  foreach($follow_up_titles as $i => $title) {
			  echo "<div class='col-sm-3'>$title:<input type='hidden' name='follow_up_title[]' value='$title'></div>";
			  echo "<div class='col-sm-3'><input type='text' name='follow_up_name[]' value='".$follow_up_names[$i]."' class='form-control'></div>";
			  echo "<div class='col-sm-3'><input type='text' name='follow_up_date[]' value='".$follow_up_dates[$i]."' class='form-control datepicker'></div>";
			  echo "<div class='col-sm-3'><input type='text' name='follow_up_who[]' value='".$follow_up_who_list[$i]."' class='form-control'></div>";
		  } ?>
		  </div>
		<?php } else { ?>

		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Follow Up Date:</label>
			<div class="col-sm-8">
			  <input name="ir14" value="<?php echo $ir14; ?>" type="text" id="name" class="datepicker">
			</div>
		  </div>

		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Assign Follow Up:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Staff..." id="assign_followup" name="assign_followup" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
					foreach($staff_list as $id) {
						$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
						echo "<option ".(strpos(','.$assign_followup.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
					} ?>
				</select>
			</div>
		  </div>
		<?php } ?>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Corrective Action".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Corrective Action,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
				Corrective Action<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_2" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Corrective Action:</label>
			<div class="col-sm-8">
			  <textarea name="ir4" rows="4" cols="50" class="form-control" ><?php echo $ir4; ?></textarea>
			</div>
		  </div>

		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Assign Corrective Action:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Staff..." id="assign_corrective" name="assign_corrective" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
					foreach($staff_list as $id) {
						$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
						echo "<option ".($assign_corrective == $id ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
					} ?>
				</select>
			</div>
		  </div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Managers Review Signature".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Managers Review Signature,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_16" >
				Managers Review Signature<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_16" class="panel-collapse collapse">
		<div class="panel-body">

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Review:</label>
			<div class="col-sm-8">
			  <textarea name="ir15" rows="4" cols="50" class="form-control" ><?php echo $ir15; ?></textarea>
			</div>
		  </div>

		  <?php if($sign != '') { ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Signature:</label>
			<div class="col-sm-8">
			  <img src="<?php echo $from_safety; ?>download/sign_<?php echo $incidentreportid; ?>.png" width="190" height="80" border="0" alt="">
			</div>
		  </div>
		   <?php } else { ?>
			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Signature:</label>
				<div class="col-sm-8">
				  <?php include ('../phpsign/sign.php'); ?>
				</div>
			  </div>
		   <?php } ?>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Multiple Signatures".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Multiple Signatures,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_signatures" >
				Sign Off<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_signatures" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="form-group">
				<div class="col-sm-8 col-sm-offset-4">
					<?php if($multisign > 0) {
						for ($i = 0; $i < $multisign; $i++) { ?>
							<img src="<?php echo $from_safety; ?>download/multisign_<?php echo $incidentreportid; ?>_<?php echo $i; ?>.png" width="190" height="80" border="0" alt=""><br /><br />
						<?php }
					} ?>
				</div>
			</div>
			<div class="form-group multisign_block">
				<label for="multisign" class="col-sm-4 control-label">Signature:</label>
				<div class="col-sm-8">
					<?php
						$output_name = "multisign[]";
						include ('../phpsign/sign_multiple.php');
					?>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-8 col-sm-offset-4" style="width: 400px;">
					<img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addSignature();">
					<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature(this);">
				</div>
			</div>

		</div>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Completed By Office".',') !== FALSE) { ?>
<div class="panel panel-default" <?= (strpos($hide_config, ',Completed By Office,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_completed_by_office" >
				Completed By Office<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_completed_by_office" class="panel-collapse collapse">
		<div class="panel-body">

		<?php if (strpos($value_config, ','."Follow Up".',') === FALSE) { ?>
			<?php if ($type == 'Critical Incident' || $type == 'Non-Critical Incicent') { ?>
			  <div class='form-group'>
			  <h4>Action and follow-up (<b>please indicate who was contacted, when and by whom</b>):</h4>
			  <div class="col-sm-3"></div><div class="col-sm-3">Who Was Contacted</div><div class="col-sm-3">When</div><div class="col-sm-3">By Whom</div>
			  <?php $follow_up_titles = explode('#*#',$follow_up_title);
			  $follow_up_names = explode('#*#',$follow_up_name);
			  $follow_up_dates = explode('#*#',$follow_up_date);
			  $follow_up_who_list = explode('#*#',$follow_up_who);
			  foreach($follow_up_titles as $i => $title) {
				  echo "<div class='col-sm-3'>$title:<input type='hidden' name='follow_up_title[]' value='$title'></div>";
				  echo "<div class='col-sm-3'><input type='text' name='follow_up_name[]' value='".$follow_up_names[$i]."' class='form-control'></div>";
				  echo "<div class='col-sm-3'><input type='text' name='follow_up_date[]' value='".$follow_up_dates[$i]."' class='form-control datepicker'></div>";
				  echo "<div class='col-sm-3'><input type='text' name='follow_up_who[]' value='".$follow_up_who_list[$i]."' class='form-control'></div>";
			  } ?>
			  </div>
			<?php } else { ?>

			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Follow Up Date:</label>
				<div class="col-sm-8">
				  <input name="ir14" value="<?php echo $ir14; ?>" type="text" id="name" class="datepicker">
				</div>
			  </div>

			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Assign Follow Up:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select Staff..." id="assign_followup" name="assign_followup" class="chosen-select-deselect form-control" width="380">
						<option value=""></option>
						<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
						foreach($staff_list as $id) {
							$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
							echo "<option ".(strpos(','.$assign_followup.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
						} ?>
					</select>
				</div>
			  </div>
			<?php } ?>
		<?php } ?>

		<?php if (strpos($value_config, ','."Corrective Action".',') === FALSE) { ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Corrective Action:</label>
			<div class="col-sm-8">
			  <textarea name="ir4" rows="4" cols="50" class="form-control" ><?php echo $ir4; ?></textarea>
			</div>
		  </div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Director Signature".',') === FALSE) { ?>
		  <?php if($director_sign != '') { ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Director Signature:</label>
			<div class="col-sm-8">
			<input type="hidden" name="director" value="<?= $director ?>">
			  <img src="<?php echo $from_safety; ?>download/sign_<?php echo $incidentreportid; ?>_director.png" width="190" height="80" border="0" alt="">
			  <input type="hidden" name="sign4" value='<?= $director_sign ?>'>
			</div>
			<label for="site_name" class="col-sm-4 control-label">Date:</label>
			<div class="col-sm-8">
			  <?php if(strpos($value_config, ','."Completed By Office_EditDates".',') !== FALSE) { ?>
				<input type="text" name="director" class="form-control datepicker" value="<?= $director ?>">
			  <?php } else {
			  	echo $director;
			  } ?>
			</div>
		  </div>
		   <?php } else { ?>
			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Director Sign-Off:</label>
				<div class="col-sm-8">
				  <?php include ('../phpsign/sign4.php'); ?>
				</div>
				<label for="site_name" class="col-sm-4 control-label">Date:</label>
				<div class="col-sm-8">
					<input type="text" name="director" class="form-control datepicker">
				</div>
			  </div>
		   <?php } ?>
		<?php } ?>

		</div>
	</div>
</div>
<?php } ?>

<script type="text/javascript">
$(document).on('change', 'select[name="type"]', function() { changeType(this); });
$(document).on('change', 'select[name="ticketid"]', function() { filterTickets(this); });
$(document).on('change', 'select[name="projectid"]', function() { filterTickets(this); });
$(document).on('change', 'select[name="project_type"]', function() { filterTickets(this); });
$(document).on('change', 'select[name="programid"]', function() { filterMembers(this); });
function filterTickets(sel) {
	if($(sel).attr('name') == 'ticketid') {
		var projectid = $(sel).find('option:selected').data('projectid');
		var projecttype = $(sel).find('option:selected').data('projecttype');
		$('select[name="projectid"]').val(projectid).trigger('change.select2');
		if(projecttype != '') {
			$('select[name="project_type"]').val(projecttype).trigger('change.select2');
		}
	} else if($(sel).attr('name') == 'projectid') {
		var projectid = $(sel).val();
		var projecttype = $(sel).find('option:selected').data('projecttype');
		$('select[name="ticketid"] option').hide().filter('[data-projectid="'+projectid+'"]').show();
		$('select[name="ticketid"]').trigger('change.select2');
		if(projecttype != '') {
			$('select[name="project_type"]').val(projecttype).trigger('change.select2');
		}
	} else if($(sel).attr('name') == 'project_type') {
		var projecttype = $(sel).val();
		if(projecttype != '') {
			$('select[name="projectid"] option').hide().filter('[data-projecttype="'+projecttype+'"]').show();
			$('select[name="projectid"]').trigger('change.select2');
			$('select[name="ticketid"] option').hide().filter('[data-projecttype="'+projecttype+'"]').show();
			$('select[name="ticketid"]').trigger('change.select2');
		} else {
			$('select[name="projectid"] option').show();
			$('select[name="projectid"]').trigger('change.select2');
			$('select[name="ticketid"] option').show();
			$('select[name="ticketid"]').trigger('change.select2');
		}
	}
}
function filterMembers(sel) {
	var programid = $(sel).val();
	$('select[name="memberid[]"] option').hide().filter('[data-businessid="'+programid+'"]').show()
	$('select[name="memberid[]"]').trigger('change.select2');
}
</script>