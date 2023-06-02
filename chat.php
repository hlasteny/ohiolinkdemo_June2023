<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);
$userMessage = $data['userMessage'];

$url = 'https://api.openai.com/v1/chat/completions';
$data = array(
    'model' => 'gpt-3.5-turbo',
    'messages' => array(
        array(
            'role' => 'system',
            'content' => 'You are a Socratic teacher who is guiding your student to create a good project idea.
             Use the Socratic method to guide the student. Give a good insight as well, not only asking questions'
        ),
        array(
            'role' => 'user',
            'content' => $userMessage
        )
    )
);

$options = array(
    'http' => array(
        'header'  => "Content-Type: application/json\r\n".
                     "Authorization: Bearer [your API key here]\r\n", //add your own api key here.
        'method'  => 'POST',
        'content' => json_encode($data)
    )
);

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    http_response_code(500);
    echo json_encode(array("error" => "Error fetching from OpenAI API"));
    return;
}

$response_data = json_decode($result, true);
$completed_message = $response_data['choices'][0]['message']['content'];

echo json_encode(array("result" => $completed_message));
?>

