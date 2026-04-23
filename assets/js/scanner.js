let codeReader;
let scanning = false;
let scanLocked = false; //  verrou anti double scan

function startScanner(baseUrl) {

    if (scanning) return;

    scanning = true;
    scanLocked = false;

    codeReader = new ZXing.BrowserBarcodeReader();

    codeReader.getVideoInputDevices()
        .then((devices) => {

            const deviceId = devices[0].deviceId;

            codeReader.decodeFromVideoDevice(
                deviceId,
                "video",
                (result, err) => {

                    //  SI déjà scanné → ignore
                    if (scanLocked) return;

                    if (result && scanning) {

                        scanLocked = true; //  verrou actif

                        const code = result.text;
                        console.log("SCAN OK:", code);

                        // STOP caméra
                        stopScanner();

                        //  petite pause pour éviter double déclenchement
                        setTimeout(() => {
                            window.location.href = baseUrl + "?code=" + encodeURIComponent(code);
                        }, 300);
                    }

                    if (err && !(err instanceof ZXing.NotFoundException)) {
                        console.log("scan...");
                    }
                }
            );
        })
        .catch(err => console.error(err));
}

function stopScanner() {
    scanning = false;
    scanLocked = true; //  bloque tout

    if (codeReader) {
        codeReader.reset();
    }
}