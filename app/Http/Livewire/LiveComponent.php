<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class LiveComponent extends Component
{

    public $mqttConfig;

    public $temp_min;
    public $temp_max;
    public $humidity_min;
    public $humidity_max;

    public function mount() {
        $mqttConfigQuerie = DB::selectOne("SELECT ip_address, port FROM mqtt_configurations LIMIT 1");
        $this->mqttConfig = $mqttConfigQuerie ? (array) $mqttConfigQuerie : null;
        $threshold = DB::table('thresholds')->first();
        if (!$threshold) {
            $this->temp_max = 'Not defined';
            $this->temp_min = 'Not defined';
            $this->humidity_min = 'Not defined';
            $this->humidity_max = 'Not defined';
        } else {
            $this->temp_max = $threshold->temp_max . "°C";
            $this->temp_min = $threshold->temp_min . "°C";
            $this->humidity_max = $threshold->humidity_max . "%";
            $this->humidity_min = $threshold->humidity_min . "%";
        }
    }
    public function render()
    {
        return view('livewire.live-component');
    }
}
