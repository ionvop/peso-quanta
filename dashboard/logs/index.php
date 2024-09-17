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
            Logs | Peso Quanta
        </title>
        <base href="../../">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .table__header {
                display: grid;
                grid-template-columns: repeat(2, 1fr) 5rem;
                color: var(--theme);
            }

            .table__header__date {
                padding: 1rem;
            }

            .table__header__status {
                padding: 1rem;
            }

            .table__render .item {
                padding: 1rem;
            }

            .table__render .item__box {
                display: grid;
                grid-template-columns: repeat(2, 1fr) 4rem;
                border-radius: 1rem;
                background-color: var(--theme__back);
            }

            .table__render .item__box__options {
                padding: 1rem;
                font-size: 3rem;
                cursor: pointer;
                color: var(--theme);
            }
        </style>
    </head>
    <body>
        <div class="main__dashboard__logs -dashboard">
            <?=SetHeader()?>
            <div class="-content">
                <?=SetSidebar()?>
                <div class="content">
                    <div class="table">
                        <div class="table__header">
                            <div class="table__header__date -center">
                                Date
                            </div>
                            <div class="table__header__status -center">
                                Status
                            </div>
                            <div></div>
                        </div>
                        <div class="table__render">
                            <?php
                                $sql = GetDatabase();

                                $query = <<<SQL
                                    SELECT * FROM `admin_logs` WHERE `admin_id` = ? ORDER BY `time` DESC;
                                SQL;

                                $stmt = $sql->prepare($query);
                                $stmt->bind_param("i", $admin["id"]);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while ($log = $result->fetch_assoc()) {
                                    echo RenderLog($log);
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="script.js"></script>
    <script>
        document.querySelector(".-header__logs > .-header__tab__box").classList.add("-header__tab__box--selected");
    </script>
</html>