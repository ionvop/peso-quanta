<?php

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
    $result = new mysqli("localhost", "root", null, "peso_quanta", 3306);
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
            <div class="-sidebar__app -sidebar__tab">
                <div class="-sidebar__tab__box -script__link" data-href="dashboard/apps/">
                    <div class="-sidebar__tab__box__icon -title -center__flex">
                        <span class="material-symbols-rounded">
                            settings
                        </span>
                    </div>
                    <div class="-sidebar__tab__box__label">
                        Manage App
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
                        User Management
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
                        USER{$user["id"]}
                    </div>
                </div>
                <div class="item__box__username -center__flex">
                    {$user["username"]}
                </div>
                <div class="item__box__password -center__flex">
                    {$user["password"]}
                </div>
                <div class="item__box__date -center__flex">
                    {$user["time"]}
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