<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Bus;
use Illuminate\Http\Request;

use App\Services\DataExportService;
use App\Jobs\ImportCSVData;

use App\Models\ImportData;

class ImportDataController extends Controller
{
    public function export()
    {
        $exportService = new DataExportService();
        
        if(ob_get_contents()){
            ob_end_clean();
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export_data_limited.csv"',
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function () use ($exportService) {
            $exportService->exportData();
        }, 200, $headers);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->getRealPath();
                $csv = file($filePath);
                $chunks = array_chunk($csv, 1000);
                $header = [];
                $batch = Bus::batch([])->dispatch();

                $header = array_map('trim', str_getcsv($csv[0]));
                $requiredColumns = ['name', 'email', 'phone', 'gender', 'reg_number'];
                if (array_diff($requiredColumns, $header)) {
                    return redirect()->route('home')->withErrors([
                        'error' => 'The uploaded file must contain the required columns: name, email, phone, gender, reg_number.',
                    ]);
                }

                foreach ($chunks as $key => $chunk) {
                    $data = array_map('str_getcsv', $chunk);

                    if ($key === 0) {
                        $header = $data[0];
                        unset($data[0]);
                    }

                    $batch->add(new ImportCSVData($data, $header));
                }

                return redirect()->route('home')->with('success', 'CSV import added to the queue. You will be updated once it is complete. <a href="'.route('csv.import.logs').'" target="_blank">View Logs</a>');
            } else {
                return redirect()->route('home')->withErrors(['error' => 'File upload is required.']);
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showLogs()
    {
        $logFilePath = storage_path('logs/csv_import.log');

        if (File::exists($logFilePath)) {
            $logs = File::get($logFilePath);
            return response($logs, 200, [
                'Content-Type' => 'text/plain',
            ]);
        }

        return response('No logs found.', 404);
    }
}
