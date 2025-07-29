import { tryToConnectToBroker } from "./mqtt.js";
const commandsTopic = "esp32/001/commands";
let mqttClient = null;
let isConnected = false;
let awaitingStatus = false;
let isConnecting = false;

const forms = [
    document.querySelector(".settings__form--http-communication"),
    document.querySelector(".settings__form--mqtt-communication"),
    document.querySelector(".settings__form--thresholds"),
];

const formEsp32Data = document.getElementById("get-esp32-data");

formEsp32Data.addEventListener("submit", async (e) => {
    e.preventDefault();
});

forms.forEach((form) => {
    if (!form) return;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        let hasError = false;

        const inputs = form.querySelectorAll(
            'input:not([type="checkbox"]), select'
        );

        inputs.forEach((input) => {
            if (!input.checkValidity() || input.value.trim() === "") {
                hasError = true;

                // Agregar clase de error al input
                input.classList.add("settings__form-input--error-visible");

                // Mostrar el span de error si existe
                const errorId = input.getAttribute("aria-describedby");
                if (errorId) {
                    const errorSpan = form.querySelector(`#${errorId}`);
                    if (errorSpan) {
                        errorSpan.classList.add("visible");

                        setTimeout(() => {
                            errorSpan.classList.remove("visible");
                            input.classList.remove(
                                "settings__form-input--error-visible"
                            );
                        }, 3000);
                    } else {
                        // Asegurarse de quitar clase del input aunque no haya span
                        setTimeout(() => {
                            input.classList.remove(
                                "settings__form-input--error-visible"
                            );
                        }, 3000);
                    }
                } else {
                    // También remover si no hay span
                    setTimeout(() => {
                        input.classList.remove(
                            "settings__form-input--error-visible"
                        );
                    }, 3000);
                }
            }
        });

        if (!hasError) {
            //form.submit();
        }
    });
});

const formConnectToBroker = document.getElementById("connect-to-broker");

formConnectToBroker.addEventListener("submit", async (e) => {
    e.preventDefault();
});

const ipInput = document.getElementById("mqtt-ip");
const portInput = document.getElementById("mqtt-port");
const mqttForm = document.getElementById("mqtt-form");
mqttForm.addEventListener("submit", (e) => {
    e.preventDefault();

    let hasError = false;

    const inputs = mqttForm.querySelectorAll(
        'input:not([type="checkbox"]), select'
    );

    inputs.forEach((input) => {
        if (!input.checkValidity() || input.value.trim() === "") {
            hasError = true;

            input.classList.add("settings__form-input--error-visible");

            const errorId = input.getAttribute("aria-describedby");
            if (errorId) {
                const errorSpan = mqttForm.querySelector(`#${errorId}`);
                if (errorSpan) {
                    errorSpan.classList.add("visible");

                    setTimeout(() => {
                        errorSpan.classList.remove("visible");
                        input.classList.remove(
                            "settings__form-input--error-visible"
                        );
                    }, 3000);
                } else {
                    setTimeout(() => {
                        input.classList.remove(
                            "settings__form-input--error-visible"
                        );
                    }, 3000);
                }
            } else {
                setTimeout(() => {
                    input.classList.remove(
                        "settings__form-input--error-visible"
                    );
                }, 3000);
            }
        }
    });

    if (!hasError) {
        const newConfig = {
            ip_address: ipInput.value,
            port: parseInt(portInput.value, 10),
        };

        Livewire.emit("updateMqttConfig", newConfig);
        setTimeout(() => {
            location.reload();
        }, 200);
    }
});

if (window.mqttConfig) {
    if (ipInput) ipInput.value = window.mqttConfig.ip_address || "";
    if (portInput) portInput.value = window.mqttConfig.port || "";
    console.log(ipInput, portInput);
}

