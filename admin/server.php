<?php

include("common.php");
Debug();

if (isset($_POST["method"])) {
    switch ($_POST["method"]) {
        case "login":
            Login();
            break;
        case "logout":
            Logout();
            break;
        case "updateAdmin":
            UpdateAdmin();
            break;
        case "verifyUser":
            VerifyUser();
            break;
        case "deleteUser":
            DeleteUser();
            break;
        case "denyUser":
            DenyUser();
            break;
    }
}

DefaultMethod();

function Login() {
    $sql = GetDatabase();

    $query = <<<SQL
        SELECT * FROM `admins` WHERE `username` = ? LIMIT 1;
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        Alert("Invalid credentials");
    }

    $admin = $result->fetch_assoc();
    
    if ($admin["password"] != base64_encode($_POST["password"])) {
        $query = <<<SQL
            INSERT INTO `admin_logs`(`admin_id`, `status`) VALUES (?, ?);
        SQL;

        $stmt = $sql->prepare($query);
        $stmt->bind_param("is", $admin["id"], $status);
        $status = "Incorrect password";
        $stmt->execute();
        Alert("Invalid credentials");
    }

    $query = <<<SQL
        INSERT INTO `admin_logs`(`admin_id`, `status`) VALUES (?, ?);
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("is", $admin["id"], $status);
    $status = "Successful login";
    $stmt->execute();
    setcookie("admin", $admin["id"]);
    header("Location: dashboard/");
}

function Logout() {
    setcookie("admin", "", time() - 3600);
    header("Location: ./");
}

function UpdateAdmin() {
    $sql = GetDatabase();
    $admin = GetAdminData();

    if ($_POST["username"] != $admin["username"]) {
        Alert("Incorrect username");
    }

    if ($_POST["password"] != $admin["password"]) {
        Alert("Incorrect password");
    }

    if ($_POST["newUsername"] == "") {
        $_POST["newUsername"] = $admin["username"];
    }

    if ($_POST["newPassword"] == "") {
        $_POST["newPassword"] = $admin["password"];
    }

    $query = <<<SQL
        UPDATE `admins` SET `username` = ?, `password` = ? WHERE `id` = ?;
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("ssi", $_POST["newUsername"], $_POST["newPassword"], $admin["id"]);
    $stmt->execute();

    $query = <<<SQL
        INSERT INTO `admin_logs`(`admin_id`, `status`) VALUES (?, ?);
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("is", $admin["id"], $status);
    $status = "Updated account";
    $stmt->execute();
    header("Location: dashboard/");
}

function VerifyUser() {
    global $BREVO_API_KEY;
    $sql = GetDatabase();

    $query = <<<SQL
        SELECT * FROM `users` WHERE `id` = ?;
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("i", $_POST["id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        Alert("User not found");
    }

    $user = $result->fetch_assoc();

    $query = <<<SQL
        UPDATE `users`
        SET `is_verified` = 1
        WHERE `id` = ?;
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("i", $_POST["id"]);
    $stmt->execute();

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
        "textContent" => "Your Peso Quanta account has been successfully verified, and you may now use the app.\n\nWelcome to Peso Quanta",
        "subject" => "Account Verification"
    ];

    SendCurl("https://api.sendinblue.com/v3/smtp/email", "POST", $headers, json_encode($body));
    header("Location: dashboard/requests/");
}

function DeleteUser() {
    $sql = GetDatabase();

    $query = <<<SQL
        DELETE FROM `users` WHERE `id` = ?;
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("i", $_POST["id"]);
    $stmt->execute();
    header("Location: dashboard/users/");
}

function DenyUser() {
    $sql = GetDatabase();

    $query = <<<SQL
        DELETE FROM `users` WHERE `id` = ?;
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("i", $_POST["id"]);
    $stmt->execute();
    header("Location: dashboard/requests/");
}

function DefaultMethod() {
    Breakpoint(base64_encode("177013"));

    Breakpoint([
        "post" => $_POST,
        "files" => $_FILES
    ]);
}

?>