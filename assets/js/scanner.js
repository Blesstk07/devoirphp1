// Ici, je gère la fonctionnalité du code-barre
Quagga.init({
    inputStream: { type: "LiveStream" },
    decoder: { readers: ["ean_reader"] }
}, function(err) {
    if (!err) {
        Quagga.start();
    }
});

Quagga.onDetected(function(result) {
    let code = result.codeResult.code;
    alert("Code : " + code);
});