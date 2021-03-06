<script>
$(document).ready(function() {
    $('.all_inventory').hide();
    $('.inventory_heading').hide();

	$('.order_list_inventory').on( 'click', function () {
        var pro_type = $(this).attr("id");

        $('.all_inventory').hide();
        $('.'+pro_type).show();
        $('.inventory_heading').show();

		$('.order_list_inventory').removeClass('active_tab');
        $(this).addClass('active_tab');
    });

	//Inventory
    var add_new_in = 1;
    $('#deleteinventory_0').hide();
    $('#add_row_in').on( 'click', function () {
        $('#deleteinventory_0').show();
        var clone = $('.additional_in').clone();
        clone.find('.form-control').val('');

        clone.find('#ininventorycat_0').attr('id', 'ininventorycat_'+add_new_in);
		clone.find('#ininventorycode_0').attr('id', 'ininventorycode_'+add_new_in);
		clone.find('#ininventorypn_0').attr('id', 'ininventorypn_'+add_new_in);
		clone.find('#ininventoryname_0').attr('id', 'ininventoryname_'+add_new_in);
		clone.find('#ininventorypart_0').attr('id', 'ininventorypart_'+add_new_in);
        clone.find('#inrp_0').attr('id', 'inrp_'+add_new_in);
        clone.find('#inap_0').attr('id', 'inap_'+add_new_in);
        clone.find('#inwp_0').attr('id', 'inwp_'+add_new_in);
        clone.find('#incomp_0').attr('id', 'incomp_'+add_new_in);
        clone.find('#incp_0').attr('id', 'incp_'+add_new_in);
        clone.find('#inmsrp_0').attr('id', 'inmsrp_'+add_new_in);
		clone.find('#infinalprice_0').attr('id', 'infinalprice_'+add_new_in);
		clone.find('#inestimateprice_0').attr('id', 'inestimateprice_'+add_new_in);
		clone.find('#inestimateqty_0').attr('id', 'inestimateqty_'+add_new_in);
		clone.find('#inestimateunit_0').attr('id', 'inestimateunit_'+add_new_in);
		clone.find('#inestimatetotal_0').attr('id', 'inestimatetotal_'+add_new_in);

        clone.find('#inc_0').attr('id', 'inc_'+add_new_in);
        clone.find('#inprofit_0').attr('id', 'inprofit_'+add_new_in);
        clone.find('#inprofitmargin_0').attr('id', 'inprofitmargin_'+add_new_in);

        clone.find('#inventory_0').attr('id', 'inventory_'+add_new_in);
        clone.find('#deleteinventory_0').attr('id', 'deleteinventory_'+add_new_in);
        $('#deleteinventory_0').hide();

        clone.removeClass("additional_in");
        $('#add_here_new_in').append(clone);

        resetChosen($("#ininventorycat_"+add_new_in));
        resetChosen($("#ininventorycode_"+add_new_in));
        resetChosen($("#ininventorypn_"+add_new_in));
        resetChosen($("#ininventoryname_"+add_new_in));
		resetChosen($("#ininventorypart_"+add_new_in));

        add_new_in++;

        return false;
    });

	var add_new_in_misc = 1;
	$('#deleteinventorymisc_0').hide();
	$('#add_row_in_misc').on( 'click', function () {

		$('#deleteinventorymisc_0').show();
        var clone_misc = $('.additional_in_misc').clone();
        clone_misc.find('.form-control').val('');
		clone_misc.find('#inid_misc_0').attr('id', 'inid_misc_'+add_new_in_misc);
        clone_misc.find('#intype_misc_0').attr('id', 'intype_misc_'+add_new_in_misc);
		clone_misc.find('#indisc_misc_0').attr('id', 'indisc_misc_'+add_new_in_misc);
		clone_misc.find('#inuom_misc_0').attr('id', 'inuom_misc_'+add_new_in_misc);
		clone_misc.find('#inheadmisc_0').attr('id', 'inheadmisc_'+add_new_in_misc);
		clone_misc.find('#incostmisc_0').attr('id', 'incostmisc_'+add_new_in_misc);
		clone_misc.find('#inqtymisc_0').attr('id', 'inqtymisc_'+add_new_in_misc);
		clone_misc.find('#intotalmisc_0').attr('id', 'intotalmisc_'+add_new_in_misc);
		clone_misc.find('#inestimatepricemisc_0').attr('id', 'inestimatepricemisc_'+add_new_in_misc);
		clone_misc.find('#inprofitmisc_0').attr('id', 'inprofitmisc_'+add_new_in_misc);
		clone_misc.find('#inmarginmisc_0').attr('id', 'inmarginmisc_'+add_new_in_misc);
        clone_misc.find('#inventorymisc_0').attr('id', 'inventorymisc_'+add_new_in_misc);
        clone_misc.find('#deleteinventorymisc_0').attr('id', 'deleteinventorymisc_'+add_new_in_misc);
        $('#deleteinventorymisc_0').hide();

        clone_misc.removeClass("additional_in_misc");

        $('#add_here_new_in_misc').append(clone_misc);

        add_new_in_misc++;

        return false;
    });
});
$(document).on('change', 'select.inv_cat_onchange', function() { selectInventoryCategory(this); });
$(document).on('change', 'select.inv_partno_onchange', function() { selectInventoryCodePartNo(this); });
$(document).on('change', 'select.inv_partname_onchange', function() { selectInventoryCodePartName(this); });

