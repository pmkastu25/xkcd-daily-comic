<?php
require_once 'functions.php';


//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);
// TODO: Implement the form and logic for email unsubscription.

$messages = [
    'success' => [],
    'error' => [],
];
if (isset($_POST['unsubscribe_email']) && !isset($_POST["verification_code"])) {
        $email = $_POST['unsubscribe_email'];
        $code =  generateVerificationCode();

        // echo "Email is: ".$email;
        // echo "Code is: ".$code;

        $datafile = __DIR__."/codes/email-code-map.json";
        $codes = file_exists($datafile) ? json_decode(file_get_contents($datafile), true) : array();

        $codes[$email] = $code;

        file_put_contents($datafile, json_encode($codes));
        
        if(sendUnsubscribeConfirmationEmail($email, $code)){
            $messages['success'][] ="Unsubscription Code Sent to $email";
        } else {
            $messages['error'][] = "Failed to send Unsubscription code";
        };    
       
    } else if(isset($_POST["verification_code"]) && isset($_POST["unsubscribe_email"])){
        $email = $_POST["unsubscribe_email"];  
        $code = $_POST["verification_code"];

        if (verifyCode($email, $code) && unsubscribeEmail($email)) {
           $messages['success'][] ="You have been Unsubscribed";
        } else {
            $messages['error'][] ="Invalid verification code.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XKCD Daily Comic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<style>
    body{
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

     .main{
        text-align: center;
        border-radius: 5px;
    }

    .inner-element{
        border: solid grey 1px;
        padding: 10px;
        margin: 10px;
        border-radius: 5px;
    }

    input{
        width: 90%;
        border-radius: 5px;
        margin: 5px;
        height: 40px;
    }

    input::placeholder{
        padding: 10px;
    }

    button{
        border-radius: 5px;
    }

    
    .alert{
        height: 50px;
        font-weight: 700;
        border-radius: 5px;
        width:90%;
        margin-left: 35px;
        padding-top: 10px;
    }

    .alert-success{
        background-color: lightgreen;
    }

    .alert-error{
        background-color: #cc0202;
        color: white !important;
    }
</style>

<body>
    <div class="container border pb-5 pt-3 main">
    <h1 class="p-3">UnSubscribe to XKCD Daily Comic</h1>

    <?php 
        foreach ($messages as $type => $msgs) {
    foreach ($msgs as $msg) {
        echo "<div class='alert alert-{$type}'>{$msg}</div>";
    }
}
    ?>
    
<div class="inner-element">
<form method="POST">
    <input type="email" name="unsubscribe_email" placeholder="your Email to unsubscribe" required /><br/>
    <button class="btn btn-primary mt-4 mb-2" id="submit-unsubscribe">Submit</button>
</form>
</div>

<div class="inner-element">
<form method="POST">
    <input type="email" name="unsubscribe_email" placeholder="Enter your Email for verification" required> <br/><br/>
    <input type="text" name="verification_code" maxlength="6" placeholder="Enter 6-digit 000000" required> <br/><br/>
    <button class="btn btn-primary mb-2" id="submit-verification">Verify and Unsubscribe</button>
</form>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
