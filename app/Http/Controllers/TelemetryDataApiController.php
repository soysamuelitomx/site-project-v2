<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TelemetryDataApiController extends Controller
{
    public function getAll()
    {
        $data = DB::select("
            SELECT 
                id,
                temperature,
                humidity,
                created_at AS measured_at,
                threshold_temp_type,
                threshold_humidity_type,
                thresholds_defined
            FROM telemetry_data
            ORDER BY created_at DESC
        ");

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, 'telemetry_data.json', [
            'Content-Type' => 'application/json',
        ]);
    }

}
