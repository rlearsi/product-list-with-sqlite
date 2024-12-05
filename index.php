<?php

    require './connect.php';
    require './products.php';

    $model = new Products();
    
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        .container {
            background-color: #1c1c1c;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
            max-width: 600px;
            width: 100%;
            text-align: left;
            margin-top: 20px;
        }
        .notification {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #00bcd4;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 188, 212, 0.5);
            z-index: 1000;
            display: none;
        }
        b {
            color: #f0ad4e;
        }
        .details {
            display: none;
            margin-left: 20px;
        }
        .toggle-btn {
            cursor: pointer;
            color: #00bcd4;
            text-decoration: underline;
        }
        .edit-form, .add-form {
            margin-top: 10px;
        }
        .edit-form input[type="text"], .add-form input[type="text"],
        .edit-form input[type="number"], .add-form input[type="number"] {
            padding: 5px;
            margin-right: 5px;
        }
        .edit-form button, .add-form button {
            padding: 5px 10px;
            background-color: #00bcd4;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .search-bar {
            margin-bottom: 20px;
            width: 96%;
            padding: 10px;
            border: 1px solid #00bcd4;
            border-radius: 5px;
            color: white;
            background-color: #333;
        }
    </style>
    <script>
        function toggleDetails(id) {
            const details = document.getElementById(id);
            details.style.display = details.style.display === "block" ? "none" : "block";
        }

        function toggleEditForm(itemId, detailId) {
            const form = document.getElementById(`edit-form-${itemId}-${detailId}`);
            form.style.display = form.style.display === "none" ? "block" : "none";
        }

        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        function filterItems() {
            const searchQuery = document.getElementById('search').value.toLowerCase();
            const items = document.querySelectorAll('.item-container');

            items.forEach(item => {
                const itemName = item.getAttribute('data-item').toLowerCase();
                if (itemName.includes(searchQuery)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <div id="notification" class="notification"></div>
    <div class="container">
        <input type="text" id="search" class="search-bar" placeholder="Search items..." onkeyup="filterItems()">

        <form method="POST" class="add-form">
            <input type="text" name="add_item" placeholder="Item" required>
            <input type="text" name="add_value" placeholder="Price" required>
            <button type="submit" name="add">Add</button>
        </form>

        <?php

        // Add a new item if the form is submitted
        if (isset($_POST["add"])) {

            $addItem = strtolower(trim($_POST["add_item"]));
            $addValue = $_POST["add_value"];

            // Add um novo
            $model->create([
                'name'  => "{$addItem}",
                'price' => "{$addValue}",
                'dt'    => time()
            ]);

            echo "<p style='color: lightgreen;'>Item <b>" . htmlspecialchars($addItem) . "</b> with value <b>" . htmlspecialchars($addValue) . "</b> added successfully!</p>";
        }

        // Edit an existing item
        if (isset($_POST["index"])) {

            $item_id = $_POST["index"];
            $item_name = $_POST["item_name"];
            $price = $_POST["value"];

            $model->update($item_id, [
                'price' => "{$price}"
            ]);

            echo "<script>showNotification(\"Item $item_name updated successfully with value $price!\");</script>";
        }

        function formatarValor($value) {

            if (substr($value, -2) === "kk") {
                $value = (int)str_replace("kk", "", $value) * 1000000;
            } elseif (substr($value, -1) === "k") {
                $value = (int)str_replace("k", "", $value) * 1000;
            } else {
                $value = (int)$value;
            }

        }

        // Function to format values
        function formatValue($value) {
            if ($value >= 1000000) {
                return round($value / 1000000, 2) . "kk";
            } elseif ($value >= 1000) {
                return round($value / 1000, 2) . "k";
            }
            return $value;
        }

        echo "</br></br>";

        $all = $model->allByGroup();
        
        $hasRecords = false;

            // Loop com while
            while ($user = $all->fetch(PDO::FETCH_ASSOC)) {

                $hasRecords = true;

                $id = htmlspecialchars($user['id']);

                $name = htmlspecialchars($user['name']);
                $price = htmlspecialchars($user['price']);
                $sells = htmlspecialchars($user['sells']);
                $earnings = htmlspecialchars($user['earnings']);

                echo "<div class='item-container' data-item='item-$name'>";
                echo "<b>" . ucwords($name) . "</b></br>";
                echo "  Média de Valor: <b>" . ($price) . "</b></br>";
                echo "  Ganhos Totais: <b>" . $earnings . "</b></br>";
                echo "  Total de vendas: " . $sells . " vezes</br>";
                echo "<span class='toggle-btn' onclick=\"toggleDetails('$id')\">[Expandir]</span></br>";
                echo "<div class='details' id='$id'>";

                $byName = $model->findByName($name);

                while ($group = $byName->fetch(PDO::FETCH_ASSOC)) {

                    $item_id = $group['id'];
                    $item_price = $group['price'];
                    $date = $group['dt'];

                    echo "<b>" . $item_price . "</b> em <i>" .Date("d/m/Y H:i:s", $date). "</i>";
                    echo " <span class='toggle-btn' onclick=\"toggleEditForm('$id', '$item_id')\">[Editar]</span></br>";

                    echo "<form class='edit-form' id='edit-form-$id-$item_id' style='display: none;' method='POST'>";
                    echo "<input type='hidden' name='item_name' value='".$name."' required>";
                    echo "<input type='text' name='value' value='" . $item_price . "' required>";
                    echo "<input type='hidden' name='index' value='" .$item_id. "'>";
                    echo "<button type='submit'>Salvar</button>";
                    echo "</form>";
                }

                echo "</div></br>";
                echo "</div>"; // Fecha item-container

            }

            //echo "</table>";
            if (!$hasRecords) {
                echo "Nenhum registro encontrado.";
            } 

        ?>
    </div>
</body>
</html>
