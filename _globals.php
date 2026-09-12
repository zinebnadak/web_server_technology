<?php
// GLOBALA VARIABLER
$siteaddress = "www.umbrellacorp.top";

// LÄS IN GLOBALA FUNKTIONER
include("_f_readtextfile.php");
include("_f_breadcrumimage.php"); // övning 4

function sendmail($recipient, $subject, $message, $sender_name = "System", $sender_code = null)
{
    $user  = $sender_code ?? ($_SESSION["employeecode"] ?? "SYSTEM");
    $namn  = $sender_name;
    $datum = date("d.m.Y");

    $mail_to      = $recipient;
    $mail_subject = "Mail from Umbrella Corp : " . $subject;
    $mail_body    = "This message is auto generated from Umbrella Corp's webpage :\n\n";
    $mail_body   .= $message . "\n\n";
    $mail_body   .= "Message sent " . $datum . " by " . $namn . " (" . $user . ")\n\n";

    // Sätt extra headers för avsändare
    $headers  = "From: support@umbrellacorp.top\r\n";
    $headers .= "Reply-To: support@umbrellacorp.top\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Kör mail() med @ för att dölja varningar om det tajmar ut
    @mail($mail_to, $mail_subject, $mail_body, $headers);
}
?>