//Inventory
function selectInventoryCategory(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "estimate_ajax_all.php?fill=in_cat_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#ininventoryname_"+arr[1]).html(response);
			$("#ininventoryname_"+arr[1]).trigger("change.select2");
		}
	});
	$.ajax({
		type: "GET",
		url: "estimate_ajax_all.php?fill=in_cat_config_partno&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#ininventorypart_"+arr[1]).html(response);
			$("#ininventorypart_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectInventoryCodePartName(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "estimate_ajax_all.php?fill=in_code_part_name_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*FFM*');
            $("#inrp_"+arr[1]).val(result[0]);
            $("#inap_"+arr[1]).val(result[1]);
            $("#inwp_"+arr[1]).val(result[2]);
            $("#incomp_"+arr[1]).val(result[3]);
            $("#incp_"+arr[1]).val(result[4]);
            $("#inmsrp_"+arr[1]).val(result[5]);
			$("#infinalprice_"+arr[1]).val(result[6]);
            $("#inc_"+arr[1]).val(result[7]);
		}
	});
	$.ajax({
		type: "GET",
		url: "estimate_ajax_all.php?fill=in_code_part_name_config_number&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#ininventorypart_"+arr[1]).html(response);
			$("#ininventorypart_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectInventoryCodePartNo(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "estimate_ajax_all.php?fill=in_code_part_no_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*FFM*');
            $("#inrp_"+arr[1]).val(result[0]);
            $("#inap_"+arr[1]).val(result[1]);
            $("#inwp_"+arr[1]).val(result[2]);
            $("#incomp_"+arr[1]).val(result[3]);
            $("#incp_"+arr[1]).val(result[4]);
            $("#inmsrp_"+arr[1]).val(result[5]);
			$("#infinalprice_"+arr[1]).val(result[6]);
		}
	});
	$.ajax({
		type: "GET",
		url: "estimate_ajax_all.php?fill=in_code_part_no_config_name&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#ininventoryname_"+arr[1]).html(response);
			$("#ininventoryname_"+arr[1]).trigger("change.select2");
		}
	});
}

function fillmargincrcinventoryvalue(est) {
	var idarray = est.id.split("_");
    var profitid = 'crc_inventory_profit_' + idarray[3];
    var profitmarginid = 'crc_inventory_margin_' + idarray[3];
    var pcid = 'crc_inventory_cost_' + idarray[3];
    var pcvalue = jQuery('#'+pcid).val();
    var pestimatevalue = est.value;
    var qty = jQuery('#crc_inventory_qty_' + idarray[3]).val();
    if(qty == '' || qty == null) {
        jQuery('#crc_inventory_qty_' + idarray[3]).val(1);
        qty = 1;
    }
    if(parseInt(pestimatevalue) < parseInt(pcvalue)) {
        jQuery('#'+profitid).val('');
        jQuery('#'+profitmarginid).val('');
    }
    else if(typeof pcvalue != 'undefined' && pcvalue != null && pcvalue != '' && pestimatevalue != null && pestimatevalue != '') {
        var deltavalue = (pestimatevalue - pcvalue) * qty;
        var deltaper = (deltavalue / (pestimatevalue * qty)) * 100;
        if(deltavalue > 0) {
            jQuery('#'+profitid).val(deltavalue);
            jQuery('#'+profitmarginid).val(deltaper.toFixed(2));
        }
    }

    changeInventoryTotal();
}

