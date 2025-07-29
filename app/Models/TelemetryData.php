<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TelemetryData extends Model
{
    protected $table = 'telemetry_data';

    /**
     * 
     *
     * @param Carbon
     * @return array
     */
    public static function getDailyAverages($date): array
    {
        $result = self::selectRaw('
            ROUND(AVG(temperature), 2) AS avg_temperature,
            ROUND(AVG(humidity), 2) AS avg_humidity
        ')
        ->whereBetween('measured_at', [
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        ])
        ->first();

        return $result ? $result->toArray() : [
            'avg_temperature' => null,
            'avg_humidity' => null,
        ];
    }

    public static function classifyThreshold($value, $min, $max)
    {
        if ($value < $min || $value > $max) {
            return 'critical';
        }

        if ($value == $min || $value == $max) {
            return 'warning';
        }

        return 'normal';
    }

}
