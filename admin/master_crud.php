<?php
include 'db/config.php';

// --- CONFIGURATION ---
$TABLE_NAME = 'hubs'; 
$FIELDS = ['hub_code', 'name', 'country_code', 'timezone']; // Columns to edit
// ---------------------

if (isset($_POST['save'])) {
    $vals = [];
    foreach($FIELDS as $f) $vals[] = $_POST[$f];
    
    // Simple Insert logic
    $placeholders = implode(',', array_fill(0, count($FIELDS), '?'));
    $cols = implode(',', $FIELDS);
    $sql = "INSERT INTO $TABLE_NAME ($cols) VALUES ($placeholders)";
    $pdo->prepare($sql)->execute($vals);
}

$data = $pdo->query("SELECT * FROM $TABLE_NAME")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage <?= ucfirst($TABLE_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h3>Manage: <?= ucfirst($TABLE_NAME) ?></h3>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <?php foreach($FIELDS as $field): ?>
                        <div class="mb-2">
                            <label><?= ucfirst(str_replace('_',' ',$field)) ?></label>
                            <input type="text" name="<?= $field ?>" class="form-control" required>
                        </div>
                        <?php endforeach; ?>
                        <button type="submit" name="save" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <table class="table table-striped border">
                <thead>
                    <tr><?php foreach($FIELDS as $f) echo "<th>$f</th>"; ?></tr>
                </thead>
                <tbody>
                    <?php foreach($data as $row): ?>
                    <tr>
                        <?php foreach($FIELDS as $f) echo "<td>{$row[$f]}</td>"; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>