<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'tasle7') {
    header('Location: index.php');
    exit();
}

require "./app/db_connection.php";
?>

<?php
include "partials/header.php";
include "partials/navBar.php";
?>

<div class="container mt-5" dir="rtl">

    <?php if (isset($message)): ?>
        <div class="alert alert-info" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <h2 class="text-center">بحث عن جهاز <i class="bi bi-search"></i></h2>
    <form action="" method="POST" id="searchForm">
        <div class="mb-3">
            <label for="search_input" class="form-label">رقم المسلسل أو رقم إذن الشغل</label>
            <input type="text" class="form-control" id="search_input" name="search_input" placeholder="أدخل رقم المسلسل أو رقم إذن الشغل" required>
        </div>
        <div class="px-4">
            <button type="submit" class="btn btn-primary w-100">بحث <i class="bi bi-search"></i></button>
        </div>
    </form>

    <div id="resultModal" class="modal fade" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="resultModalLabel">بيانات الجهاز</h5>
                </div>
                <div class="modal-body">
                    <p id="deviceDetails"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var searchInput = document.getElementById('search_input').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/search_device.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('deviceDetails').innerHTML = xhr.responseText;
                var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                resultModal.show();
            } else {
                alert('Error retrieving device details.'); // Error handling
            }
        };
        xhr.send('search_input=' + encodeURIComponent(searchInput));
    });

    function submitRepair() {
        var serialNumber = document.getElementById('search_input').value;
        var repairDate = document.getElementById('fix_date').value;
        var faultType = document.getElementById('fault_type').value;
        var whoFixed = document.getElementById('who_fixed').value;
        var toolsUsed = document.getElementById('tools_used').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/submit_repair.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert(xhr.responseText);
                location.reload();
            } else {
                alert('Error submitting repair details.'); // Error handling
            }
        };
        xhr.send('serial_number=' + encodeURIComponent(serialNumber) + '&fix_date=' + encodeURIComponent(repairDate) + '&fault_type=' + encodeURIComponent(faultType) + '&who_fixed=' + encodeURIComponent(whoFixed) + '&tools_used=' + encodeURIComponent(toolsUsed));
    }
</script>

<?php include "partials/footer.php"; ?>