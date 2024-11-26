<?php

include("common.php");
Debug();
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

$_POST = json_decode(file_get_contents("php://input"), true);

if (isset($_POST["method"])) {
    switch ($_POST["method"]) {
        case "login":
            Login();
            break;
        case "register":
            Register();
            break;
        case "save":
            Save();
            break;
        case "history":
            History();
            break;
        case "getUser":
            GetUser();
            break;
        case "getBalance":
            GetBalance();
            break;
        case "updateUser":
            UpdateUser();
            break;
        case "resetPassword":
            ResetPassword();
            break;
        case "verifyCode":
            VerifyCode();
            break;
        default:
            DefaultMethod();
            break;
    }
} else {
    DefaultMethod();
}

exit();

function Login() {
    $data = GetDatabase();

    $query = <<<SQL
        SELECT *
        FROM `users`
        WHERE `email` = ?
        LIMIT 1;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("s", $_POST["email"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode([
            "status" => 401,
            "message" => "Invalid credentials"
        ], JSON_PRETTY_PRINT);

        exit();
    }

    $result = $result->fetch_assoc();

    if (base64_encode($_POST["password"]) != $result["password"]) {
        echo json_encode([
            "status" => 401,
            "message" => "Invalid credentials"
        ], JSON_PRETTY_PRINT);

        exit();
    }

    if ($result["is_verified"] == 0) {
        echo json_encode([
            "status" => 401,
            "message" => "Account not yet verified"
        ], JSON_PRETTY_PRINT);

        exit();
    }

    echo json_encode([
        "status" => 200,
        "message" => [
            "id" => $result["id"]
        ]
    ], JSON_PRETTY_PRINT);

    exit();
}

function Register() {
    $data = GetDatabase();
    $_POST["password"] = base64_encode($_POST["password"]);

    $query = <<<SQL
        INSERT INTO `users`(`firstname`, `lastname`, `email`, `phone`, `password`, `is_verified`) VALUES (?, ?, ?, ?, ?, 0);
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("sssss", $_POST["firstname"], $_POST["lastname"], $_POST["email"], $_POST["phone"], $_POST["password"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);
        
        exit();
    }

    echo json_encode([
        "status" => 200,
        "message" => [
            "id" => $_POST["email"]
        ]
    ], JSON_PRETTY_PRINT);
}

function Save() {
    $data = GetDatabase();

    $query = <<<SQL
        INSERT INTO `user_data`(`userid`, `value`) VALUES (?, ?);
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("is", $_POST["userid"], $_POST["result"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    }

    echo json_encode([
        "status" => 200,
        "message" => [
            "id" => $_POST["userid"]
        ]
    ], JSON_PRETTY_PRINT);
}

function History() {
    $data = GetDatabase();

    $query = <<<SQL
        SELECT * FROM `user_data` WHERE `userid` = ? ORDER BY `time` DESC;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("i", $_POST["userid"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $result = $stmt->get_result();
    $response = [];

    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }

    echo json_encode([
        "status" => 200,
        "message" => $response
    ], JSON_PRETTY_PRINT);
}

function GetUser() {
    $data = GetDatabase();

    $query = <<<SQL
        SELECT * FROM `users` WHERE `id` = ? LIMIT 1;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("i", $_POST["userid"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $result = $stmt->get_result();
    $result = $result->fetch_assoc();

    echo json_encode([
        "status" => 200,
        "message" => $result
    ], JSON_PRETTY_PRINT);
}

function GetBalance() {
    $data = GetDatabase();

    $query = <<<SQL
        SELECT SUM(`value`) FROM `user_data` WHERE `userid` = ?;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("i", $_POST["userid"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $result = $stmt->get_result();
    $balance = $result->fetch_row()[0] ?? 0;

    echo json_encode([
        "status" => 200,
        "message" => $balance
    ], JSON_PRETTY_PRINT);
}

function UpdateUser() {
    $data = GetDatabase();
    $_POST["password"] = base64_encode($_POST["password"]);

    $query = <<<SQL
        UPDATE `users` SET `password` = ? WHERE `id` = ?;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("si", $_POST["password"], $_POST["userid"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $query = <<<SQL
        DELETE FROM `password_reset_requests` WHERE `user_id` = ?;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("i", $_POST["userid"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    echo json_encode([
        "status" => 200,
        "message" => [
            "id" => $_POST["userid"]
        ]
    ], JSON_PRETTY_PRINT);
}

function ResetPassword() {
    global $BREVO_API_KEY;
    $data = GetDatabase();

    $query = <<<SQL
        DELETE FROM `password_reset_requests` WHERE `time` < NOW() - INTERVAL 1 HOUR;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $query = <<<SQL
        SELECT * FROM `users` WHERE `email` = ? LIMIT 1;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("s", $_POST["email"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode([
            "status" => 401,
            "message" => "Invalid credentials"
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $query = <<<SQL
        INSERT INTO `password_reset_requests`(`user_id`, `code`) VALUES (?, ?);
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("is", $user["id"], $code);
    $code = "" . rand(100000, 999999);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $headers = [
        "Content-Type: application/json",
        "Accept: application/json",
        "Api-Key: {$BREVO_API_KEY}"
    ];

    $body = [
        "sender" => [
            "name" => "Peso Quanta",
            "email" => "pesoquanta@gmail.com"
        ],
        "to" => [
            [
                "email" => $user["email"]
            ]
        ],
        "textContent" => "Greetings, your password reset code is {$code}",
        "subject" => "Password Reset Request",
    ];

    SendCurl("https://api.sendinblue.com/v3/smtp/email", "POST", $headers, json_encode($body));

    echo json_encode([
        "status" => 200,
        "message" => "Password reset email sent"
    ], JSON_PRETTY_PRINT);
}

function VerifyCode() {
    $data = GetDatabase();

    $query = <<<SQL
        SELECT * FROM `password_reset_requests` WHERE `code` = ? LIMIT 1;
    SQL;

    $stmt = $data->prepare($query);
    $stmt->bind_param("s", $_POST["code"]);
    $stmt->execute();

    if ($stmt->errno != 0) {
        echo json_encode([
            "status" => 500,
            "message" => $stmt->error
        ], JSON_PRETTY_PRINT);

        exit();
    };

    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if (!$request) {
        echo json_encode([
            "status" => 401,
            "message" => "Invalid credentials"
        ], JSON_PRETTY_PRINT);

        exit();
    };

    echo json_encode([
        "status" => 200,
        "message" => intval($request["user_id"])
    ], JSON_PRETTY_PRINT);
}

function DefaultMethod() {
    Breakpoint([
        "post" => $_POST,
        "files" => $_FILES
    ]);
}

?>