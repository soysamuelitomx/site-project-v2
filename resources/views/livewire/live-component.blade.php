@extends('layouts.app')
@section('title', 'Monitoring | IoT Platform')
@section('content')

<section class="dashboard" role="region" aria-label="Real-time IoT Monitoring Widgets">

    <header class="dashboard__widget dashboard__widget--header" role="group" aria-label="Monitoring Controls">
        <button id="toggle-monitoring" class="dashboard__header-button" aria-pressed="false"></button>
    </header>

    <figure class="dashboard__widget dashboard__widget--temperature-realtime" role="region" aria-labelledby="temperature-caption">
        <figcaption id="temperature-caption" class="dashboard__widget-figcaption dashboard__widget-figcaption--temperature">
            Current Temperature Indicator
        </figcaption>
        <data id="temperature-data" class="dashboard__widget-data dashboard__widget-data--temperature-realtime" value="" aria-live="polite">0 °C</data>
    </figure>

    <figure class="dashboard__widget dashboard__widget--humidity-realtime" role="region" aria-labelledby="humidity-caption">
        <figcaption id="humidity-caption" class="dashboard__widget-figcaption dashboard__widget-figcaption--humidity">
            Current Humidity Indicator
        </figcaption>
        <data id="humidity-data" class="dashboard__widget-data dashboard__widget-data--humidity-realtime" value="" aria-live="polite">0 %</data>
    </figure>

    <figure class="dashboard__widget dashboard__widget--thresholds" role="region" aria-labelledby="thresholds-caption">
        <figcaption id="thresholds-caption" class="dashboard__widget-figcaption dashboard__widget-figcaption--thresholds">
            Thresholds Overview
        </figcaption>
        <div class="dashboard__widget-content">
            <data id="temperature-min-data" class="dashboard__widget-data dashboard__widget-data--threshold-temperature-min" value="" aria-live="polite">{{ $temp_min }}</data>
            <data id="temperature-max-data" class="dashboard__widget-data dashboard__widget-data--threshold-temperature-max" value="" aria-live="polite">{{ $temp_max }}</data>
            <data id="humidity-min-data" class="dashboard__widget-data dashboard__widget-data--threshold-humidity-min" value="" aria-live="polite">{{ $humidity_min }}</data>
            <data id="humidity-max-data" class="dashboard__widget-data dashboard__widget-data--threshold-humidity-max" value="" aria-live="polite">{{ $humidity_max }}</data>
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--temperature-chart" role="region" aria-labelledby="temperature-chart-caption">
        <figcaption id="temperature-chart-caption" class="dashboard__widget-figcaption dashboard__widget-figcaption--temperature">
            Hourly Temperature History
        </figcaption>
        <div class="dashboard__widget-chart-container">
            <canvas id="temperature-chart" role="img" aria-label="Line chart showing temperature history over time"></canvas>
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--humidity-chart" role="region" aria-labelledby="humidity-chart-caption">
        <figcaption id="humidity-chart-caption" class="dashboard__widget-figcaption dashboard__widget-figcaption--humidity">
            Hourly Humidity History
        </figcaption>
        <div class="dashboard__widget-chart-container">
            <canvas id="humidity-chart" role="img" aria-label="Line chart showing humidity history over time"></canvas>
        </div>
    </figure>

    <figure class="dashboard__widget dashboard__widget--broker-status" role="region" aria-labelledby="broker-caption">
        <figcaption id="broker-caption" class="dashboard__widget-figcaption dashboard__widget-figcaption--broker-status"></figcaption>
        <div class="dashboard__widget-content">
            <data id="broker-data-status" class="dashboard__widget-data-broker-status" aria-live="polite"></data>
        </div>
    </figure>

</section>
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

<script>
    window.mqttConfig = @json($mqttConfig);
</script>

@endsection
