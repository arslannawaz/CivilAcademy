<?php

$mailheader = "MIME-Version: 1.0" . "\r\n";
$mailheader .= "From: ".$request->email."\r\n";
$mailheader .= "Reply-To: ".$request->email."\r\n";
$mailheader .= "Content-type: text/html; charset=iso-8859-1\r\n" .
    "X-Mailer: PHP/" . phpversion();
$ToEmail = 'arslnwaz@gmail.com';
$EmailSubject = 'User Contact Information';
$MESSAGE_BODY = '<table style=" background:#F4F4F4 ; text-align : center">

	<tr>
		<th colspan="2" style="padding:10px;">
			<b>Contact Details</b>
		</th>
	</tr>
	<tr>
		<td style="padding:10px;">
			<b>Name:</b>
		</td>
		<td style="padding:10px;">'.$request->first_name.'</td>
	</tr>
	<tr>
		<td style="padding:10px;">
			<b>E-mail:</b>
		</td>
		<td style="padding:10px;">'.$request->email.'</td>
	</tr>
	<tr>
		<td style="padding:10px;">
			<b>Contact:</b>
		</td>
		<td style="padding:10px;">'.$request->phone.'</td>
	</tr>


</table>';
//mail($ToEmail, $EmailSubject, $MESSAGE_BODY, $mailheader) or die ("Failure");




// $mailheader1 = "MIME-Version: 1.0" . "\r\n";
// $mailheader1 .= "From: support@breaking-ad.com\r\n";
// $mailheader1 .= "Reply-To: support@breaking-ad.com\r\n";
// $mailheader1 .= "Content-type: text/html; charset=iso-8859-1\r\n".
//     "X-Mailer: PHP/" . phpversion();
// $ToEmail1 = $request->email;
// $EmailSubject1 = 'Reply From Breaking-Ad';
// $MESSAGE_BODY1 = '<table style=" background:#F4F4F4 ; text-align : center">


// 	<tr>
// 		<th colspan="2" style="padding:10px;">
// 			<b>Thank you for contacting us.</b>
// 		</th>
// 	</tr>
// 	<tr>
// 		<td colspan="2" style="padding:10px;">
// 			You are very important to us, all information received will always remain confidential.
// 		</td>

// 	</tr>
// </table>';


// mail($ToEmail1, $EmailSubject1, $MESSAGE_BODY1, $mailheader1) or die ("Failure");
// header('location: https://breaking-ad.com/');

