let codeReader;
let scanning = false;
let lastScan = null;
let scanTimeout = null;

function startScanner(baseUrl) {

    if (scanning) return;
    scanning = true;

    codeReader = new ZXing.BrowserBarcodeReader();

    codeReader.getVideoInputDevices()
        .then((devices) => {

            if (devices.length === 0) {
                alert("Aucune caméra détectée");
                return;
            }

            const deviceId = devices[0].deviceId;

            codeReader.decodeFromVideoDevice(
                deviceId,
                "video",
                (result, err) => {

                    if (result && scanning) {

                        const code = result.text;

                        // 🔒 ANTI DOUBLE SCAN
                        if (code === lastScan) {
                            return;
                        }

                        lastScan = code;

                        console.log("SCAN OK:", code);

                        // ⏳ Bloque pendant 1 seconde
                        clearTimeout(scanTimeout);
                        scanTimeout = setTimeout(() => {
                            lastScan = null;
                        }, 1000);

                        // STOP caméra
                        stopScanner();

                        // REDIRECTION
                        window.location.href = baseUrl + "?code=" + encodeURIComponent(code);
                    }

                    if (err && !(err instanceof ZXing.NotFoundException)) {
                        console.log(err);
                    }
                }
            );
        })
        .catch(err => console.error(err));
}

function stopScanner() {
    scanning = false;

    if (codeReader) {
        codeReader.reset();
    }
}