<?php

namespace App\Services;

use App\Models\ImportData;

class DataExportService
{
    public function exportData()
    {
        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'ID', 'Name', 'Email', 'Phone', 'Alternate Phone',
            'Gender', 'DOB', 'Registration Number', 'Address',
            'Created At', 'Updated At',
        ]);

        ImportData::select(
            'id', 'name', 'email', 'phone', 'alternate_phone',
            'gender', 'dob', 'reg_number', 'address',
            'created_at', 'updated_at'
        )
        ->orderBy('id')
        ->chunk(1000, function ($rows) use ($output) {
            foreach ($rows as $row) {
                fputcsv($output, $this->sanitizeRow($row));
            }
        });

        fclose($output);
    }

    /**
     * Sanitize each row by stripping HTML tags and ensuring proper formatting.
     */
    private function sanitizeRow($row)
    {
        return [
            $row->id,
            $this->sanitize($row->name),
            $this->sanitize($row->email),
            $this->sanitize($row->phone),
            $this->sanitize($row->alternate_phone ?? 'N/A'),
            ucfirst($this->sanitize($row->gender ?? 'N/A')),
            $row->dob ? $row->dob->format('Y-m-d') : 'N/A',
            $this->sanitize($row->reg_number),
            $this->sanitize($row->address ?? 'N/A'),
            $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : 'N/A',
            $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    /**
     * Helper function to sanitize a string by removing HTML tags and trimming spaces.
     */
    private function sanitize($value)
    {
        return trim(strip_tags($value));
    }
}
