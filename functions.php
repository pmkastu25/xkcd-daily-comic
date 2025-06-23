<?php

declare(strict_types=1);

// Autoload PHPMailer classes
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    $num = (string) random_int(0, 999999);
    return str_pad($num, 6, '0', STR_PAD_LEFT);
    // TODO: Implement this function
}

/**
 * Send a verification code to an email.
 * Used smtp sendmail service of xampp or phpmailer
 */
function sendVerificationEmail(string $email, string $code): bool {
    $mail = new PHPMailer(true);

    try {
        // ─── SMTP Settings ───────────────────────────────────────────────────
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';          // your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = "xkcddailycomic@gmail.com";;     // replace with your Gmail
        $mail->Password   = "hdtg jlll mczb iqpk";       // use a Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        // $mail->SMTPSecure = 'tls';

        // ─── Recipients ──────────────────────────────────────────────────────
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->setFrom('no-reply@example.com', 'XKCD Service');
        $mail->addAddress($email);

        // ─── Content ─────────────────────────────────────────────────────────
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body    = "<p>Your verification code is: <strong>{$code}</strong></p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
       file_put_contents(__DIR__ . '/mail-error.log', date('c') . " → PM Error: {$mail->ErrorInfo}\n", FILE_APPEND);
        return false;
    }
    // TODO: Implement this function
}

function verifyCode($email, $code): bool {
    $file = __DIR__ . '/codes/email-code-map.json';
    
    if (!file_exists($file)) {
        error_log("Verification failed: code file not found.");
        return false;
    }

    $data = json_decode(file_get_contents($file), true);

    if (!isset($data[$email])) {
        error_log("Verification failed: email not found in codes.");
        return false;
    }

    if ($data[$email] !== $code) {
        error_log("Verification failed: code mismatch. Expected {$data[$email]}, got {$code}");
        return false;
    }

    return true;
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
    // TODO: Implement this function
    $file = __DIR__ . '/registered_emails.txt';
   
    $email = trim(strtolower($email));

    if (!file_exists($file)) {
        file_put_contents($file, $email . PHP_EOL);
        return true;
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $emails = array_map('strtolower', array_map('trim', $emails));

    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
        return true;
    }

    return false;
}

/**
 * Unsubscribe an email by removing it from the list.
 */

function sendUnsubscribeConfirmationEmail(string $email, string $code): bool {
    $mail = new PHPMailer(true);

    try {
        // SMTP config same as above
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "xkcddailycomic@gmail.com";
        $mail->Password   = "hdtg jlll mczb iqpk";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('no-reply@example.com', 'XKCD Service');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Confirm Un-subscription';
        $mail->Body    = "<p>To confirm un-subscription, use this code: <strong>{$code}</strong></p>";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

function unsubscribeEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
  $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

  $emails = array_filter($emails, fn($e) => $e !== $email);

  file_put_contents($file, implode(PHP_EOL, $emails).PHP_EOL);
  return true;
    // TODO: Implement this function
}

/**
 * Fetch random XKCD comic and format data as HTML.
 */
function fetchAndFormatXKCDData(string $email = ''): string {
    // TODO: Implement this function
    $randomID = random_int(1,3000);
    $url = "https://xkcd.com/$randomID/info.0.json";
    $data = json_decode(file_get_contents($url), true);

    $img = htmlspecialchars($data["img"]);

      $unsubscribeLink = $email ? "http://localhost/xkcd-pmkastu25/src/unsubscribe.php?email=" . urlencode($email) : "#";

    return "<h2>XKCD Comic</h2><img src=\"$img\" alt=\"XKCD Comic\"><p><a href=\"$unsubscribeLink\" id=\"unsubscribe-btn\">Unsubscribe</a></p>";
}

/**
 * Send the formatted XKCD updates to registered emails.
 */
function sendXKCDUpdatesToSubscribers(): void {
    // TODO: Implement this function
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file,  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $subject = "Your XKCD Comic";
    $headers = "From: no-reply@example.com\r\n" .
               "Content-type: text/html; charset=UTF-8\r\n";

    foreach ($emails as $email) {
      $content = fetchAndFormatXKCDData($email);
      
      $mail = new PHPMailer(true);

        try {
            // ─── SMTP Settings ───────────────────────────────────────────────────
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';          // same as above
            $mail->SMTPAuth   = true;
            $mail->Username   = "xkcddailycomic@gmail.com";     // same credentials
            $mail->Password   = "hdtg jlll mczb iqpk";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ─── Recipients ──────────────────────────────────────────────────────
            $mail->setFrom('no-reply@example.com', 'XKCD Service');
            $mail->addAddress($email);

            // ─── Content ─────────────────────────────────────────────────────────
            $mail->isHTML(true);
            $mail->Subject = 'Your XKCD Comic';
            $mail->Body    = $content;
            // (optional) $mail->AltBody = strip_tags($comicHtml);

            $mail->send();
        } catch (Exception $e) {
            // If you’d like to log failures: error_log("XKCD send failed to $email: " . $mail->ErrorInfo);
        }
    }
}
