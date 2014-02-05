<?php
/***************************************
$Revision:: 94                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 14:11:09#$: Date of last commit
***************************************/
/*
aggregateDonationsReport2FPDF.php
tjs 110303
*/

require ('fpdf.php');

/*
for given account (memberId) select all charities order by name
for each charityId in the above list
select all donations (for charity and member id) order by date desc
report date amount for each
for total report
count of zero amounts, total of non-zero amounts and average of non-zero amounts

at very end show
count of all zeros, grand total of all donations and average donation

(late provide for year filter)
*/

/*
e.g. of data content:

see also http://www.fpdf.org/
*/

require_once( "Member.class.php" );
//tjs 110511 above ensures that config.php has been loaded as well
$username=DB_USERNAME;
$password=DB_PASSWORD;
$database=DB_NAME;

session_start();

//$lastName = "AAAAAA";
$lastName = "";
$charity = false;
$lines = 0;
$zeros = 0;
$nonZeros = 0;
$amounts = 0;
$limit = 80;
//tjs 101209
$charityHeader = 16;
$charityDetail = 12;
$goalLineLimit = 570;
$redZoneThreshold = 510;
//$goalLineLimit = 520;
//$redZoneThreshold = 480;
//$goalLineLimit = 450;
//$redZoneThreshold = 410;
//$goalLineLimit = 420;
//$redZoneThreshold = 380;
$pageProgress = 0;
$grandTotal = 0;
//tjs110308
$grandTotalForProfit = 0;
$grandTotalNonProfit = 0;
$lastisforprofitornot = 0;

//$pdf=new PDF();
$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

$account = $_GET['account'];

if (strlen($account) > 0 && $account != '0') {
	//echo $account;
} else {
	//echo "No account";
	if (isset($_SESSION['member'])) {
		$member = $_SESSION['member'];
		$account = $member->getValue( "id" );
	} 
}

$start = $_GET['start'];
$end = $_GET['end'];
//tjs110304
$hideDonations = $_GET['hideDonations']; 

define("MYSQL_HOST", "localhost");

//$username="root";
//$password="root";
//$database="COLLORG";

