</div> <!-- container -->

<footer class="glass-footer text-center py-3 mt-5">
    <small class="text-white-50">
        © <?= date('Y'); ?> SmartWasteHub. All rights reserved.
    </small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<button id="notifRequestBtn" style="display:none;"></button>

<script>
// Ask for permission only if browser hasn't decided yet
document.addEventListener("DOMContentLoaded", () => {
    if (Notification && Notification.permission === "default") {
        document.getElementById("notifRequestBtn").click();
    }
});

document.getElementById("notifRequestBtn").addEventListener("click", () => {
    Notification.requestPermission().then(result => {
        console.log("Permission:", result);
    });
});

// Function to show notification
function showNotification(title, message, url = null) {
    if (Notification.permission !== "granted") return;

    let n = new Notification(title, {
        body: message,
        icon: "/smartwastehub/icons/icon-128.png"
    });

    if (url) {
        n.onclick = () => window.location.href = url;
    }
}
</script>

</body>
</html>
