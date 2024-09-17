<?php

chdir("../../");
include("common.php");
Debug();
$admin = GetAdminData();

if ($admin == false) {
    Alert("You are not logged in", "../../");
}

?>

<html>
    <head>
        <title>
            Account | Peso Quanta
        </title>
        <base href="../../">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .account__label {
                padding: 1rem;
                padding-top: 3rem;
                color: var(--theme);
                font-size: 1rem;
            }

            .account__input {
                border-bottom: 0.3rem solid #000;
                font-size: 1rem;
            }

            .account__input > input {
                padding: 0rem;
                font-size: 1rem;
            }

            .account__submit {
                padding-top: 5rem;
            }

            .account__submit > button {
                font-size: 1rem;
            }
        </style>
    </head>
    <body>
        <div class="main__dashboard__account -dashboard">
            <?=SetHeader()?>
            <div class="-content">
                <?=SetSidebar()?>
                <div class="content -center__flex">
                    <form class="-form account" action="server.php" method="post" enctype="multipart/form-data">
                        <div class="account__label -center">
                            Admin_name
                        </div>
                        <div class="account__input">
                            <input class="-input" name="username" required>
                        </div>
                        <div class="account__label -center">
                            Password
                        </div>
                        <div class="account__input">
                            <input class="-input" type="password" name="password" required>
                        </div>
                        <div class="account__label -center">
                            New_Admin_name
                        </div>
                        <div class="account__input">
                            <input class="-input" name="newUsername">
                        </div>
                        <div class="account__label -center">
                            New_Password
                        </div>
                        <div class="account__input">
                            <input class="-input" type="password" name="newPassword">
                        </div>
                        <div class="account__submit -center">
                            <button class="-button" name="method" value="updateAdmin">
                                Confirm Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
    <script src="script.js"></script>
    <script>
        document.querySelector(".-header__account > .-header__tab__box").classList.add("-header__tab__box--selected");
    </script>
</html>