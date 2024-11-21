<?php

include("../server/config.php");

/**
 * Enable debugging.
 */
function Debug() {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

/**
 * Prints the given message and exits the script.
 *
 * @param mixed $message The message to be printed.
 * @return void
 */
function Breakpoint($message) {
    header("Content-type: application/json");
    print_r($message);
    exit();
}

/**
 * Prints the given message as an alert and redirects the user.
 *
 * @param mixed $message The message to be displayed.
 * @param string $redirect The URL to redirect the user to. If empty, the user will be redirected back.
 * @return void
 */
function Alert($message, $redirect = "") {
    $message = json_encode($message);

    $redirectScript = <<<JS
        window.history.back();
    JS;
    
    if ($redirect != "") {
        $redirect = json_encode($redirect);

        $redirectScript = <<<JS
            location.href = {$redirect};
        JS;
    }

    echo <<<HTML
        <script>
            alert({$message});
            {$redirectScript}
        </script>
    HTML;

    exit();
}

function GetDatabase() {
    global $DB_CONFIG;
    $result = new mysqli($DB_CONFIG["hostname"], $DB_CONFIG["username"], $DB_CONFIG["password"], $DB_CONFIG["database"]);
    return $result;
}

/**
 * Sends an HTTP request using cURL and returns the response.
 *
 * This function initiates a cURL session to send an HTTP request to the specified URL using the given method, headers, 
 * and data. It supports custom request methods and bypasses SSL verification. If the request fails, the function returns false.
 *
 * @param string $url     The URL to which the request is sent.
 * @param string $method  The HTTP method to use for the request (e.g., 'GET', 'POST', 'PUT', 'DELETE').
 * @param array  $headers An array of HTTP headers to include in the request.
 * @param mixed  $data    The data to send with the request. Typically an associative array or a JSON string.
 *
 * @return mixed The response from the server as a string, or false if the request fails.
 */
function SendCurl($url, $method, $headers, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);

    if (curl_errno($ch) != 0) {
        return false;
    }

    curl_close($ch);
    return $result;
}

function GetAdminData() {
    $sql = GetDatabase();
    
    $query = <<<SQL
        SELECT * FROM `admins` WHERE `id` = ? LIMIT 1;
    SQL;

    $stmt = $sql->prepare($query);
    $stmt->bind_param("i", $_COOKIE["admin"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        return false;
    }

    return $result->fetch_assoc();
}

function SetHeader() {
    return <<<HTML
        <div class="-header">
            <div class="-header__logo">
                <img src="assets/logo.png">
            </div>
            <div></div>
            <div class="-header__account -header__tab -center__flex">
                <div class="-header__tab__box -script__link" data-href="dashboard/account/">
                    Account
                </div>
            </div>
            <div class="-header__logs -header__tab -center__flex">
                <div class="-header__tab__box -script__link" data-href="dashboard/logs/">
                    Logs
                </div>
            </div>
            <div class="-header__logout -header__tab -center__flex">
                <form class="-form -header__tab__box" action="server.php" method="post" enctype="multipart/form-data" onclick="this.submit();">
                    LOGOUT
                    <input type="hidden" name="method" value="logout">
                </form>
            </div>
        </div>
    HTML;
}

function SetSidebar() {
    return <<<HTML
        <div class="-sidebar">
            <div class="-sidebar__dashboard -sidebar__tab">
                <div class="-sidebar__tab__box -script__link" data-href="dashboard/">
                    <div class="-sidebar__tab__box__icon -title -center__flex">
                        <span class="material-symbols-rounded">
                            home
                        </span>
                    </div>
                    <div class="-sidebar__tab__box__label">
                        Dashboard
                    </div>
                </div>
            </div>
            <div class="-sidebar__user -sidebar__tab">
                <div class="-sidebar__tab__box -script__link" data-href="dashboard/users/">
                    <div class="-sidebar__tab__box__icon -title -center__flex">
                        <span class="material-symbols-rounded">
                            person
                        </span>
                    </div>
                    <div class="-sidebar__tab__box__label">
                        Users
                    </div>
                </div>
            </div>
            <div class="-sidebar__request -sidebar__tab">
                <div class="-sidebar__tab__box -script__link" data-href="dashboard/requests/">
                    <div class="-sidebar__tab__box__icon -title -center__flex">
                        <span class="material-symbols-rounded">
                            person_add
                        </span>
                    </div>
                    <div class="-sidebar__tab__box__label">
                        Requests
                    </div>
                </div>
            </div>
        </div>
    HTML;
}

function RenderUser($user) {
    $time = strtotime($user["time"]);
    $user["time"] = date("n/j/Y", $time);

    return <<<HTML
        <div class="item">
            <div class="item__box">
                <div class="item__box__id">
                    <div class="item__box__id__icon -center__flex">
                        <img src="assets/user.png">
                    </div>
                    <div class="item__box__id__label">
                        ID {$user["id"]}
                    </div>
                </div>
                <div class="item__box__firstname -center">
                    {$user["firstname"]}
                </div>
                <div class="item__box__lastname -center">
                    {$user["lastname"]}
                </div>
                <div class="item__box__email -center">
                    {$user["email"]}
                </div>
                <div class="item__box__phone -center">
                    {$user["phone"]}
                </div>
                <div class="item__box__date -center">
                    {$user["time"]}
                </div>
                <div class="item__box__options -center__flex" onclick="btnOption(this)">
                    <span class="material-symbols-rounded">
                        more_horiz
                    </span>
                </div>
            </div>
        </div>
    HTML;
}

function RenderRequest($user) {
    $time = strtotime($user["time"]);
    $user["time"] = date("n/j/Y", $time);

    return <<<HTML
        <div class="item">
            <div class="item__box">
                <div class="item__box__id">
                    <div class="item__box__id__icon -center__flex">
                        <img src="assets/user.png">
                    </div>
                    <div class="item__box__id__label">
                        ID {$user["id"]}
                    </div>
                </div>
                <div class="item__box__firstname -center">
                    {$user["firstname"]}
                </div>
                <div class="item__box__lastname -center">
                    {$user["lastname"]}
                </div>
                <div class="item__box__email -center">
                    {$user["email"]}
                </div>
                <div class="item__box__phone -center">
                    {$user["phone"]}
                </div>
                <div class="item__box__date -center">
                    {$user["time"]}
                </div>
                <div class="item__box__options -center__flex" onclick="btnDeny(this)">
                    <span class="material-symbols-rounded">
                        close
                    </span>
                </div>
                <div class="item__box__options -center__flex" onclick="btnAllow(this)">
                    <span class="material-symbols-rounded">
                        check
                    </span>
                </div>
            </div>
        </div>
    HTML;
}

function RenderLog($log) {
    $time = strtotime($log["time"]);
    $log["time"] = date("n/j/Y", $time);

    return <<<HTML
        <div class="item">
            <div class="item__box">
                <div class="item__box__date -center__flex">
                    {$log["time"]}
                </div>
                <div class="item__box__status -center__flex">
                    {$log["status"]}
                </div>
                <div class="item__box__options -center__flex">
                    <span class="material-symbols-rounded">
                        more_horiz
                    </span>
                </div>
            </div>
        </div>
    HTML;
}

?>