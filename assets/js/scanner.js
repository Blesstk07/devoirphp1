let scanning = false;
let lastCode = null;

function startScanner(baseUrl) {

    console.log("START SCANNER");

    if (scanning) return;
    scanning = true;
    lastCode = null;

    if (typeof Quagga === "undefined") {
        alert("Quagga non chargé !");
        return;
    }

    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector("#scanner-container"),
            constraints: {
                facingMode: "environment"
            }
        },
        decoder: {
            readers: [
                "ean_reader",
                "code_128_reader",
                "ean_8_reader"
            ]
        }
    }, function (err) {

        if (err) {
            console.error(err);
            scanning = false;
            return;
        }

        Quagga.start();
        console.log("CAMERA STARTED");
    });

    Quagga.offDetected(); // 🔥 important reset events

    Quagga.onDetected(function (data) {

        if (!scanning) return;

        const code = data.codeResult.code;

        if (code === lastCode) return;

        lastCode = code;
        scanning = false;

        console.log("SCAN:", code);

        Quagga.stop();

        setTimeout(() => {
            window.location.href = baseUrl + "?code=" + encodeURIComponent(code);
        }, 200);
    });
}

function stopScanner() {

    console.log("STOP SCANNER");

    scanning = false;
    lastCode = null;

    if (typeof Quagga !== "undefined") {
        Quagga.stop();
    }
}