function countInventory(txb) {
    if(txb != 'delete') {
        var get_id = txb.id;

        var split_id = get_id.split('_');

        var lbqty = $('#inestimateqty_'+split_id[1]).val();
        if(lbqty == null || lbqty == '') {
            lbqty = 1;
        }

        document.getElementById('inestimatetotal_'+split_id[1]).value = parseFloat($('#inestimateprice_'+split_id[1]).val() * lbqty);
    }

    var sum_fee = 0;
    //$('[name="inestimatetotal[]"]').each(function () {
        sum_fee += +document.getElementById('inestimatetotal_'+split_id[1]).value || 0;
    //});
    $('[name="crc_inventory_total[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    sum_fee += +$('[name="inventory_total"]').val();
    $('[name="inventory_total"]').val(round2Fixed(sum_fee));
    $('[name="inventory_summary"]').val(round2Fixed(sum_fee));

    var inventory_budget = $('[name="inventory_budget"]').val();
    if(inventory_budget >= sum_fee) {
        $('[name="inventory_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="inventory_total"]').css("background-color", "#ff9999"); // Green
    }
}

function fillmarginvalueInventory(est) {
    var idarray = est.id.split("_");
    var profitid = 'inprofit_' + idarray[1];
    var profitmarginid = 'inprofitmargin_' + idarray[1];
    var pcid = 'inc_' + idarray[1];
    var pcvalue = jQuery('#'+pcid).val();
    var pestimatevalue = est.value;
    var qty = jQuery('#inestimateqty_' + idarray[1]).val();
    if(qty == '' || qty == null) {
        jQuery('#inestimateqty_' + idarray[1]).val(1);
        qty = 1;
    }
    if(parseInt(pestimatevalue) < parseInt(pcvalue)) {
        jQuery('#'+profitid).val('');
        jQuery('#'+profitmarginid).val('');
    }
    else if(typeof pcvalue != 'undefined' && pcvalue != null && pcvalue != '' && pestimatevalue != null && pestimatevalue != '') {
        var deltavalue = (pestimatevalue - pcvalue) * qty;
        var deltaper = (deltavalue / (pestimatevalue * qty)) * 100;
        if(deltavalue > 0) {
            jQuery('#'+profitid).val(deltavalue);
            jQuery('#'+profitmarginid).val(deltaper.toFixed(2));
        }
    }

    changeInventoryTotal();
}

function fillmarginmiscvalueInventory(est) {
	var idarray = est.id.split("_");
    var profitid = 'inprofitmisc_' + idarray[1];
    var profitmarginid = 'inmarginmisc_' + idarray[1];
    var pcid = 'incostmisc_' + idarray[1];
    var pcvalue = jQuery('#'+pcid).val();
    var pestimatevalue = est.value;
    var qty = jQuery('#inqtymisc_' + idarray[1]).val();
    if(qty == '' || qty == null) {
        jQuery('#inqtymisc_' + idarray[1]).val(1);
        qty = 1;
    }
    if(parseInt(pestimatevalue) < parseInt(pcvalue)) {
        jQuery('#'+profitid).val('');
        jQuery('#'+profitmarginid).val('');
    }
    else if(typeof pcvalue != 'undefined' && pcvalue != null && pcvalue != '' && pestimatevalue != null && pestimatevalue != '') {
        var deltavalue = (pestimatevalue - pcvalue) * qty;
        var deltaper = (deltavalue / (pestimatevalue * qty)) * 100;
        if(deltavalue > 0) {
            jQuery('#'+profitid).val(deltavalue);
            jQuery('#'+profitmarginid).val(deltaper.toFixed(2));
        }
    }
	changeInventoryTotal();
}

function qtychangevalueInventory(qty) {
    var idarray = qty.id.split("_");
    var profitid = 'inprofit_' + idarray[1];
    var profitmarginid = 'inprofitmargin_' + idarray[1];
    var pestimateid = 'inestimateprice_' + idarray[1];
    var pcid = 'inc_' + idarray[1];
    var del = (jQuery('#'+pestimateid).val() - jQuery('#'+pcid).val()) * qty.value;
    var delper = (del / (jQuery('#'+pestimateid).val() * qty.value)) * 100;
    jQuery('#'+profitid).val(del);
    jQuery('#'+profitmarginid).val(delper.toFixed(2));
    changeInventoryTotal();
}

function qtychangemiscvalueInventory(qty) {
    var idarray = qty.id.split("_");
    var profitid = 'inprofitmisc_' + idarray[1];
    var profitmarginid = 'inmarginmisc_' + idarray[1];
    var pestimateid = 'inestimatepricemisc_' + idarray[1];
    var pcid = 'incostmisc_' + idarray[1];
    var del = (jQuery('#'+pestimateid).val() - jQuery('#'+pcid).val()) * qty.value;
    var delper = (del / (jQuery('#'+pestimateid).val() * qty.value)) * 100;
    jQuery('#'+profitid).val(del);
    jQuery('#'+profitmarginid).val(delper.toFixed(2));
	changeInventoryTotal();
}

function qtychangecrcvalueInventory(qty) {
    var idarray = qty.id.split("_");
    var profitid = 'crc_inventory_profit_' + idarray[3];
    var profitmarginid = 'crc_inventory_margin_' + idarray[3];
    var pestimateid = 'crc_inventory_custprice_' + idarray[3];
    var pcid = 'crc_inventory_cost_' + idarray[3];
    var del = (jQuery('#'+pestimateid).val() - jQuery('#'+pcid).val()) * qty.value;
    var delper = (del / (jQuery('#'+pestimateid).val() * qty.value)) * 100;
    jQuery('#'+profitid).val(del);
    jQuery('#'+profitmarginid).val(delper.toFixed(2));
	changeInventoryTotal();
}

function changeInventoryTotal() {
    var sum_profit = 0;
    var sum_profit_margin = 0;
    var misc_profit_margin = 0;
    var crc_profit_fee = 0;
    var crc_margin_fee = 0;
    jQuery('[name="inprofit[]"]').each(function () {
        sum_profit += +$(this).val() || 0;
    });

	jQuery('[name="inprofitmisc[]"]').each(function () {
        sum_profit += +$(this).val() || 0;
    });

    for(var loop = 0; loop < 500; loop++) {
        if(typeof $('[name="crc_inventory_profit_'+loop+'"]').val() !='undefined')
        {
            crc_profit_fee += +$('[name="crc_inventory_profit_'+loop+'"]').val();
        }
        else {
            break;
        }
    }

    sum_profit += +crc_profit_fee;

    var count = 0;
    jQuery('[name="inprofitmargin[]"]').each(function () {
        sum_profit_margin += +$(this).val() || 0;
		if(sum_profit_margin != 0)
			count++;
    });

	jQuery('[name="inmarginmisc[]"]').each(function () {
        sum_profit_margin += +$(this).val() || 0;
        misc_profit_margin += +$(this).val() || 0;
        if(misc_profit_margin != 0)
        count++;
    });

    for(var loop = 0; loop < 500; loop++) {
        if(typeof $('[name="crc_inventory_margin_'+loop+'"]').val() !='undefined')
        {
            var temp_margin = 0;
            temp_margin = $('[name="crc_inventory_margin_'+loop+'"]').val();
            if(temp_margin != 0 && temp_margin != '') {
                crc_margin_fee += +temp_margin;
                count++;
            }
        }
        else {
            break;
        }
    }

    sum_profit_margin += crc_margin_fee;

    per_profit_margin = sum_profit_margin / count;

    jQuery('#inventory_profit').val(round2Fixed(sum_profit));
    jQuery('#inventory_profit_margin').val(round2Fixed(per_profit_margin));
}

function countRCTotalInventory(sel) {
	var stage = sel.value;
	var typeId = sel.id;

	var arr = typeId.split('_');
    var del = (jQuery('#crc_inventory_custprice_'+arr[3]).val() * jQuery('#crc_inventory_qty_'+arr[3]).val());

    jQuery('#crc_inventory_total_'+arr[3]).val(round2Fixed(del));

    var sum_fee = 0;
    var crc_sum_fee = 0;
    $('[name="inestimatetotal[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });
    for(var loop = 0; loop < 500; loop++) {
        if(typeof $('[name="crc_inventory_total_'+loop+'"]').val() !='undefined')
        {
            crc_sum_fee += +$('[name="crc_inventory_total_'+loop+'"]').val();
        }
        else {
            break;
        }
    }

    sum_fee += +crc_sum_fee;

    $('[name="inventory_total"]').val(round2Fixed(sum_fee));

}
function countMiscInventory(txb)
{

	var get_id = txb.id;

	var split_id = get_id.split('_');
	if(split_id[0] == 'inestimatepricemisc') {
		var estqty = $('#inqtymisc_'+split_id[1]).val();
		if(estqty == null || estqty == '') {
			estqty = 1;
			document.getElementById('inqtymisc_'+split_id[1]).value = 1;
		}

		document.getElementById('intotalmisc_'+split_id[1]).value = parseFloat($('#inestimatepricemisc_'+split_id[1]).val() * estqty);
	}

	if(split_id[0] == 'inqtymisc') {
		var estqty = txb.value;
		if(estqty == null || estqty == '') {
			estqty = 1;
			document.getElementById('inqtymisc_'+split_id[1]).value = 1;
		}

		document.getElementById('intotalmisc_'+split_id[1]).value = parseFloat($('#inestimatepricemisc_'+split_id[1]).val() * estqty);
	}

    var sum_fee = 0;
	//$('[name="intotalmisc[]"]').each(function () {
    sum_fee += +document.getElementById('intotalmisc_'+split_id[1]).value || 0;
    //});

    sum_fee += +$('[name="inventory_total"]').val();
    $('[name="inventory_total"]').val(round2Fixed(sum_fee));
    $('[name="inventory_summary"]').val(round2Fixed(sum_fee));

    var inventory_budget = $('[name="inventory_budget"]').val();
    if(inventory_budget >= sum_fee) {
        $('[name="inventory_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="inventory_total"]').css("background-color", "#ff9999"); // Green
    }
}
function changeProfitInventoryPrice(profit)
{
    var get_id = profit.id;
    var split_id = get_id.split('_');
    jQuery('#inestimateqty_' + split_id[1]).val(1);
    qty = 1;
    pcost = 'inc_' + split_id[1];
    pestimateid = 'inestimateprice_' + split_id[1];
    ptotal = 'intotalmisc_' + split_id[1];
    profitid = 'inprofit_' + split_id[1];
    marginid = 'inprofitmargin_' + split_id[1];
    var estimateValue = 0;
    if(jQuery('#'+pcost).val() != '') {
        if(split_id[0] == 'inprofit')
        {
            estimateValue = parseInt(profit.value) + parseInt(jQuery('#'+pcost).val());
            var deltaper = (profit.value / (estimateValue * qty)) * 100;
            jQuery('#'+pestimateid).val(estimateValue.toFixed(2));
            jQuery('#'+ptotal).val(estimateValue.toFixed(2));
            jQuery('#'+marginid).val(deltaper.toFixed(2));
        }

        if(split_id[0] == 'inprofitmargin')
        {
            estimateValue = (parseInt(jQuery('#' + pcost).val()) * 100 / (100 - parseInt(profit.value)));
            var deltavalue = estimateValue - parseInt(jQuery('#'+pcost).val());
            jQuery('#'+pestimateid).val(estimateValue.toFixed(2));
            jQuery('#'+ptotal).val(estimateValue.toFixed(2));
            jQuery('#'+profitid).val(deltavalue.toFixed(2));
        }
    }

    changeInventoryTotal();
}

function changeProfitInventoryRCPrice(profit)
{
    var get_id = profit.id;
    var split_id = get_id.split('_');
    jQuery('#crc_inventory_qty_' + split_id[3]).val(1);
    qty = 1;
    pcost = 'crc_inventory_cost_' + split_id[3];
    pestimateid = 'crc_inventory_custprice_' + split_id[3];
    ptotal = 'crc_inventory_total_' + split_id[3];
    profitid = 'crc_inventory_profit_' + split_id[3];
    marginid = 'crc_inventory_margin_' + split_id[3];
    var estimateValue = 0;
    if(jQuery('#'+pcost).val() != '') {
        if(split_id[2] == 'profit')
        {
            estimateValue = parseInt(profit.value) + parseInt(jQuery('#'+pcost).val());
            var deltaper = (profit.value / (estimateValue * qty)) * 100;
            jQuery('#'+pestimateid).val(estimateValue.toFixed(2));
            jQuery('#'+ptotal).val(estimateValue.toFixed(2));
            jQuery('#'+marginid).val(deltaper.toFixed(2));

        }

        if(split_id[2] == 'margin')
        {

            estimateValue = (parseInt(jQuery('#' + pcost).val()) * 100 / (100 - parseInt(profit.value)));
            var deltavalue = estimateValue - parseInt(jQuery('#'+pcost).val());
            jQuery('#'+pestimateid).val(estimateValue.toFixed(2));
            jQuery('#'+ptotal).val(estimateValue.toFixed(2));
            jQuery('#'+profitid).val(deltavalue.toFixed(2));
        }
    }

    changeInventoryTotal();
}

function changeProfitInventoryMiscPrice(profit)
{
    var get_id = profit.id;
    var split_id = get_id.split('_');
    jQuery('#inqtymisc_' + split_id[1]).val(1);
    qty = 1;
    pcost = 'incostmisc_' + split_id[1];
    pestimateid = 'inestimatepricemisc_' + split_id[1];
    ptotal = 'intotalmisc_' + split_id[1];
    profitid = 'inprofitmisc_' + split_id[1];
    marginid = 'inmarginmisc_' + split_id[1];
    var estimateValue = 0;
    if(jQuery('#'+pcost).val() != '') {
        if(split_id[0] == 'inprofitmisc')
        {
            estimateValue = parseInt(profit.value) + parseInt(jQuery('#'+pcost).val());
            var deltaper = (profit.value / (estimateValue * qty)) * 100;
            jQuery('#'+pestimateid).val(estimateValue.toFixed(2));
            jQuery('#'+ptotal).val(estimateValue.toFixed(2));
            jQuery('#'+marginid).val(deltaper.toFixed(2));

        }

        if(split_id[0] == 'inmarginmisc')
        {

            estimateValue = (parseInt(jQuery('#' + pcost).val()) * 100 / (100 - parseInt(profit.value)));
            var deltavalue = estimateValue - parseInt(jQuery('#'+pcost).val());
            jQuery('#'+pestimateid).val(estimateValue.toFixed(2));
            jQuery('#'+ptotal).val(estimateValue.toFixed(2));
            jQuery('#'+profitid).val(deltavalue.toFixed(2));
        }
    }

    changeInventoryTotal();
}
</script>
<?php
$get_field_config_inventory = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(inventory_dashboard SEPARATOR ',') AS inventory FROM field_config_inventory"));
$field_config_inventory = ','.$get_field_config_inventory['inventory'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix">
            <?php if (strpos($base_field_config, ','."Inventory Category".',') !== FALSE) { ?>
            <label class="col-sm-2 text-center">Category</label>
            <?php } ?>
			 <?php if (strpos($base_field_config, ','."Inventory Part No".',') !== FALSE) { ?>
			<label class="col-sm-2 text-center">Part Number</label>
			 <?php } ?>
            <label class="col-sm-2 text-center">Product Name</label>
            <?php if (strpos($field_config_inventory, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
           <?php if (strpos($field_config_inventory, ','."Cost".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Cost</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center">Bid Price</label>
            <label class="col-sm-1 text-center">Quantity</label>
            <label class="col-sm-1 text-center">UOM</label>
            <label class="col-sm-1 text-center">Total</label>
            <label class="col-sm-1 text-center">$ Profit</label>
            <label class="col-sm-1 text-center">% Margin</label>
        </div>

        <?php
        $get_inventory = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_inventory'),'**#**');
                $get_inventory  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_inventory'),'**#**');
                $get_inventory  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_inventory'),'**#**');
                $get_inventory  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['estimateid'])) {
            $inventory = $get_contact['inventory'];
            $each_data = explode('**',$inventory);
            foreach($each_data as $id_all) {
                if($id_all != '') {
                    $data_all = explode('#',$id_all);
                    $get_inventory .= '**'.$data_all[0].'#'.$data_all[2].'#'.$data_all[1].'#'.$data_all[3].'#'.$data_all[4].'#'.$data_all[5];
                }
            }
        }
        $final_total_inventory = 0;
        $final_total_inventory_profit = 0;
        $final_total_inventory_margin = 0;
        ?>

        <?php if(!empty($get_inventory)) {
            $each_assign_inventory = explode('**',$get_inventory);
            $total_count = mb_substr_count($get_inventory,'**');
            $id_loop = 500;
			$inv_cost_field = get_config($dbc,'inventory_cost');
            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {

                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $inventoryid = '';
                $qty = '';
                $unit = '';
                $est = '';
                if(isset($each_item[0])) {
                    $inventoryid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $qty = $each_item[1];
                }
                if(isset($each_item[2])) {
                    $est = $each_item[2];
                }
                if(isset($each_item[3])) {
                    $unit = $each_item[3];
                }
                if(isset($each_item[4])) {
                    $profit = $each_item[4];
                }
                if(isset($each_item[5])) {
                    $margin = $each_item[5];
                }
                $total = $qty*$est;
                $final_total_inventory += $total;

                if($inventoryid != '') {
                    $inventory = explode('**', $get_rc['inventory']);
                    $rc_price = 0;
                    foreach($inventory as $pp){
                        if (strpos('#'.$pp, '#'.$inventoryid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>

            <div class="form-group clearfix" id="<?php echo 'inventory_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."Inventory Category".',') !== FALSE) { ?>
                <div class="col-sm-2">
                    <select data-placeholder="Choose a Category..." id="<?php echo 'ininventorycat_'.$id_loop; ?>" class="chosen-select-deselect form-control inventoryid inv_cat_onchange" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT category FROM inventory WHERE inventoryid = '$inventoryid' order by category");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_inventory($dbc, $inventoryid, 'category') == $row['category']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <?php if (strpos($base_field_config, ','."Inventory Part No".',') !== FALSE) { ?>
                    <div class="col-sm-2">
                    <select data-placeholder="Choose a Part Number..." id="<?php echo 'ininventorypart_'.$id_loop; ?>" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid inv_partno_onchange" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory WHERE inventoryid = '$inventoryid' order by part_no");
                        while($row = mysqli_fetch_array($query)) {
                            if ($inventoryid == $row['inventoryid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['inventoryid']."'>".$row['part_no'].'</option>';
                        }
                        ?>
                    </select>
                    </div>
                <?php } ?>
                <div class="col-sm-2">
                <select name='inventoryid[]' data-placeholder="Choose a Name..." id="<?php echo 'ininventoryname_'.$id_loop; ?>" class="chosen-select-deselect form-control inventoryid inv_partname_onchange" width="380">
                    <option value=''></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory WHERE inventoryid = '$inventoryid' order by name");
                    while($row = mysqli_fetch_array($query)) {
                        if ($inventoryid == $row['inventoryid']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $row['inventoryid']."'>".decryptIt($row['name']).'</option>';
                    }
                    ?>
                </select>
                </div>
                <?php if (strpos($field_config_inventory, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1">
                    <input name="inrp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'final_retail_price');?>" id="<?php echo 'inrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="inap[]" value="<?php echo get_inventory($dbc, $inventoryid, 'admin_price');?>" id="<?php echo 'inap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="inwp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'wholesale_price');?>" id="<?php echo 'inwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="incomp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'commercial_price');?>" id="<?php echo 'incomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="incp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'client_price');?>" id="<?php echo 'incp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="inmsrp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'msrp');?>" id="<?php echo 'inmsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Cost".',') !== FALSE) { ?>
                <div class="col-sm-1">
                    <input name="inc[]" value="<?php echo get_inventory($dbc, $inventoryid, $inv_cost_field);?>" id="<?php echo 'inc_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" >
                    <input name="infinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'infinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inestimateprice[]" value="<?php echo $est; ?>" id="<?php echo 'inestimateprice_'.$id_loop; ?>" onchange="countInventory(this); fillmarginvalueInventory(this);" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inestimateqty[]" id="<?php echo 'inestimateqty_'.$id_loop; ?>" onchange="countInventory(this); qtychangevalueInventory(this);" value="<?php echo $qty; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inestimateunit[]" id="<?php echo 'inestimateunit_'.$id_loop; ?>" value="<?php echo $unit; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inestimatetotal[]" value="<?php echo $total; ?>" id="<?php echo 'inestimatetotal_'.$id_loop; ?>" type="text" class="form-control" />
                </div>

                <div class="col-sm-1" >
                    <input name="inprofit[]" id="<?php echo 'inprofit_'.$id_loop; ?>" onchange="changeProfitInventoryPrice(this);" value="<?php echo $profit; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inprofitmargin[]" id="<?php echo 'inprofitmargin_'.$id_loop; ?>" onchange="changeProfitInventoryPrice(this);" value="<?php echo $margin; ?>" type="text" class="form-control" />
                </div>

                <div class="col-sm-1" >
                    <a href="#" onclick="deleteEstimate(this,'inventory_','ininventoryname_'); return false;" id="<?php echo 'deleteinventory_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_in clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="inventory_0">
                <?php if (strpos($base_field_config, ','."Inventory Category".',') !== FALSE) { ?>
                <div class="col-sm-2">
                    <select data-placeholder="Choose a Category..." id="ininventorycat_0" class="chosen-select-deselect form-control inventoryid inv_cat_onchange" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM inventory order by category");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
				<div class="col-sm-2"  <?php if (strpos($base_field_config, ','."Inventory Part No".',') == FALSE) { echo 'style="display:none"'; } ?>>
                    <select data-placeholder="Choose a Part Number..." id="ininventorypart_0" class="chosen-select-deselect form-control inventoryid inv_partno_onchange" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory order by part_no");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['inventoryid']."'>".$row['part_no'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select data-placeholder="Choose a Name..." id="ininventoryname_0" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid inv_partname_onchange" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory order by name");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['inventoryid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php if (strpos($field_config_inventory, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1">
                    <input name="inrp[]" id="inrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="inap[]" id="inap_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="inwp[]" id="inwp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="incomp[]" id="incomp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="incp[]" id="incp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" >
                    <input name="inmsrp[]" id="inmsrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>

                <?php if (strpos($field_config_inventory, ','."Cost".',') !== FALSE) { ?>
                <div class="col-sm-1">
                    <input name="inc[]" id="inc_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>

                <div class="col-sm-1" >
                    <input name="infinalprice[]" readonly id="infinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inestimateprice[]" id='inestimateprice_0' onchange="countInventory(this); fillmarginvalueInventory(this);" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inestimateqty[]" id='inestimateqty_0' onchange="countInventory(this); qtychangevalueInventory(this);" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inestimateunit[]" id='inestimateunit_0' type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inestimatetotal[]" id='inestimatetotal_0' type="text" class="form-control" />
                </div>

                <div class="col-sm-1" >
                    <input name="inprofit[]" id='inprofit_0' onchange="changeProfitInventoryPrice(this);" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <input name="inprofitmargin[]" id='inprofitmargin_0' onchange="changeProfitInventoryPrice(this);" type="text" class="form-control" />
                </div>

                <div class="col-sm-1" >
                    <a href="#" onclick="deleteEstimate(this,'inventory_','ininventoryname_'); return false;" id="deleteinventory_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_in"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_in" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>

		<div class="form-group clearfix" style="margin-left:5px">
			<h3>Misc Items</h3>
			<div class="form-group clearfix">
				<label class="col-sm-1 text-center">Type</label>
				<label class="col-sm-2 text-center">Heading</label>
				<label class="col-sm-1 text-center">Description</label>
				<label class="col-sm-1 text-center">UOM</label>
				<label class="col-sm-1 text-center">Cost</label>
				<label class="col-sm-1 text-center">Bid Price</label>
				<label class="col-sm-1 text-center">Quantity</label>
				<label class="col-sm-1 text-center">Total</label>
				<label class="col-sm-1 text-center">$ Profit</label>
				<label class="col-sm-1 text-center">% Margin</label>
			</div>
			<div class="additional_in_misc clearfix">
				<div class="clearfix"></div>

				<div class="form-group clearfix" id="inventorymisc_0">

					<div class="col-sm-1">
						<input name="intype_misc[]" id="intype_misc_0" type="text" class="form-control" />
					</div>

					<div class="col-sm-2">
						<input name="inheadmisc[]" id="inheadmisc_0" type="text" class="form-control" />
					</div>

					<div class="col-sm-1">
						<input name="indisc_misc[]" id="indisc_misc_0" type="text" class="form-control" />
					</div>

					<div class="col-sm-1">
						<input name="inuom_misc[]" id="inuom_misc_0" type="text" class="form-control" />
					</div>

					<div class="col-sm-1">
						<input name="incostmisc[]" id="incostmisc_0" type="text" class="form-control" />
					</div>


					<div class="col-sm-1">
						<input name="inestimatepricemisc[]" id="inestimatepricemisc_0" type="text" class="form-control" onchange="countMiscInventory(this); fillmarginmiscvalueInventory(this);" />
					</div>

					<div class="col-sm-1">
						<input name="inqtymisc[]" id="inqtymisc_0" type="text" class="form-control" onchange="countMiscInventory(this); qtychangemiscvalueInventory(this);" />
					</div>

					<div class="col-sm-1">
						<input name="intotalmisc[]" id="intotalmisc_0" type="text" class="form-control" />
					</div>

					<div class="col-sm-1">
						<input name="inprofitmisc[]" id="inprofitmisc_0" type="text" onchange="changeProfitInventoryMiscPrice(this);" class="form-control" />
					</div>

					<div class="col-sm-1">
						<input name="inmarginmisc[]" id="inmarginmisc_0" type="text" onchange="changeProfitInventoryMiscPrice(this);" class="form-control" />
					</div>

					<div class="col-sm-1" >
						<a href="#" onclick="deleteEstimate(this,'inventorymisc_','inheadmisc_'); return false;" id="deleteinventorymisc_0" class="btn brand-btn">Delete</a>
					</div>
				</div>
			</div>

			<div id="add_here_new_in_misc"></div>

			<div class="form-group triple-gapped clearfix">
				<div class="col-sm-offset-4 col-sm-8">
					<button id="add_row_in_misc" class="btn brand-btn pull-left">Add Row</button>
				</div>
			</div>
            <br>
            <?php
            $query_misc_rc = mysqli_query($dbc,"SELECT * FROM bid_misc WHERE accordion='Inventory' AND estimateid=" . $_GET['estimateid']);
            $misc_num_rows = mysqli_num_rows($query_misc_rc);
            if($misc_num_rows > 0) { ?>
                <div class="form-group clearfix products_misc_heading">
                    <label class="col-sm-2 text-center">Type</label>
                    <label class="col-sm-2 text-center">Heading</label>
                    <label class="col-sm-1 text-center">Description</label>
                    <label class="col-sm-1 text-center">UOM</label>
                    <label class="col-sm-1 text-center">Cost</label>
                    <label class="col-sm-1 text-center">Bid Price</label>
                    <label class="col-sm-1 text-center">Quantity</label>
                    <label class="col-sm-1 text-center">Total</label>
                    <label class="col-sm-1 text-center">$ Profit</label>
                    <label class="col-sm-1 text-center">% Margin</label>
                </div>
                <?php
            }

            $misc_rc = 0;
            while($misc_row_rc = mysqli_fetch_array($query_misc_rc)) { ?>
                <div class="clearfix"></div>

				<div class="form-group clearfix">
					<div class="col-sm-2">
						<input name="ptype_misc_display[]" id="ptype_misc" value="<?php echo $misc_row_rc['type'] ?>" readonly type="text" class="form-control" />
					</div>

					<div class="col-sm-2">
						<input name="pheadmisc_display[]" id="pheadmisc" value="<?php echo $misc_row_rc['heading'] ?>" type="text" readonly class="form-control" />
					</div>

					<div class="col-sm-1">
						<input name="pdisc_misc_display[]" id="pdisc_misc" type="text" value="<?php echo $misc_row_rc['description'] ?>" readonly class="form-control" />
					</div>

					<div class="col-sm-1">
						<input name="puom_misc_display[]" id="puom_misc" type="text" value="<?php echo $misc_row_rc['uom'] ?>" readonly class="form-control" />
					</div>

					<div class="col-sm-1">
						<input name="pcost_misc_display[]" id="pcost_misc" type="text" value="<?php echo $misc_row_rc['cost'] ?>" readonly class="form-control" />
					</div>


					<div class="col-sm-1">
						<input name="pestimatepricemisc_display[]" id="pestimatepricemisc" readonly value="<?php echo $misc_row_rc['estimate_price'] ?>" type="text" class="form-control" onchange="countMiscProduct(this);" />
					</div>

					<div class="col-sm-1">
						<input name="pqtymisc_display[]" id="pqtymisc" type="text" readonly class="form-control" value="<?php echo $misc_row_rc['qty'] ?>" onchange="countMiscProduct(this);" />
					</div>

					<div class="col-sm-1">
						<input name="ptotalmisc_display[]" id="ptotalmisc" value="<?php echo $misc_row_rc['total'] ?>" readonly type="text" class="form-control" />
					</div>

                    <div class="col-sm-1">
						<input name="pprofitmisc_display[]" id="pprofitmisc" value="<?php echo $misc_row_rc['profit'] ?>" readonly type="text" class="form-control" />
					</div>

                    <div class="col-sm-1">
						<input name="pmarginmisc_display[]" id="pmarginmisc" value="<?php echo $misc_row_rc['margin'] ?>" readonly type="text" class="form-control" />
					</div>
				</div>
            <?php
                $misc_rc++;
                $final_total_misc_inventory += $misc_row_rc['total'];
            }
            ?>
		</div>
    </div>
</div>

<?php
if(!empty($_GET['estimateid'])) {

    $querxy = mysqli_query ($dbc, "SELECT DISTINCT(rate_card_types) FROM company_rate_card WHERE (rate_card_name='$company_rate_card_name' AND IFNULL(`rate_categories`,'')='$company_rate_categories') AND tile_name='Inventory'");
    while($row = mysqli_fetch_array ($querxy)) {
        $no_space_rate_card_types = str_replace(' ', '', $row['rate_card_types']);
        ?>
        <a id="<?php echo $no_space_rate_card_types; ?>" class="btn brand-btn order_list_inventory mobile-100" ><?php echo $row['rate_card_types']; ?></a>
    <?php }

    $query_rc = mysqli_query($dbc,"SELECT * FROM company_rate_card WHERE (rate_card_name='$company_rate_card_name' AND IFNULL(`rate_categories`,'')='$company_rate_categories') AND tile_name='Inventory'");

    $num_rows = mysqli_num_rows($query_rc);
    if($num_rows > 0) { ?>
        <div class="form-group clearfix inventory_heading">
            <label class="col-sm-2 text-center">Type</label>
            <label class="col-sm-2 text-center">Heading</label>
            <label class="col-sm-1 text-center">Description</label>
            <label class="col-sm-1 text-center">UOM</label>
            <label class="col-sm-1 text-center">Cost</label>
            <label class="col-sm-1 text-center">Bid Price</label>
            <label class="col-sm-1 text-center">Quantity</label>
            <label class="col-sm-1 text-center">Total</label>
            <label class="col-sm-1 text-center">$ Profit</label>
            <label class="col-sm-1 text-center">% Margin</label>
        </div>
        <?php
    }
    $rc = 0;
    while($row_rc = mysqli_fetch_array($query_rc)) {

        $companyrcid = $row_rc['companyrcid'];

        $estimate_company_rate_card = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM bid_company_rate_card WHERE companyrcid='$companyrcid' AND estimateid='$estimateid'"));
        $no_space_rate_card_types = str_replace(' ', '', $row_rc['rate_card_types']);

        ?>
        <div class="form-group clearfix all_inventory <?php echo $no_space_rate_card_types; ?> rc_est_inventory_<?php echo $rc; ?>" width="100%">

            <input type="hidden" name="crc_inventory_companyrcid_<?php echo $rc; ?>" value="<?php echo $row_rc['companyrcid']; ?>" />

            <div class="col-sm-2">
                <input value= "<?php echo $row_rc['rate_card_types']; ?>" readonly="" name="crc_inventory_type_<?php echo $rc; ?>" type="text" class="form-control" />
            </div>
            <div class="col-sm-2">
                <input value= "<?php echo htmlspecialchars($row_rc['heading']); ?>" readonly="" name="crc_inventory_heading_<?php echo $rc; ?>" type="text" class="form-control" />
            </div>
            <div class="col-sm-1">
                <input value= "<?php echo $row_rc['description']; ?>" readonly="" name="crc_inventory_description_<?php echo $rc; ?>" type="text" class="form-control" />
            </div>
            <div class="col-sm-1">
                <input value= "<?php echo $row_rc['uom']; ?>" readonly="" name="crc_inventory_uom_<?php echo $rc; ?>" type="text" class="form-control" />
            </div>
            <div class="col-sm-1">
                <input value= "<?php echo $row_rc['cost']; ?>" readonly="" name="crc_inventory_cost_<?php echo $rc; ?>" id="crc_inventory_cost_<?php echo $rc; ?>" type="text" class="form-control" />
            </div>
            <div class="col-sm-1">
                <input value= "<?php echo $estimate_company_rate_card['cust_price']; ?>" onchange="fillmargincrcinventoryvalue(this); countRCTotalInventory(this)" name="crc_inventory_cust_price_<?php echo $rc; ?>" type="text" id="crc_inventory_custprice_<?php echo $rc;?>" class="form-control" />
            </div>
            <div class="col-sm-1">
                <input name="crc_inventory_qty_<?php echo $rc; ?>" value= "<?php echo $estimate_company_rate_card['qty']; ?>" type="text" onchange="qtychangecrcvalueInventory(this); countRCTotalInventory(this)" id="crc_inventory_qty_<?php echo $rc;?>" class="form-control crc_inventory_qty" />
            </div>
            <div class="col-sm-1">
                <input name="crc_inventory_total_<?php echo $rc; ?>" value= "<?php echo $estimate_company_rate_card['rc_total']; ?>"  id="crc_inventory_total_<?php echo $rc;?>" type="text" class="form-control" />
            </div>
            <div class="col-sm-1">
                <input name="crc_inventory_profit_<?php echo $rc; ?>" onchange="changeProfitInventoryRCPrice(this)" value= "<?php echo $estimate_company_rate_card['profit']; ?>"  id="crc_inventory_profit_<?php echo $rc;?>" type="text" class="form-control" />
            </div>
            <div class="col-sm-1">
                <input name="crc_inventory_margin_<?php echo $rc; ?>" onchange="changeProfitInventoryRCPrice(this)" value= "<?php echo $estimate_company_rate_card['margin']; ?>"  id="crc_inventory_margin_<?php echo $rc;?>" type="text" class="form-control" />
            </div>
        </div>

    <?php
        $rc++;
        $final_total_inventory += $estimate_company_rate_card['rc_total'];
        $final_total_inventory_profit += $estimate_company_rate_card['profit'];
        $final_total_inventory_margin += $estimate_company_rate_card['margin'];
    }
}
?>

<input type="hidden" name="total_rc_inventory" value="<?php echo $rc; ?>" />

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total $ Profit: </label>
    <div class="col-sm-8">
      <input name="inventory_profit" id="inventory_profit" value="<?php echo $final_total_inventory_profit; ?>" readonly="" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total % Margin: </label>
    <div class="col-sm-8">
      <input name="inventory_profit_margin" id="inventory_profit_margin" value="<?php echo $final_total_inventory_margin; ?>"  value="" readonly="" type="text" class="form-control">
    </div>
</div>

<!--
<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="inventory_budget" value="<?php echo $budget_price[9]; ?>" type="text" class="form-control">
    </div>
</div>
-->

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="inventory_total" value="<?php echo $final_total_inventory + $final_total_misc_inventory;?>" type="text" class="form-control">
    </div>
</div>
