<?php

require __DIR__.'/vendor/autoload.php';

$sqlite = new PDO('sqlite:'.__DIR__.'/database/database.sqlite');
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$mysql = new PDO('mysql:host=127.0.0.1;port=3307;dbname=sigc;charset=utf8mb4', 'sigc', 'sigc');
$mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$mysql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$tables = [
  'roles',
  'permissions', 
  'clinicas',
  'especialidades',
  'users',
  'role_user',
  'permission_role',
  'invites',
  'pacientes',
  'medicos',
  'horarios',
  'consultas',
  'notas',
  'notificaciones'
];

$mysql->exec('SET FOREIGN_KEY_CHECKS=0');

foreach ($tables as $table) {
    echo "Migrating {$table}...\n";
    $mysql->exec("TRUNCATE TABLE `{$table}`");
    
    $stmt = $sqlite->query("SELECT * FROM `{$table}`");
    $rows = $stmt->fetchAll();
    
    if (empty($rows)) {
        echo "  Empty\n";
        continue;
    }
    
    $columns = array_keys($rows[0]);
    $placeholders = implode(',', array_fill(0, count($columns), '?'));
    $cols = implode('`,`', $columns);
    
    $insert = $mysql->prepare("INSERT INTO `{$table}` (`{$cols}`) VALUES ({$placeholders})");
    
    foreach ($rows as $row) {
        $insert->execute(array_values($row));
    }
    
    echo "  Copied " . count($rows) . " rows\n";
}

$mysql->exec('SET FOREIGN_KEY_CHECKS=1');
echo "\nDone!\n";
