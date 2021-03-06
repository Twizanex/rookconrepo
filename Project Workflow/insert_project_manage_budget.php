<?php
$get_field_config_html = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(project_manage SEPARATOR ',') AS config_pdf FROM field_config_project_manage WHERE tile = '$tile' AND tab = '$tab' "));
$config_fields_quote = ','.$get_field_config_html['config_pdf'].',';

//Packages
$package = '';
$package_html = '';
$review_profit_loss = '';
$review_budget = '';

$financial_cost = 0;
$financial_price = 0;
$financial_plus_minus = 0;

$total_package = 0;
$j=0;
foreach ($_POST['packageid'] as $packageid_all) {
    if($packageid_all != '') {
        $package .= $packageid_all.'#'.$_POST['packageprojectprice'][$j].'**';
        $total_price += $_POST['packageprojectprice'][$j];
        $total_package += $_POST['packageprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM package WHERE packageid='$packageid_all'"));

        $package_html .= '<tr nobr="true">';
        $package_html .= '<td>Package</td>';

        $package_html .= '<td>';
        if (strpos($config_fields_quote, ','."Package Service Type".',') !== FALSE) {
            $package_html .= 'Service Type : '.$query['service_type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Package Category".',') !== FALSE) {
            $package_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Package Heading".',') !== FALSE) {
            $package_html .= 'Heading : '.$query['heading'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Package Description".',') !== FALSE) {
            $package_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Package Quote Description".',') !== FALSE) {
            $package_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $package_html .= '</td>';

        $package_html .= '<td>-</td>';
        $package_html .= '<td>-</td>';
        $package_html .= '<td>$'.$_POST['packageprojectprice'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['packageprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['packageprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['packageprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Packages</td><td>'.$query['service_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['packageprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['packageprojectprice'][$j];

        $temp_ticket_desc = '';
        if($query['service_type'] != '') {
            $temp_ticket_desc .= 'Service Type : '.$query['service_type'].'<br>';
        }
        if($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if($query['heading'] != '') {
            $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }

        $st = $query['service_type'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Package', '$packageid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_package != 0) {
    $review_budget .= '<tr><td>Packages</td><td>$'.$_POST['budget_price_0'].'</td> <td>$'.$total_package.'</td></tr>';
}

//Promotion
$promotion = '';
$promotion_html = '';
$total_promotion = 0;
$j=0;
foreach ($_POST['promotionid'] as $promotionid_all) {
    if($promotionid_all != '') {
        $promotion .= $promotionid_all.'#'.$_POST['promotionprojectprice'][$j].'**';
        $total_price += $_POST['promotionprojectprice'][$j];
        $total_promotion += $_POST['promotionprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM promotion WHERE promotionid='$promotionid_all'"));

        $promotion_html .= '<tr nobr="true">';
        $promotion_html .= '<td>Promotion</td>';

        $promotion_html .= '<td>';
        if (strpos($config_fields_quote, ','."Promotion Service Type".',') !== FALSE) {
            $promotion_html .= 'Service Type : '.$query['service_type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Promotion Category".',') !== FALSE) {
            $promotion_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Promotion Heading".',') !== FALSE) {
            $promotion_html .= 'Heading : '.$query['heading'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Promotion Description".',') !== FALSE) {
            $promotion_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Promotion Quote Description".',') !== FALSE) {
            $promotion_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $promotion_html .= '</td>';

        $promotion_html .= '<td>-</td>';
        $promotion_html .= '<td>-</td>';
        $promotion_html .= '<td>$'.$_POST['promotionprojectprice'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['promotionprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['promotionprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['promotionprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Promotions</td><td>'.$query['service_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['promotionprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['promotionprojectprice'][$j];

        $temp_ticket_desc = '';
        if($query['service_type'] != '') {
            $temp_ticket_desc .= 'Service Type : '.$query['service_type'].'<br>';
        }
        if($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if($query['heading'] != '') {
            $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }
        $st = $query['service_type'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Promotion', '$promotionid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_promotion != 0) {
    $review_budget .= '<tr><td>Promotions</td><td>$'.$_POST['budget_price_1'].'</td> <td>$'.$total_promotion.'</td></tr>';
}

//Custom
$custom = '';
$custom_html = '';
$total_custom = 0;
$j=0;
foreach ($_POST['customid'] as $customid_all) {
    if($customid_all != '') {
        $custom .= $customid_all.'#'.$_POST['customprojectprice'][$j].'**';
        $total_price += $_POST['customprojectprice'][$j];
        $total_custom += $_POST['customprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM custom WHERE customid='$customid_all'"));

        $custom_html .= '<tr nobr="true">';
        $custom_html .= '<td>Custom</td>';

        $custom_html .= '<td>';
        if (strpos($config_fields_quote, ','."Custom Service Type".',') !== FALSE) {
            $custom_html .= 'Service Type : '.$query['service_type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Custom Category".',') !== FALSE) {
            $custom_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Custom Heading".',') !== FALSE) {
            $custom_html .= 'Heading : '.$query['heading'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Custom Description".',') !== FALSE) {
            $custom_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Custom Quote Description".',') !== FALSE) {
            $custom_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $custom_html .= '</td>';

        $custom_html .= '<td>-</td>';
        $custom_html .= '<td>-</td>';
        $custom_html .= '<td>$'.$_POST['customprojectprice'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['customprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['customprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['customprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Custom</td><td>'.$query['service_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['customprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['customprojectprice'][$j];

        $temp_ticket_desc = '';
        if($query['service_type'] != '') {
            $temp_ticket_desc .= 'Service Type : '.$query['service_type'].'<br>';
        }
        if($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if($query['heading'] != '') {
            $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }
        $st = $query['service_type'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Custom', '$customid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}

if($total_custom != 0) {
    $review_budget .= '<tr><td>Custom</td><td>$'.$_POST['budget_price_2'].'</td> <td>$'.$total_custom.'</td></tr>';
}

// Material
$material = '';
$m_html = '';
$total_material = 0;
$j=0;
$material_total = 0;
$material_price_total = 0;
foreach ($_POST['materialid'] as $materialid_all) {
    if($materialid_all != '') {
        $material .= $materialid_all.'#'.$_POST['mprojectprice'][$j].'#'.$_POST['mprojectqty'][$j].'#'.$_POST['mprojectunit'][$j].'**';
        $total_price += $_POST['mprojectprice'][$j]*$_POST['mprojectqty'][$j];
        $total_material += $_POST['mprojectprice'][$j]*$_POST['mprojectqty'][$j];

        $material_total += $_POST['mprojectqty'][$j];
        $material_price_total += $_POST['mprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM material WHERE materialid='$materialid_all'"));

        $m_html .= '<tr nobr="true">';
        $m_html .= '<td>Material</td>';

        $m_html .= '<td>';
        if (strpos($config_fields_quote, ','."Material Code".',') !== FALSE) {
            $m_html .= 'Code : '.$query['code'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Category".',') !== FALSE) {
            $m_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Sub-Category".',') !== FALSE) {
            $m_html .= 'Sub-Category : '.$query['sub_category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Material Name".',') !== FALSE) {
            $m_html .= 'Name : '.$query['name'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Description".',') !== FALSE) {
            $m_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Quote Description".',') !== FALSE) {
            $m_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Width".',') !== FALSE) {
            $m_html .= 'Width : '.$query['width'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Length".',') !== FALSE) {
            $m_html .= 'Length : '.$query['length'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Units".',') !== FALSE) {
            $m_html .= 'Units : '.$query['units'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Unit Weight".',') !== FALSE) {
            $m_html .= 'Unit Weight : '.$query['unit_weight'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Material Weight Per Foot".',') !== FALSE) {
            $m_html .= 'Weight Per Foot : '.$query['weight_per_feet'].'<br>';
        }
        $m_html .= '</td>';

        $m_html .= '<td>$'.$_POST['mprojectprice'][$j].'</td>';
        $m_html .= '<td>'.$_POST['mprojectqty'][$j].'</td>';
        $m_html .= '<td>'.$_POST['mprojectunit'][$j].'</td>';
        $m_html .= '<td>$'.$_POST['mprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['price'] > $_POST['mprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['price']-$_POST['mprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['mprojectprice'][$j]-$query['price'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Material</td><td>'.$query['code'].' - '.$query['name'].'</td> <td>$'.$query['price'].'</td><td>$'.$_POST['mprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['price'];
        $financial_price += $_POST['mprojectprice'][$j];

        $temp_ticket_desc = '';
        if ($query['code'] != '') {
            $temp_ticket_desc .= 'Code : '.$query['code'].'<br>';
        }
        if ($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if ($query['sub_category'] != '') {
            $temp_ticket_desc .= 'Sub-Category : '.$query['sub_category'].'<br>';
        }
        if ($query['name'] != '') {
            $temp_ticket_desc .= 'Name : '.$query['name'].'<br>';
        }
        if ($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }
        if ($query['width'] != '') {
            $temp_ticket_desc .= 'Width : '.$query['width'].'<br>';
        }
        if ($query['length'] != '') {
            $temp_ticket_desc .= 'Length : '.$query['length'].'<br>';
        }
        if ($query['units'] != '') {
            $temp_ticket_desc .= 'Units : '.$query['units'].'<br>';
        }
        if ($query['unit_weight'] != '') {
            $temp_ticket_desc .= 'Unit Weight : '.$query['unit_weight'].'<br>';
        }
        if ($query['weight_per_feet'] != '') {
            $temp_ticket_desc .= 'Weight Per Foot : '.$query['weight_per_feet'].'<br>';
        }
        $st = $query['category'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Material', '$materialid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}

if($total_material != 0) {
    $review_budget .= '<tr><td>Material</td><td>$'.$_POST['budget_price_14'].'</td> <td>$'.$total_material.'</td></tr>';
}

//Services
$services = '';
$s_html = '';
$total_service = 0;
$j=0;
$service_total = 0;
$service_price_total = 0;
foreach ($_POST['serviceid'] as $serviceid_all) {
    if($serviceid_all != '') {
        $services .= $serviceid_all.'#'.$_POST['sprojectprice'][$j].'#'.$_POST['sprojectqty'][$j].'#'.$_POST['sprojectunit'][$j].'**';
        $total_price += $_POST['sprojectprice'][$j]*$_POST['sprojectqty'][$j];
        $total_service += $_POST['sprojectprice'][$j]*$_POST['sprojectqty'][$j];

        $service_total += $_POST['sprojectqty'][$j];
        $service_price_total += $_POST['sprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM services WHERE serviceid='$serviceid_all'"));

        $s_html .= '<tr nobr="true">';
        $s_html .= '<td>Service</td>';

        $s_html .= '<td>';
        if (strpos($config_fields_quote, ','."Services Service Type".',') !== FALSE) {
            $s_html .= 'Service Type : '.$query['service_type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Services Category".',') !== FALSE) {
            $s_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Services Heading".',') !== FALSE) {
            $s_html .= 'Heading : '.$query['heading'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Services Description".',') !== FALSE) {
            $s_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Services Quote Description".',') !== FALSE) {
            $s_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $s_html .= '</td>';

        $s_html .= '<td>$'.$_POST['sprojectprice'][$j].'</td>';
        $s_html .= '<td>'.$_POST['sprojectqty'][$j].'</td>';
        $s_html .= '<td>'.$_POST['sprojectunit'][$j].'</td>';
        $s_html .= '<td>$'.$_POST['sprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['sprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['sprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['sprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Services</td><td>'.$query['service_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['sprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['sprojectprice'][$j];

        $temp_ticket_desc = '';
        if($query['service_type'] != '') {
            $temp_ticket_desc .= 'Service Type : '.$query['service_type'].'<br>';
        }
        if($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if($query['heading'] != '') {
            $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }
        $st = $query['service_type'].' : '.$query['category'].' : '.$query['heading'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Service', '$serviceid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_service != 0) {
    $review_budget .= '<tr><td>Services</td><td>$'.$_POST['budget_price_3'].'</td> <td>$'.$total_service.'</td></tr>';
}

//Products
$products = '';
$p_html = '';
$total_product = 0;
$j=0;
$product_total = 0;
$product_price_total = 0;
foreach ($_POST['productid'] as $productid_all) {
    if($productid_all != '') {
        $products .= $productid_all.'#'.$_POST['pprojectprice'][$j].'#'.$_POST['pprojectqty'][$j].'#'.$_POST['pprojectunit'][$j].'**';
        $total_price += $_POST['pprojectprice'][$j]*$_POST['pprojectqty'][$j];
        $total_product += $_POST['pprojectprice'][$j]*$_POST['pprojectqty'][$j];

        $product_total += $_POST['pprojectqty'][$j];
        $product_price_total += $_POST['pprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM products WHERE productid='$productid_all'"));

        $p_html .= '<tr nobr="true">';
        $p_html .= '<td>Product</td>';

        $p_html .= '<td>';
        if (strpos($config_fields_quote, ','."Products Product Type".',') !== FALSE) {
            $p_html .= 'Product Type : '.$query['product_type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Products Category".',') !== FALSE) {
            $p_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Products Heading".',') !== FALSE) {
            $p_html .= 'Heading : '.$query['heading'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Products Description".',') !== FALSE) {
            $p_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Products Quote Description".',') !== FALSE) {
            $p_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $p_html .= '</td>';

        $p_html .= '<td>$'.$_POST['pprojectprice'][$j].'</td>';
        $p_html .= '<td>'.$_POST['pprojectqty'][$j].'</td>';
        $p_html .= '<td>'.$_POST['pprojectunit'][$j].'</td>';
        $p_html .= '<td>$'.$_POST['pprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['pprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['pprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['pprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Products</td><td>'.$query['product_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['pprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['pprojectprice'][$j];

        $temp_ticket_desc = '';
        if($query['product_type'] != '') {
            $temp_ticket_desc .= 'Product Type : '.$query['product_type'].'<br>';
        }
        if($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if($query['heading'] != '') {
            $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }
        $st = $query['product_type'].' : '.$query['category'].' : '.$query['heading'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `product_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Product', '$productid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_product != 0) {
    $review_budget .= '<tr><td>Products</td><td>$'.$_POST['budget_price_16'].'</td> <td>$'.$total_product.'</td></tr>';
}

//SR & ED
$sred = '';
$sred_html = '';
$total_sred = 0;
$j=0;
$sred_total = 0;
$sred_price_total = 0;
foreach ($_POST['sredid'] as $sredid_all) {
    if($sredid_all != '') {
        $sred .= $sredid_all.'#'.$_POST['sredprojectprice'][$j].'#'.$_POST['sredprojectqty'][$j].'#'.$_POST['sredprojectunit'][$j].'**';
        $total_price += $_POST['sredprojectprice'][$j]*$_POST['sredprojectqty'][$j];
        $total_sred += $_POST['sredprojectprice'][$j]*$_POST['sredprojectqty'][$j];

        $sred_total += $_POST['sredprojectqty'][$j];
        $sred_price_total += $_POST['sredprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM sred WHERE sredid='$sredid_all'"));

        $sred_html .= '<tr nobr="true">';
        $sred_html .= '<td>SR&ED</td>';

        $sred_html .= '<td>';
        if (strpos($config_fields_quote, ','."SRED SRED Type".',') !== FALSE) {
            $sred_html .= 'SR&ED Type : '.$query['sred_type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."SRED Category".',') !== FALSE) {
            $sred_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."SRED Heading".',') !== FALSE) {
            $sred_html .= 'Heading : '.$query['heading'].'<br>';
        }
        if (strpos($config_fields_quote, ','."SRED Description".',') !== FALSE) {
            $sred_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."SRED Quote Description".',') !== FALSE) {
            $sred_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $sred_html .= '</td>';

        $sred_html .= '<td>$'.$_POST['sredprojectprice'][$j].'</td>';
        $sred_html .= '<td>'.$_POST['sredprojectqty'][$j].'</td>';
        $sred_html .= '<td>'.$_POST['sredprojectunit'][$j].'</td>';
        $sred_html .= '<td>$'.$_POST['sredprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['sredprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['sredprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['sredprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>SR&ED</td><td>'.$query['sred_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['sredprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['sredprojectprice'][$j];

        $temp_ticket_desc = '';
        if($query['sred_type'] != '') {
            $temp_ticket_desc .= 'SR&ED Type : '.$query['sred_type'].'<br>';
        }
        if($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if($query['heading'] != '') {
            $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }
        $st = $query['sred_type'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'SRED', '$sredid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_sred != 0) {
    $review_budget .= '<tr><td>SR&ED</td><td>$'.$_POST['budget_price_15'].'</td> <td>$'.$total_sred.'</td></tr>';
}

//Staff
$staff = '';
$staff_html = '';
$total_staff = 0;
$j=0;
$staff_total = 0;
$staff_price_total = 0;
foreach ($_POST['contactid'] as $contactid_all) {
    if($contactid_all != '') {
        $staff .= $contactid_all.'#'.$_POST['stprojectprice'][$j].'#'.$_POST['stprojectqty'][$j].'#'.$_POST['stprojectunit'][$j].'**';
        $total_price += $_POST['stprojectprice'][$j]*$_POST['stprojectqty'][$j];
        $total_staff += $_POST['stprojectprice'][$j]*$_POST['stprojectqty'][$j];

        $staff_total += $_POST['stprojectqty'][$j];
        $staff_price_total += $_POST['stprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name, cost, description, quote_description  FROM contacts WHERE contactid='$contactid_all'"));

        $staff_html .= '<tr nobr="true">';
        $staff_html .= '<td>Staff</td>';

        $staff_html .= '<td>';
        if (strpos($config_fields_quote, ','."Staff Contact Person".',') !== FALSE) {
            $staff_html .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
        }
        if (strpos($config_fields_quote, ','."Staff Description".',') !== FALSE) {
            $staff_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Staff Quote Description".',') !== FALSE) {
            $staff_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $staff_html .= '</td>';

        $staff_html .= '<td>$'.$_POST['stprojectprice'][$j].'</td>';
        $staff_html .= '<td>'.$_POST['stprojectqty'][$j].'</td>';
        $staff_html .= '<td>'.$_POST['stprojectunit'][$j].'</td>';
        $staff_html .= '<td>$'.$_POST['stprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['stprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['stprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green;"';
            $plus_minus = $_POST['stprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Staff</td><td>'.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['stprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['stprojectprice'][$j];

        $temp_ticket_desc = '';
        $temp_ticket_desc .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }
        $st = decryptIt($query['first_name']).' '.decryptIt($query['last_name']);
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Staff', '$contactid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_staff != 0) {
    $review_budget .= '<tr><td>Staff</td><td>$'.$_POST['budget_price_4'].'</td> <td>$'.$total_staff.'</td></tr>';
}

//Contractor
$contractor = '';
$cont_html = '';
$total_contractor = 0;
$j=0;
$contractor_total = 0;
$contractor_price_total = 0;
foreach ($_POST['contractorid'] as $contractorid_all) {
    if($contractorid_all != '') {
        $contractor .= $contractorid_all.'#'.$_POST['cntprojectprice'][$j].'#'.$_POST['cntprojectqty'][$j].'#'.$_POST['cntprojectunit'][$j].'**';
        $total_price += $_POST['cntprojectprice'][$j]*$_POST['cntprojectqty'][$j];
        $total_contractor += $_POST['cntprojectprice'][$j]*$_POST['cntprojectqty'][$j];

        $contractor_total += $_POST['cntprojectqty'][$j];
        $contractor_price_total += $_POST['cntprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name, description, quote_description, cost  FROM contacts WHERE contactid='$contractorid_all'"));

        $cont_html .= '<tr nobr="true">';
        $cont_html .= '<td>Contractor</td>';

        $cont_html .= '<td>';
        if (strpos($config_fields_quote, ','."Contractor Contact Person".',') !== FALSE) {
            $cont_html .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
        }
        if (strpos($config_fields_quote, ','."Contractor Description".',') !== FALSE) {
            $cont_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Contractor Quote Description".',') !== FALSE) {
            $cont_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $cont_html .= '</td>';

        $cont_html .= '<td>$'.$_POST['cntprojectprice'][$j].'</td>';
        $cont_html .= '<td>'.$_POST['cntprojectqty'][$j].'</td>';
        $cont_html .= '<td>'.$_POST['cntprojectunit'][$j].'</td>';
        $cont_html .= '<td>$'.$_POST['cntprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['cntprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['cntprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['cntprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Contractor</td><td>'.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['cntprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['cntprojectprice'][$j];

        $temp_ticket_desc = '';
        $temp_ticket_desc .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }
        $st = decryptIt($query['first_name']).' '.decryptIt($query['last_name']);
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Contractor', '$contractorid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_contractor != 0) {
    $review_budget .= '<tr><td>Contractor</td><td>$'.$_POST['budget_price_5'].'</td> <td>$'.$total_contractor.'</td></tr>';
}

//Client
$client = '';
$c_html = '';
$total_client = 0;
$j=0;
foreach ($_POST['clientid'] as $clientid_all) {
    if($clientid_all != '') {
        $client .= $clientid_all.'#'.$_POST['clprojectprice'][$j].'**';
        $total_price += $_POST['clprojectprice'][$j];
        $total_client += $_POST['clprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name, first_name, last_name, description, quote_description, cost FROM contacts WHERE contactid='$clientid_all'"));

        $c_html .= '<tr nobr="true">';
        $c_html .= '<td>Client</td>';

        $c_html .= '<td>';
        if (strpos($config_fields_quote, ','."Clients Client Name".',') !== FALSE) {
            $c_html .= 'Client : '.$query['name'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Clients Contact Person".',') !== FALSE) {
            $c_html .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
        }
        if (strpos($config_fields_quote, ','."Clients Description".',') !== FALSE) {
            $c_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Clients Quote Description".',') !== FALSE) {
            $c_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $c_html .= '</td>';

        $c_html .= '<td>-</td>';
        $c_html .= '<td>-</td>';
        $c_html .= '<td>$'.$_POST['clprojectprice'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['clprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['clprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['clprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Clients</td><td>'.$query['name'].'-'.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['clprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['clprojectprice'][$j];

        $temp_ticket_desc = '';
        $temp_ticket_desc .= 'Client : '.$query['name'].'<br>';
        $temp_ticket_desc .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }

        $st = decryptIt($query['first_name']).' '.decryptIt($query['last_name']);
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Client', '$clientid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_client != 0) {
    $review_budget .= '<tr><td>Clients</td><td>$'.$_POST['budget_price_6'].'</td> <td>$'.$total_client.'</td></tr>';
}

//Vendor
$vendor = '';
$v_html = '';
$total_vendor = 0;
$j=0;
$vendor_total = 0;
$vendor_price_total = 0;
foreach ($_POST['vendorperson'] as $vendorperson_all) {
    if($vendorperson_all != '') {
        $vendor .= $vendorperson_all.'#'.$_POST['vprojectprice'][$j].'#'.$_POST['vprojectqty'][$j].'#'.$_POST['vprojectunit'][$j].'**';
        $total_price += $_POST['vprojectprice'][$j]*$_POST['vprojectqty'][$j];
        $total_vendor += $_POST['vprojectprice'][$j]*$_POST['vprojectqty'][$j];

        $vendor_total += $_POST['vprojectqty'][$j];
        $vendor_price_total += $_POST['vprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT vp.*, c.name AS vendor_name FROM vendor_pricelist vp, contacts c WHERE c.contactid = vp.vendorid AND vp.pricelistid='$vendorperson_all'"));

        $v_html .= '<tr nobr="true">';
        $v_html .= '<td>Pricelist</td>';

        $v_html .= '<td>';

        if (strpos($config_fields_quote, ','."Vendor Pricelist Vendor".',') !== FALSE) {
            $v_html .= 'Vendor : '.$query['vendor_name'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Price List".',') !== FALSE) {
            $v_html .= 'Price List : '.$query['pricelist_name'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Category".',') !== FALSE) {
            $v_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Product".',') !== FALSE) {
            $v_html .= 'Product : '.$query['name'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Code".',') !== FALSE) {
            $v_html .= 'Code : '.$query['code'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Sub-Category".',') !== FALSE) {
            $v_html .= 'Sub-Category : '.$query['sub_category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Size".',') !== FALSE) {
            $v_html .= 'Size : '.$query['size'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Type".',') !== FALSE) {
            $v_html .= 'Type : '.$query['type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Part No".',') !== FALSE) {
            $v_html .= 'Part No : '.$query['part_no'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Variance".',') !== FALSE) {
            $v_html .= 'Variance : '.$query['inv_variance'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Description".',') !== FALSE) {
            $v_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Vendor Pricelist Quote Description".',') !== FALSE) {
            $v_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $v_html .= '</td>';

        $v_html .= '<td>$'.$_POST['vprojectprice'][$j].'</td>';
        $v_html .= '<td>'.$_POST['vprojectqty'][$j].'</td>';
        $v_html .= '<td>'.$_POST['vprojectunit'][$j].'</td>';
        $v_html .= '<td>$'.$_POST['vprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cdn_cpu'] > $_POST['vprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cdn_cpu']-$_POST['vprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['vprojectprice'][$j]-$query['cdn_cpu'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Vendor Price List</td><td>'.$query['pricelist_name'].' : '.$query['category'].' : '.$query['name'].'</td> <td>$'.$query['cdn_cpu'].'</td><td>$'.$_POST['vprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cdn_cpu'];
        $financial_price += $_POST['vprojectprice'][$j];

        $temp_ticket_desc = '';
        if ($query['vendor_name'] != '') {
            $temp_ticket_desc .= 'Vendor : '.$query['vendor_name'].'<br>';
        }
        if ($query['pricelist_name'] != '') {
            $temp_ticket_desc .= 'Price List : '.$query['pricelist_name'].'<br>';
        }
        if ($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if ($query['name'] != '') {
            $temp_ticket_desc .= 'Product : '.$query['name'].'<br>';
        }
        if ($query['code'] != '') {
            $temp_ticket_desc .= 'Code : '.$query['code'].'<br>';
        }
        if ($query['sub_category'] != '') {
            $temp_ticket_desc .= 'Sub-Category : '.$query['sub_category'].'<br>';
        }
        if ($query['size'] != '') {
            $temp_ticket_desc .= 'Size : '.$query['size'].'<br>';
        }
        if ($query['type'] != '') {
            $temp_ticket_desc .= 'Type : '.$query['type'].'<br>';
        }
        if ($query['part_no'] != '') {
            $temp_ticket_desc .= 'Part No : '.$query['part_no'].'<br>';
        }
        if ($query['inv_variance'] != '') {
            $temp_ticket_desc .= 'Variance : '.$query['inv_variance'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }

        $st = $query['pricelist_name'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Vendor Price List', '$vendorperson_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_vendor != 0) {
    $review_budget .= '<tr><td>Vendor Price List</td><td>$'.$_POST['budget_price_7'].'</td> <td>$'.$total_vendor.'</td></tr>';
}

//customer
$customer = '';
$cust_html = '';
$total_customer = 0;
$j=0;
foreach ($_POST['customerid'] as $customerid_all) {
    if($customerid_all != '') {
        $customer .= $customerid_all.'#'.$_POST['custprojectprice'][$j].'**';
        $total_price += $_POST['custprojectprice'][$j];
        $total_customer += $_POST['custprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name, first_name, last_name, description, quote_description, cost FROM contacts WHERE contactid='$customerid_all'"));

        $cust_html .= '<tr nobr="true">';
        $cust_html .= '<td>Customer</td>';

        $cust_html .= '<td>';
        if (strpos($config_fields_quote, ','."Customer Client Name".',') !== FALSE) {
            $cust_html .= 'Customer : '.$query['name'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Customer Contact Person".',') !== FALSE) {
            $cust_html .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
        }
        if (strpos($config_fields_quote, ','."Customer Description".',') !== FALSE) {
            $cust_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Customer Quote Description".',') !== FALSE) {
            $cust_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $cust_html .= '</td>';

        $cust_html .= '<td>-</td>';
        $cust_html .= '<td>-</td>';
        $cust_html .= '<td>$'.$_POST['custprojectprice'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['custprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['custprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['custprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Customer</td><td>'.$query['name'].'-'.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['custprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['custprojectprice'][$j];

        $temp_ticket_desc = '';
        $temp_ticket_desc .= 'Customer : '.$query['name'].'<br>';
        $temp_ticket_desc .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }

        $st = $query['name'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Customer', '$customerid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_customer != 0) {
    $review_budget .= '<tr><td>Customer</td><td>$'.$_POST['budget_price_8'].'</td> <td>$'.$total_customer.'</td></tr>';
}

// Inventory
$inventory = '';
$in_html = '';
$total_inventory = 0;
$j=0;
$inventory_total = 0;
$inventory_price_total = 0;
foreach ($_POST['inventoryid'] as $inventoryid_all) {
    if($inventoryid_all != '') {
        $inventory .= $inventoryid_all.'#'.$_POST['inprojectprice'][$j].'#'.$_POST['inprojectqty'][$j].'#'.$_POST['inprojectunit'][$j].'**';
        $total_price += $_POST['inprojectprice'][$j]*$_POST['inprojectqty'][$j];
        $total_inventory += $_POST['inprojectprice'][$j]*$_POST['inprojectqty'][$j];

        $inventory_total += $_POST['inprojectqty'][$j];
        $inventory_price_total += $_POST['inprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory WHERE inventoryid='$inventoryid_all'"));

        $in_html .= '<tr nobr="true">';
        $in_html .= '<td>Inventory</td>';

        $in_html .= '<td>';
        if (strpos($config_fields_quote, ','."Inventory Category".',') !== FALSE) {
            $in_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Product Name".',') !== FALSE) {
            $in_html .= 'Name : '.$query['name'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Code".',') !== FALSE) {
            $in_html .= 'Code : '.$query['code'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Sub-Category".',') !== FALSE) {
            $in_html .= 'Sub-Category : '.$query['sub_category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Size".',') !== FALSE) {
            $in_html .= 'Size : '.$query['size'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Type".',') !== FALSE) {
            $in_html .= 'Type : '.$query['type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Part No".',') !== FALSE) {
            $in_html .= 'Part No : '.$query['part_no'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Location".',') !== FALSE) {
            $in_html .= 'Location : '.$query['location'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Variance".',') !== FALSE) {
            $in_html .= 'Variance : '.$query['inv_variance'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Weight".',') !== FALSE) {
            $in_html .= 'Weight : '.$query['weight'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory ID Number".',') !== FALSE) {
            $in_html .= 'ID Number : '.$query['id_number'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Operator".',') !== FALSE) {
            $in_html .= 'Operator : '.$query['operator'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory LSD".',') !== FALSE) {
            $in_html .= 'LSD : '.$query['lsd'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Comments".',') !== FALSE) {
            $in_html .= 'Comments : '.$query['comment'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Questions".',') !== FALSE) {
            $in_html .= 'Questions : '.$query['question'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Requests".',') !== FALSE) {
            $in_html .= 'Requests : '.$query['request'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Description".',') !== FALSE) {
            $in_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Inventory Quote Description".',') !== FALSE) {
            $in_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $in_html .= '</td>';

        $in_html .= '<td>$'.$_POST['inprojectprice'][$j].'</td>';
        $in_html .= '<td>'.$_POST['inprojectqty'][$j].'</td>';
        $in_html .= '<td>'.$_POST['inprojectunit'][$j].'</td>';
        $in_html .= '<td>$'.$_POST['inprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['inprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['inprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['inprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Inventory</td><td>'.$query['category'].' - '.$query['code'].' : '.$query['part_no'].' - '.$query['name'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['inprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['inprojectprice'][$j];

        $temp_ticket_desc = '';
        if ($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if ($query['name'] != '') {
            $temp_ticket_desc .= 'Name : '.$query['name'].'<br>';
        }
        if ($query['code'] != '') {
            $temp_ticket_desc .= 'Code : '.$query['code'].'<br>';
        }
        if ($query['sub_category'] != '') {
            $temp_ticket_desc .= 'Sub-Category : '.$query['sub_category'].'<br>';
        }
        if ($query['size'] != '') {
            $temp_ticket_desc .= 'Size : '.$query['size'].'<br>';
        }
        if ($query['type'] != '') {
            $temp_ticket_desc .= 'Type : '.$query['type'].'<br>';
        }
        if ($query['part_no'] != '') {
            $temp_ticket_desc .= 'Part No : '.$query['part_no'].'<br>';
        }
        if ($query['location'] != '') {
            $temp_ticket_desc .= 'Location : '.$query['location'].'<br>';
        }
        if ($query['inv_variance'] != '') {
            $temp_ticket_desc .= 'Variance : '.$query['inv_variance'].'<br>';
        }
        if ($query['weight'] != '') {
            $temp_ticket_desc .= 'Weight : '.$query['weight'].'<br>';
        }
        if ($query['id_number'] != '') {
            $temp_ticket_desc .= 'ID Number : '.$query['id_number'].'<br>';
        }
        if ($query['operator'] != '') {
            $temp_ticket_desc .= 'Operator : '.$query['operator'].'<br>';
        }
        if ($query['lsd'] != '') {
            $temp_ticket_desc .= 'LSD : '.$query['lsd'].'<br>';
        }
        if ($query['comment'] != '') {
            $temp_ticket_desc .= 'Comments : '.$query['comment'].'<br>';
        }
        if ($query['question'] != '') {
            $temp_ticket_desc .= 'Questions : '.$query['question'].'<br>';
        }
        if ($query['request'] != '') {
            $temp_ticket_desc .= 'Requests : '.$query['request'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }

        $st = $query['category'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Inventory', '$inventoryid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_inventory != 0) {
    $review_budget .= '<tr><td>Inventory</td><td>$'.$_POST['budget_price_9'].'</td> <td>$'.$total_inventory.'</td></tr>';
}

//Equipemt
$equipment = '';
$eq_html = '';
$total_equipment = 0;
$j=0;
$equipment_total = 0;
$equipment_price_total = 0;

foreach ($_POST['equipmentid'] as $equipmentid_all) {
    if($equipmentid_all != '') {
        $equipment .= $equipmentid_all.'#'.$_POST['eqprojectprice'][$j].'#'.$_POST['eqprojectqty'][$j].'#'.$_POST['eqprojectunit'][$j].'**';
        $total_price += $_POST['eqprojectprice'][$j]*$_POST['eqprojectqty'][$j];
        $total_equipment += $_POST['eqprojectprice'][$j]*$_POST['eqprojectqty'][$j];

        $equipment_total += $_POST['eqprojectqty'][$j];
        $equipment_price_total += $_POST['eqprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM equipment WHERE equipmentid='$equipmentid_all'"));

        $eq_html .= '<tr nobr="true">';
        $eq_html .= '<td>Equipment</td>';

        $eq_html .= '<td>';
        if (strpos($config_fields_quote, ','."Equipment Category".',') !== FALSE) {
            $eq_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Unit Number".',') !== FALSE) {
            $eq_html .= 'Unit Number : '.$query['unit_number'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Serial Number".',') !== FALSE) {
            $eq_html .= 'Serial Number : '.$query['serial_number'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Type".',') !== FALSE) {
            $eq_html .= 'Type : '.$query['type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Make".',') !== FALSE) {
            $eq_html .= 'Make : '.$query['make'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Model".',') !== FALSE) {
            $eq_html .= 'Model : '.$query['model'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Model Year".',') !== FALSE) {
            $eq_html .= 'Model Year : '.$query['model_year'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Year Purchased".',') !== FALSE) {
            $eq_html .= 'Year Purchased : '.$query['year_purchased'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Mileage".',') !== FALSE) {
            $eq_html .= 'Mileage : '.$query['mileage'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Hours Operated".',') !== FALSE) {
            $eq_html .= 'Hours Operated : '.$query['hours_operated'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Notes".',') !== FALSE) {
            $eq_html .= 'Notes : '.$query['notes'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Nickname".',') !== FALSE) {
            $eq_html .= 'Nickname : '.$query['nickname'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment VIN Number".',') !== FALSE) {
            $eq_html .= 'VIN Number : '.$query['vin_number'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Color".',') !== FALSE) {
            $eq_html .= 'Color : '.$query['color'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Licence Plate".',') !== FALSE) {
            $eq_html .= 'Licence Plate : '.$query['licence_plate'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Ownership Status".',') !== FALSE) {
            $eq_html .= 'Ownership Status : '.$query['ownership_status'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Description".',') !== FALSE) {
            $eq_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Equipment Quote Description".',') !== FALSE) {
            $eq_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $eq_html .= '</td>';

        $eq_html .= '<td>$'.$_POST['eqprojectprice'][$j].'</td>';
        $eq_html .= '<td>'.$_POST['eqprojectqty'][$j].'</td>';
        $eq_html .= '<td>'.$_POST['eqprojectunit'][$j].'</td>';
        $eq_html .= '<td>$'.$_POST['eqprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['eqprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['eqprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['eqprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Equipment</td><td>'.$query['category'].' - '.$query['unit_number'].' : '.$query['serial_number'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['eqprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['eqprojectprice'][$j];

        $temp_ticket_desc = '';
        if($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if($query['unit_number'] != '') {
            $temp_ticket_desc .= 'Unit Number : '.$query['unit_number'].'<br>';
        }
        if($query['serial_number'] != '') {
            $temp_ticket_desc .= 'Serial Number : '.$query['serial_number'].'<br>';
        }
        if($query['type'] != '') {
            $temp_ticket_desc .= 'Type : '.$query['type'].'<br>';
        }
        if($query['make'] != '') {
            $temp_ticket_desc .= 'Make : '.$query['make'].'<br>';
        }
        if($query['model'] != '') {
            $temp_ticket_desc .= 'Model : '.$query['model'].'<br>';
        }
        if($query['model_year'] != '') {
            $temp_ticket_desc .= 'Model Year : '.$query['model_year'].'<br>';
        }
        if($query['year_purchased'] != '') {
            $temp_ticket_desc .= 'Year Purchased : '.$query['year_purchased'].'<br>';
        }
        if($query['mileage'] != '') {
            $temp_ticket_desc .= 'Mileage : '.$query['mileage'].'<br>';
        }
        if($query['hours_operated'] != '') {
            $temp_ticket_desc .= 'Hours Operated : '.$query['hours_operated'].'<br>';
        }
        if($query['notes'] != '') {
            $temp_ticket_desc .= 'Notes : '.$query['notes'].'<br>';
        }
        if($query['nickname'] != '') {
            $temp_ticket_desc .= 'Nickname : '.$query['nickname'].'<br>';
        }
        if($query['vin_number'] != '') {
            $temp_ticket_desc .= 'VIN Number : '.$query['vin_number'].'<br>';
        }
        if($query['color'] != '') {
            $temp_ticket_desc .= 'Color : '.$query['color'].'<br>';
        }
        if($query['licence_plate'] != '') {
            $temp_ticket_desc .= 'Licence Plate : '.$query['licence_plate'].'<br>';
        }
        if($query['ownership_status'] != '') {
            $temp_ticket_desc .= 'Ownership Status : '.$query['ownership_status'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }

        $st = $query['category'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Equipment', '$equipmentid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_equipment != 0) {
    $review_budget .= '<tr><td>Equipment</td><td>$'.$_POST['budget_price_10'].'</td> <td>$'.$total_equipment.'</td></tr>';
}

//Labour
$labour = '';
$l_html = '';
$total_labour = 0;
$j=0;
$labour_total = 0;
$labour_price_total = 0;
foreach ($_POST['labourid'] as $labourid_all) {
    if($labourid_all != '') {
        $labour .= $labourid_all.'#'.$_POST['lprojectprice'][$j].'#'.$_POST['lprojectqty'][$j].'#'.$_POST['lprojectunit'][$j].'**';
        $total_price += $_POST['lprojectprice'][$j]*$_POST['lprojectqty'][$j];
        $total_labour += $_POST['lprojectprice'][$j]*$_POST['lprojectqty'][$j];

        $labour_total += $_POST['lprojectqty'][$j];
        $labour_price_total += $_POST['lprojectprice'][$j];

        $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM labour WHERE labourid='$labourid_all'"));

        $l_html .= '<tr nobr="true">';
        $l_html .= '<td>Labour</td>';

        $l_html .= '<td>';
        if (strpos($config_fields_quote, ','."Labour Type".',') !== FALSE) {
            $l_html .= 'Type : '.$query['labour_type'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Labour Heading".',') !== FALSE) {
            $l_html .= 'Heading : '.$query['heading'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Labour Category".',') !== FALSE) {
            $l_html .= 'Category : '.$query['category'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Labour Labour Code".',') !== FALSE) {
            $l_html .= 'Labour Code : '.$query['labour_code'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Labour Name".',') !== FALSE) {
            $l_html .= 'Name : '.$query['name'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Labour Description".',') !== FALSE) {
            $l_html .= 'Description : '.$query['description'].'<br>';
        }
        if (strpos($config_fields_quote, ','."Labour Quote Description".',') !== FALSE) {
            $l_html .= 'Description : '.$query['quote_description'].'<br>';
        }
        $l_html .= '</td>';

        $l_html .= '<td>$'.$_POST['lprojectprice'][$j].'</td>';
        $l_html .= '<td>'.$_POST['lprojectqty'][$j].'</td>';
        $l_html .= '<td>'.$_POST['lprojectunit'][$j].'</td>';
        $l_html .= '<td>$'.$_POST['lprojecttotal'][$j].'</td></tr>';

        $color_off = '';
        if($query['cost'] > $_POST['lprojectprice'][$j]) {
            $color_off = 'style = "color:red; "';
            $plus_minus = $query['cost']-$_POST['lprojectprice'][$j];
            $financial_plus_minus -= $plus_minus;
        } else {
            $color_off = 'style = "color:green; "';
            $plus_minus = $_POST['lprojectprice'][$j]-$query['cost'];
            $financial_plus_minus += $plus_minus;
        }
        $review_profit_loss .= '<tr><td>Labour</td><td>'.$query['labour_type'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['lprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

        $financial_cost += $query['cost'];
        $financial_price += $_POST['lprojectprice'][$j];

        $temp_ticket_desc = '';
        if($query['labour_type'] != '') {
            $temp_ticket_desc .= 'Type : '.$query['labour_type'].'<br>';
        }
        if($query['heading'] != '') {
            $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
        }
        if($query['category'] != '') {
            $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
        }
        if($query['labour_code'] != '') {
            $temp_ticket_desc .= 'Labour Code : '.$query['labour_code'].'<br>';
        }
        if($query['name'] != '') {
            $temp_ticket_desc .= 'Name : '.$query['name'].'<br>';
        }
        if($query['description'] != '') {
            $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
        }

        $st = $query['labour_type'];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Labour', '$labourid_all', '$st', '$temp_ticket_desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_labour != 0) {
    $review_budget .= '<tr><td>Labour</td><td>$'.$_POST['budget_price_13'].'</td> <td>$'.$total_labour.'</td></tr>';
}

//Expense
$expense = '';
$ex_html = '';
$total_expense = 0;
$j=0;
foreach ($_POST['expensetype'] as $expensetype_all) {
    if($expensetype_all != '') {
        $expense .=     $expensetype_all.'#'.$_POST['expensecategory'][$j].'#'.$_POST['expprojectprice'][$j].'**';
        $total_price += $_POST['expprojectprice'][$j];
        $total_expense += $_POST['expprojectprice'][$j];

        $ex_html .= '<tr nobr="true"><td>'.$expensetype_all.'</td> <td>'.$_POST['expensecategory'][$j].'</td> <td>$'.$_POST['expprojectprice'][$j].'</td></tr>';

        $review_profit_loss .= '<tr><td>Expense</td><td>'.$expensetype_all.' : '.$_POST['expensecategory'][$j].'</td> <td>-</td><td>$'.$_POST['expprojectprice'][$j].'</td><td >-</td></tr>';

        $desc = $expensetype_all.' : '.$_POST['expensecategory'][$j];
        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Expenses', '$desc')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_expense != 0) {
    $review_budget .= '<tr><td>Expense</td><td>$'.$_POST['budget_price_11'].'</td> <td>$'.$total_expense.'</td></tr>';
}

//Other
$other = '';
$other_html = '';
$total_other = 0;
$j=0;
foreach ($_POST['other_detail'] as $other_detail_all) {
    if($other_detail_all != '') {
        $other .=     $other_detail_all.'#'.$_POST['otherprojectprice'][$j].'**';
        $total_price += $_POST['otherprojectprice'][$j];
        $total_other += $_POST['otherprojectprice'][$j];

        $other_html .= '<tr nobr="true"><td>'.$other_detail_all.'</td><td>$'.$_POST['otherprojectprice'][$j].'</td></tr>';

        $review_profit_loss .= '<tr><td>Other Items</td><td>'.$other_detail_all.'</td> <td>-</td><td>$'.$_POST['otherprojectprice'][$j].'</td><td >-</td></tr>';

        $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Other', '$other_detail_all')";
        $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
    }
    $j++;
}
if($total_other != 0) {
    $review_budget .= '<tr><td>Other Items</td><td>$'.$_POST['budget_price_12'].'</td> <td>$'.$total_other.'</td></tr>';
}

$html = '';

$html .= '<table border="0px" style="width:100%;">';
$html .= '<tr nobr="true">
        <td style="width:70%;">';
$html .= 'TO : '.get_client($dbc, $businessid);
//if(get_staff($dbc, $clientid) != '') {
//    $html .= '<br>'.get_staff($dbc, $clientid);
//}
if(get_contact($dbc, $businessid, 'business_address') != '') {
    $html .= '<br>'.get_contact($dbc, $businessid, 'business_address');
    $html .= '<br>'.get_contact($dbc, $businessid, 'city');
    $html .= ', '.get_contact($dbc, $businessid, 'province');
    $html .= '<br>'.get_contact($dbc, $businessid, 'country');
    $html .= ', '.get_contact($dbc, $businessid, 'zip_code');
}

if(get_contact($dbc, $businessid, 'office_phone') != '') {
    $html .= '<br>'.get_contact($dbc, $businessid, 'office_phone');
}
if(get_contact($dbc, $businessid, 'cell_phone') != '') {
    $html .= '<br>'.get_contact($dbc, $businessid, 'cell_phone');
}
if(get_contact($dbc, $businessid, 'email_address') != '') {
    $html .= '<br>'.get_contact($dbc, $businessid, 'email_address');
}

if(!empty($_POST['projectmanageid'])) {
    $projectmanageid = $_POST['projectmanageid'];
} else {
    $projectmanageid = 0;
}
if(!empty($_POST['unique_id'])) {
    $unique_id = $_POST['unique_id'];
} else {
    $unique_id = 0;
}

$get_field_config_pdf = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pdf_payment_term, pdf_due_period FROM field_config_project_manage WHERE tile = '$tile' AND tab = '$tab' AND accordion IS NULL"));

$html .= '</td><td style="width:30%; text-align:right;">';
$html .= 'Serial No. : '.$unique_id.'<br>Date : '.date('Y-m-d').'<br>Expiration Date : '.$expiration_date;
$html .= '</td></tr>';
$html .= '</table><br><br><br>';

$html .= '<table border="1px" style="padding:3px; border:1px solid black; width:100%;">
        <tr nobr="true" style="background-color:lightgrey; color:black;  width:100%;">
        <th>Sales Person</th><th>Job</th><th>Payment Terms</th><th>Due Period</th></tr>';
$html .= '<tr nobr="true">
        <td>'.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'</td><td>'.$short_name.' '.$heading.'</td><td>'.$get_field_config_pdf['pdf_payment_term'].'</td><td>'.$get_field_config_pdf['pdf_due_period'].'</td></tr>';
$html .= "</table><br><br><br>\n";

// Fabrication Details
if($fabrication != '') {
	$html .= '<!--FABRICATION--><table border="1px" style="padding:3px; border:1px solid black; width:100%;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:100%;">
		<th>Fabrication Details</th></tr>
		<tr nobr="true"><td>'.$_POST['fabrication']."</td></tr></table><br /><br /><!--FABRICATION END-->\n";
}

// Painting Details
if($paint != '' && $sandblast != '' && $primer != '' && $foam != '') {
	$html .= '<!--PAINTING--><table border="1px" style="padding:3px; border:1px solid black; width:100%;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:100%;">
        <th>Painting Details</th></tr>
		<tr nobr="true"><td>'.$_POST['paint'].'<br />
		<strong>Sandblast:</strong> '.$_POST['sandblast'].'<br />
		<strong>Primer:</strong> '.$_POST['primer'].'<br />
		<strong>Foam:</strong> '.$_POST['foam'].'<br />
		<strong>Rockguard:</strong> '.$_POST['rockguard']."</td></tr></table><br /><br /><!--PAINTING END-->\n";
}

// Rigging Details
if($rigging != '') {
	$html .= '<!--RIGGING--><table border="1px" style="padding:3px; border:1px solid black; width:100%;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:100%;">
        <th>Rigging Details</th></tr>
		<tr nobr="true"><td>'.$_POST['rigging']."</td></tr></table><br /><br /><!--RIGGING END-->\n";
}

// Structure Details
if($structure != '') {
	$html .= '<!--STRUCTURE--><table border="1px" style="padding:3px; border:1px solid black; width:100%;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:100%;">
        <th>Structure Details</th></tr>
		<tr nobr="true"><td>'.$_POST['structure']."</td></tr></table><br /><br /><!--STRUCTURE END-->\n";
}

$html .= '<table border="1px" style="padding:3px; border:1px solid black; width:100%;">
        <tr nobr="true" style="background-color:lightgrey; color:black;  width:100%;">
        <th style="width:10%;">Type</th><th style="width:50%;">Description</th><th style="width:10%;">Price</th><th style="width:10%;">Qty</th><th style="width:10%;">Unit of Measure</th><th style="width:10%;">Line Total</th></tr>'."\n";

if($package_html != '') {
    $html .= $package_html;
}
if($promotion_html != '') {
    $html .= $promotion_html;
}
if($custom_html != '') {
    $html .= $custom_html;
}
if($m_html != '') {
    $html .= $m_html;
}
if($s_html != '') {
    $html .= $s_html;
}
if($p_html != '') {
    $html .= $p_html;
}
if($sred_html != '') {
    $html .= $sred_html;
}
if($l_html != '') {
    $html .= $l_html;
}
if($staff_html != '') {
    $html .= $staff_html;
}
if($cont_html != '') {
    $html .= $cont_html;
}
if($c_html != '') {
    $html .= $c_html;
}
if($v_html != '') {
    $html .= $v_html;
}
if($cust_html != '') {
    $html .= $cust_html;
}
if($in_html != '') {
    $html .= $in_html;
}
if($eq_html != '') {
    $html .= $eq_html;
}
if($ex_html != '') {
    $html .= $ex_html;
}
if($other_html != '') {
    $html .= $other_html;
}
$html .= '<tr><td colspan="5" border="0px" style="border-left:0px white hidden; border-bottom:0px white hidden;"><p style="text-align:right;">Sub Total</p></td><td>$'.$total_price.'</td></tr>';

$value_config = get_config($dbc, 'quote_tax');

$quote_tax = explode('*#*',$value_config);

$total_count = mb_substr_count($value_config,'*#*');
$tax_rate = 0;
for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
    $quote_tax_name_rate = explode('**',$quote_tax[$eq_loop]);
    $tax_rate += $quote_tax_name_rate[1];
        if($quote_tax_name_rate[1] != '') {
        $html .= '<tr><td colspan="5" border="0px" style="border-left:0px white hidden; border-bottom:0px white hidden; border-top:0px white hidden;"><p   style="text-align:right;">'.$quote_tax_name_rate[0].'<br>';
        if($quote_tax_name_rate[2] != '') {
            $html .= '<em>['.$quote_tax_name_rate[2].']</em>';
        }
        $html .= '</p></td><td>'.$quote_tax_name_rate[1].'%</td></tr>';
    }
}

$final = ($total_price*$tax_rate)/100;
$final_total = ($total_price+$final);

$html .= '<tr><td colspan="5" border="0px" style="border-left:0px white hidden; border-top:0px white hidden;  border-bottom:0px white hidden;"><p style="text-align:right;">Total</p></td><td>$'.$final_total.'</td></tr>';

$html .= '</table>';

$html_1 = addslashes($html);
$review_profit_loss_1 = mysqli_real_escape_string($dbc, $review_profit_loss);

$review_budget_1 = mysqli_real_escape_string($dbc, $review_budget);

$completion_date = $_POST['completion_date'];