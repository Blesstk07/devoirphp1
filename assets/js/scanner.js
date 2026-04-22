let codeReader;
let streamActive = false;

function startScanner(urlRedirect) {

    codeReader = new ZXing.BrowserBarcodeReader();
    const videoElement = document.getElementById('video');
    const resultElement = document.getElementById('result');

    console.log("Scanner lancé ✔");

    streamActive = true;

    codeReader.decodeFromVideoDevice(null, videoElement, (result, err) => {

        if (!streamActive) return;

        if (result) {

            const code = result.text;

            console.log("CODE DETECTÉ:", code);

            resultElement.innerText = "Code détecté : " + code;

            stopScanner(); // arrêt automatique après scan

            window.location.href = urlRedirect + "?code=" + code;
        }

        if (err && !(err instanceof ZXing.NotFoundException)) {
            console.error(err);
        }
    });
}

// 🔴 ARRÊTER LA CAMÉRA
function stopScanner() {

    if (codeReader) {
        codeReader.reset();
    }

    streamActive = false;

    const video = document.getElementById('video');

    if (video && video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
        video.srcObject = null;
    }

    console.log("Scanner arrêté ❌");
}