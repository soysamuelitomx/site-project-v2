@section('title', 'Settings | IoT Platform')

<div class="settings">

    <section class="settings__element settings__element--type-config" aria-label="Configuration Settings">

        <form id="connect-to-broker" class="settings__form" method="POST" aria-labelledby="status-title">
            @csrf
            <h2 id="connect-title" class="settings__form-title">Connect to Mqtt Broker</h2>
            <p>
                <label for="status-button">Check if the Mqtt Broker is active.</label>
            </p>
            <div class="settings__form-group--submit-form">
                <button id="connect-to-broker-button" class="settings__submit-button settings__submit-button--connect-to-broker-button" type="submit" aria-live="polite">Connect to broker</button>
            </div>
        </form>

        <form id="get-esp32-data" class="settings__form" method="POST" aria-labelledby="status-title">
            @csrf
            <h2 id="status-title" class="settings__form-title">Status</h2>
            <p>
                <label for="status-button">Check if the ESP32 device is active.</label>
            </p>
            <div class="settings__form-group--submit-form">
                <button id="status-button" class="settings__submit-button settings__submit-button--status-button" type="submit" aria-live="polite">Get Status</button>
            </div>
        </form>

        <form id="http-form" class="settings__form settings__form--http-communication" aria-labelledby="http-title">
            @csrf
            <h2 id="http-title" class="settings__form-title">HTTP Communication</h2>

            <div class="settings__form-group">
                <label for="http-interval">Set HTTP Interval:</label>
                <select class="settings__form-input" name="http_interval" id="http-interval" aria-required="true">
                    <option class="settings__form-input-option" value="" disabled selected>Set interval</option>
                    <option class="settings__form-input-option" value="180000">3m</option>
                    <option class="settings__form-input-option" value="1800000">30m</option>
                    <option class="settings__form-input-option" value="3600000">1h</option>
                    <option class="settings__form-input-option" value="5400000">1h and 30m</option>
                    <option class="settings__form-input-option" value="7200000">2h</option>
                    <option class="settings__form-input-option" value="9000000">2h and 30m</option>
                    <option class="settings__form-input-option" value="10800000">3h</option>
                </select>

            </div>

            <div class="settings__form-group">
                <input id="http-enabled" class="settings__form-input--checkbox" type="checkbox" name="http_enabled" aria-checked="false">
                <label for="http-enabled">Enable HTTP Communication</label>
            </div>

            <div class="settings__form-group--submit-form">
                <button class="settings__submit-button settings__submit-button--store-button" type="submit">Save</button>
            </div>
        </form>

        <form id="mqtt-form" class="settings__form settings__form--mqtt-communication" aria-labelledby="mqtt-title">
            @csrf
            <h2 id="mqtt-title" class="settings__form-title">MQTT Communication</h2>

            <div class="settings__form-group">
                <label for="mqtt-ip">IP Address:</label>
                <input class="settings__form-input" type="text" name="mqtt_ip" id="mqtt-ip" aria-required="true" aria-describedby="mqtt-ip-error">
            </div>

            <div class="settings__form-group">
                <label for="mqtt-port">Port:</label>
                <input class="settings__form-input" type="number" name="mqtt_port" id="mqtt-port" min="1" aria-required="true" aria-describedby="mqtt-port-error">
            </div>

            <div class="settings__form-group--submit-form">
                <button class="settings__submit-button settings__submit-button--store-button" type="submit">Save</button>
            </div>
        </form>

        <form id="thresholds-settings-form" class="settings__form settings__form--thresholds" aria-labelledby="thresholds-title">
            @csrf
            <h2 id="thresholds-title" class="settings__form-title">Thresholds</h2>

            <div class="settings__form-group">
                <label for="temp_min">Minimum Temperature (°C):</label>
                <input
                    class="settings__form-input"
                    type="number"
                    name="temp_min"
                    id="temp_min"
                    step="0.1"
                    min="-50"
                    max="150"
                    aria-required="true"
                    aria-describedby="temp_min_error"
                >
            </div>

            <div class="settings__form-group">
                <label for="temp_max">Maximum Temperature (°C):</label>
                <input
                    class="settings__form-input"
                    type="number"
                    name="temp_max"
                    id="temp_max"
                    step="0.1"
                    min="-50"
                    max="150"
                    aria-required="true"
                    aria-describedby="temp_max_error"
                >
            </div>

            <div class="settings__form-group">
                <label for="humidity_min">Minimum Humidity (%):</label>
                <input
                    class="settings__form-input"
                    type="number"
                    name="humidity_min"
                    id="humidity_min"
                    step="1"
                    min="0"
                    max="100"
                    aria-required="true"
                    aria-describedby="humidity_min_error"
                >
            </div>

            <div class="settings__form-group">
                <label for="humidity_max">Maximum Humidity (%):</label>
                <input
                    class="settings__form-input"
                    type="number"
                    name="humidity_max"
                    id="humidity_max"
                    step="1"
                    min="0"
                    max="100"
                    aria-required="true"
                    aria-describedby="humidity_max_error"
                >
            </div>

            <div class="settings__form-group--submit-form">
                <button class="settings__submit-button settings__submit-button--store-button" type="submit">
                    Save
                </button>
            </div>
        </form>


        <form id="restart-esp32-form" class="settings__form" aria-labelledby="reset-title">
            @csrf
            <h2 id="reset-title" class="settings__form-title">ESP32 Remote Reset</h2>
            <p>
                <label for="reset-button">Safely restart the device.</label>
            </p>
            <div class="settings__form-group--submit-form">
                <button id="reset-button" class="settings__submit-button settings__reset-esp32" type="submit" aria-live="polite">Reset</button>
            </div>
        </form>

    </section>


    <aside class="settings__element settings__element--type-console" aria-label="Console Output" role="region" tabindex="0">
        <code id="console-output" class="settings__console-output"></code>
    </aside>

</div>
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
    window.thresholdsConfig = @json($thresholdsConfig);
    window.httpConfig = @json($httpConfig);
</script>