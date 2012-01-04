<?php
/***************************************
$Revision:: 151                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-02 10:37:44#$: Date of last commit
***************************************/
/*
Collaborators/
view_omitted_charities.php
tjs 111102

file version 1.00 

release version 1.00
*/

require_once( "common.inc.php" );
require_once( "config.php" );
require_once( "Member.class.php" );
require_once( "Charity.class.php" );

$account = isset( $_GET["account"] ) ? (int)$_GET["account"] : 0;
$from = isset( $_GET["from"] ) ? (int)$_GET["from"] : 2000;
$to = isset( $_GET["to"] ) ? (int)$_GET["to"] : 2100;
$start = isset( $_GET["start"] ) ? (int)$_GET["start"] : 0;
$order = isset( $_GET["order"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["order"] ) : "charityName";
list( $charities, $totalRows ) = Charity::getOmittedCharities( $account, $from, $to, $start, PAGE_SIZE, $order );
displayPageHeader( "View Omitted Charitable Solicitations for Donations" );

?>
    <h2>Displaying charities <?php echo $start + 1 ?> - <?php echo min( $start +  PAGE_SIZE, $totalRows ) ?> of <?php echo $totalRows ?></h2>

    <table cellspacing="0" style="width: 30em; border: 1px solid #666;">
      <tr>
        <th><?php if ( $order != "charityName" ) { ?><a href="view_omitted_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=charityName"><?php } ?>charityName<?php if ( $order != "charityName" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "numStars" ) { ?><a href="view_omitted_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=numStars"><?php } ?>rate<?php if ( $order != "numStars" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "shortName" ) { ?><a href="view_omitted_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;order=shortName"><?php } ?>solicitations<?php if ( $order != "shortName" ) { ?></a><?php } ?></th>
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
      </tr>
<?php
}
?>
    </table>

    <div style="width: 30em; margin-top: 20px; text-align: center;">
<?php if ( $start > 0 ) { ?>
      <a href="view_omitted_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;start=<?php echo max( $start - PAGE_SIZE, 0 ) ?>&amp;order=<?php echo $order ?>">Previous page</a>
<?php } ?>
&nbsp;
<?php if ( $start + PAGE_SIZE < $totalRows ) { ?>
      <a href="view_omitted_charities.php?account=<?php echo $account ?>&amp;from=<?php echo $from ?>&amp;to=<?php echo $to ?>&amp;start=<?php echo min( $start + PAGE_SIZE, $totalRows ) ?>&amp;order=<?php echo $order ?>">Next page</a>
<?php } ?>
    </div>
    <br/>
<a class="even" href="javascript:newLocation('reports', 'logistics')" title="Reports">Back to Contributions Report</a>
<?php
displayPageFooter();
?>

