<script>
$(document).ready(function() {
	//Customer
    var add_new_cust = 1;
    $('#deletecustomer_0').hide();
    $('#add_row_cust').on( 'click', function () {
        $('#deletecustomer_0').show();
        var clone = $('.additional_cust').clone();
        clone.find('.form-control').val('');

        clone.find('#custcustomerid_0').attr('id', 'custcustomerid_'+add_new_cust);
        clone.find('#custcustomerperson_0').attr('id', 'custcustomerperson_'+add_new_cust);
        clone.find('#custrp_0').attr('id', 'custrp_'+add_new_cust);
        clone.find('#custap_0').attr('id', 'custap_'+add_new_cust);
        clone.find('#custwp_0').attr('id', 'custwp_'+add_new_cust);
        clone.find('#custcomp_0').attr('id', 'custcomp_'+add_new_cust);
        clone.find('#custcp_0').attr('id', 'custcp_'+add_new_cust);
        clone.find('#custmsrp_0').attr('id', 'custmsrp_'+add_new_cust);
		clone.find('#custfinalprice_0').attr('id', 'custfinalprice_'+add_new_cust);
        clone.find('#customerest_0').attr('id', 'customerest_'+add_new_cust);

        clone.find('#customer_0').attr('id', 'customer_'+add_new_cust);
        clone.find('#deletecustomer_0').attr('id', 'deletecustomer_'+add_new_cust);
        $('#deletecustomer_0').hide();

        clone.removeClass("additional_cust");
        $('#add_here_new_cust').append(clone);

        resetChosen($("#custcustomerid_"+add_new_cust));
        resetChosen($("#custcustomerperson_"+add_new_cust));

        add_new_cust++;

        return false;
    });
});
//Customer
function selectCustomer(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "estimate_ajax_all.php?fill=cust_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*FFM*');
            $("#custrp_"+arr[1]).val(result[0]);
            $("#custap_"+arr[1]).val(result[1]);
            $("#custwp_"+arr[1]).val(result[2]);
            $("#custcomp_"+arr[1]).val(result[3]);
            $("#custcp_"+arr[1]).val(result[4]);
            $("#custmsrp_"+arr[1]).val(result[5]);
			$("#custfinalprice_"+arr[1]).val(result[6]);

            $("#custcustomerid_"+arr[1]).html(result[7]);
			$("#custcustomerid_"+arr[1]).trigger("change.select2");
            $("#custcustomerperson_"+arr[1]).html(result[8]);
			$("#custcustomerperson_"+arr[1]).trigger("change.select2");
		}
	});
}
function countCustomer() {
    var sum_fee = 0;
    $('[name="custestimateprice[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });
    $('[name="crc_customer_total[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });
    $('[name="customer_total"]').val('$'+round2Fixed(sum_fee));
    $('[name="customer_summary"]').val('$'+round2Fixed(sum_fee)).change();

    var customer_budget = $('[name="customer_budget"]').val();
    if(customer_budget >= sum_fee) {
        $('[name="customer_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="customer_total"]').css("background-color", "#ff9999"); // Green
    }
}

function countRCTotalCust(sel) {
	var stage = sel.value;
	var typeId = sel.id;

	var arr = typeId.split('_');
    var del = (jQuery('#crc_customer_custprice_'+arr[3]).val() * jQuery('#crc_customer_qty_'+arr[3]).val());
    jQuery('#crc_customer_total_'+arr[3]).val(round2Fixed(del));

    var sum_fee = 0;
    $('[name="custestimateprice[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });
    $('[name="crc_customer_total[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

	countCustomer();

}
</script>
<?php
$get_field_config_customer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT customer FROM field_config_contact"));
$field_config_customer = ','.$get_field_config_customer['customer'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <label class="col-sm-2 text-center">Customer</label>
            <label class="col-sm-2 text-center">Contact Person</label>
            <?php if (strpos($field_config_customer, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config_customer, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config_customer, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config_customer, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config_customer, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config_customer, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?= ESTIMATE_TILE ?> Price</label>
        </div>

        <?php
        $get_customer = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_customer'),'**#**');
                $get_customer  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_customer'),'**#**');
                $get_customer  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_customer'),'**#**');
                $get_customer  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['estimateid'])) {
            $customer = $get_contact['customer'];
            $each_data = explode('**',$customer);
            foreach($each_data as $id_all) {
                if($id_all != '') {
                    $data_all = explode('#',$id_all);
                    $get_customer .= '**'.$data_all[0].'#'.$data_all[1];
                }
            }
        }
        $final_total_customer = 0;
        ?>

        <?php if(!empty($get_customer)) {
            $each_assign_inventory = explode('**',$get_customer);
            $total_count = mb_substr_count($get_customer,'**');
            $id_loop = 500;
            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {

                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $contactid = '';
                $est = '';
                if(isset($each_item[0])) {
                    $contactid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $est = $each_item[1];
                }
                $final_total_customer += $est;
                if($contactid != '') {
                    $customer = explode('**', $get_rc['customer']);
                    $rc_price = 0;
                    foreach($customer as $pp){
                        if (strpos('#'.$pp, '#'.$contactid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>

            <div class="form-group clearfix" id="<?php echo 'customer_'.$id_loop; ?>" >
                <div class="col-sm-2">
                    <select onChange='selectCustomer(this)' data-placeholder="Choose a Customer..." id="<?php echo 'custcustomerid_'.$id_loop; ?>" name="customerid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Customer' ORDER BY name");
                        while($row = mysqli_fetch_array($query)) {
                            if ($contactid == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select onChange='selectCustomer(this)' data-placeholder="Choose a Customer..." id="<?php echo 'custcustomerperson_'.$id_loop; ?>" name="customerperson[]" class="chosen-select-deselect form-control" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Customer' AND deleted=0 order by first_name");
                        while($row = mysqli_fetch_array($query)) {
                            if ($contactid == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php if (strpos($field_config_customer, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1">
                    <input name="custrp[]" value="<?php echo get_contact($dbc, $contactid, 'final_retail_price');?>" id="<?php echo 'custrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custap[]" value="<?php echo get_contact($dbc, $contactid, 'admin_price');?>" id="<?php echo 'custap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custwp[]" value="<?php echo get_contact($dbc, $contactid, 'wholesale_price');?>" id="<?php echo 'custwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custcomp[]" value="<?php echo get_contact($dbc, $contactid, 'commercial_price');?>" id="<?php echo 'custcomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custcp[]" value="<?php echo get_contact($dbc, $contactid, 'client_price');?>" id="<?php echo 'custcp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custmsrp[]" value="<?php echo get_contact($dbc, $contactid, 'msrp');?>" id="<?php echo 'custmsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" >
                    <input name="custfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'custfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="custestimateprice[]" value="<?php echo $est; ?>" onchange="countCustomer()" id="<?php echo 'customerest_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteEstimate(this,'customer_','custcustomerid_'); return false;" id="<?php echo 'deletecustomer_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_cust clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="customer_0">
                <div class="col-sm-2">
                    <select onChange='selectCustomer(this)' data-placeholder="Choose a Customer..." id="custcustomerid_0" name="customerid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Customer' ORDER BY name");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select onChange='selectCustomer(this)' data-placeholder="Choose a Customer..." id="custcustomerperson_0" name="customerperson[]" class="chosen-select-deselect form-control" width="380">
                        <option value=''></option>
						<?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Customer' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
							foreach($query as $id) {
								$selected = '';
								//$selected = strpos(','.$foremanid.',', ','.$id.',') !== FALSE ? 'selected = "selected"' : '';
								echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
							}
						?>
                    </select>
                </div>
                <?php if (strpos($field_config_customer, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1">
                    <input name="custrp[]" id="custrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custap[]" id="custap_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custwp[]" id="custwp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custcomp[]" id="custcomp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custcp[]" id="custcp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="custmsrp[]" id="custmsrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" >
                    <input name="custfinalprice[]" readonly id="custfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="custestimateprice[]" onchange="countCustomer()" id="customerest_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteEstimate(this,'customer_','custcustomerid_'); return false;" id="deletecustomer_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_cust"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_cust" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>

<?php
if(!empty($_GET['estimateid'])) {
    $query_rc = mysqli_query($dbc,"SELECT * FROM company_rate_card WHERE ((rate_card_name='$company_rate_card_name' AND IFNULL(`rate_categories`,'')='$company_rate_categories' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')) OR $universal_rc_search) AND tile_name='Customer'");

    $num_rows = mysqli_num_rows($query_rc);
    if($num_rows > 0) { ?>
        <div class="form-group clearfix">
			<?php foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Heading':
					case 'Description':
					case 'Type':
						echo '<label class="col-sm-2 text-center">'.$data[1].'</label>';
						break;
					case 'UOM':
					case 'Quantity':
					case 'Cost':
					case 'Price':
					case 'Total':
						echo '<label class="col-sm-1 text-center">'.$data[1].'</label>';
						break;
				}
			} ?>
        </div>
        <?php
    }
    $rc = 0;
    while($row_rc = mysqli_fetch_array($query_rc)) {

        $companyrcid = $row_rc['companyrcid'];

        $estimate_company_rate_card = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM estimate_company_rate_card WHERE companyrcid='$companyrcid' AND estimateid='$estimateid'"));

        ?>
        <input type="hidden" name="crc_customer_companyrcid[]" value="<?php echo $row_rc['companyrcid']; ?>" />

        <div class="form-group clearfix" width="100%">
			<?php foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Heading': ?><div class="col-sm-2">
							<input value= "<?php echo htmlspecialchars($row_rc['heading']); ?>" readonly="" name="crc_customer_heading[]" type="text" class="form-control" />
						</div><?php
						break;
					case 'Description': ?><div class="col-sm-2">
							<input value= "<?php echo $row_rc['description']; ?>" readonly="" name="crc_customer_description[]" type="text" class="form-control" />
						</div><?php
						break;
					case 'Type': ?><div class="col-sm-2">
							<input value= "<?php echo $row_rc['rate_card_types']; ?>" readonly="" name="crc_customer_type[]" type="text" class="form-control" />
						</div><?php
						break;
					case 'UOM': ?><div class="col-sm-1">
							<input value= "<?php echo $row_rc['uom']; ?>" readonly="" name="crc_customer_uom[]" type="text" class="form-control" />
						</div><?php
						break;
					case 'Quantity': ?><div class="col-sm-1">
							<input name="crc_customer_qty[]" value= "<?php echo $estimate_company_rate_card['qty']; ?>" type="text" onchange="countRCTotalCust(this)" id="crc_customer_qty_<?php echo $rc;?>" class="form-control" />
						</div><?php
						break;
					case 'Cost': ?><div class="col-sm-1">
							<input value= "<?php echo $row_rc['cost']; ?>" readonly="" name="crc_customer_cost[]" type="text" class="form-control" />
						</div><?php
						break;
					case 'Price': ?><div class="col-sm-1">
							<input value= "<?php echo $estimate_company_rate_card['cust_price']; ?>" onchange="countRCTotalCust(this)" name="crc_customer_cust_price[]" type="text" id="crc_customer_custprice_<?php echo $rc;?>" class="form-control" />
						</div><?php
						break;
					case 'Total': ?><div class="col-sm-1">
							<input name="crc_customer_total[]" value= "<?php echo $estimate_company_rate_card['rc_total']; ?>"  id="crc_customer_total_<?php echo $rc;?>" type="text" class="form-control" />
						</div><?php
						break;
				}
			} ?>
        </div>

    <?php
        $rc++;
        $final_total_customer += $estimate_company_rate_card['rc_total'];
    }
}
?>

<!--
<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="customer_budget" value="<?php echo $budget_price[8]; ?>" type="text" class="form-control">
    </div>
</div>
-->

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="customer_total" value="<?php echo $final_total_customer;?>" type="text" class="form-control">
    </div>
</div>
