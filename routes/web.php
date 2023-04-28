<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// enable CORS 

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: *');



// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$router->post('/api/jokes', function (\Illuminate\Http\Request $request) {


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // get the email addresses from the request body
        $emails = json_decode(file_get_contents('php://input'), true);




  // Send a request to the Chuck Norris API to get a random joke
  $url = 'https://api.chucknorris.io/jokes/random';
  $response = file_get_contents($url);
  $data = json_decode($response, true);

  // Extract the joke from the response
  $joke = $data['value'];

  // Send the joke via email using PHPMailer
  $mail = new PHPMailer(true);



        // send the Chuck Norris joke email to each email address
        foreach ($emails as $email) {
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = 0;    //Enable verbose debug output
                $mail->isSMTP();       //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';      //Set the SMTP server to send through
                $mail->SMTPAuth   = true;         //Enable SMTP authentication
                $mail->Username   = 'cgdevemail@gmail.com';     //SMTP username
                $mail->Password   = 'fvhnmyfjcxgbqzne';     //SMTP password
                $mail->SMTPSecure = 'tls';     //Enable implicit TLS encryption
                $mail->Port       = 587;      //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('your_email@gmail.com', 'Your Name');
                $mail->addAddress($email);   //Add a recipient
                $mail->addReplyTo('your_email@gmail.com', 'Your Name');

                //Content
                $mail->isHTML(true);   //Set email format to HTML
                $mail->Subject = 'Chuck Norris Joke';
                // $mail->Body    = 'Why did the chicken cross the road? To get away from Chuck Norris!';
               
                $mail->Body    = $joke;

                $mail->send();
                echo json_encode(array('message' => 'Email sent successfully'));
                exit();
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array('message' => 'Error sending email: '.$mail->ErrorInfo));
                exit();
            }
        }

    } else {
        http_response_code(405);
        echo json_encode(array('message' => 'Method not allowed'));
        exit();
    }
});



        $router->get('/api/jokes', function () use ($router) {
          return response()->json([
              'joke' => 'Chuck Norris doesn\'t need a debugger, he just stares down the bug until the code confesses.'
          ]);
      });
      