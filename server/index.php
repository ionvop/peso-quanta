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

    echo json_encode([
        "status" => 200,
        "message" => [
            "id" => $_POST["userid"]
        ]
    ], JSON_PRETTY_PRINT);
}

function DefaultMethod() {
    Breakpoint([
        "post" => $_POST,
        "files" => $_FILES
    ]);
}

?>