<?php
/***************************************
$Revision:: 91                         $: Revision of last commit
$LastChangedBy::                       $: Author of last commit
$LastChangedDate:: 2011-05-11 11:31:55#$: Date of last commit
***************************************/
/*
Collaborators/
adManagerFile.php
tjs 110105

file version 1.00 

release version 1.00
*/

require_once( "common.inc.php" );

  displayPageHeader( "Assign ad for Collogistics collaborator has been uploaded!" );

//todo ensure user is logged in (has a session)
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/png")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/pjpeg"))
//&& ($_FILES["file"]["size"] < 20000))
&& ($_FILES["file"]["size"] < 100000))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

    if (file_exists("images/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {
      //todo ensure set for server as well as test servers
      define('UPLOAD_DIR', '/Users/thomassoucy/Sites/donationLog/images/');
      
      //todo before move ensure by lookup that name had been registered
      move_uploaded_file($_FILES["file"]["tmp_name"],
      UPLOAD_DIR . $_FILES["file"]["name"]);
      echo "Stored in: " . "images/" . $_FILES["file"]["name"];
      }
    }
  }
else
  {
  echo "Invalid file";
  }
?> 
    <p>Thank you, your ad is now scheduled to run via Collogistics browser client access.</p>
    <br/>
    <a href="index.php">Home</a>

<?php
  displayPageFooter();
?>