let codeReader;
let scanning = false;

function startScanner(baseUrl) {

    if (scanning) return;
    scanning = true;

    codeReader = new ZXing.BrowserBarcodeReader();

    codeReader.getVideoInputDevices()
        .then((devices) => {

            const deviceId = devices[0].deviceId;

            codeReader.decodeFromVideoDevice(
                deviceId,
                "video",
                (result, err) => {

                    if (result && scanning) {

                        scanning = false;

                        const code = result.text;

                        console.log("SCAN OK:", code);

                        // STOP caméra immédiatement
                        codeReader.reset();

                        // REDIRECTION UNIQUE
                        window.location.href = baseUrl + "?code=" + encodeURIComponent(code);
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
    if (codeReader) {
        codeReader.reset();
    }
}