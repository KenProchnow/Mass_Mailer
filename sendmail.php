<?php
/**
* Sends mass emails based on a csv file in the same directoy, which specifies subject, body, attachment as filename. 
*/

/**
* Variables to change 
*/
$username = "yourname";
$password ="yourpassword";
$filename = "mail.csv";

//copy your signature from gmail by composing a message, then inspecting element, then copying the html and css into the $signature variable below. 
$signature = '
<div class="gmail_signature"><div dir="ltr"><div><div><b><span style="font-family: arial,helvetica,sans-serif;">your name</span></b><br></div><span style="color: rgb(11, 83, 148);">yourname@gmail.com<br></span></div><span style="color: rgb(11, 83, 148);">555 555 5555</span><br></div></div>
';

/**
* Initiate the PHPMailer class and set properties to work with gmail's server
*/
require_once("PHPMailer/class.phpmailer.php");
require_once("PHPMailer/class.smtp.php");
$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP
// $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled REQUIRED for GMail
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587
$mail->IsHTML(true); //true so you can send with signature and <br> tags
$mail->Username = $username;
$mail->Password = $password;
$mail->SetFrom($username);
// $mail->AddCC('person2@domain.com', 'Person Two');

/**
* Read from csv file for mass emailings
*/

// Must specify line ending for csv file
ini_set('auto_detect_line_endings',true);

// open the file
$file = fopen($filename,'r');

/**
* @var array Get the first row and create an array
*/
$header = fgetcsv($file,'r');

/* 
Build a a multi-dimensional array so that all row values map to the column name
@return array of arrays that house all data from csv file.
*/

$data = array();

while($row = fgetcsv($file)){
   $arr = array();
   foreach ($header as $i => $col){
      $arr[$col] = $row[$i];
      }
   $data[] = $arr;
   
}


/** 
* print for testing
*
*/

// echo "<pre>";
// print_r($data);   
// echo "</pre>";


// close the file
fclose($file);


/** 
* $body an be used as a replacement to what is in the csv file for body
*
* would be used if you want to make the csv file simpler to use.
*
*/

// $body = "
// Hello,
// <br/><br/>
// This is a friendly reminder that you currently have a past due balance on your account.  If payment has recently been made or is en route, please disregard this email.  If not, please process payment immediately to avoid possible service disruptions.
// <br/><br/>
// Please reply directly to this email if you need a copy of your past due invoice(s), or require payment instructions.
// <br/><br/>
// Thank you,
// <br/><br/>
// ";


$spacer = '<br/><br/>';

/** 
* Loop through the multi-dimensional array built from the csv file
*
* instantiate the mailer class each iteration and send the mail
*
* An alternative would be to bcc everyone on the array and only instantiate once.
*/

foreach($data as $row){

   $email   = $row['email'];
   $subject = $row['subject'];

   $body    = $row['body'];
   $body .=$spacer;
   $body .=$signature;
   
   $attachment = $row['attachment'];   

   $mail->AddAttachment($attachment);
   $mail->Subject = $subject;

   
   $mail->Body = $body; 
   $mail->AddAddress($email);
   echo !$mail->Send() ? "$email Failed" . $mail->ErrorInfo : "$email successfully sent";    
   echo "<br>";
   $mail->ClearAllRecipients();

}





?>