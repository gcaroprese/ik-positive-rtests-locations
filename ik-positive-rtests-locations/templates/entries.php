<?php
/* 
Positive R. Test Locations - Entries Page
Created: 01/21/2022
Last Update: 03/12/2022
Author: Gabriel Caroprese
*/

if ( ! defined('ABSPATH')) exit('restricted access');

wp_enqueue_script('ik_prt_location_map_form_script', IK_PRT_LOCATION_MAP_PUBLIC . '/js/convert-to-csv.js', array(), '1.1.2', true );


$qtyListing = 50;

// I check listing page
$page = 1;
if (isset($_GET["listing"])){
    // I check if value is integer to avoid errors
    if (strval($_GET["listing"]) == strval(intval($_GET["listing"])) && $_GET["listing"] > 0){
        $page = intval($_GET["listing"]);
    }
}
$offset = ($page - 1) * $qtyListing;

$zip_codeOrder = '';
$zip_cityOrder = '';
$zip_stateOrder = '';
$num_pos_casesOrder = '';
$phoneOrder = '';
$timestampOrder = '';
$emailOrder = '';
$ipOrder = '';
$ip_cityOrder = '';
$ip_stateOrder = '';
$ip_countryOrder = '';
$user_agentOrder = '';
$idOrder = '';


// Order data
if (isset($_GET["orderLink"]) && isset($_GET["orderdir"])){
    $orderLink = sanitize_text_field($_GET["orderLink"]);
    $orderdir = sanitize_text_field($_GET["orderdir"]);

    if ($orderdir == 'asc'){
        $orderdir = 'ASC';
        $ordercss = 'asc';
    } else {
        $orderdir = 'DESC';
        $ordercss = 'desc';
    }
    
    if ($orderLink == 'zip_code'){
        $orderLink = 'zip_code';
        $zip_codeOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'zip_city'){
        $orderLink = 'zip_city';
        $zip_cityOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'zip_state'){
        $orderLink = 'zip_state';
        $zip_stateOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'num_pos_cases'){
        $orderLink = 'num_pos_cases';
        $num_pos_casesOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'timestamp'){
        $orderLink = 'timestamp';
        $timestampOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'phone'){
        $orderLink = 'phone';
        $phoneOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'email'){
        $orderLink = 'email';
        $emailOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'ip'){
        $orderLink = 'ip';
        $ipOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'ip_city'){
        $orderLink = 'ip_city';
        $ip_cityOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'ip_state'){
        $orderLink = 'ip_state';
        $ip_stateOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'ip_country'){
        $orderLink = 'ip_country';
        $ip_countryOrder = ' sorted '.$ordercss;
    } else if ($orderLink == 'user_agent'){
        $orderLink = 'user_agent';
        $user_agentOrder = ' sorted '.$ordercss;
    } else {
        $orderLink = 'id';
        $idOrder = ' sorted '.$ordercss;
    }


} else {
    $orderLink = 'id';
    $orderdir = 'DESC';
    $ordercss = 'desc';
    $idOrder = ' sorted '.$ordercss;
}


?>
<div id ="ik_prt_location_existing_records">
    <h1>Positive R. Test Locations - Records</h1>
<?php


//If search or date filter was made
$where = '';
$keyword = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['search'])){
        $keyword = sanitize_text_field($_POST['search']);
        $where = " WHERE zip_code LIKE '".$keyword."' OR zip_city LIKE '".$keyword."' OR zip_state LIKE '".$keyword."' OR phone LIKE '".$keyword."' OR email LIKE '".$keyword."' OR ip LIKE '".$keyword."' OR ip_city LIKE '".$keyword."' OR ip_state LIKE '".$keyword."' OR ip_country LIKE '".$keyword."' OR user_agent LIKE '".$keyword."'";
    }
    if (isset($_POST['date_filter_from']) || isset($_POST['date_filter_to'])){
        
        if (isset($_POST['date_filter_from']) && isset($_POST['date_filter_to'])){
            $from_date = date("Y-m-d H:i:s",strtotime($_POST['date_filter_from']));
            $to_date = date("Y-m-d H:i:s",strtotime($_POST['date_filter_to']));
            $to_date = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($to_date)));

            if ($where == ''){
                $where = " WHERE timestamp BETWEEN '".$from_date."' AND '".$to_date."'";
            } else {
                $where = " AND timestamp BETWEEN '".$from_date."' AND '".$to_date."'";
            }                
        } else {
            if (isset($_POST['date_filter_from'])){
                $from_date = date("Y-m-d H:i:s",strtotime($_POST['date_filter_from']));
                $to_date = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($from_date)));
                if ($where == ''){
                    $where = " WHERE timestamp BETWEEN '".$from_date."' AND '".$to_date."'";
                } else {
                    $where = " AND timestamp BETWEEN '".$from_date."' AND '".$to_date."'";
                }
            } else {
                $from_date = date("Y-m-d H:i:s",strtotime($_POST['date_filter_to']));
                $to_date = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($from_date)));
                if ($where == ''){
                    $where = " WHERE timestamp BETWEEN '".$from_date."' AND '".$to_date."'";
                } else {
                    $where = " AND timestamp BETWEEN '".$from_date."' AND '".$to_date."'";
                }
            }
        }
    }
    
}