const thresholdsForm = document.getElementById("thresholds-settings-form");
thresholdsForm.addEventListener("submit", (e) => {
    e.preventDefault();

    let hasError = false;

    const inputs = thresholdsForm.querySelectorAll(
        'input:not([type="checkbox"]), select'
    );

    inputs.forEach((input) => {
        if (!input.checkValidity() || input.value.trim() === "") {
            hasError = true;

            input.classList.add("settings__form-input--error-visible");

            const errorId = input.getAttribute("aria-describedby");
            if (errorId) {
                const errorSpan = thresholdsForm.querySelector(`#${errorId}`);
                if (errorSpan) {
                    errorSpan.classList.add("visible");

                    setTimeout(() => {
                        errorSpan.classList.remove("visible");
                        input.classList.remove(
                            "settings__form-input--error-visible"
                        );
                    }, 3000);
                } else {
                    setTimeout(() => {
                        input.classList.remove(
                            "settings__form-input--error-visible"
                        );
                    }, 3000);
                }
            } else {
                setTimeout(() => {
                    input.classList.remove(
                        "settings__form-input--error-visible"
                    );
                }, 3000);
            }
        }
    });

    if (!hasError) {
        const newThresholds = {
            temp_min: tempMinInput.value,
            temp_max: tempMaxInput.value,
            humidity_min: humMinInput.value,
            humidity_max: humMaxInput.value,
        };
        Livewire.emit("updateThresholdsConfig", newThresholds);
        setTimeout(() => {
            location.reload();
        }, 200);
    }
});
const tempMinInput = document.getElementById("temp_min");
const tempMaxInput = document.getElementById("temp_max");
const humMinInput = document.getElementById("humidity_min");
const humMaxInput = document.getElementById("humidity_max");
if (window.thresholdsConfig) {
    if (tempMinInput)
        tempMinInput.value = window.thresholdsConfig.temp_min ?? "";
    if (tempMaxInput)
        tempMaxInput.value = window.thresholdsConfig.temp_max ?? "";
    if (humMinInput)
        humMinInput.value = window.thresholdsConfig.humidity_min ?? "";
    if (humMaxInput)
        humMaxInput.value = window.thresholdsConfig.humidity_max ?? "";
}
console.log(window.thresholdsConfig);
console.log("httpConfig:", window.httpConfig);

const httpForm = document.getElementById("http-form");
const httpIntervalInput = document.getElementById("http-interval");
const httpEnabledInput = document.getElementById("http-enabled");

if (window.httpConfig) {
    httpIntervalInput.value = httpConfig.http_interval ?? "";
    httpEnabledInput.checked = httpConfig.http_enabled === 1;
} else {
    httpIntervalInput.value = "";
    httpEnabledInput.checked = false;
}
httpForm.addEventListener("submit", (e) => {
    e.preventDefault();

    let hasError = false;

    const inputs = httpForm.querySelectorAll(
        'input:not([type="checkbox"]), select'
    );

    inputs.forEach((input) => {
        if (!input.checkValidity() || input.value.trim() === "") {
            hasError = true;
            input.classList.add("settings__form-input--error-visible");

            const errorId = input.getAttribute("aria-describedby");
            if (errorId) {
                const errorSpan = httpForm.querySelector(`#${errorId}`);
                if (errorSpan) {
                    errorSpan.classList.add("visible");
                    setTimeout(() => {
                        errorSpan.classList.remove("visible");
                        input.classList.remove(
                            "settings__form-input--error-visible"
                        );
                    }, 3000);
                }
            } else {
                setTimeout(() => {
                    input.classList.remove(
                        "settings__form-input--error-visible"
                    );
                }, 3000);
            }
        }
    });

    if (!hasError) {
        const newHttpConfig = {
            http_interval: httpIntervalInput.value,
            http_enabled: httpEnabledInput.checked ? 1 : 0,
        };

        const message = JSON.stringify(newHttpConfig);

        if (isConnected) {
            mqttClient.publish(
                commandsTopic,
                message,
                { qos: 1, retain: false },
                (error) => {
                    if (error) {
                        console.error("❌ Error al publicar:", error);
                    } else {
                        console.log("✅ Configuración publicada al ESP32.");
                        setTimeout(() => {
                            console.log(
                                "Emit updateHttpConfig:",
                                newHttpConfig
                            );
                            Livewire.emit("updateHttpConfig", newHttpConfig);
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                        }, 3000);
                    }
                }
            );
        } else {
            showAlertModal(
                "🛑",
                "You need to connect to broker first",
                "error"
            );
        }
    }
});

const restartEsp32Form = document.getElementById("restart-esp32-form");

restartEsp32Form.addEventListener("submit", (e) => {
    e.preventDefault();
    const message = JSON.stringify({
        restart: true,
    });

    if (isConnected) {
        mqttClient.publish(
            commandsTopic,
            message,
            { qos: 1, retain: false },
            (error) => {
                if (error) {
                    console.error("❌ Error al publicar:", error);
                } else {
                    console.log("✅ Configuración publicada al ESP32.");
                }
            }
        );
    } else {
        showAlertModal("🛑", "You need to connect to broker first", "error");
    }
});

