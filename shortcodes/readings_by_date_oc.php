<?php
//cmog_readings-by-date2   
add_shortcode( 'readings_by_date2', 'cmog_readingsbydate2' );
function cmog_readingsbydate2(){
global $wpdb; //This is used for database queries
$outputreadings = '';
$SMonth = (!empty($_REQUEST['f_month'] )) ? $_REQUEST['f_month'] : '';
$SYear = (!empty ($_REQUEST['f_year'] )) ?  $_REQUEST['f_year'] : '';
$SClass = (!empty ($_REQUEST['f_class'] )) ? $_REQUEST['f_class'] : '';
$date = getDate();
if ($SMonth == "") $SMonth = $date["mon"];
if ($SYear == "") $SYear = $date["year"];
$outputreadings = '';        
?>
<?php $outputreadings .= "<form id='templates-filter' method='get'>\n";?>
<?php $outputreadings .= "<br />\n";?>
<?php if ( array_key_exists('published',$_REQUEST )) {
//$status_filter =  " and published = " . $_REQUEST['published'] . " " ;
$outputreadings .= "<input type='hidden' id='published' name='published' value='" . $_REQUEST['published'] . "'>\n"; 
} ?>
<?php $outputreadings .= "Year: \n";?> 
<?php
$years = $wpdb->get_results( "SELECT DISTINCT `Year` FROM `" . $wpdb->prefix . "cmog_events` where Class = 'read' order by Year ", 'ARRAY_A' ); 
?>
<?php $outputreadings .= "<select name='f_year' >\n";?>	
<?php		
foreach($years as  $y): 
	$outputreadings .= "<option value=" . $y['Year'] ; 
	if (  $SYear == $y['Year']  )  $outputreadings .= " selected "; 
	$outputreadings .= ">" . $y['Year'] . "</option>\n";	
	endforeach; 
?>
<?php $outputreadings .= "</select>\n";?>		
<?php $outputreadings .= " Month:\n";?> 
<?php $outputreadings .= "<select name='f_month' >\n";?>	
<?php $outputreadings .= "<option value= '' ";?><?php if (  $SMonth == null  )  $outputreadings .= " selected ";?><?php $outputreadings .= "></option>\n";?>
<?php $outputreadings .= "<option value= 1 ";?><?php if (   $SMonth == 1  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">January</option>\n";?>
<?php $outputreadings .= "<option value= 2 ";?><?php if (   $SMonth == 2  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">February</option>\n";?>
<?php $outputreadings .= "<option value= 3 ";?><?php if (   $SMonth == 3  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">March</option>\n";?>
<?php $outputreadings .= "<option value= 4 ";?><?php if (   $SMonth == 4  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">April</option>\n";?>
<?php $outputreadings .= "<option value= 5 ";?><?php if (  $SMonth == 5  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">May</option>\n";?>
<?php $outputreadings .= "<option value= 6 ";?><?php if (  $SMonth == 6  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">June</option>\n";?>
<?php $outputreadings .= "<option value= 7 ";?><?php if (  $SMonth == 7  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">July</option>\n";?>
<?php $outputreadings .= "<option value= 8 ";?><?php if (  $SMonth == 8  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">August</option>\n";?>
<?php $outputreadings .= "<option value= 9 ";?><?php if (  $SMonth == 9  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">September</option>\n";?>
<?php $outputreadings .= "<option value= 10 ";?><?php if (  $SMonth == 10  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">October</option>\n";?>
<?php $outputreadings .= "<option value= 11 ";?><?php if (  $SMonth == 11  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">November</option>\n";?>
<?php $outputreadings .= "<option value= 12 ";?><?php if (  $SMonth == 12  )  $outputreadings .= " selected ";?><?php $outputreadings .= ">December</option>\n";?>
<?php $outputreadings .= "</select>		\n";?>
<?php $outputreadings .= "<input type='submit' value='Filter'>\n";?>
<?php $outputreadings .= "</form>";?>
<?php $outputreadings .= "<br />\n";?>
<?php
$core = new coreLIB; 
for ($SDay=1; $SDay<=32; $SDay++) {
	$jd = unixtojd(mktime(0, 0, 0,$SMonth,$SDay,$SYear));
	$d = cal_from_jd($jd, CAL_GREGORIAN); 
    if ($d['month'] <> $SMonth) {
		BREAK;
	}
	$a = $core->calculateDay($d['month'], $d['day'], $d['year']); 
	$outputreadings .= "<hr /><p>" . $SMonth . "/" . $SDay . "/" . $SYear .   "</p> ";
	$outputreadings .= "<p>";
	if (!$a['tone']) {$tone="";} else {$tone=" &mdash; Tone {$a['tone']}";}
	if ($a['pname']) {$outputreadings .=  "<b>" . $a['pname'] . $tone . "</b><br />";}
	if ($a['fname']) {$outputreadings .=  "<b>" . $a['fname'] . "</b> ";}
	if ($a['saint']) {$outputreadings .=  $a['saint'] ;} 
	$outputreadings .= "</p>\n";
	$readings_list=$core->retrieveReadings($a);
	$xs=array();
	foreach ($readings_list['displays'] as $k=>$v)
		{  
		if ($readings_list['descs'][$k]) {$desc=" (".$readings_list['descs'][$k].")";} else {$desc="";}
		if ($readings_list['links'][$k]) {
			$xs[]=  "<a  HREF='" . $readings_list['links'][$k] . "'> " . $v . "</a> (" . $readings_list['types'][$k] . ")" . $desc ;
			} else { 
			$xs[]=  $v . " (" . $readings_list['types'][$k] . ")" . $desc ;
			}
		}	
	$x=implode("</li>\n<li class='read'>", $xs); unset($xs);
	$outputreadings .= "<ul><li class='read'>$x</li></ul>\n";
	}
return $outputreadings;
}