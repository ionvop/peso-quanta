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
            Users | Peso Quanta
        </title>
        <base href="../../">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .content {
                display: grid;
                grid-template-rows: 1fr max-content;
            }

            .table__header {
                display: grid;
                grid-template-columns: repeat(6, 1fr) repeat(2, 6rem);
                color: var(--theme);
            }

            .table__header__firstname {
                padding: 1rem;
            }

            .table__header__lastname {
                padding: 1rem;
            }

            .table__header__email {
                padding: 1rem;
            }

            .table__header__phone {
                padding: 1rem;
            }

            .table__header__password {
                padding: 1rem;
            }

            .table__header__date {
                padding: 1rem;
            }

            .table__render .item {
                padding: 1rem;
            }

            .table__render .item__box {
                display: grid;
                grid-template-columns: repeat(6, 1fr) repeat(2, 5rem);
                border-radius: 1rem;
                background-color: var(--theme__back);
            }

            .table__render .item__box__id {
                display: grid;
                grid-template-columns: max-content 1fr;
            }

            .table__render .item__box__id__icon {
                padding: 1rem;
            }

            .table__render .item__box__id__icon > img {
                width: 5rem;
            }

            .table__render .item__box__id__label {
                display: flex;
                align-items: center;
                padding: 1rem;
                font-weight: bold;
                font-size: 1rem;
                color: var(--theme);
            }

            .table__render .item__box__firstname {
                padding-top: 2.5rem;
                overflow: auto;
            }

            .table__render .item__box__lastname {
                padding-top: 2.5rem;
                overflow: auto;
            }

            .table__render .item__box__email {
                padding-top: 2.5rem;
                overflow: auto;
            }

            .table__render .item__box__phone {
                padding-top: 2.5rem;
                overflow: auto;
            }

            .table__render .item__box__date {
                padding-top: 2.5rem;
                overflow: auto;
            }

            .table__render .item__box__options {
                font-size: 3rem;
                cursor: pointer;
                color: var(--theme);
            }

            .new {
                display: grid;
                grid-template-columns: 1fr max-content;
            }

            .new__button {
                padding: 1rem;
            }

            .new__button > button > span {
                font-weight: bolder;
            }
        </style>
    </head>
    <body>
        <div class="main__dashboard__users -dashboard">
            <?=SetHeader()?>
            <div class="-content">
                <?=SetSidebar()?>
                <div class="content">
                    <div class="table">
                        <div class="table__header">
                            <div></div>
                            <div class="table__header__firstname -center">
                                First name
                            </div>
                            <div class="table__header__lastname -center">
                                Last name
                            </div>
                            <div class="table__header__email -center">
                                Email
                            </div>
                            <div class="table__header__phone -center">
                                Phone
                            </div>
                            <div class="table__header__date -center">
                                Date
                            </div>
                            <div></div>
                        </div>
                        <div class="table__render">
                            <?php
                                $sql = GetDatabase();

                                $query = <<<SQL
                                    SELECT *
                                    FROM `users`
                                    WHERE `is_verified` = 0;
                                SQL;

                                $stmt = $sql->prepare($query);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while ($user = $result->fetch_assoc()) {
                                    echo RenderRequest($user);
                                }
                            ?>
                        </div>
                    </div>
                    <!-- <div class="new">
                        <div></div>
                        <div class="new__button">
                            <button class="-button -title">
                                <span class="material-symbols-rounded">
                                    add
                                </span>
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </body>
    <script src="script.js"></script>
    <script>
        document.querySelector(".-sidebar__request > .-sidebar__tab__box").classList.add("-sidebar__tab__box--selected");

        function btnAllow(element) {
            let userId = element.parentElement.querySelector(".item__box__id__label").innerText.substring(2);

            if (confirm("Are you sure you want to verify this user?")) {
                let form = document.createElement("form");
                form.action = "server.php";
                form.method = "post";

                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "method";
                input.value = "verifyUser";

                let input2 = document.createElement("input");
                input2.type = "hidden";
                input2.name = "id";
                input2.value = userId;

                form.appendChild(input);
                form.appendChild(input2);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function btnDeny(element) {
            let userId = element.parentElement.querySelector(".item__box__id__label").innerText.substring(2);

            if (confirm("Are you sure you want to deny this user?")) {
                let form = document.createElement("form");
                form.action = "server.php";
                form.method = "post";

                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "method";
                input.value = "denyUser";

                let input2 = document.createElement("input");
                input2.type = "hidden";
                input2.name = "id";
                input2.value = userId;

                form.appendChild(input);
                form.appendChild(input2);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</html>