const terminal = document.getElementById("console-output");

function logToConsole(message) {
    terminal.textContent += `\n${message}`;
    terminal.scrollTop = terminal.scrollHeight;
}

function printEsp32Status(data) {
    if (
        !data.ip_address ||
        !data.mac_address ||
        data.request_interval_ms === undefined
    ) {
        console.warn("❌ Datos incompletos, se ignora esta respuesta.");
        return;
    }

    terminal.textContent = "";

    const lines = [
        "**********************************",
        "*                                *",
        "*          ESP32 STATUS          *",
        "*                                *",
        "**********************************",
        "--------------------",
        `IP Address: ${data.ip_address}`,
        `MAC Address: ${data.mac_address}`,
        `Is HTTP enabled: ${data.is_enabled}`,
        `HTTP Interval: ${Math.floor(data.request_interval_ms / 1000)} seconds`,
        "System Status: OK",
    ];

    let delay = 0;
    lines.forEach((line) => {
        setTimeout(() => {
            logToConsole(line);
        }, delay);
        delay += 100;
    });
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
            const payloadStr = payload.toString().trim();
            console.log(`📨 Mensaje recibido en "${topic}":`, payloadStr);

            if (topic === "esp32/001/status" && awaitingStatus) {
                try {
                    const parsed = JSON.parse(payloadStr);
                    if (parsed.ip_address && parsed.mac_address) {
                        printEsp32Status(parsed);
                        awaitingStatus = false;
                    }
                } catch (err) {
                    console.error("❌ Error al parsear JSON:", err.message);
                }
            }
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
        showAlertModal("🛑", "Error while connect to Mqtt Broker.", "error");
        return false;
    }
}

function handleConnectionReady() {
    updateToogleBtn("Disconnect from Mqtt Broker", "stop-connection", false);
    console.log("✅ Conectado al broker.");
    showAlertModal("✅", "Connected to Mqtt Broker", "succes");
}

function disconnectFromBroker() {
    if (mqttClient) {
        mqttClient.end(true);
        mqttClient = null;
    }

    isConnected = false;
    updateToogleBtn("Connect to Broker", "connect-to-broker-button", false);
    showAlertModal("🛑", "Disconect to Mqtt Broker.", "error");

    console.log("🛑 Cliente desconectado manualmente");
}

function updateToogleBtn(content, className, disabled) {
    toggleBtn.textContent = content;
    toggleBtn.classList.remove(
        "settings__submit-button--connect-to-broker-button"
    );
    toggleBtn.classList.remove("settings__submit-button--stop-connection");
    toggleBtn.classList.add(`settings__submit-button--${className}`);
    toggleBtn.disabled = disabled;
}

const toggleBtn = document.getElementById("connect-to-broker-button");
const getStatusButton = document.getElementById("status-button");

updateToogleBtn("Connect to Broker", "connect-to-broker-button", false);

toggleBtn.addEventListener("click", async () => {
    if (!isConnected) {
        isConnecting = true;
        isConnected = await waitForMqttClient();
        isConnecting = false;
    } else {
        disconnectFromBroker();
    }
});

getStatusButton.addEventListener("click", () => {
    if (!isConnected) {
        console.warn("❌ No estás conectado al broker MQTT.");
        showAlertModal("🛑", "Disconect to Mqtt Broker yet.", "error");
        return;
    }

    awaitingStatus = true;
    mqttClient.subscribe("esp32/001/status", { qos: 1 }, (err) => {
        if (err) {
            console.error("❌ Error al suscribirse a esp32/001/status:", err);
            return;
        }

        const message = JSON.stringify({ get_status: true });

        mqttClient.publish(
            commandsTopic,
            message,
            { qos: 1, retain: false },
            (error) => {
                if (error) {
                    console.error("❌ Error al publicar:", error);
                } else {
                    console.log("✅ Solicitud de estado enviada al ESP32.");
                }
            }
        );
    });
});
if (window.mqttConfig) {
    const ipInput = document.getElementById("mqtt-ip");
    const portInput = document.getElementById("mqtt-port");

    if (ipInput && portInput) {
        ipInput.value = window.mqttConfig.ip_address || "";
        portInput.value = window.mqttConfig.port || "";
    }
    console.log(ipInput, portInput);
}
