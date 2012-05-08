<?php
require_once("../../../engine/initialise.php");

if (!$session->is_logged_in()) {
	redirect_to("login.php");
}
?>

<?php include_once ("../../../views/html_head.php"); ?>
<style>
.table1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	background: #000000;
	color: #000000;
	border-style: solid;
	border-width: 0px;
}

.default1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	color: #272622;
}
</style>

<body>

<?php
// get the month/year submitted, then work out how many days are in that month
$monthClaim = date('m', strftime($_POST['monthClaim']));
$yearClaim = date('Y',strftime($_POST['monthClaim']));
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthClaim, $yearClaim);

// now build the start/end times for the mileage claim
$dateFrom = date('Y-m-d H:i:s', mktime(0, 0, 0, $monthClaim, 1, $yearClaim));
$dateTo = date('Y-m-d H:i:s', mktime(23, 59, 59, $monthClaim, $daysInMonth, $yearClaim));

// get all the visits for the submitted technician between start/end of this month
$visits = Visit::find_users_vists($_POST['user_uid'], $dateFrom, $dateTo);
$totalDistance = 0;
?>




<table width="996" border="0" align="center" cellpadding="0" cellspacing="1" class="table1">
  <tr bgcolor="#999999">
    <td colspan="13"><strong>Business Mileage and Expenses<br />You must attach a VAT fuel receipt for all mileage claimed (see the Travel Expenses Manual for further guidance)</strong></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="70" align="center" valign="top">Date</td>
    <td width="140" align="left" valign="top"><div align="center">Journey Description </div></td>
    <td width="200" align="left" valign="top"><div align="center">Reason</div></td>
    <td width="70" align="center" valign="top" bgcolor="#FFFFFF">Mileage<br>
      Travelled</td>
    <td width="97" align="center" valign="top" bgcolor="#FFFFFF">Mileage*<br>
        <div align="center">Claimed</div></td>
    <td width="6" rowspan="2" align="left" valign="top" background="http://ict.wallingford.oxon.sch.uk/modules/visits/images/shell/spacer3.gif" bgcolor="#000000"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/shell/spacer3.gif" width="1" height="1"></td>
    <td width="70" align="center" valign="top">Expense Details</td>
    <td width="70" align="center" valign="top" bgcolor="#FFFFFF">Time<br>
      Left </td>
    <td width="70" align="center" valign="top" bgcolor="#FFFFFF">Time Returned</td>
    <td colspan="2" align="center" valign="top">Amount Gross<br>
&pound;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;P</td>
    <td colspan="2" align="center" valign="top">VAT<br>
&pound;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;P</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td width="45" align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td width="54" align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td width="45" align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td width="45" align="center" valign="top" bgcolor="#999999"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
  </tr>
  
  
  
  <?php foreach ($visits AS $visit) {
  	  $school = Group::find_by_uid($visit->school_uid);
  	  $totalDistance = $totalDistance + $school->distance;
  ?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><?php echo date('Y/m/d', strtotime($visit->arrival)); ?></td>
    <td><div align="center"><?php echo $school->name; ?></div></td>
    <td><div align="center"><?php echo $visit->category; ?></div></td>
    <td align="center" bgcolor="#999999"><?php echo $school->distance; ?></td>
    <td align="center"><?php echo $school->distance; ?></td>
    <td align="left" valign="top" background="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" bgcolor="#000000"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="6" height="6"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php } ?>
  
  
  
  
  
  <tr bgcolor="#FFFFFF">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">Continuation Sheet total</td>
    <td align="center" bgcolor="#999999">&nbsp;</td>
    <td>&nbsp;</td>
    <td rowspan="2" background="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" bgcolor="#000000"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="6" height="6"></td>
    <td colspan="3" align="right">Continuation Sheet total</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"><p>TOTAL BUSINESS MILEAGE<strong> (A)</strong></p></td>
    <td align="center" bgcolor="#999999"><span class="style4"><?php echo $totalDistance; ?></span></td>
    <td>&nbsp;</td>
    <td colspan="3" align="right">BUSINESS EXPENSES<strong> (D) </strong></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="13" bgcolor="#999999"><strong>NI & TAXABLE MILEAGE AND EXPENSES [includes office relocation mileage which is payable <u>up to</u> a maximum of 4 years]</strong></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td bgcolor="#999999">&nbsp;</td>
    <td>&nbsp;</td>
    <td background="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" bgcolor="#000000"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="6" height="6"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">Continuation Sheet total </td>
    <td bgcolor="#999999">&nbsp;</td>
    <td>&nbsp;</td>
    <td rowspan="3" background="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" bgcolor="#000000"><img src="http://ict.wallingford.oxon.sch.uk/modules/visits/images/spacer3.gif" width="1" height="1"></td>
    <td colspan="3" align="right">Continuation Sheet total</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">TOTAL NI &amp; TAXABLE MILEGAE<strong> (B) </strong></td>
    <td bgcolor="#999999">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3" align="right">NI &amp; TAXABLE EXPENSES<strong> (E) </strong></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">TOTAL MILEAGE (A+B)<strong> (C) </strong></td>
    <td align="center" bgcolor="#999999"><span class="style4"><?php echo $totalDistance; ?></span></td>
    <td>&nbsp;</td>
    <td colspan="3" align="right">TOTAL EXPENSES (D+E)<strong> (F) </strong></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<br>
<table width="996"  border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#000000" class="default1">
  <tr>
    <td bgcolor="#FFFFFF"><p><strong>Declarations</strong></p>
        <p>I have claimed the mileage in box C and expenses in box F due to me and arrising from authorised Council business. No other claim for any of these items has been or will be made. Additional expenditure has been incurred on each meal referred to on this claim. My vehicle is insured for Council business purposes and the insurance indemnities the Council against third party claims arising from the use of my vehicle for business. This claim complies with the guidance notes overleaf.</p>
        <p>I HAVE/HAVE NOT changed my car since the last claim <em>(delete as appropriate)</em><br>
        There are NO/....... Continuation sheets attatched to this claim <em>(delete as appropriate)</em></p>
        <p>I certify to the best of my knowledge the journeys for which expenses and allowances are claimed were necessarily made on Council business and were arranged that a minimum of expense was incurred. <b>I attach a VAT fuel receipt to cover the mileage I have claimed/or I have already attached a VAT fuel receipt to a previous claim that will also cover the mileage I have claimed on this form.</b><br />
        <strong>Signature of Claimant</strong> ................................................ <strong>Date</strong> ...........................................</p>
        <p>I certify that all calculations and amounts claimed have been checked for accuracy, are <b>supported by the attached VAT receipts</b>, and are reasonable and legitimate. <b>Directorate managers should refer to the appropriate Scheme of Delegation and Headteachers to the Schools Financial Manual of Guidance if they are unsure of the appropriate signatory.</b></p>
        <p><strong>Signature of Line Manager </strong> ................................................ <strong>(Print Name)</strong> ........................................... <strong>Date</strong> ...........................................<br>
        I certify that payment for the above expenses can be released.</p>
        <p><strong>Certified for Payment </strong> ................................................ <strong>(Print Name)</strong> ........................................... <strong>Date</strong> ...........................................</p></td>
  </tr>
</table>





</body>
</html>