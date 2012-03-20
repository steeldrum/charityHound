<?php
/***************************************
$Revision::                            $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate::                     $: Date of last commit
***************************************/
/*
charityhound/
view_designated_charities.php
tjs 120319

file version 1.00 

release version 1.00
*/

require_once( "common.inc.php" );
require_once( "config.php" );
require_once( "Member.class.php" );
//require_once( "Charity.class.php" );
require_once( "RatedCharity.class.php" );

$account = isset( $_GET["account"] ) ? (int)$_GET["account"] : 0;
$from = isset( $_GET["from"] ) ? (int)$_GET["from"] : 2000;
$to = isset( $_GET["to"] ) ? (int)$_GET["to"] : 2100;
$start = isset( $_GET["start"] ) ? (int)$_GET["start"] : 0;
$order = isset( $_GET["order"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["order"] ) : "charityName";
//list( $charities, $totalRows, $totalSolicitations, $totalDonations ) = Charity::getSolicitationCountByCharities( $account, $from, $to, $start, PAGE_SIZE, $order );
list( $charities, $totalRows ) = RatedCharity::getDesignatedCharities( $account, $from, $to, $start, PAGE_SIZE, $order );
displayPageHeader( "View Designated Charities" );

?>
    <h2>Displaying charities <?php echo $start + 1 ?> - <?php echo min( $start +  PAGE_SIZE, $totalRows ) ?> of <?php echo $totalRows ?></h2>

    <table cellspacing="0" style="width: 30em; border: 1px solid #666;">
      <tr>
        <th><?php if ( $order != "charityName" ) { ?><a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=charityName"><?php } ?>charityName<?php if ( $order != "charityName" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "baseid" ) { ?><a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=baseid"><?php } ?>rate<?php if ( $order != "baseid" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "numStars" ) { ?><a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=numStars"><?php } ?>solicitations<?php if ( $order != "numStars" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "schedule" ) { ?><a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=schedule"><?php } ?>reminder schedules<?php if ( $order != "schedule" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "confidential" ) { ?><a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=confidential"><?php } ?>privacy pledges<?php if ( $order != "confidential" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "blank" ) { ?><a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=blank"><?php } ?>blank envelopes<?php if ( $order != "blank" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "currency" ) { ?><a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=currency"><?php } ?>currency envelopes<?php if ( $order != "currency" ) { ?></a><?php } ?></th>
      </tr>
<?php
$rowCount = 0;

foreach ( $charities as $charity ) {
  $rowCount++;
?>
      <tr<?php if ( $rowCount % 2 == 0 ) echo ' class="alt"' ?>>
        <td><a href="view_charity.php?charityId=<?php echo $charity->getValueEncoded( "id" ) ?>&amp;account=<?php echo $account ?>&amp;prior=<?php echo $from ?>&amp;current=<?php echo $to ?>&amp;start=<?php echo $start ?>&amp;order=<?php echo $order ?>&amp;back=omitted"><?php echo $charity->getValueEncoded( "charityName" ) ?></a></td>
        <td><?php echo $charity->getValueEncoded( "baseId" ) ?></td>
        <td><?php echo $charity->getValueEncoded( "numStars" ) ?></td>
        <td><?php echo $charity->getValueEncoded( "appealReminderSchedules" ) ?></td>
        <td><?php echo $charity->getValueEncoded( "appealPrivacyPledges" ) ?></td>
        <td><?php echo $charity->getValueEncoded( "blankEnvelopeAppeals" ) ?></td>
        <td><?php echo $charity->getValueEncoded( "currencyBatedAppeals" ) ?></td>
      </tr>
<?php
}
?>
    </table>

    <div style="width: 30em; margin-top: 20px; text-align: center;">
<?php if ( $start > 0 ) { ?>
      <a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;start=<?php echo max( $start - PAGE_SIZE, 0 ) ?>&amp;order=<?php echo $order ?>">Previous page</a>
<?php } ?>
&nbsp;
<?php if ( $start + PAGE_SIZE < $totalRows ) { ?>
      <a href="view_designated_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;start=<?php echo min( $start + PAGE_SIZE, $totalRows ) ?>&amp;order=<?php echo $order ?>">Next page</a>
<?php } ?>
    </div>
    <br/>
<a class="even" href="javascript:newLocation('reports', 'logistics')" title="Reports">Back to Contributions Report</a>
<?php
displayPageFooter();
?>

