let codeReader = null;
let scanning = false;
let lastCode = null;

function startScanner(baseUrl) {

    if (scanning) return;
    scanning = true;
    lastCode = null;

    codeReader = new ZXing.BrowserBarcodeReader();

    codeReader.getVideoInputDevices()
        .then((devices) => {

            const deviceId = devices[0].deviceId;

            codeReader.decodeFromVideoDevice(
                deviceId,
                "video",
                (result, err) => {

                    if (result && scanning) {

                        const code = result.text;

                        // 🔥 anti double scan
                        if (code === lastCode) return;

                        lastCode = code;
                        scanning = false;

                        console.log("SCAN OK:", code);

                        // stop caméra propre
                        codeReader.reset();

                        setTimeout(() => {
                            window.location.href = baseUrl + "?code=" + encodeURIComponent(code);
                        }, 150);
                    }

                    if (err && !(err instanceof ZXing.NotFoundException)) {
                        console.log("scan...");
                    }
                }
            );
        })
        .catch(err => {
            console.error("Erreur caméra:", err);
            scanning = false;
        });
}

function stopScanner() {

    scanning = false;
    lastCode = null;

    if (codeReader) {
        codeReader.reset();
    }
}