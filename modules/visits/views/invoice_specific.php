<?php
require('../actions/fpdf16/fpdf.php');
require_once("../../../engine/initialise.php");
gatekeeper(1);

$invoiceFrom = date('c', strtotime($_POST['invoiceFrom'] . " 00:00:01"));
$invoiceTo = date('c', strtotime($_POST['invoiceTo'] . " 23:59:59"));




// reset the totals
$totalMinutes = 0;
$totalMiles = 0;
$perMile = (SITE_PERMILE);
$TOTALCOST = 0;



class PDF extends FPDF {
//Page header
function Header() {
	// Header Logo
	$this->Image('../images/header.png',4,0,200);
}

function invoiceDate() {
	$this->SetFont('Verdana','',8);
	//Move to the right
	$this->Cell(90);
	//Title
	$this->Cell(30,80,date("Y/m/d"),0,0,'C');
	$this->Ln(1);
}

//Page footer
function Footer()
{
    //Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Verdana','',8);
    $this->SetTextColor(0,0,204);
    //Page number
    $this->Cell(0,10,'Sending every young person into the world able and qualified.',0,0,'C');
}

function schoolAddress() {
	$school = Group::find_by_uid($_POST['schoolUID']);
	
	//Move to the left
	$this->Ln(42);
	$this->SetFont('Verdana_Bold','',8);
	$this->Cell(0,4,$school->headteacher,0,2);
	$this->SetFont('Verdana','',8);
	$this->Cell(0,4,$school->name,0,2);
	$this->Cell(0,4,$school->address1,0,2);
	$this->Cell(0,4,$school->address2,0,2);
	$this->Cell(0,4,$school->address3,0,2);
	$this->Cell(0,4,$school->address4,0,2);
	$this->Cell(0,4,$school->address5,0,2);
	$this->Cell(0,4,"Phone: " . $school->phone1,0,2);
	$this->Cell(0,4,"Fax: " . $school->fax1,0,2);
	//Line break
	$this->Ln(3);
}

function BasicTable($header = NULL, $data = NULL) {	
	//Header
	$this->SetFont('Verdana_Bold','',8);
	$this->Cell(20,4,$header[0],1);
	$this->Cell(25,4,$header[1],1);
	$this->Cell(20,4,$header[2],1);
	$this->Cell(60,4,$header[3],1);
	$this->Cell(30,4,$header[4],1);
	$this->Cell(18,4,$header[5],1);
	$this->Ln();
	
	//Data
	$this->SetFont('Verdana','',8);
	foreach($data as $row) {
		$this->Cell(20,4,$row[0],1);
		$this->Cell(25,4,$row[1],1);
		$this->Cell(20,4,$row[2],1);
		$this->Cell(60,4,$row[3],1);
		$this->Cell(30,4,$row[4],1);
		$this->Cell(18,4,$row[5],1);
		$this->Ln();
	}
}

function title() {
	$this->Ln(2);
	
	$this->SetFont('Verdana_Bold','',16);
	$this->Cell(0,4,"ICT Support Invoice: " . $_POST['invoiceNumber'],0,2);
	$this->SetFont('Verdana','',8);
	$this->Cell(0,4,"(2011/01/01 - 2011/04/01)",0,2);
	
	//Line break
	$this->Ln(3);
}

function paymentDue() {
	$this->Ln(3);
	$this->Cell(20);
	$this->Cell(0,4,"Payment is due within 21 days.",0,2);
	$this->Cell(0,4,"Please make cheques payable to " . INVOICE_PAYABLE . " and return your payment to the ",0,2);
	$this->Cell(0,4,"Wallingford School's Finance Office, together with the remittance advice below.",0,2);
}

function remittance() {
	$this->Ln(3);
	$this->Cell(20);
	$this->Cell(0,4,"---------------------------------------------------------------------------------------------------------------------",0,2);
	$this->SetFont('Verdana_Bold','',15);
	$this->Cell(0,4,"WALLINGFORD SCHOOL - REMITTANCE ADVICE",0,2);
	$this->Ln(3);
	$this->Cell(35);
	$this->Cell(60,8,"Invoice Number:",1,0);
	$this->Cell(50,8,$_POST['invoiceNumber'],1,1);
	$this->Cell(35);
	$this->Cell(60,8,"Date:",1,0);
	$this->Cell(50,8,date("Y/m/d"),1,2);
	$this->Ln();
}
} // END OF PDF CLASS


//Instanciation of inherited class
$pdf=new PDF();
$pdf->AddFont('Verdana');
$pdf->AddFont('Verdana_Bold');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->invoiceDate();
$pdf->schoolAddress();
$pdf->title();


$pdf->SetFont('Times','',12);


$headers[] = "Date";
$headers[] = "Time";
$headers[] = "Technician";
$headers[] = "Description";
$headers[] = "Mileage";
$headers[] = "Total Cost";

$visits = Visit::allVisitsBySchool($invoiceFrom, $invoiceTo, $_POST['schoolUID']);

foreach ($visits AS $visit) {
	$school = Group::find_by_uid($visit->school_uid);
	$technician = User::find_by_uid($visit->user_uid);
	
	// get the cost for the technician per hour
	// then break this down to a cost per minute
	$techCost = UserSettings::get_setting($technician->uid, "user_per_hour_cost");
	$techCost = $techCost / 60;
	
	$visitMinutes = 0;
	$visitMiles = 0;
	$visitMilesCost = 0;
	$visitCost = 0;
	$visitMinutes = round((strtotime($visit->departure)-strtotime($visit->arrival))/60);
	
	if ($visit->mileage_claim == "1") {
		$visitMiles = $school->distance;
	}
	
	$visitMilesCost = $visitMiles * SITE_PERMILE;
	$visitCost = (($techCost * $visitMinutes) + $visitMilesCost);
	
	$TOTALCOST = $TOTALCOST + $visitCost;
	$totalMiles = $totalMiles + $visitMiles;
	$totalMinutes = $totalMinutes + $visitMinutes;
	
	if (strlen($visit->description) > 35) {
		$description = substr($visit->description,0,35) . "...";
	} else {
		$description = $visit->description;
	}
	$arrivalDate = date('Y/m/d',strtotime($visit->arrival));
	$time = "(" . date('G:i',strtotime($visit->arrival)) . " - " . date('G:i',strtotime($visit->departure)) . ")";
	$cost = "£" . number_format($visitCost,2,'.',',');
	
	// DISPLAY THE RIGHT IMAGE FOR MILEAGE
	if ($visit->mileage_claim == "1") {
		$mileage = "£" . number_format($visitMilesCost,2,'.',',');
	} else {
		$mileage = "";
	}
	
	$data[] = array($arrivalDate, $time, $technician->firstname, $description, $mileage, $cost);
}
$data[] = array("", "", "", "", "", "£" . number_format($TOTALCOST,2,'.',','));
$pdf->BasicTable($headers, $data);
$pdf->paymentDue();
$pdf->remittance();
	

$pdf->Output();
?>