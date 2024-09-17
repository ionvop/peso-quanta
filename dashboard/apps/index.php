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
            Apps | Peso Quanta
        </title>
        <base href="../../">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .render .item {
                padding: 1rem;
                color: var(--theme);
            }

            .render .item__box {
                display: grid;
                grid-template-columns: max-content 1fr max-content;
                border-radius: 1rem;
                background-color: var(--theme__back);
            }

            .render .item__box__icon {
                padding: 1rem;
            }

            .render .item__box__icon > img {
                width: 5rem;
            }

            .render .item__box__title {
                display: flex;
                align-items: center;
                padding: 1rem;
            }

            .render .item__box__options {
                padding: 1rem;
                font-size: 3rem;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="main__dashboard__apps -dashboard">
            <?=SetHeader()?>
            <div class="-content">
                <?=SetSidebar()?>
                <div class="content">
                    <div class="render">
                        <div class="item">
                            <div class="item__box">
                                <div class="item__box__icon">
                                    <img src="assets/logo.png">
                                </div>
                                <div class="item__box__title">
                                    Peso Quanta V1
                                </div>
                                <div class="item__box__options -center__flex">
                                    <span class="material-symbols-rounded">
                                        more_horiz
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="script.js"></script>
    <script>
        document.querySelector(".-sidebar__app > .-sidebar__tab__box").classList.add("-sidebar__tab__box--selected");
    </script>
</html>