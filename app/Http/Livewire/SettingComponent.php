<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class SettingComponent extends Component
{
    public $mqttConfig;
    public $thresholdsConfig;
    public $httpConfig;

    protected $listeners = [
        'updateMqttConfig',
        'updateThresholdsConfig',
        'updateHttpConfig',
    ];

    public function mount()
    {
        $mqttConfigQuerie = DB::selectOne("SELECT ip_address, port FROM mqtt_configurations LIMIT 1");
        $this->mqttConfig = $mqttConfigQuerie ? (array) $mqttConfigQuerie : null;

        $thresholdsConfigQuerie = DB::selectOne("SELECT temp_min, temp_max, humidity_min, humidity_max FROM thresholds LIMIT 1");
        $this->thresholdsConfig = $thresholdsConfigQuerie ? (array) $thresholdsConfigQuerie : null;

        $httpConfigQuerie = DB::selectOne("SELECT http_interval, http_enabled FROM http_configurations LIMIT 1");
        $this->httpConfig = $httpConfigQuerie ? (array) $httpConfigQuerie : null;
    }

    public function updateMqttConfig($mqttConfig)
    {
        $exists = DB::selectOne("SELECT id FROM mqtt_configurations LIMIT 1");

        if ($exists) {
            DB::update("UPDATE mqtt_configurations SET ip_address = ?, port = ?, updated_at = NOW() WHERE id = ?", [
                $mqttConfig['ip_address'],
                $mqttConfig['port'],
                $exists->id,
            ]);
        } else {
            DB::insert("INSERT INTO mqtt_configurations (ip_address, port, created_at, updated_at) VALUES (?, ?, NOW(), NOW())", [
                $mqttConfig['ip_address'],
                $mqttConfig['port'],
            ]);
        }

        $this->mqttConfig = (array) DB::selectOne("SELECT ip_address, port FROM mqtt_configurations LIMIT 1");
    }

    public function updateThresholdsConfig($thresholds)
    {
        $exists = DB::selectOne("SELECT id FROM thresholds LIMIT 1");

        if ($exists) {
            DB::update("UPDATE thresholds SET temp_min = ?, temp_max = ?, humidity_min = ?, humidity_max = ?, updated_at = NOW() WHERE id = ?", [
                $thresholds['temp_min'],
                $thresholds['temp_max'],
                $thresholds['humidity_min'],
                $thresholds['humidity_max'],
                $exists->id,
            ]);
        } else {
            DB::insert("INSERT INTO thresholds (temp_min, temp_max, humidity_min, humidity_max, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())", [
                $thresholds['temp_min'],
                $thresholds['temp_max'],
                $thresholds['humidity_min'],
                $thresholds['humidity_max'],
            ]);
        }

        $this->thresholdsConfig = (array) DB::selectOne("SELECT temp_min, temp_max, humidity_min, humidity_max FROM thresholds LIMIT 1");
    }

    public function updateHttpConfig($httpConfig)
    {
        $exists = DB::selectOne("SELECT id FROM http_configurations LIMIT 1");

        if ($exists) {
            DB::update(
                "UPDATE http_configurations SET http_interval = ?, http_enabled = ?, updated_at = NOW() WHERE id = ?",
                [
                    $httpConfig['http_interval'],
                    $httpConfig['http_enabled'],
                    $exists->id,
                ]
            );
        } else {
            DB::insert(
                "INSERT INTO http_configurations (http_interval, http_enabled, created_at, updated_at) VALUES (?, ?, NOW(), NOW())",
                [
                    $httpConfig['http_interval'],
                    $httpConfig['http_enabled'],
                ]
            );
        }

        $this->httpConfig = (array) DB::selectOne("SELECT http_interval, http_enabled FROM http_configurations LIMIT 1");
    }

    public function render()
    {
        return view('livewire.setting-component');
    }
}