global $wpdb;
$query = "SELECT * FROM " . $wpdb->prefix . "ik_prt_entries ".$where." ORDER BY ".$orderLink." ".$orderdir." LIMIT ".$qtyListing." OFFSET ".$offset;
$queryAll = "SELECT id FROM " . $wpdb->prefix . "ik_prt_entries".$where;
$all_records = $wpdb->get_results($queryAll);

$total_records = count($all_records);
$total_pages = intval($total_records / $qtyListing) + 1;

global $wpdb;
$records = $wpdb->get_results($query);
?>
    <div class="search-filter-csv">
        <div class="search-box">
            <form action="" method="post" enctype="multipart/form-data" autocomplete="no">
                <input type="search" id="post-search-input" name="search" value="<?php echo $keyword; ?>">
    		    <input type="submit" id="search-submit" class="button" value="Search">
		    </form>
        </div>
        <div class="filter-date">
            <form action="" method="post" enctype="multipart/form-data" autocomplete="no">
                <input type="date" required name="date_filter_from" class="date_filter_from" value="">
    		    <input type="date" required name="date_filter_to" class="date_filter_to" value="">
		        <input type="submit" id="search-submit" class="button" value="Filter by date">
		    </form>
        </div>
        <?php
        if ($records != NULL){?> 
        <div class="export-csv">
            <?php
            //I create the export to CSV name 
            $exportName = "report-cases-".get_site_url()."-".date('m-d-Y').".csv";
            ?>
            <button id="ik_exportar_csv" class="button button-primary" onclick="exportTableToCSV('<?php echo $exportName; ?>')">Export to CSV</button>
        </div>
        <?php } ?> 
    </div>

