<?php
date_default_timezone_set('Asia/Jakarta');

$dbServername = "localhost";
$dbUsername = "lalymyid_lolo";
$dbPassword = "FFburik@11";
$dbName = "lalymyid_fufufafa";

$connect = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);


if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}


if (isset($_POST['add_record'])) {
    $reason = $_POST['reason'];
    $price = $_POST['price'];
    
    $sql = "INSERT INTO history_records (reason, price, datetime) VALUES ('$reason', '$price', NOW())";
    $connect->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


if (isset($_POST['edit_record'])) {
    $id = $_POST['edit_id'];
    $reason = $_POST['reason'];
    $price = $_POST['price'];
    $sql = "UPDATE history_records SET reason='$reason', price='$price' WHERE id=$id";
    $connect->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['delete_record'])) {
    $id = $_POST['edit_id'];
    $sql = "DELETE FROM history_records WHERE id=$id";
    $connect->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT * FROM history_records ORDER BY datetime DESC";
$result = $connect->query($sql);

$sqlReasons = "SELECT DISTINCT reason FROM history_records";
$resultReasons = $connect->query($sqlReasons);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Records</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        #history-list {
    display: flex;
    flex-direction: column-reverse;
    max-height: 400px;
    overflow-y: auto;
}

        
        #history-list .record {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        button {
            margin: 10px;
        }
        #modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            display: none; /* Hide modal initially */
        }
        .reason-btn {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>History</h1>

    <div id="history-list">
        <?php
        $currentDate = null;
        $totalPerDay = 0;
        while ($row = $result->fetch_assoc()) {
            $date = date('Y-m-d', strtotime($row['datetime']));
            $time = date('H:i', strtotime($row['datetime']));
            $dayOfWeek = date('l', strtotime($row['datetime']));
          
            if ($currentDate != $date) {
                if ($currentDate) {
                    echo "<hr>-------- Total for $currentDate ($dayOfWeek): $$totalPerDay<br>";
                }
                $currentDate = $date;
                $totalPerDay = 0;
            }

            $totalPerDay += $row['price'];

            echo "<div class='record'>
                    <span>$time - {$row['reason']} - \${$row['price']}</span>";
            
         
            $timeDiff = time() - strtotime($row['datetime']);
            if ($timeDiff <= 86400) {
                echo "<button class='edit-btn' data-id='{$row['id']}'>Edit</button>";
            }

            echo "</div>";
        }
        if ($currentDate) {
            echo "<hr>-------- Total for $currentDate ($dayOfWeek): $$totalPerDay<br>";
        }
        ?>
    </div>

  
    <button id="new-record-btn">Tambahkan</button>

    <div id="modal">
        <form id="record-form" method="POST" action="">
            <label for="reason">Reason:</label>
            <div id="reason-buttons">
                <?php while ($rowReason = $resultReasons->fetch_assoc()): ?>
                    <button type="button" class="reason-btn"><?= $rowReason['reason'] ?></button>
                <?php endwhile; ?>
            </div>
            <input type="text" id="reason-input" name="reason" required>
            
            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price-input" name="price" required>
            
            <input type="hidden" id="record-id" name="edit_id">
            
            <button type="submit" name="add_record" id="add-btn">Add Record</button>
            <button type="submit" name="edit_record" id="edit-btn" style="display:none;">Save Changes</button>
            <button type="submit" name="delete_record" id="delete-btn" style="display:none;">Delete</button>
        </form>
    </div>

    <script>
        document.getElementById('new-record-btn').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'block';
            document.getElementById('add-btn').style.display = 'block';
            document.getElementById('edit-btn').style.display = 'none';
            document.getElementById('delete-btn').style.display = 'none';
            document.getElementById('record-form').reset();
        });

        document.querySelectorAll('.reason-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('reason-input').value = this.textContent;
            });
        });

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const recordId = this.dataset.id;
                document.getElementById('record-id').value = recordId;
                document.getElementById('modal').style.display = 'block';
                document.getElementById('add-btn').style.display = 'none';
                document.getElementById('edit-btn').style.display = 'block';
                document.getElementById('delete-btn').style.display = 'block';
            });
        });
    </script>
</body>
</html>
