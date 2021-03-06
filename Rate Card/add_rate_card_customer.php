<script>
$(document).ready(function() {
	//Customer
    $('#deletecustomer_0').hide();
    var add_new_cust = 1;
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
$(document).on('change', 'select[name="customerid[]"]', function() { selectCustomer(this); });
$(document).on('change', 'select[name="customerperson[]"]', function() { selectCustomer(this); });
//Customer
function selectCustomer(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({
		type: "GET",
		url: "ratecard_ajax_all.php?fill=cust_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*FFM*');
            $("#custrp_"+arr[1]).val(result[0]);
            $("#custap_"+arr[1]).val(result[1]);
            $("#custwp_"+arr[1]).val(result[2]);
            $("#custcomp_"+arr[1]).val(result[3]);
            $("#custcp_"+arr[1]).val(result[4]);
            $("#custmsrp_"+arr[1]).val(result[5]);

            $("#custcustomerid_"+arr[1]).html(result[6]);
			$("#custcustomerid_"+arr[1]).trigger("change.select2");
            $("#custcustomerperson_"+arr[1]).html(result[7]);
			$("#custcustomerperson_"+arr[1]).trigger("change.select2");
		}
	});
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
        </div>

        <?php if(!empty($_GET['ratecardid'])) {
            $each_customer = explode('**', $customer);
            $total_count = mb_substr_count($customer,'**');
            $id_loop = 500;
            for($pid_loop=0; $pid_loop<$total_count; $pid_loop++) {

                $contactid = '';

                if(isset($each_customer[$pid_loop])) {
                    $each_val = explode('#', $each_customer[$pid_loop]);
                    $contactid = $each_val[0];
                    $ratecardprice = $each_val[1];
                }

                if($contactid != '') {
            ?>
            <div class="form-group clearfix" id="<?php echo 'customer_'.$id_loop; ?>">
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Customer:</label>
                    <select data-placeholder="Choose a Customer..." id="<?php echo 'custcustomerid_'.$id_loop; ?>" name="customerid[]" class="chosen-select-deselect form-control equipmentid" width="380">
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
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Contact Person:</label>
                    <select data-placeholder="Choose a Contact..." id="<?php echo 'custcustomerperson_'.$id_loop; ?>" name="customerperson[]" class="chosen-select-deselect form-control" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Customer' AND deleted=0  order by first_name");
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
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="custrp[]" value="<?php echo get_contact($dbc, $contactid, 'final_retail_price');?>" id="<?php echo 'custrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="custap[]" value="<?php echo get_contact($dbc, $contactid, 'admin_price');?>" id="<?php echo 'custap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="custwp[]" value="<?php echo get_contact($dbc, $contactid, 'wholesale_price');?>" id="<?php echo 'custwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="custcomp[]" value="<?php echo get_contact($dbc, $contactid, 'commercial_price');?>" id="<?php echo 'custcomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="custcp[]" value="<?php echo get_contact($dbc, $contactid, 'client_price');?>" id="<?php echo 'custcp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="custmsrp[]" value="<?php echo get_contact($dbc, $contactid, 'msrp');?>" id="<?php echo 'custmsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="custfinalprice[]" value="<?php echo $ratecardprice;?>"  id="<?php echo 'custfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteRatecard(this,'customer_','custcustomerid_'); return false;" id="<?php echo 'deletecustomer_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
        <?php  $id_loop++;
                }
            }
        } ?>


        <div class="additional_cust clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="customer_0">
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Customer:</label>
                    <select data-placeholder="Choose a Customer..." id="custcustomerid_0" name="customerid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Customer' ORDER BY name");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Contact Person:</label>
                    <select data-placeholder="Choose a Contact..." id="custcustomerperson_0" name="customerperson[]" class="chosen-select-deselect form-control" width="380">
                        <option value=''></option>
						<?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Customer' AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
							foreach($query as $id) {
								$selected = '';
								//$selected = $id == $search_user ? 'selected = "selected"' : '';
								echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
							}
						  ?>
                    </select>
                </div>
                <?php if (strpos($field_config_customer, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="custrp[]" id="custrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="custap[]" id="custap_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="custwp[]" id="custwp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="custcomp[]" id="custcomp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="custcp[]" id="custcp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_customer, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="custmsrp[]" id="custmsrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="custfinalprice[]" id="custfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteRatecard(this,'customer_','custcustomerid_'); return false;" id="deletecustomer_0" class="btn brand-btn">Delete</a>
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
