<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom shadow-sm" dir="rtl">
    <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

        <ul class="navbar-nav ms-auto">
                <li class="nav-item fs-5">
                    <a class="nav-link"><i class="bi bi-person-circle"></i> مستخدم رقم : <?php echo $_SESSION['username']; ?></a>
                </li>
            </ul>

            <ul class="navbar-nav me-auto">
                <?php if ($_SESSION['role'] == 'A') { ?>
                    <li class="nav-item p-2">
                        <a class="nav-link btn btn-primary text-white me-2" href="reception.php">استلام جهاز <i class="bi bi-file-arrow-down"></i></a>
                    </li>
                    <li class="nav-item  p-2">
                        <a class="nav-link btn btn-success text-white" href="search-device.php">بحث عن جهاز <i class="bi bi-search"></i></a>
                    </li>

                <?php } elseif ($_SESSION['role'] == 'B') { ?>
                    <li class="nav-item">
                        <a class="nav-link mt-2">تصليح جهاز</a>
                    </li>
                <?php } ?>


                <li class="nav-item p-2">
                    <a class="nav-link btn btn-danger text-white" href="app/logout.php">تسجيل الخروج <i class="bi bi-box-arrow-in-left"></i></a>
                </li>
            </ul>


            
        </div>
    </div>
</nav>
