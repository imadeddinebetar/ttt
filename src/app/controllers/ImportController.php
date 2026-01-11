<?php

namespace App\Controllers;

use App\Core\Controller as BaseController;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Logger;
use App\Models\Import;
use App\Models\Vehicle;
use App\Models\History;
use App\Services\ImportService;
use App\Enums\ImportStatus;

class ImportController extends BaseController
{
    private $vehicleModel;
    private $importModel;
    private $historyModel;
    private $importService;

    public function __construct()
    {

        Middleware::auth();

        $this->vehicleModel = new Vehicle();
        $this->importModel = new Import();
        $this->historyModel = new History();
        $this->importService = new ImportService();
    }
    public function upload()
    {
        $imports = $this->importModel->getAllImports();
        $this->render('import/upload', [
            'title' => 'Upload History',
            'imports' => $imports
        ]);
    }

    public function store()
    {
        $file = Request::file('ssp_file');

        $importData = $this->importService->prepareImport($file);

        if (!empty($importData) && is_array($importData)) {

            $imported = $this->importModel->createImport($importData);

            if (!empty($imported) && is_array($imported)) {

                $headerValid = $this->importService->isValidHeader($imported);

                if ($headerValid) {

                    $dataHistory = $this->importService->loadHistory($imported);

                    // $this->historyModel->importHistory($dataHistory);

                    $this->importModel->updateImport([
                        'id' => $imported['id'],
                        'records' => count($dataHistory),
                        'updated_at' => now()
                    ]);

                    // $vehicleIds = array_unique(array_column($dataHistory, 'vehicle_id'));

                    // foreach ($vehicleIds as $vehicleId) {

                    //     $vehicle = $this->vehicleModel->getVehicleById(['id' => $vehicleId]);

                    //     $oil_plan = $vehicle[0]['oil_plan'] ?? 10000;

                    //     $vehicleHistory = $this->historyModel->getHistoryByVehicleVin(['vin' =>  $vehicle[0]['vin']]);

                    //     if (count($vehicleHistory) > 1) {
                    //         $dataVehicle = $this->importService->prepareVehicle($oil_plan, $vehicleHistory);
                    //         $this->vehicleModel->updateVehicle($dataVehicle);
                    //     }
                    // }

                    // $this->importModel->updateImport([
                    //     'id' => $imported['id'],
                    //     'status' => ImportStatus::COMPLETED,
                    //     'updated_at' => now()
                    // ]);
                }
            }
        }

        $this->redirect('imports/upload');
    }

    public function predict($id)
    {
        $this->importModel->updateImport([
            'id' => $id,
            'status' => ImportStatus::PROCESSING,
            'updated_at' => now()
        ]);

        $scriptPath = __DIR__ . '/../scripts/process_import.php';
        $logFile = __DIR__ . '/../scripts/script.log';

        // $command = sprintf(
        //     'php %s %d > %s 2>&1 & echo $!',
        //     escapeshellarg($scriptPath),
        //     (int)$id,
        //     escapeshellarg($logFile)
        // );
        $command = 'php ' . escapeshellarg($scriptPath) . ' ' . (int)$id . ' > ' . escapeshellarg($logFile) . ' 2>&1 & echo $!';
        exec($command, $output, $returnCode);
        if ($returnCode !== 0) {
            error_log("Failed to start background process: " . implode("\n", $output));
            throw new Exception("Failed to start import processing");
        }
        
        $this->redirect('imports/upload');
    }
}
