<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TelemetryData;

class DashboardComponent extends Component
{
    public $records = [];
    public $latestTemperatureRecords = [];
    public $latestHumidityRecords = [];
    public $timestamps = [];
    public $temperatureThresholdCounts = [];
    public $humidityThresholdCounts = [];
    public $averageTemperature = null;
    public $averageHumidity = null;
    public $maxTemperature = null;
    public $minHumidity = null;

    public $todayAverageTemperature = null;
    public $todayAverageHumidity = null;
    public $yesterdayAverageTemperature = null;
    public $yesterdayAverageHumidity = null;
    public $temperatureDifference = null;
    public $humidityDifference = null;
    public $temp_max = null;
    public $humidity_max = null;
    public $mqttConfig;

    public function mount()
    {
        $this->records = DB::select("
            SELECT 
                temperature,
                humidity,
                measured_at,
                DATE_FORMAT(measured_at, '%D:%H:%i') AS hour_formatted,
                LOWER(threshold_temp_type) as threshold_temp_type,
                LOWER(threshold_humidity_type) as threshold_humidity_type
            FROM telemetry_data
            ORDER BY measured_at DESC
            LIMIT 10
        ");

        if (count($this->records) > 0) {
            $temperatures = array_column($this->records, 'temperature');
            $humidities = array_column($this->records, 'humidity');

            $this->averageTemperature = round(array_sum($temperatures) / count($temperatures), 2) . "°C";
            $this->averageHumidity = round(array_sum($humidities) / count($humidities), 2) . "%";
            $this->maxTemperature = max($temperatures) . "°C";
            $this->minHumidity = min($humidities) . "%";
        } else {
            // No hay datos: asignar valores nulos o default
            $this->averageTemperature = "Not defined";
            $this->averageHumidity = "Not defined";
            $this->maxTemperature = "Not defined";
            $this->minHumidity = "Not defined";
        }

        $this->latestTemperatureRecords = [];
        $this->latestHumidityRecords = [];
        $this->timestamps = [];

        $this->temperatureThresholdCounts = [
            'normal' => 0,
            'warning' => 0,
            'critical' => 0,
        ];
        $this->humidityThresholdCounts = [
            'normal' => 0,
            'warning' => 0,
            'critical' => 0,
        ];

        foreach ($this->records as $r) {
            $this->latestTemperatureRecords[] = $r->temperature;
            $this->latestHumidityRecords[] = $r->humidity;
            $this->timestamps[] = $r->hour_formatted;

            if (isset($this->temperatureThresholdCounts[$r->threshold_temp_type])) {
                $this->temperatureThresholdCounts[$r->threshold_temp_type]++;
            }

            if (isset($this->humidityThresholdCounts[$r->threshold_humidity_type])) {
                $this->humidityThresholdCounts[$r->threshold_humidity_type]++;
            }
        }

        $todayAverages = TelemetryData::getDailyAverages(Carbon::today());
        $yesterdayAverages = TelemetryData::getDailyAverages(Carbon::yesterday());

        $this->todayAverageTemperature = $todayAverages['avg_temperature'];
        $this->todayAverageHumidity = $todayAverages['avg_humidity'];

        $this->yesterdayAverageTemperature = $yesterdayAverages['avg_temperature'];
        $this->yesterdayAverageHumidity = $yesterdayAverages['avg_humidity'];

        if (!is_null($this->todayAverageTemperature) && !is_null($this->yesterdayAverageTemperature)) {
            $this->temperatureDifference = round($this->todayAverageTemperature - $this->yesterdayAverageTemperature, 2) . "°C";
        } else {
            $this->temperatureDifference = "Not defined";
        }

        if (!is_null($this->todayAverageHumidity) && !is_null($this->yesterdayAverageHumidity)) {
            $this->humidityDifference = round($this->todayAverageHumidity - $this->yesterdayAverageHumidity, 2) . "%";
        } else {
            $this->humidityDifference = "Not defined";
        }

        $mqttConfigQuerie = DB::selectOne("SELECT ip_address, port FROM mqtt_configurations LIMIT 1");
        $this->mqttConfig = $mqttConfigQuerie ? (array) $mqttConfigQuerie : null;

        $threshold = DB::table('thresholds')->first();
        if (!$threshold) {
            $this->temp_max = 'Not defined';
            $this->humidity_max = 'Not defined';
        } else {
            $this->temp_max = $threshold->temp_max . "°C";
            $this->humidity_max = $threshold->humidity_max . "%";
        }
    }

    public function render()
    {
        return view('livewire.dashboard-component', [
            'latestTemperatureRecords' => $this->latestTemperatureRecords,
            'latestHumidityRecords' => $this->latestHumidityRecords,
            'timestamps' => $this->timestamps,
            'temperatureThresholdCounts' => $this->temperatureThresholdCounts,
            'humidityThresholdCounts' => $this->humidityThresholdCounts,
        ]);
    }
}
