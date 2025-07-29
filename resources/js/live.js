import { tryToConnectToBroker } from "./mqtt.js";

const tempElement = document.getElementById("temperature-data");
const humElement = document.getElementById("humidity-data");
const tempCtx = document.getElementById("temperature-chart").getContext("2d");
const humCtx = document.getElementById("humidity-chart").getContext("2d");

const telemetryTopic = "esp32/001/telemetry";
const commandTopic = "esp32/001/commands";
const MAX_DATA_POINTS = 10;

const temperatureChart = new Chart(tempCtx, {
    type: "line",
    data: {
        labels: [],
        datasets: [
            {
                label: "Temperature (°C)",
                data: [],
                borderColor: "rgba(255, 99, 132, 1)",
                backgroundColor: "rgba(255, 99, 132, 0.2)",
                fill: true,
                tension: 0.4,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                ticks: { color: "#f1c40f" },
                grid: { color: "rgba(255, 255, 255, 0.1)" },
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: "Temperature (°C)",
                    color: "rgba(255, 99, 132, 1)",
                },
                ticks: { color: "rgba(255, 99, 132, 1)" },
                grid: { color: "rgba(255, 99, 132, 0.2)" },
            },
        },
    },
});

const humidityChart = new Chart(humCtx, {
    type: "line",
    data: {
        labels: [],
        datasets: [
            {
                label: "Humidity (%)",
                data: [],
                borderColor: "rgba(75, 192, 192, 1)",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                fill: true,
                tension: 0.4,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                ticks: { color: "#f1c40f" },
                grid: { color: "rgba(255, 255, 255, 0.1)" },
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: "Humidity (%)",
                    color: "rgba(75, 192, 192, 1)",
                },
                ticks: { color: "rgba(75, 192, 192, 1)" },
                grid: { color: "rgba(75, 192, 192, 0.2)" },
            },
        },
    },
});

function updateChart(chart, value) {
    const label = new Date().toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
    });
    chart.data.labels.push(label);
    chart.data.datasets[0].data.push(value);
    if (chart.data.labels.length > MAX_DATA_POINTS) {
        chart.data.labels.shift();
        chart.data.datasets[0].data.shift();
    }
    chart.update("none");
}

const modal = document.getElementById("alert-modal");
const iconModal = document.getElementById("icon-modal");
const paragraphModal = document.getElementById("paragraph-modal");
const buttonModal = document.getElementById("button-modal");

let modalTimeoutId = null;
let isModalShowing = false;

function showAlertModal(
    icon,
    message,
    type = "info",
    duration = 3000,
    exitDirection = "up"
) {
    if (isModalShowing) {
        hideAlertModal(exitDirection);
        setTimeout(() => {
            showAlertModal(icon, message, type, duration, exitDirection);
        }, 400);
        return;
    }

    isModalShowing = true;

    if (!modal.open) modal.show();

    iconModal.textContent = icon;
    paragraphModal.textContent = message;

    modal.classList.remove(
        "dashboard__modal--visible",
        "dashboard__modal--hidden-up",
        "dashboard__modal--hidden-left",
        "dashboard__modal--hidden-right",
        "dashboard__modal--succes",
        "dashboard__modal--error",
        "dashboard__modal--info"
    );

    modal.classList.add(`dashboard__modal--${type}`);
    modal.classList.add("dashboard__modal--visible");

    modalTimeoutId = setTimeout(() => {
        hideAlertModal(exitDirection);
    }, duration);
}

function hideAlertModal(direction = "up") {
    modal.classList.remove("dashboard__modal--visible");

    if (direction === "left") {
        modal.classList.add("dashboard__modal--hidden-left");
    } else if (direction === "right") {
        modal.classList.add("dashboard__modal--hidden-right");
    } else {
        modal.classList.add("dashboard__modal--hidden-up");
    }

    if (modalTimeoutId) clearTimeout(modalTimeoutId);

    setTimeout(() => {
        if (modal.open) modal.close();
        modal.classList.remove(
            "dashboard__modal--hidden-up",
            "dashboard__modal--hidden-left",
            "dashboard__modal--hidden-right"
        );
        isModalShowing = false;
    }, 400);
}

buttonModal.addEventListener("click", () => hideAlertModal("up"));

let mqttClient = null;
let isConnecting = false;
let isConected = false;
let pingIntervalId = null;

function startPing() {
    if (pingIntervalId) return;

    pingIntervalId = setInterval(() => {
        if (mqttClient?.connected) {
            mqttClient.publish(
                commandTopic,
                JSON.stringify({ client_active: true })
            );
            console.log("📡 Ping enviado al ESP32");
        }
    }, 5000);
}

