<div class="form-group">
<label for="file[]" class="col-sm-4 control-label">Upload Logo
<span class="popover-examples list-inline">&nbsp;
<a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
</span>
:</label>
<div class="col-sm-8">
<!--<?php if($pdf_logo != '') {
    echo '<a href="download/'.$pdf_logo.'" target="_blank">View</a>';
    ?>
    <input type="hidden" name="logo_file" value="<?php echo $pdf_logo; ?>" />
    <input name="pdf_logo" type="file" data-filename-placement="inside" class="form-control" />
  <?php } else { ?>
  <input name="pdf_logo" type="file" data-filename-placement="inside" class="form-control" />
  <?php } ?>-->
</div>
</div>

<ul style="list-style-type: none;">
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields1,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields1">Objectives</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields2,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields2">Product/Service Description</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields3,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields3">Customers/Clients</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields4,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields4">Competitors</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields5,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields5">Positioning</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields6,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields6">Pricing</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields7,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields7">Sales & Support</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields8,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields8">Promotion</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields9,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields9">Budget</li>
<li><input type="checkbox" <?php if (strpos(','.$fields.',', ',fields10,') !== FALSE) { echo " checked"; } ?>  name="fields[]" value="fields10">Action Plan</li>
</ul>