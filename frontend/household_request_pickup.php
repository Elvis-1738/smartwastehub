<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

// fetch waste categories
$categories = $conn->query("SELECT id, name FROM waste_categories ORDER BY name ASC");

$success = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $category_id    = $_POST['category_id'] ?? '';
    $scheduled_date = $_POST['scheduled_date'] ?? '';
    $time_slot      = $_POST['time_slot'] ?? '';

    if (!$category_id || !$scheduled_date || !$time_slot) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO pickup_requests (user_id, category_id, scheduled_date, time_slot) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $_SESSION['user_id'], $category_id, $scheduled_date, $time_slot);

        if ($stmt->execute()) {
            $success = "Pickup request submitted successfully!";
        } else {
            $errors[] = "Error saving request.";
        }
        $stmt->close();
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow">
            <h3 class="text-center text-success fw-bold mb-3">Request Waste Pickup</h3>

            <?php foreach ($errors as $e): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <!-- Waste category -->
                <label class="form-label fw-semibold">Waste Category</label>
                <select name="category_id" id="categorySelect" class="form-control mb-3" required>
                    <option value="">Select category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endwhile; ?>
                </select>

                <!-- Pickup date -->
                <label class="form-label fw-semibold">Pickup Date</label>
                <input type="date" name="scheduled_date" class="form-control mb-3" required>

                <!-- Time slot -->
                <label class="form-label fw-semibold">Preferred Time Slot</label>
                <select name="time_slot" class="form-control mb-3" required>
                    <option value="">Select time slot</option>
                    <option value="Morning (8AM - 11AM)">Morning (8AM - 11AM)</option>
                    <option value="Afternoon (12PM - 3PM)">Afternoon (12PM - 3PM)</option>
                    <option value="Evening (4PM - 7PM)">Evening (4PM - 7PM)</option>
                </select>

                <!-- AI-powered image helper (Google Vision) -->
                <label class="form-label fw-semibold">Upload photo of waste (AI helper)</label>
                <input type="file" id="wasteImage" accept="image/*" class="form-control mb-2">

                <small id="aiStatus" class="text-muted">
                    Optional: upload a photo and SmartWaste AI will suggest the category.
                </small>

                <div id="aiSuggestionBox" class="mt-2" style="display:none;">
                    <div class="alert alert-info py-2 px-3 mb-0" id="aiSuggestionText" style="font-size: 0.9rem;"></div>
                </div>

                <button class="btn btn-success w-100 mt-3">Submit Request</button>
            </form>

        </div>
    </div>
</div>

<script>
// Simple helper to show AI status messages
function setAIStatus(msg, isError = false) {
    const statusEl = document.getElementById('aiStatus');
    if (!statusEl) return;
    statusEl.textContent = msg;
    statusEl.className = isError ? 'text-danger' : 'text-muted';
}

const wasteImageInput = document.getElementById('wasteImage');
const categorySelect   = document.getElementById('categorySelect');
const suggestionBox    = document.getElementById('aiSuggestionBox');
const suggestionText   = document.getElementById('aiSuggestionText');

if (wasteImageInput) {
    wasteImageInput.addEventListener('change', async () => {
        const file = wasteImageInput.files[0];
        if (!file) return;

        setAIStatus('Analyzing image with SmartWaste AI… this may take a few seconds.');

        const formData = new FormData();
        formData.append('image', file);

        try {
            const resp = await fetch('/smartwastehub/backend/ai/ai_detect.php', {
                method: 'POST',
                body: formData
            });

            const data = await resp.json();

            if (!data.success) {
                console.error('Vision error', data);
                setAIStatus(data.error || 'Could not analyze image.', true);
                suggestionBox.style.display = 'none';
                return;
            }

            // Show labels for debugging (optional – you can remove this)
            console.log('Vision labels:', data.labels);

            if (!data.category_guess) {
                setAIStatus('AI could not confidently match a category. Please choose manually.');
                suggestionBox.style.display = 'none';
                return;
            }

            const guess = data.category_guess;

            // Auto-select in dropdown if it exists
            if (categorySelect) {
                const option = [...categorySelect.options].find(
                    o => o.value === String(guess.id)
                );
                if (option) {
                    categorySelect.value = option.value;
                }
            }

            // Show nice suggestion card
            suggestionText.textContent =
                `AI suggests: ${guess.name} (based on label “${guess.label}”, confidence ${(guess.score * 100).toFixed(0)}%).`;
            suggestionBox.style.display = 'block';

            setAIStatus('AI suggestion applied. You can still change the category if it looks wrong.');
        } catch (err) {
            console.error(err);
            setAIStatus('Error contacting AI service. Please select category manually.', true);
            suggestionBox.style.display = 'none';
        }
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
