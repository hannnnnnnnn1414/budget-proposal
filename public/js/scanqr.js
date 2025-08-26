let scanner;
document.addEventListener("DOMContentLoaded", function () {
    if (localStorage.getItem("startScanner") === "true") {
        localStorage.removeItem("startScanner"); // Hapus status setelah digunakan
        startScanner(); // Mulai scanner
    }
});

function startScanner() {
    document.getElementById("preview").style.display = "block"; // Tampilkan video
    scanner = new Instascan.Scanner({
        video: document.getElementById("preview"),
    });

    scanner.addListener("scan", function (content) {
        document.getElementById("qrcode_data").value = content;
        document.getElementById("scan-form").submit();
    });

    Instascan.Camera.getCameras()
        .then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                alert("Kamera tidak ditemukan!");
            }
        })
        .catch(function (e) {
            console.error(e);
        });
}
