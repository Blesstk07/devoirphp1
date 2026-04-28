let codeReader = null;
let scanning = false;
let lastCode = null;

/**
 * 🚀 Démarrer le scanner
 */
function startScanner(baseUrl) {

    if (scanning) return;
    scanning = true;
    lastCode = null;

    codeReader = new ZXing.BrowserBarcodeReader();

    // 📱 choisir caméra arrière si disponible
    codeReader.getVideoInputDevices()
        .then((devices) => {

            let selectedDeviceId = devices[0].deviceId;

            // tenter caméra arrière (mobile)
            devices.forEach(device => {
                if (device.label.toLowerCase().includes('back')) {
                    selectedDeviceId = device.deviceId;
                }
            });

            return codeReader.decodeFromVideoDevice(
                selectedDeviceId,
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
                        // on ignore les erreurs normales de scan
                    }
                }
            );
        })
        .catch(err => {
            console.error("Erreur caméra:", err);
            scanning = false;
        });
}

/**
 * ⛔ Stop scanner
 */
function stopScanner() {

    scanning = false;
    lastCode = null;

    if (codeReader) {
        codeReader.reset();
    }
}