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

        // validate the input
        if (!is_array($emails)) {
            http_response_code(400);
            echo json_encode(array('message' => 'Invalid request body'));
            exit();
        }

        // load SMTP credentials from environment variables
        $smtp_host = getenv('MAIL_HOST');
        $smtp_user = getenv('MAIL_USERNAME');
        $smtp_pass = getenv('MAIL_PASSWORD');
        $smtp_port = getenv('MAIL_PORT');
        $from_email = getenv('MAIL_FROM_ADDRESS');
        $from_name = getenv('MAIL_FROM_NAME');

        // create a new PHPMailer instance
        $mail = new PHPMailer(true);

        // configure PHPMailer
        try {
            $mail->SMTPDebug = 0; // disable debug output
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_user;
            $mail->Password = $smtp_pass;
            $mail->SMTPSecure = 'tls';
            $mail->Port = $smtp_port;
            $mail->setFrom($from_email, $from_name);
            $mail->addReplyTo($from_email, $from_name);
            $mail->isHTML(true);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('message' => 'Error configuring email sender: '.$mail->ErrorInfo));
            exit();
        }

        // send the Chuck Norris joke email to each email address
        foreach ($emails as $email) {
            try {
                // send a request to the Chuck Norris API to get a random joke
                $url = 'https://api.chucknorris.io/jokes/random';
                $response = file_get_contents($url);
                $data = json_decode($response, true);

                // extract the joke from the response
                $joke = $data['value'];

                // send the email
                $mail->addAddress($email);
                $mail->Subject = 'Chuck Norris Joke';
                $mail->Body = $joke;
                $mail->send();

                // clear the recipient list
                $mail->clearAddresses();

            } catch (Exception $e) {
                http_response_code(500);
                error_log('Error sending email: '.$mail->ErrorInfo);
                echo json_encode(array('message' => 'Error sending email'));
                exit();
            }
        }

        // return a success response
        echo json_encode(array('message' => 'Email sent successfully'));

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
      