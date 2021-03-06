<?php
/*
Add	Sheet
*/
include ('../database_connection.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);
?>
<style>
.form-control {
    width: 40%;
    display: inline;
}
</style>
<script type="text/javascript">
	$(document).ready(function(){
        $("#form1").submit(function( event ) {
            var jobid = $("#jobid").val();
            var contactid = $("input[name=contactid]").val();
            var job_location = $("input[name=location]").val();
            if (contactid == '' || job_location == '') {
                //alert("Please make sure you have filled in all of the required fields.");
                //return false;
            }
        });
    });
</script>
</head>
<body>

<?php
$today_date = date('Y-m-d');
$contactid = $_SESSION['contactid'];
$location = '';
$project_number = '';
$supervisor = '';
$item_discussed = '';

    if(!empty($_GET['formid'])) {
        $formid = $_GET['formid'];

        echo '<input type="hidden" name="fieldlevelriskid" value="'.$formid.'">';

        $get_field_level = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety_toolbox_safety_meeting WHERE fieldlevelriskid='$formid'"));

        $today_date = $get_field_level['today_date'];
        $project_number = $get_field_level['project_number'];
        $contactid = $get_field_level['contactid'];
        $location = $get_field_level['location'];
        $supervisor = $get_field_level['supervisor'];
        $item_discussed = $get_field_level['item_discussed'];
        
    }
?>

<?php
//$form_config = ','.get_config($dbc, 'safety_field_level_risk_assessment').',';
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_safety WHERE tab='$tab' AND form='$form'"));
$form_config = ','.$get_field_config['fields'].',';
?>

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
                    Information<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_info" class="panel-collapse collapse">
            <div class="panel-body">

                <?php if (strpos($form_config, ','."fields1".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Date/Time:</label>
                    <div class="col-sm-8">
                        <input type="text" name="today_date" value="<?php echo $today_date; ?>" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

				<?php if (strpos($form_config, ','."fields2".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Project/Job Number:</label>
                    <div class="col-sm-8">
                        <input name="project_number" value="<?php echo $project_number; ?>" type="text" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

                <?php if (strpos($form_config, ','."fields3".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Location of Work:</label>
                    <div class="col-sm-8">
                        <input name="location" value="<?php echo $location; ?>" type="text" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

				<?php if (strpos($form_config, ','."fields4".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Supervisor:</label>
                    <div class="col-sm-8">
                        <input name="supervisor" value="<?php echo $supervisor; ?>" type="text" class="form-control" />
                    </div>
                  </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php if (strpos($form_config, ','."fields5".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info1" >
                    Item Discussed<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_info1" class="panel-collapse collapse">
            <div class="panel-body">

                  <div class="form-group">
                    <label for="first_name[]" class="col-sm-4 control-label">Item Discussed:</label>
                    <div class="col-sm-8">
                      <textarea name="item_discussed" rows="5" cols="50" class="form-control"><?php echo $item_discussed; ?></textarea>
                    </div>
                  </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if(!empty($_GET['formid'])) {
    	$sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$formid' AND safetyid='$safetyid'");
        $sa_inc=  0;
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_sa = $row_sa['assign_staff'];
            $assign_staff_id = $row_sa['safetyattid'];
            $assign_staff_done = $row_sa['done'];
            ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sa<?php echo $sa_inc;?>" >
                    <?php echo $assign_staff_sa; ?><span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_sa<?php echo $sa_inc;?>" class="panel-collapse collapse">
            <div class="panel-body">

                <?php
                if($assign_staff_done == 0) { ?>

                <?php if (strpos($assign_staff_sa, 'Extra') !== false) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Name:</label>
                    <div class="col-sm-8">
                        <input name="assign_staff_<?php echo $assign_staff_id;?>" type="text" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

               <div class="form-group">
                <label for="business_street" class="col-sm-4 control-label">Employer:</label>
                <div class="col-sm-8">
                    <input name="staffcheck_<?php echo $assign_staff_id;?>[]" type="text" class="form-control" />
                </div>
              </div>

            <?php $output_name = 'sign_'.$assign_staff_id;
            include('../phpsign/sign_multiple.php'); ?>

            <?php } else {
                echo '<img src="toolbox_safety_meeting/download/safety_'.$assign_staff_id.'.png">';
            } ?>

            </div>
        </div>
    </div>
    <?php $sa_inc++;
        }
    } ?>

</div>