<?php
if ($records != NULL){

?>    

   	<table>
		<thead>
			<tr>
			    <th><input type="checkbox" class="select_all" /></th>
				<th order="id" class="exportth orderthis shortth<?php echo $idOrder; ?>">ID<span class="sorting-indicator"></span></th>
				<th order="zip_code" class="exportth orderthis mediumth<?php echo $zip_codeOrder; ?>">zip_code<span class="sorting-indicator"></span></th>
				<th order="zip_city" class="exportth orderthis mediumth<?php echo $zip_cityOrder; ?>">zip_city<span class="sorting-indicator"></span></th>
				<th order="zip_state" class="exportth orderthis mediumth<?php echo $zip_stateOrder; ?>">zip_state<span class="sorting-indicator"></span></th>
				<th order="num_pos_cases" class="exportth orderthis shortth<?php echo $num_pos_casesOrder; ?>">#<span class="sorting-indicator"></span></th>
				<th order="timestamp" class="exportth exportth orderthis longth<?php echo $timestampOrder; ?>">Time<span class="sorting-indicator"></span></th>
				<th order="phone" class="exportth orderthis longth<?php echo $phoneOrder; ?>">phone<span class="sorting-indicator"></span></th>
				<th order="email" class="exportth orderthis longth<?php echo $emailOrder; ?>">email<span class="sorting-indicator"></span></th>
				<th order="ip" class="exportth orderthis mediumth<?php echo $ipOrder; ?>">ip<span class="sorting-indicator"></span></th>
				<th order="ip_city" class="exportth orderthis mediumth<?php echo $ip_cityOrder; ?>">ip_city<span class="sorting-indicator"></span></th>
				<th order="ip_state" class="exportth orderthis mediumth<?php echo $ip_stateOrder; ?>">ip_state<span class="sorting-indicator"></span></th>
				<th order="ip_country" class="exportth orderthis mediumth<?php echo $ip_countryOrder; ?>">ip_country<span class="sorting-indicator"></span></th>
				<th order="user_agent" class="exportth orderthis longth<?php echo $user_agentOrder; ?>">user_agent<span class="sorting-indicator"></span></th>
				<th><a href="#" class="ik_prt_location_map_delete_selected button action">Remove</a></th>
			</tr>
		</thead>
	<tbody>
    </tbody>
<?php    
    foreach ($records as $record){
        echo '<tr record_id="'.$record->id.'">';
        echo '<td><input type="checkbox" class="select_data" /></td>';
        echo '<td class="export_field">'.$record->id.'</td>';
        echo '<td class="export_field">'.$record->zip_code.'</td>';
        echo '<td class="export_field">'.$record->zip_city.'</td>';
        echo '<td class="export_field" class="export_field">'.$record->zip_state.'</td>';
        echo '<td class="export_field">'.$record->num_pos_cases.'</td>';
        echo '<td class="export_field">'.$record->timestamp.'</td>';
        echo '<td class="export_field">'.$record->phone.'</td>';
        echo '<td class="export_field">'.$record->email.'</td>';
        echo '<td class="export_field">'.$record->ip.'</td>';
        echo '<td class="export_field">'.$record->ip_city.'</td>';
        echo '<td class="export_field">'.$record->ip_state.'</td>';
        echo '<td class="export_field">'.$record->ip_country.'</td>';
        echo '<td class="export_field">'.$record->user_agent.'</td>';
        echo '<td record_id="'.$record->id.'"><button class="ik_prt_location_map_delete_record button action">Remove</button></td>';
        echo '</tr>';
    }
?>    
	    <tfoot>
			<tr>
    			<th><input type="checkbox" class="select_all" /></th>
				<th>ID</th>
				<th>zip_code</th>
				<th>zip_city</th>
				<th>zip_state</th>
				<th>#</th>
				<th>Time</th>
				<th>phone</th>
				<th>email</th>
				<th>ip</th>
				<th>ip_city</th>
				<th>ip_state</th>
				<th>ip_country</th>
				<th>user_agent</th>
    			<th><a href="#" class="ik_prt_location_map_delete_selected button action">Remove</a></th>
			</tr>
		</tfoot>
		<tbody>
	</table>
    <?php
    $total_pages = intval($total_records / $qtyListing);
    $url_records_list = get_site_url().'/wp-admin/admin.php?page=ik_prt_location_map_entries_page';
    
    if ($total_records > $qtyListing && $page <= $total_pages){
        echo '<div class="ik_prt_location_pages">';
        
        //If there are a lot of pages
        if ($total_pages > 11){
            $almostlastpage1 = $total_pages - 1;
            $almostlastpage2 = $total_pages - 2;
            $halfpages1 = intval($total_pages/2);
            $halfpages2 = intval($total_pages/2)-1;
            
            $listing_limit = array('1', '2', $page, $halfpages2, $halfpages1, $almostlastpage2, $almostlastpage1, $total_pages);
            
            $pages_limited = true;
        } else{
            $listing_limit[0] = false;
            $pages_limited = false;
        }
        $arrowprevious = $page - 1;
        $arrownext = $page + 1;
        if ($arrowprevious > 1){
            echo '<a href="'.$url_records_list.'&listing='.$arrowprevious.'"><</a>';
        }
        for ($i = 1; $i <= $total_pages; $i++) {
            $showpage = true;
            
            if ($pages_limited == true && !in_array($i, $listing_limit)){
                $nextpage = $page+1;
                $beforepage = $page - 1;
                if ($page != $i && $nextpage != $i && $beforepage != $i){
                    $showpage = false;
                }
            }
            
            if ($showpage == true){
                if ($page == $i){
                    $selectedPageN = 'class="actual_page"';
                } else {
                    $selectedPageN = "";
                }
                
                echo '<a '.$selectedPageN.' href="'.$url_records_list.'&listing='.$i.'">'.$i.'</a>';
                
            }
            
        }
        if ($arrownext < $total_pages){
            echo '<a href="'.$url_records_list.'&listing='.$arrownext.'">></a>';
        }
        echo '</div>';
    }
?>
<script>
    jQuery('#ik_prt_location_existing_records').on('click','td .ik_prt_location_map_delete_record', function(e){
        e.preventDefault();
        var confirmar =confirm('Confirm deleting?');
        if (confirmar == true) {
            var record_id = jQuery(this).parent().attr('record_id');
            var record_tr = jQuery('#ik_prt_location_existing_records tbody').find('tr[record_id='+record_id+']');
            
            var data = {
    			action: "ik_prt_location_ajax_delete_record",
    			"post_type": "post",
    			"record_id": record_id,
    		};  
    
    		jQuery.post( ajaxurl, data, function(response) {
    			if (response){
                    record_tr.fadeOut(700);
                    record_tr.remove();
    		    }        
            });
        }
    });

    jQuery("#ik_prt_location_existing_records th .select_all").on( "click", function() {
        if (jQuery(this).attr('selectedrecord') != 'yes'){
            jQuery('#ik_prt_location_existing_records th .select_all').prop('checked', true);
            jQuery('#ik_prt_location_existing_records th .select_all').attr('checked', 'checked');
            jQuery('#ik_prt_location_existing_records tbody tr').each(function() {
                jQuery(this).find('.select_data').prop('checked', true);
                jQuery(this).find('.select_data').attr('checked', 'checked');
            });        
            jQuery(this).attr('selectedrecord', 'yes');
        } else {
            jQuery('#ik_prt_location_existing_records th .select_all').prop('checked', false);
            jQuery('#ik_prt_location_existing_records th .select_all').removeAttr('checked');
            jQuery('#ik_prt_location_existing_records tbody tr').each(function() {
                jQuery(this).find('.select_data').prop('checked', false);
                jQuery(this).find('.select_data').removeAttr('checked');
            });   
            jQuery(this).attr('selectedrecord', 'no');
            
        }
    });
    
    jQuery("#ik_prt_location_existing_records").on( "click", ".ik_prt_location_map_delete_selected", function() {
        jQuery('#ik_prt_location_existing_records tbody tr').each(function() {
            var element_delete = jQuery(this).parent();
            if (jQuery(this).find('.select_data').prop('checked') == true){
                
                var record_tr = jQuery(this);
                var record_id = record_tr.attr('record_id');
                
                var data = {
    				action: "ik_prt_location_ajax_delete_record",
    				"post_type": "post",
    				"record_id": record_id,
    			};  
    
        		jQuery.post( ajaxurl, data, function(response) {
        			if (response){
                        record_tr.fadeOut(700);
                        record_tr.remove();
        		    }        
                });
            }
        }); 
        
        jQuery('#ik_prt_location_existing_records th .select_all').attr('selectedrecord', 'no');
        jQuery('#ik_prt_location_existing_records th .select_all').prop('checked', false);
        jQuery('#ik_prt_location_existing_records th .select_all').removeAttr('checked');
        return false;
    });
</script>
<?php
} else {
?>
    <p>No Records found</p>
<?php
    if ($where != ''){
    ?>    
        <a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=ik_prt_location_map" class="button">Show All</a>
    <?php    
    }
}
?>
</div>
<script>
jQuery(document).ready(function ($) {
    jQuery('#ik_prt_location_existing_records').on('click','.orderthis', function(e){
        e.preventDefault();

        var order = jQuery(this).attr('order');
        var urlnow = window.location.href;
        
        if (order != undefined){
            if (jQuery(this).hasClass('desc')){
                var direc = 'asc';
            } else {
                var direc = 'desc';
            }
            if (order == 'id'){
                var orderLink = '&orderLink=id&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'zip_code'){
                var orderLink = '&orderLink=zip_code&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'zip_city'){
                var orderLink = '&orderLink=zip_city&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'zip_state'){
                var orderLink = '&orderLink=zip_state&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'num_pos_cases'){
                var orderLink = '&orderLink=num_pos_cases&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'timestamp'){
                var orderLink = '&orderLink=timestamp&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'phone'){
                var orderLink = '&orderLink=phone&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'email'){
                var orderLink = '&orderLink=email&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'ip'){
                var orderLink = '&orderLink=ip&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'ip_city'){
                var orderLink = '&orderLink=ip_city&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'ip_state'){
                var orderLink = '&orderLink=ip_state&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else if (order == 'ip_country'){
                var orderLink = '&orderLink=ip_country&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            } else {
                var orderLink = '&orderLink=user_agent&orderdir='+direc;
                window.location.href = urlnow+orderLink;
            }
        }

    });
    
});
</script>