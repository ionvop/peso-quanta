<?php

include("common.php");
Debug();

if (isset($_COOKIE["admin"])) {
    $admin = GetAdminData();

    if ($admin != false) {
        header("Location: dashboard/");
        exit();
    }

    setcookie("admin", "", time() - 3600);
}

?>

<html>
    <head>
        <title>
            Login | Peso Quanta
        </title>
        <base href="./">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .content {
                height: 99vh;
            }

            .login__title {
                font-weight: bolder;
                text-decoration: underline;
            }

            .login__username {
                display: grid;
                grid-template-columns: max-content 1fr;
                padding-top: 5rem;
                border-bottom: 0.3rem solid #000;
            }

            .login__password {
                display: grid;
                grid-template-columns: max-content 1fr;
                padding-top: 1rem;
                border-bottom: 0.3rem solid #000;
            }
            
            .login__submit {
                padding-top: 5rem;
            }
        </style>
    </head>
    <body>
        <div class="main">
            <div class="content -center__flex">
                <form class="-form login" action="server.php" method="post" enctype="multipart/form-data">
                    <div class="login__title -center -title">
                        ADMIN LOGIN
                    </div>
                    <div class="login__username">
                        <div class="login__username__icon -center__flex -title">
                            <span class="material-symbols-rounded">
                                person
                            </span>
                        </div>
                        <div class="login__username__input">
                            <input class="-input" name="username" placeholder="Admin_name">
                        </div>
                    </div>
                    <div class="login__password">
                        <div class="login__password__icon -center__flex -title">
                            <span class="material-symbols-rounded">
                                lock
                            </span>
                        </div>
                        <div class="login__password__input">
                            <input class="-input" type="password" name="password" placeholder="Password">
                        </div>
                    </div>
                    <div class="login__submit -center">
                        <button class="-button" name="method" value="login">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </body>
    <script src="script.js"></script>
    <script>

    </script>
</html>