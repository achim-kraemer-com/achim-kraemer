function copySignature() {
    console.log('läuft');
    const signature = document.getElementById("signature").innerHTML;
    const blob = new Blob([signature], { type: "text/html" });
    const data = [new ClipboardItem({ "text/html": blob })];
    navigator.clipboard.write(data);
    alert("Signatur kopiert!");
}

document.getElementById('copy-signature').addEventListener('click', function () {
    copySignature();
});
