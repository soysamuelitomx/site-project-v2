<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TelemetryData;

class TelemetryController extends Controller
{
    // Atributo privado que contendrá el dato completo a insertar
    private array $telemetryData = [];

    public function store(Request $request)
    {
        // Paso 1: Obtener umbrales (si no existen, terminamos)
        $threshold = DB::table('thresholds')->first();

        if (!$threshold) {
            return response()->json([
                'status' => 'skipped',
                'message' => 'No thresholds defined.'
            ], 200);
        }

        // Paso 2: Valores recibidos
        $temperature = $request->input('temperature');
        $humidity = $request->input('humidity');

        // Paso 3: Clasificación usando el modelo
        $tempType = TelemetryData::classifyThreshold($temperature, $threshold->temp_min, $threshold->temp_max);
        $humidityType = TelemetryData::classifyThreshold($humidity, $threshold->humidity_min, $threshold->humidity_max);

        // Paso 4: Fecha y hora actual
        $now = Carbon::now()->toDateTimeString();

        // Paso 5: Compactar los datos en el atributo
        $this->telemetryData = [
            'temperature' => $temperature,
            'humidity' => $humidity,
            'measured_at' => $now,
            'threshold_temp_type' => $tempType,
            'threshold_humidity_type' => $humidityType,
            'thresholds_defined' => $threshold->temp_min . '-' . $threshold->temp_max . '/' . $threshold->humidity_min . '-' . $threshold->humidity_max,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // Paso 6: Ejecutar la inserción con SQL pura
        DB::insert(
            "INSERT INTO telemetry_data 
                (temperature, humidity, measured_at, threshold_temp_type, threshold_humidity_type, thresholds_defined, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($this->telemetryData)
        );

        return response()->json([
            'status' => 'success',
            'data' => $this->telemetryData
        ], 200);
    }
}