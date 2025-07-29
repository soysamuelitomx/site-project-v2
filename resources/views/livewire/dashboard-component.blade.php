@extends('layouts.app')
@section('title', 'Dashboard | IoT Platform')

@section('content')
<section class="dashboard" aria-label="Dashboard overview">

    <figure class="dashboard__widget dashboard__widget--avg-temperature" aria-labelledby="avg-temperature-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="avg-temperature-title">Average Temperature</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="avg-temperature-data" class="dashboard__widget-data" value="">{{ $averageTemperature }}</data>
            <img class="dashboard__widget-img" src="{{ asset('images/dashboard_images/snowflake.png') }}" alt="Cold temperature icon" />
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--avg-humidity" aria-labelledby="avg-humidity-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="avg-humidity-title">Average Humidity</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="avg-humidity-data" class="dashboard__widget-data" value="">{{ $averageHumidity }}</data>
            <img class="dashboard__widget-img" src="{{ asset('images/dashboard_images/humidity.png') }}" alt="Humidity level icon" />
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--max-temperature" aria-labelledby="max-temperature-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="max-temperature-title">Maximum Temperature</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="max-temperature-data" class="dashboard__widget-data" value="">{{ $maxTemperature }}</data>
            <img class="dashboard__widget-img" src="{{ asset('images/dashboard_images/thermometer.png') }}" alt="Max temperature icon" />
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--min-humidity" aria-labelledby="min-humidity-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="min-humidity-title">Minimum Humidity</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="min-humidity-data" class="dashboard__widget-data" value="">{{ $minHumidity }}</data>
            <img class="dashboard__widget-img" src="{{ asset('images/dashboard_images/droplet.png') }}" alt="Min humidity icon" />
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--bar-chart" aria-labelledby="line-chart-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title">Temperature & Humidity</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        <canvas id="lineChart" role="img" aria-label="Line chart showing temperature and humidity trends"></canvas>
        <button class="dashboard__widget-button dashboard__widget-button--canva-button" id="toggle-line-chart-data" aria-pressed="false">Toggle Temperature/Humidity</button>
    </figure>

    <figure class="dashboard__widget dashboard__widget--pie-chart" aria-labelledby="pie-chart-title">
        <canvas id="pieChart" role="img" aria-label="Pie chart showing threshold status distribution"></canvas>
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="pie-chart-title"></h3>
        </figcaption>
        <button class="dashboard__widget-button dashboard__widget-button--canva-button" id="toggle-pie-chart-data" aria-pressed="false">Toggle Temperature/Humidity</button>
    </figure>

    <figure class="dashboard__widget dashboard__widget--threshold-compare" aria-labelledby="prev-day-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="threshold-compare-title">Thresholds</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="threshold-compare-data" class="dashboard__widget-data" value="">{{ $temp_max }}</data>
            <data id="threshold-compare-data" class="dashboard__widget-data" value="">{{ $humidity_max }}</data>
            <img class="dashboard__widget-img" src="{{ asset('images/dashboard_images/threshold-compare.png') }}" alt="Arrow down icon representing decrease" />
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--first-threshold" aria-labelledby="temp-variation-title temp-variation-time">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="temp-variation-title">Daily Temperature Variation</h3>
            <time datetime="{{ now()->toIso8601String() }}" class="dashboard__widget-time" id="temp-variation-time">
                {{ now()->format('F j, Y \a\t H:i') }}
            </time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="temperature-variation-data" class="dashboard__widget-data" value="{{ $temperatureDifference }}">
                {{ $temperatureDifference }}
            </data>
            <img class="dashboard__widget-img" src="{{ asset('images/dashboard_images/warning.png') }}" alt="Temperature variation warning icon" />
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--second-threshold" aria-labelledby="humidity-variation-title humidity-variation-time">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="humidity-variation-title">Daily Humidity Variation</h3>
            <time datetime="{{ now()->toIso8601String() }}" class="dashboard__widget-time" id="humidity-variation-time">
                {{ now()->format('F j, Y \a\t H:i') }}
            </time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="humidity-variation-data" class="dashboard__widget-data" value="{{ $humidityDifference }}">
                {{ $humidityDifference }}
            </data>
            <img class="dashboard__widget-img" src="{{ asset('images/dashboard_images/warning.png') }}" alt="Humidity variation warning icon" />
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--broker-status" aria-labelledby="broker-status-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="broker-status-title">Broker test</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="broker-status-data" class="dashboard__widget-data-broker-status"></data>
        </div>
        <button id="connect-to-broker-button" class="dashboard__widget-button dashboard__widget-button--connect-to-broker-button"></button>
    </figure>

    <figure class="dashboard__widget dashboard__widget--recent-records" aria-labelledby="records-table-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="records-table-title">Recent Records Table</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        @if (empty($records))
            <p class="dashboard__widget-empty">No records available.</p>
        @else 
            <table class="dashboard__widget-table">
                <thead class="dashboard__widget-table-thead">
                    <tr>
                        <th>Temperature</th>
                        <th>Humidity</th>
                        <th>Date Time</th>
                        <th>Threshold Temp Type</th>
                        <th>Threshold Humidity Type</th>
                    </tr>
                </thead>
                <tbody class="dashboard__widget-table-tbody">
                        @foreach ($records as $record)
                            <tr>
                                <td>{{ $record->temperature }} °C</td>
                                <td>{{ $record->humidity }} %</td>
                                <td>{{ $record->measured_at }}</td>
                                <td data-threshold="{{ $record->threshold_temp_type }}">{{ $record->threshold_temp_type }}</td>
                                <td data-threshold="{{ $record->threshold_humidity_type }}">{{ $record->threshold_humidity_type }}</td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        @endif
    </figure>

    <figure class="dashboard__widget dashboard__widget--last-disconnection" aria-labelledby="disconnection-title">
        <figcaption class="dashboard__widget-figcaption">
            <h3 class="dashboard__widget-title" id="disconnection-title">Last Disconnection</h3>
            <time datetime="" class="dashboard__widget-time">{{ now()->format('F j, Y \a\t H:i') }}</time>
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="last-disconnection-data" class="dashboard__widget-data">{{ auth()->user()->last_login_at }}</data>
        </div>
    </figure>
    <dialog id="alert-modal" class="dashboard__modal" role="alertdialog" aria-labelledby="modal-alert" aria-describedby="modal-description ">
        <div class="dashboard__modal-icon-container">
            <i id="icon-modal" class="dashboard__modal-icon"></i>
        </div>

        <div class="dashboard__modal-paragraph-container">
            <p id="paragraph-modal" class="dashboard__paragraph"></p>
        </div>

        <div class="dashboard__modal-button-container">
            <button id="button-modal" class="dashboard__modal-button" aria-label="Close modal">✖</button>
        </div>
    </dialog>

</section>

<script>
    window.mqttConfig = @json($mqttConfig);
</script>

<script id="telemetry-json" type="application/json">
    {!! json_encode([
        'latestTemperatureRecords' => $latestTemperatureRecords,
        'latestHumidityRecords' => $latestHumidityRecords,
        'timestamps' => $timestamps,
        'temperatureThresholdCounts' => $temperatureThresholdCounts,
        'humidityThresholdCounts' => $humidityThresholdCounts,
    ]) !!}
</script>

@endsection
