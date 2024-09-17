<?php

chdir("../");
include("common.php");
Debug();
$admin = GetAdminData();

if ($admin == false) {
    Alert("You are not logged in", "../");
}

?>

<html>
    <head>
        <title>
            Dashboard | Peso Quanta
        </title>
        <base href="../">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .content {
                padding: 1rem;
            }

            .box {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                background-color: var(--theme__back);
                border-radius: 1rem;
                color: var(--theme);
            }

            .box__users {
                padding: 1rem;
            }

            .box__users__display {
                display: grid;
                grid-template-columns: 1fr max-content repeat(2, 1fr);
            }

            .box__users__display__icon {
                padding: 1rem;
            }

            .box__users__display__icon > img {
                width: 7rem;
            }

            .box__users__display__count {
                padding: 1rem;
                font-size: 5rem;
                font-weight: bold;
            }

            .box__users__label {
                padding: 1rem;
                font-weight: bold;
            }

            .box__apps {
                padding: 1rem;
            }

            .box__apps__display {
                display: grid;
                grid-template-columns: 1fr max-content repeat(2, 1fr);
            }

            .box__apps__display__icon {
                padding: 1rem;
            }

            .box__apps__display__icon > img {
                width: 7rem;
            }

            .box__apps__display__version {
                padding: 1rem;
                font-size: 5rem;
                font-weight: bold;
            }

            .box__apps__label {
                padding: 1rem;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="main__dashboard -dashboard">
            <?=SetHeader()?>
            <div class="-content">
                <?=SetSidebar()?>
                <div class="content">
                    <div class="box">
                        <div class="box__users">
                            <div class="box__users__display">
                                <div></div>
                                <div class="box__users__display__icon">
                                    <img src="assets/user.png">
                                </div>
                                <div class="box__users__display__count -center__flex">
                                    <?php
                                        $sql = GetDatabase();

                                        $query = <<<SQL
                                            SELECT COUNT(*) FROM `users`;
                                        SQL;

                                        $stmt = $sql->prepare($query);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $row = $result->fetch_row();
                                        echo $row[0];
                                    ?>
                                </div>
                                <div></div>
                            </div>
                            <div class="box__users__label -title -center">
                                USER
                            </div>
                        </div>
                        <div class="box__apps">
                            <div class="box__apps__display">
                                <div></div>
                                <div class="box__apps__display__icon">
                                    <img src="assets/app.png">
                                </div>
                                <div class="box__apps__display__version -center__flex">
                                    V1
                                </div>
                                <div></div>
                            </div>
                            <div class="box__apps__label -title -center">
                                Application
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="script.js"></script>
    <script>
        document.querySelector(".-sidebar__dashboard > .-sidebar__tab__box").classList.add("-sidebar__tab__box--selected");
    </script>
</html>