$con = mysql_connect("".MYSQL_HOST."",$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

$donations = array();
/*
$query="SELECT * FROM charities where memberId = ".$account." and isInactive is null order by charityName";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while ($i < $num) {
	$charityId=mysql_result($result,$i,"id");

	$charityName=mysql_result($result,$i,"charityName");
	$shortName=mysql_result($result,$i,"shortName");

	//$query="SELECT * FROM donations where memberId = ".$account." and charityId = ".$charityId." order by madeOn desc";
	$query="SELECT * FROM donations where memberId = ".$account." and charityId = ".$charityId." and madeOn BETWEEN '".$start."-01-01 00:00:00' AND '".$end."-12-31 23:59:59' order by madeOn desc";
	$result2=mysql_query($query);
	$num2=mysql_numrows($result2);
	$j=0;
	while ($j < $num2) {
		$id=mysql_result($result2,$j,"id");
		$amount=mysql_result($result2,$j,"amount");
		$date=mysql_result($result2,$j,"madeOn");
		$d = new Donation($id, $amount, $date, $charityId, $charityName, $shortName);
		$donations[] = $d;
		$j++;
	}
	$i++;
}
*/

//$query="SELECT c.charityName, c.shortName, a.id, a.baseId, a.amount, a.madeOn FROM charities c, allDonations a WHERE c.id = a.baseId  and a.madeOn BETWEEN '".$start."-01-01 00:00:00' AND '".$end."-12-31 23:59:59' ORDER BY c.charityName";
// TODO postgres
$query="SELECT c.charityName, c.shortName, c.isForProfit, a.id, a.baseId, a.amount, a.madeOn FROM charities c, allDonations a WHERE c.id = a.baseId  and a.madeOn BETWEEN '".$start."-01-01 00:00:00' AND '".$end."-12-31 23:59:59' ORDER BY c.charityName";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while ($i < $num) {
	$id=mysql_result($result,$i,"baseId");
	$charityId=mysql_result($result,$i,"id");
	$charityName=mysql_result($result,$i,"charityName");
	$shortName=mysql_result($result,$i,"shortName");
	$isForProfit=mysql_result($result,$i,"isForProfit");
	$amount=mysql_result($result,$i,"amount");
	$date=mysql_result($result,$i,"madeOn");
	//$d = new Donation($id, $amount, $date, $charityId, $charityName, $shortName);
	$d = new Donation($id, $amount, $date, $charityId, $charityName, $shortName, $isForProfit);
	$donations[] = $d;
	$i++;
}

class Donation {
	public $_id;
	public $_amount;
	public $_date;
	public $_charity;
	public $_name;
	public $_shortname;
	public $_isforprofit;
		
	//public function __construct($id, $amount, $date, $charity, $name, $shortname) {
	public function __construct($id, $amount, $date, $charity, $name, $shortname, $isforprofit) {
		$this->_id = $id;
		$this->_amount = $amount;
		$this->_date = $date;
		$this->_charity = $charity;
		$this->_name = $name;
		$this->_shortname = $shortname;
		$this->_isforprofit = $isforprofit;
	}
	
	public function getName() {
		return $this->_name;
	}

	public function getAmount() {
		return $this->_amount;
	}
	public function getDate() {
		return $this->_date;
	}
	public function getShortname() {
		return $this->_shortname;
	}
	public function getIsforprofit() {
		return $this->_isforprofit;
	}
	/*
	public function getInfo() {
		$info = array();
		//$info[] = $this->getName();
		//$info[] = $this->getCity();
		$info[] = $this->_name;
		$info[] = $this->_city;
		return $info;
	}*/
	
	public function showDetails() {
		echo "donation ".$this->_name." id ".$this->_id."\n";
		echo $this->_amount.", ".$this->_date." ".$this->_shortname."\n";		
	}
	
	    /* This is the static comparing function: */
    static function cmp_obj($a, $b)
    {
        $al = strtolower($a->getName());
        $bl = strtolower($b->getName());
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

}

//todo date, url, 2nd street
//todo link support
//todo header/footer page numbering
//todo skip extra page if count less than two page threshold

	/*
API Notes
Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
w Cell width. If 0, the cell extends up to the right margin. 
h Cell height. Default value: 0. 
txt String to print. Default value: empty string. 
border:
	* 0: no border
	* 1: frame
OR
    * L: left
    * T: top
    * R: right
    * B: bottom
ln    after call position:
    * 0: to the right
    * 1: to the beginning of the next line
    * 2: below
    Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0. 
align
    * L or empty string: left align (default value)
    * C: center
    * R: right align
fill   Indicates if the cell background must be painted (true) or transparent (false). Default value: false.
URL or identifier returned by AddLink(). 
	*/

//$lines = 0;
//$zeros = 0;
//$nonzeros = 0;
//$amounts = 0;

function outputDonation($pdf, $donation) {
	global $lastName;
	global $lines;
	global $charity;
	global $zeros;
	global $nonZeros;
	global $amounts;
	global $limit;
	
	//tjs 101209
	global $charityHeader;
	global $charityDetail;
	global $goalLineLimit;
	global $redZoneThreshold;
	global $pageProgress;	
	global $grandTotal;

	//tjs110304
	global $hideDonations;

//tjs110308
	global $grandTotalForProfit;
	global $grandTotalNonProfit;
	global $lastisforprofitornot;
	
	$charityName = $donation->getName();
	//$lastNameUC = strtoupper($lastName);
	//$lastStartChar = $lastNameUC[0];
	//$charityNameUC = strtoupper($charityName);
	//$currentStartChar = $charityNameUC[0];
	//if ($currentStartChar > $lastStartChar) {
	//if ($lastName != "" && $charityName != $lastname) {
	if ($charityName != $lastName) {
		//$pdf->AddPage();
		if ($lastName != "") {
			$averageamount = 0;
			if ($nonZeros > 0) {
				$averageamount = $amounts/$nonZeros;
			}
			//$pdf->Cell(0,5,$zeros,0,0,'L');
			//$pdf->Cell(0,5,$averageamount,0,0,'L');
			//	$cityStateZip = sprintf("%s, %s %s", $member->getCity(), $member->getState(), $member->getZip());
			$summary = sprintf("solicitations (dropped): %d, average donation: %d", $zeros, $averageamount);

			$pdf->Cell(0,5,$summary,0,0,'L');
			//$pdf->Cell(50,5,$zeros,0,0,'L');
			//$pdf->Cell(50,5,$averageamount,0,0,'L');
			//$total = sprintf("total: %d", $amounts);
			if ($lastisforprofitornot == 1) {
				$total = sprintf("total (for profit): %d", $amounts);
				$grandTotalForProfit += $amounts;
			} else {
				$total = sprintf("total: %d", $amounts);
				$grandTotalNonProfit += $amounts;
			}
			$grandTotal += $amounts;

			//$pdf->Cell(0,5,$amounts,'B',1,'R');
			$pdf->Cell(0,5,$total,'B',1,'R');
			$zeros = 0;
			$nonZeros = 0;
			$amounts = 0;		
			$lines++;
			$pageProgress += $charityDetail;
		}
		$lastName = $charityName;
		$lastisforprofitornot = $donation->getIsforprofit();
		$charity = true;
	}  else {
		$charity = false;
	}
	if ($charity) {
		if ($pageProgress > $redZoneThreshold) {
			$pdf->AddPage();
			$lines = 0;
			$pageProgress = 0;
		}
		//$pdf->SetFont('Arial','B',16);
		$pdf->SetFont('Arial','B',$charityHeader);

		//0 width (extends to right), height, text, B means border at bottom
		//$pdf->Cell(0,20,$charityName,'B');
		//0 width (extends to right), height, text, 0 means no border, 1 means ln at next line's start
		$pdf->Cell(0,20,$charityName,0, 1);
		$lines ++;
		//$pageProgress += $charityHeader;		
		$pageProgress += $charityHeader*3;		
	}
/*	
	if ($lines > $limit) {
		$pdf->AddPage();
		$lines = 0;
	}
*/
	if ($pageProgress > $goalLineLimit) {
		$pdf->AddPage();
		$lines = 0;
		$pageProgress = 0;
	}
	
	//$pdf->SetFont('Arial','B',16);

	//0 width (extends to right), height, text, B means border at bottom
	//$pdf->Cell(0,20,$charityName,'B');
	//0 width (extends to right), height, text, 0 means no border, 1 means ln at next line's start
	//$pdf->Cell(0,20,$charityName,0, 1);
	//$pdf->SetFont('Arial','B',12); //P, R
	//$pdf->SetFont('Arial','',12);
	$pdf->SetFont('Arial','',$charityDetail);

	//$pdf->Cell(0,5,$donation->getAmount(),0,1,'R');
	//$pdf->Cell(0,5,$donation->getDate(),0,1,'R');
	//tjs110303
	//$pdf->Cell(0,5,$donation->getDate(),0,0,'L');
	//$amount = $donation->getAmount();
	//$pdf->Cell(0,5,$amount,0,1,'R');
	$amount = $donation->getAmount();
	if ($amount == 0) {
		$zeros++;
	} else {
		$nonZeros++;
		$amounts += $amount;
		if ($hideDonations == 'false') {
			$pdf->Cell(0,5,$donation->getDate(),0,0,'L');
			$pdf->Cell(0,5,$amount,0,1,'R');
			$lines++;
			$pageProgress += $charityDetail;
		}
	}
/*
	$amounts += $amount;
	if ($amount == 0) {
		$zeros++;
	} else {
		$nonZeros++;
	}
	$lines++;
	*/
	//$pageProgress += $charityDetail;
}

foreach($donations as $donation) {
	outputDonation($pdf, $donation);
}
			$averageamount = 0;
			if ($nonZeros > 0) {
				$averageamount = $amounts/$nonZeros;
			}
			//$pdf->Cell(0,5,$zeros,0,0,'L');
			//$pdf->Cell(0,5,$averageamount,0,0,'L');
			$summary = sprintf("solicitations (dropped): %d, average donation: %d", $zeros, $averageamount);

			$pdf->Cell(0,5,$summary,0,0,'L');

			//$total = sprintf("total: %d", $amounts);
			if ($lastisforprofitornot == 1) {
				$total = sprintf("total (for profit): %d", $amounts);
				$grandTotalForProfit += $amounts;
			} else {
				$total = sprintf("total: %d", $amounts);
				$grandTotalNonProfit += $amounts;
			}

			//$pdf->Cell(0,5,$amounts,'B',1,'R');
			$pdf->Cell(0,5,$total,'B',1,'R');

			//tjs110304
			$grandTotal += $amounts;

			$forProfitTotal = sprintf("grand for profit total: %d", $grandTotalForProfit);

			//$pdf->Cell(0,5,$amounts,'B',1,'R');
			$pdf->Cell(0,5,$forProfitTotal,'B',1,'R');

			$nonProfitTotal = sprintf("grand non profit total: %d", $grandTotalNonProfit);

			//$pdf->Cell(0,5,$amounts,'B',1,'R');
			$pdf->Cell(0,5,$nonProfitTotal,'B',1,'R');

			$finalTotal = sprintf("grand total: %d", $grandTotal);

			//$pdf->Cell(0,5,$amounts,'B',1,'R');
			$pdf->Cell(0,5,$finalTotal,'B',1,'R');

$pdf->Output();

?> 


