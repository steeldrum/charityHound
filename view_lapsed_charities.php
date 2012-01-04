<?php
/***************************************
$Revision:: 151                        $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-11-02 10:37:44#$: Date of last commit
***************************************/
/*
Collaborators/
view_lapsed_charities.php
tjs 111020

file version 1.00 

release version 1.00
*/

require_once( "common.inc.php" );
require_once( "config.php" );
require_once( "Member.class.php" );
require_once( "Charity.class.php" );

$account = isset( $_GET["account"] ) ? (int)$_GET["account"] : 0;
$prior = isset( $_GET["prior"] ) ? (int)$_GET["prior"] : 2000;
$current = isset( $_GET["current"] ) ? (int)$_GET["current"] : 2100;
$start = isset( $_GET["start"] ) ? (int)$_GET["start"] : 0;
$order = isset( $_GET["order"] ) ? preg_replace( "/[^ a-zA-Z]/", "", $_GET["order"] ) : "charityName";
//list( $members, $totalRows ) = Member::getMembers( $start, PAGE_SIZE, $order );
//list( $charities, $totalRows ) = Charity::getLapsedCharities( 1, 2010, 2011, $start, PAGE_SIZE, $order );
//list( $charities, $totalRows ) = Charity::getLapsedCharities( '1', '2010', '2011', $start, PAGE_SIZE, $order );
//list( $charities, $totalRows ) = Charity::getLapsedCharities( 1, '2010', '2011', $start, PAGE_SIZE, $order );
list( $charities, $totalRows ) = Charity::getLapsedCharities( $account, $prior, $current, $start, PAGE_SIZE, $order );
displayPageHeader( "View Lapsed Charitable Donations" );

?>
    <h2>Displaying charities <?php echo $start + 1 ?> - <?php echo min( $start +  PAGE_SIZE, $totalRows ) ?> of <?php echo $totalRows ?></h2>

    <table cellspacing="0" style="width: 30em; border: 1px solid #666;">
      <tr>
        <th><?php if ( $order != "charityName" ) { ?><a href="view_lapsed_charities.php?account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;order=charityName"><?php } ?>charityName<?php if ( $order != "charityName" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "numStars" ) { ?><a href="view_lapsed_charities.php?account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;order=numStars"><?php } ?>rate<?php if ( $order != "numStars" ) { ?></a><?php } ?></th>
        <th><?php if ( $order != "shortName" ) { ?><a href="view_lapsed_charities.php?account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;order=shortName"><?php } ?>shortName<?php if ( $order != "shortName" ) { ?></a><?php } ?></th>
      </tr>
<?php
$rowCount = 0;

foreach ( $charities as $charity ) {
  $rowCount++;
?>
      <tr<?php if ( $rowCount % 2 == 0 ) echo ' class="alt"' ?>>
        <td><a href="view_charity.php?charityId=<?php echo $charity->getValueEncoded( "id" ) ?>&amp;account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;start=<?php echo $start ?>&amp;order=<?php echo $order ?>&amp;back=lapsed"><?php echo $charity->getValueEncoded( "charityName" ) ?></a></td>
        <td><?php echo $charity->getValueEncoded( "numStars" ) ?></td>
        <td><?php echo $charity->getValueEncoded( "shortName" ) ?></td>
      </tr>
<?php
}
?>
    </table>

    <div style="width: 30em; margin-top: 20px; text-align: center;">
<?php if ( $start > 0 ) { ?>
      <a href="view_lapsed_charities.php?account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;start=<?php echo max( $start - PAGE_SIZE, 0 ) ?>&amp;order=<?php echo $order ?>">Previous page</a>
<?php } ?>
&nbsp;
<?php if ( $start + PAGE_SIZE < $totalRows ) { ?>
      <a href="view_lapsed_charities.php?account=<?php echo $account ?>&amp;prior=<?php echo $prior ?>&amp;current=<?php echo $current ?>&amp;start=<?php echo min( $start + PAGE_SIZE, $totalRows ) ?>&amp;order=<?php echo $order ?>">Next page</a>
<?php } ?>
    </div>
    <br/>
    <a class="even" href="javascript:newLocation('reports', 'logistics')" title="Reports">Back to Contributions Report</a>

<?php
displayPageFooter();
?>