function stopPing() {
    if (pingIntervalId) {
        clearInterval(pingIntervalId);
        pingIntervalId = null;
        console.log("🛑 Ping detenido");
    }
}

async function waitForMqttClient() {
    if (mqttClient && mqttClient.connected) {
        console.log("🟢 Ya estás conectado al broker.");
        return true;
    }

    if (mqttClient && mqttClient.removeAllListeners) {
        mqttClient.removeAllListeners();
        mqttClient.end(true);
        mqttClient = null;
    }

    try {
        mqttClient = await tryToConnectToBroker(
            `ws://${window.mqttConfig.ip_address}:${window.mqttConfig.port}`,
            3000
        );
        handleConnectionReady();

        mqttClient.on("message", (topic, payload) => {
            if (topic !== telemetryTopic) return;

            try {
                const json = JSON.parse(payload.toString());
                console.log("📥 Telemetría recibida:", json);

                tempElement.textContent = `${json.temperature} °C`;
                tempElement.setAttribute("value", json.temperature);
                humElement.textContent = `${json.humidity} %`;
                humElement.setAttribute("value", json.humidity);

                updateChart(temperatureChart, json.temperature);
                updateChart(humidityChart, json.humidity);
            } catch (e) {
                console.error("❌ Error al parsear JSON:", e);
            }
        });
        mqttClient.subscribe(telemetryTopic, (err) => {
            if (err) {
                console.error("❌ Error al suscribirse al tópico:", err);
                return;
            }

            setTimeout(() => {
                showAlertModal(
                    "ℹ️",
                    "If you don’t see any data, the ESP32 firmware might not be properly programmed.",
                    "info"
                );
            }, 1000);
        });

        mqttClient.on("close", () => {
            console.log("⚠️ La conexión con el broker se cerró.");
            disconnectFromBroker();
        });

        mqttClient.on("offline", () => {
            console.log("⚠️ Cliente MQTT está offline.");
            disconnectFromBroker();
        });

        mqttClient.on("error", (err) => {
            console.error("❌ Error en MQTT:", err);
            disconnectFromBroker();
        });

        return true;
    } catch (error) {
        console.log("❌ Error en la comunicación:", error);
        showAlertModal("🛑", "Error while connect to Mqtt Broker.", "error");
        return false;
    }
}

function handleConnectionReady() {
    console.log("🟢 Cliente conectado:", mqttClient.connected);
    updateBrokerWidget("Connected to Broker.", "success");
    updateToogleBtn("Stop Monitoring", "stop-connection", false);
    showAlertModal("✅", "Connected to Mqtt Broker", "succes");
    startPing();
}

function disconnectFromBroker() {
    if (mqttClient) {
        mqttClient.end(true);
        mqttClient = null;
    }

    isConected = false;
    stopPing();
    updateToogleBtn("Start Monitoring", "disconnected", false);
    updateBrokerWidget("Disconect to Broker.", "disconnected");
    showAlertModal("🛑", "Disconect to Mqtt Broker.", "error");
}

function updateToogleBtn(content, className, disabled) {
    toggleBtn.textContent = content;
    toggleBtn.classList.remove("dashboard__header-button--disconnected");
    toggleBtn.classList.remove("dashboard__header-button--stop-connection");
    toggleBtn.classList.add(`dashboard__header-button--${className}`);
    toggleBtn.disabled = disabled;
}

function updateBrokerWidget(content, className) {
    brokerCaption.textContent = content;

    brokerDataStatus.classList.remove(
        "dashboard__widget-data-broker-status--disconnected"
    );
    brokerDataStatus.classList.remove(
        "dashboard__widget-data-broker-status--success"
    );

    brokerDataStatus.classList.add(
        `dashboard__widget-data-broker-status--${className}`
    );
}

const toggleBtn = document.getElementById("toggle-monitoring");
const brokerCaption = document.getElementById("broker-caption");
const brokerDataStatus = document.getElementById("broker-data-status");

updateToogleBtn("Start Monitoring", "disconnected", false);
updateBrokerWidget("Disconect to Broker.", "disconnected");

toggleBtn.addEventListener("click", async () => {
    if (isConnecting) {
        console.log("🔄 Conexión en progreso, espera...");
        return;
    }

    if (!isConected) {
        isConnecting = true;
        isConected = await waitForMqttClient();
        isConnecting = false;
    } else {
        disconnectFromBroker();
    }
});
