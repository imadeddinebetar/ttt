<?php

namespace App\Services;

use App\Core\FileSystem;
use App\Core\Session;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\History;
use App\Enums\ImportStatus;
use DateTime;

class ImportService
{
    private $fileSystem;
    private $historyModel;
    private $vehicleModel;
    private $driverModel;
    public function __construct()
    {
        $this->driverModel = new Driver();
        $this->vehicleModel = new Vehicle();
        $this->historyModel = new History();
        $this->fileSystem = new Filesystem();
    }
    public function prepareImport(array $file)
    {
        $import = [];
        $safeFileName = $this->fileSystem->uniqueName('import_') . '.' . $this->fileSystem->extension($file);
        $this->fileSystem->upload($file, $safeFileName);
        $fileSize = $this->fileSystem->size($safeFileName);

        if ($this->fileSystem->exists($safeFileName)) {
            $import = [
                'imported_by' => 1,
                'file_name' => $safeFileName,
                'file_size' => $fileSize,
                'file_path' => '/uploads/' . $safeFileName,
                'records' => 0,
                'status' => ImportStatus::PENDING,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $import;
    }

    public function isValidHeader(array $imported): bool
    {
        $expectedHeaders = [
            'Nom',
            'Teleph',
            'Email',
            'Ville',
            'Véhicule',
            'Chassis',
            'A IMMAT',
            'RECEPTIONNAIRE',
            'D.Entree',
            'Access Type',
            'KM',
            'Status'
        ];
        $filePath = storage_path($imported['file_path']);
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $firstRow = fgetcsv($handle, 0, ";");
        }

        foreach ($firstRow as $i => $cell) {
            // Remove BOM
            $cell = str_replace("\xEF\xBB\xBF", '', $cell);

            // Convert encoding if needed
            if (!mb_check_encoding($cell, 'UTF-8')) {
                $cell = mb_convert_encoding($cell, 'UTF-8', 'Windows-1252');
            }

            // Trim whitespace
            $cell = trim($cell);

            if ($cell != $expectedHeaders[$i]) {
                return false;
            }
        }

        return true;
    }


    public function loadHistory(array $imported)
    {

        $dataHistory = [];
        $user_id = Session::get('user_id');


        $filePath = storage_path($imported['file_path']);
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            // Optional: Skip the header row
            fgetcsv($handle, 0, ";");

            while (($row = fgetcsv($handle, 0, ";")) !== FALSE) {

                $driver_full_name = $row[0];
                $driver_phone = $row[1];
                $driver_email = $row[2];
                $driver_city = $row[3];
                $vin = $row[5];
                $registration = $row[6];
                $assistant = $row[7];
                $mileage = $row[10];
                $entryDate = $row[8];

                $entryDate = is_numeric($entryDate)
                    ? excelDateToPhpDate($entryDate)
                    : $entryDate;

                $vehicle = $this->vehicleModel->getVehicleByVin([
                    'vin' => $vin,
                ]);

                if (empty($vehicle)) {

                    if ($driver_phone != '') {
                        $driver = $this->driverModel->getDriver([
                            'phone1' => $driver_phone,
                            'email' => $driver_email,
                        ]);
                    }

                    if (empty($driver)) {
                        $parts =  $driver_full_name ? explode(' ', $driver_full_name, 2) : [];
                        $first_name = $parts[0] ?? '';
                        $last_name  = isset($parts[1]) ? $parts[1] : '';
                        $driver = $this->driverModel->createDriver([
                            'created_by' => $user_id,
                            'first_name' =>  $first_name ?? null,
                            'last_name' =>  $last_name ?? null,
                            'phone1' =>  $driver_phone ?? null,
                            'city' =>  $driver_city ?? null,
                            'email' =>  $driver_email ?? null,
                            'created_at' =>  now(),
                            'updated_at' =>  now(),
                        ]);
                    }

                    $vehicle = $this->vehicleModel->createVehicle([
                        'created_by' => $user_id,
                        'driver_id' => $driver['id'] ?? $driver[0]['id'],
                        'vin' => $vin,
                        'registration' => $registration,
                        'oil_plan' => 10000,
                        'created_at' =>  now(),
                        'updated_at' =>  now(),
                    ]);
                }

                $history = [
                    'import_id' => $imported['id'],
                    'assistant' => $assistant,
                    'driver_full_name' =>  $driver_full_name,
                    'driver_phone' => $driver_phone,
                    'driver_email' => $driver_email,
                    'driver_ville' => $driver_city,
                    'vehicle_id' => $vehicle['id'] ?? $vehicle[0]['id'],
                    'vin' => $vin,
                    'registration' => $registration,
                    'mileage' => $mileage,
                    'entry_date' => DateTime::createFromFormat('d/m/Y', $entryDate)->format('Y-m-d')
                ];

                $this->historyModel->createHistory($history);

                $dataHistory[] = $history;
                $vehicle = null;
                $driver = null;
            }
            fclose($handle);
        }

        return $dataHistory;
    }

    public function prepareVehicle($oil_plan,  $vehicleHistory): array
    {

        $firstDay = reset($vehicleHistory);
        $lastDay = end($vehicleHistory);

        $dailyAverage = $this->dailyAverage($firstDay, $lastDay);

        $estimatedDays = (int) ($oil_plan / $dailyAverage);

        $nextOilChangeDate = (new DateTime($lastDay['entry_date']))->modify('+' . round($estimatedDays) . ' days')->format('Y-m-d');

        return [
            'id' => $vehicleHistory['vehicle_id'] ?? $vehicleHistory[0]['vehicle_id'],
            'first_oil_date' => $firstDay['entry_date'],
            'first_mileage' => $firstDay['mileage'],
            'last_mileage' => $lastDay['mileage'],
            'last_oil_date' => $lastDay['entry_date'],
            'daily_average' => $dailyAverage,
            'estimated_days' => $estimatedDays,
            'next_oil_date' => $nextOilChangeDate,
            'updated_at' => now(),
        ];
    }


    private function dailyAverage($firstDay, $lastDay)
    {
        $dailyAverage = 0.0;

        $milesDiff = $lastDay['mileage'] - $firstDay['mileage'];
        $daysDiff = (new DateTime($firstDay['entry_date']))->diff(new DateTime($lastDay['entry_date']))->days;

        if ($daysDiff > 0 && $milesDiff > 0) {
            $dailyAverage = $milesDiff / $daysDiff;
        }

        return number_format($dailyAverage, 2);
    }
}
