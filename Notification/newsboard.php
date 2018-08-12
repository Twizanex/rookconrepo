<?php $nb_count = 0;
if(basename($_SERVER['SCRIPT_FILENAME']) != 'newsboard.php') {
    $search_user = $_SESSION['contactid'];
    // Count the unseen newsboard items
    $query = $dbc->query("SELECT newsboardid FROM newsboard `news` WHERE news.deleted = 0 AND ".(isset($dbczen) && isset($sea_software_dbc) ? 'news.cross_software_approval = 1 AND' : '')." (news.expiry_date > NOW() OR news.expiry_date='0000-00-00' OR news.issue_date > DATE_SUB(NOW(), INTERVAL 1 YEAR))");
    while($nb_row = $query->fetch_assoc()['newsboardid']) {
        $nb_count += $dbc->query("SELECT COUNT(*) `seen` FROM `newsboard_seen` WHERE `newsboardid`='".$nb_row."' AND `contactid`='$search_user' AND `newsboard_src` IS NULL")->fetch_assoc()['seen'] > 0 ? 0 : 1;
    }
    $query_software_wide = mysqli_query($dbc_htg,"SELECT newsboardid FROM newsboard `news` WHERE news.deleted = 0 AND news.newsboard_src = 'Softwarewide' AND (news.expiry_date > NOW() OR news.expiry_date='0000-00-00' OR news.issue_date > DATE_SUB(NOW(), INTERVAL 1 YEAR))");
    while($nb_row = mysqli_fetch_assoc($query_software_wide)['newsboardid']) {
        $nb_count += $dbc->query("SELECT COUNT(*) `seen` FROM `newsboard_seen` WHERE `newsboardid`='".$nb_row."' AND `contactid`='$search_user' AND `newsboard_src`='sw'")->fetch_assoc()['seen'] > 0 ? 0 : 1;
    }
} ?>
<a href="<?php echo WEBSITE_URL;?>/newsboard.php" class="newsboard-button">
    <img src="<?= WEBSITE_URL ?>/img/newsboard-icon.png" title="Newsboard" class="inline-img">
    <?php if($nb_count > 0) { ?>
        <span class="planner-icon-notifications" title="Notifications" style="<?= $nb_count > 99 ? 'font-size:6px;' : '' ?>"><?= $nb_count > 99 ? '99+' : $nb_count ?></span>
    <?php } ?>
</a>