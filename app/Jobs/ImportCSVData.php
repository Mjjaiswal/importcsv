<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batchable;

use App\Models\ImportData;

class ImportCSVData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $header;
    protected $importData;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param array $header
     */
    public function __construct(array $data, array $header)
    {
        $this->importData = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->importData as $data) {
            try {
                if (count($this->header) !== count($data)) {
                    //Log::channel('csv_import')->warning('Skipping row due to mismatched header count', ['importData' => $importInput, 'data' => $data]);
                    continue;
                }
                
                $importInput = array_combine($this->header, $data);

                if (!$this->hasRequiredColumns($importInput)) {
                    Log::channel('csv_import')->warning('Skipping row due to missing required columns', ['data' => $data]);
                    continue;
                }

                [$isValid, $errorMessage] = $this->validateData($importInput);
                if (!$isValid) {
                    Log::channel('csv_import')->warning("Skipping row due to validation errors: $errorMessage", ['data' => $data]);
                    continue;
                }

                ImportData::create($importInput);
            } catch (\Exception $e) {
                Log::channel('csv_import')->warning('Error importing row', [
                    'data' => $data,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }


    protected function hasRequiredColumns(array $data): bool
    {
        $requiredColumns = ['name', 'email', 'phone', 'gender', 'reg_number'];
        foreach ($requiredColumns as $column) {
            if (empty($data[$column])) {
                return false;
            }
        }
        return true;
    }

    protected function validateData(array $data): array
    {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [false, 'Invalid email format'];
        }

        if (!preg_match('/^\d{10}$/', $data['phone'])) {
            return [false, 'Phone number must be a 10-digit number'];
        }

        $validGenders = ['male', 'female', 'other'];
        if (!in_array(strtolower($data['gender']), $validGenders, true)) {
            return [false, "Gender must be one of 'male', 'female', or 'other'"];
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $data['reg_number'])) {
            return [false, 'Registration number must be alphanumeric'];
        }

        return [true, ''];
    }
}
