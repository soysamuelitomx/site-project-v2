<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuerieComponent extends Component
{
    public $startDate, $startTime;
    public $endDate, $endTime;
    public $temperatureThresholdCounts = [];
    public $humidityThresholdCounts = [];

    public $querie = [];

    public function querie()
    {
        if (!$this->startDate || !$this->startTime || !$this->endDate || !$this->endTime) {
            return;
        }

        $start = Carbon::parse("{$this->startDate} {$this->startTime}");
        $end = Carbon::parse("{$this->endDate} {$this->endTime}");

        $this->querie = DB::select(
            "SELECT temperature, humidity, created_at AS measured_at,
                    threshold_temp_type, threshold_humidity_type,
                    thresholds_defined
            FROM telemetry_data
            WHERE created_at BETWEEN ? AND ?
            ORDER BY created_at DESC",
            [$start, $end]
        );
    }

    public function clearForm()
    {
        $this->startDate = null;
        $this->startTime = null;
        $this->endDate = null;
        $this->endTime = null;

        $this->querie = [];
    }

    public function render()
    {
        return view('livewire.querie-component')->layout('layouts.app');
    }
}
