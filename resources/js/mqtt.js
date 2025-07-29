export function tryToConnectToBroker(brokerUrl, timeoutMs) {
    let client = null;
    return new Promise((resolve, reject) => {
        console.log(brokerUrl);

        if (client && client.connected) {
            return resolve(client);
        }

        client = mqtt.connect(brokerUrl, {
            clientId: "web_client_" + Math.random().toString(16).substr(2, 8),
        });

        const timeout = setTimeout(() => {
            client.end(true);
            reject("⏱ Tiempo de espera agotado al conectar con el broker");
        }, timeoutMs);

        client.on("connect", () => {
            clearTimeout(timeout);
            console.log("✅ Conectado al broker MQTT");
            resolve(client);
        });

        client.on("error", (err) => {
            clearTimeout(timeout);
            console.error("❌ Error al conectar:", err);
            reject(err);
        });
    });
}
