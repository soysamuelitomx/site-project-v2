console.log("DASHBOARD SCRIPT");
import { tryToConnectToBroker } from "./mqtt.js";

let mqttClient = null;
let isConnected = false;
let isConnecting = false;

const connectToBrokerButton = document.getElementById(
    "connect-to-broker-button"
);

const brokerStatusData = document.getElementById("broker-status-data");

connectToBrokerButton.addEventListener("click", async () => {
    if (!isConnected) {
        isConnecting = true;
        isConnected = await waitForMqttClient();
        isConnecting = false;
    } else {
        disconnectFromBroker();
    }
});
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
    updateBrokerWidget("success");
    updateToogleBtn("Stop Monitoring", "stop-connection", false);
    showAlertModal("✅", "Connected to Mqtt Broker", "succes");
}

function disconnectFromBroker() {
    if (mqttClient) {
        mqttClient.end(true);
        mqttClient = null;
    }

    isConnected = false;
    updateBrokerWidget("disconnected");
    updateToogleBtn("Start Monitoring", "disconnected", false);
    showAlertModal("🛑", "Disconect to Mqtt Broker.", "error");
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

function updateToogleBtn(content, className, disabled) {
    connectToBrokerButton.textContent = content;
    connectToBrokerButton.classList.remove(
        "dashboard__widget-button--disconnected"
    );
    connectToBrokerButton.classList.remove(
        "dashboard__widget-button--stop-connection"
    );
    connectToBrokerButton.classList.add(
        `dashboard__widget-button--${className}`
    );
    connectToBrokerButton.disabled = disabled;
}

function updateBrokerWidget(className) {
    brokerStatusData.classList.remove(
        "dashboard__widget-data-broker-status--disconnected"
    );
    brokerStatusData.classList.remove(
        "dashboard__widget-data-broker-status--success"
    );

    brokerStatusData.classList.add(
        `dashboard__widget-data-broker-status--${className}`
    );
}

updateToogleBtn("Test communication", "disconnected", false);
updateBrokerWidget("disconnected");
const telemetryScript = document.getElementById("telemetry-json");
const telemetryData = JSON.parse(telemetryScript.textContent);
const records = telemetryData.records;
const table = document.querySelector(".dashboard__widget-table");
const headers = Array.from(table.querySelectorAll("thead th"));
const rows = table.querySelectorAll("tbody tr");

rows.forEach((row) => {
    Array.from(row.children).forEach((cell, i) => {
        if (headers[i]) {
            cell.setAttribute("data-label", headers[i].textContent.trim());
        }
    });
});

function capitalize(str) {
    if (!str) return "";
    return str.charAt(0).toUpperCase() + str.slice(1);
}

const ctxLine = document.getElementById("lineChart").getContext("2d");

const lineChart = new Chart(ctxLine, {
    type: "line",
    data: {
        labels: telemetryData.timestamps,
        datasets: [
            {
                label: "Temperature (°C)",
                data: telemetryData.latestTemperatureRecords,
                borderColor: "rgba(255, 99, 132, 1)",
                backgroundColor: "rgba(255, 99, 132, 1)",
                fill: false,
                tension: 0.4,
                yAxisID: "y",
                hidden: false,
            },
            {
                label: "Humidity (%)",
                data: telemetryData.latestHumidityRecords,
                borderColor: "rgba(75, 192, 192, 1)",
                backgroundColor: "rgba(75, 192, 192, 1)",
                fill: false,
                tension: 0.4,
                yAxisID: "y1",
                hidden: true,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: { mode: "index", intersect: false },
        stacked: false,
        scales: {
            x: {
                ticks: {
                    display: false,
                },
                grid: {
                    color: "rgba(255, 255, 255, 0.1)",
                },
            },
            y: {
                type: "linear",
                position: "left",
                beginAtZero: true,
                title: {
                    display: true,
                    text: "Temperature (°C)",
                    color: "rgba(255, 99, 132, 1)",
                    font: { weight: "bold" },
                },
                ticks: { color: "rgba(255, 99, 132, 1)" },
                grid: { color: "rgba(255, 99, 132, 0.2)" },
            },
            y1: {
                type: "linear",
                position: "right",
                beginAtZero: true,
                grid: { drawOnChartArea: false },
                display: false,
                title: {
                    display: true,
                    text: "Humidity (%)",
                    color: "rgba(75, 192, 192, 1)",
                    font: { weight: "bold" },
                },
                ticks: { color: "rgba(75, 192, 192, 1)" },
            },
        },
    },
});

let showingLineTemperature = true;

document
    .getElementById("toggle-line-chart-data")
    .addEventListener("click", () => {
        if (showingLineTemperature) {
            lineChart.data.datasets[0].hidden = true;
            lineChart.data.datasets[1].hidden = false;

            lineChart.options.scales.y.display = false;
            lineChart.options.scales.y1.display = true;
        } else {
            lineChart.data.datasets[0].hidden = false;
            lineChart.data.datasets[1].hidden = true;

            lineChart.options.scales.y.display = true;
            lineChart.options.scales.y1.display = false;
        }

        showingLineTemperature = !showingLineTemperature;
        lineChart.update();
    });

const pieChartTitle = document.getElementById("pie-chart-title");

const ctxPie = document.getElementById("pieChart").getContext("2d");

const pieChart = new Chart(ctxPie, {
    type: "pie",
    data: {
        labels: ["Normal", "Warning", "Critical"],
        datasets: [
            {
                label: "Temperature",
                data: [
                    telemetryData.temperatureThresholdCounts.normal,
                    telemetryData.temperatureThresholdCounts.warning,
                    telemetryData.temperatureThresholdCounts.critical,
                ],
                backgroundColor: ["#25df00", "#ffbe40", "#ff0000"],
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: "bottom" },
        },
    },
});

pieChartTitle.textContent = `${pieChart.data.datasets[0].label} threshold distribution`;

let showingPieTemperature = true;

document
    .getElementById("toggle-pie-chart-data")
    .addEventListener("click", () => {
        if (showingPieTemperature) {
            pieChart.data.datasets[0].label = "Humidity";
            pieChart.data.datasets[0].data = [
                telemetryData.humidityThresholdCounts.normal,
                telemetryData.humidityThresholdCounts.warning,
                telemetryData.humidityThresholdCounts.critical,
            ];
        } else {
            pieChart.data.datasets[0].label = "Temperature";
            pieChart.data.datasets[0].data = [
                telemetryData.temperatureThresholdCounts.normal,
                telemetryData.temperatureThresholdCounts.warning,
                telemetryData.temperatureThresholdCounts.critical,
            ];
        }

        pieChartTitle.textContent = `${pieChart.data.datasets[0].label} threshold distribution`;

        showingPieTemperature = !showingPieTemperature;
        pieChart.update();
    });
