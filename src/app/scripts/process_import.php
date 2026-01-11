<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../helpers.php';


App\Core\Env::load(__DIR__ . '/../../.env');

$host = env('DB_HOST');
$db   = env('DB_NAME');
$user = env('DB_USER');
$pass = env('DB_PASSWORD');
$charset = 'utf8mb4';
$connection = null;

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];


$connection = new PDO($dsn, $user, $pass, $options);


$importId = (int)$argv[1];


$stmt = $connection->prepare("SELECT * FROM oil_change_history WHERE import_id = :import_id");
$stmt->execute(['import_id' => $importId]);
$dataHistory = $stmt->fetchAll();

$vehicleIds = array_unique(array_column($dataHistory, 'vehicle_id'));

foreach ($vehicleIds as $vehicleId) {

    $stmt = $connection->prepare("SELECT * FROM vehicles WHERE id = :vehicle_id");
    $stmt->execute(['vehicle_id' => $vehicleId]);
    $vehicle = $stmt->fetch();

    $oil_plan = $vehicle['oil_plan'] ?? 10000;

    $stmt = $connection->prepare(
        "SELECT * FROM oil_change_history 
         WHERE vehicle_id = :vehicle_id 
         ORDER BY entry_date ASC, mileage ASC"
    );
    $stmt->execute(['vehicle_id' => $vehicleId]);
    $vehicleHistory = $stmt->fetchAll();

    if (count($vehicleHistory) > 1) {

        $dataVehicle = [];


        $firstDay = reset($vehicleHistory);
        $lastDay = end($vehicleHistory);

        $milesDiff = $lastDay['mileage'] - $firstDay['mileage'];
        $firstDate = new \DateTime($firstDay['entry_date']);
        $lastDate  = new \DateTime($lastDay['entry_date']);
        $daysDiff  = $firstDate->diff($lastDate)->days;

        if ($daysDiff > 0 && $milesDiff > 0) {
            $dailyAverage = $milesDiff / $daysDiff;
            $estimatedDays = (int) ($oil_plan / $dailyAverage);

            $nextOilChangeDate = (new DateTime($lastDay['entry_date']))
                ->modify('+' . round($estimatedDays) . ' days')
                ->format('Y-m-d');


            $dataVehicle = [
                'id' => $vehicleHistory[0]['vehicle_id'],
                'first_oil_date' => $firstDay['entry_date'],
                'first_mileage' => $firstDay['mileage'],
                'last_mileage' => $lastDay['mileage'],
                'last_oil_date' => $lastDay['entry_date'],
                'daily_average' => round($dailyAverage, 2),
                'estimated_days' => $estimatedDays,
                'next_oil_date' => $nextOilChangeDate,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } 
        
        if (!empty($dataVehicle)) {
            $stmt = $connection->prepare("UPDATE vehicles SET first_oil_date = :first_oil_date, first_mileage = :first_mileage, last_mileage = :last_mileage, last_oil_date = :last_oil_date, daily_average = :daily_average, estimated_days = :estimated_days, next_oil_date = :next_oil_date, updated_at = :updated_at WHERE id = :id");
            $stmt->execute($dataVehicle);
        }
    }
}


$stmt = $connection->prepare("UPDATE imports SET status = 'completed', updated_at = :updated_at WHERE id = :import_id");
$data = ['import_id' => $importId, 'updated_at' => date('Y-m-d H:i:s')];
$stmt->execute($data);

$connection = null;