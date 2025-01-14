<?php
include "./service/database.php";
session_start();

if (isset($_SESSION["is_logout"]) && $_SESSION["is_logout"] == true) {
    header("location: home.php");
    exit();
}

if (isset($_POST['logout'])) {
    $_SESSION["is_login"] = false;
    $_SESSION["is_logout"] = true;
    header("Location: home.php");
    exit();
  }

if (isset($_SESSION['ID'])) {
    $id = $_SESSION['ID'];
    $sql_select = "SELECT email, username, password, oshi FROM user WHERE id = ?";
    $stmt = $db->prepare($sql_select);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($email, $username, $password, $oshi);
    $stmt->fetch();
    $stmt->close();
}

  if (isset($_POST['update'])) {
    // Tangkap data dari form
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $oshi = $_POST['member'];
    $id = $_SESSION['ID'];

    // Validasi data
    if (!empty($email) && !empty($username) && !empty($password) && !empty($oshi)) {
        // Query untuk update data
        $_SESSION['oshi'] = $oshi;
        $sql_update = "UPDATE user SET email = ?, username = ?, password = ?, oshi = ? WHERE id = ?";
        $stmt = $db->prepare($sql_update);
        $stmt->bind_param('ssssi', $email, $username, $password, $oshi, $id);

        if ($stmt->execute()) {
            $_SESSION['updatesukses'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $updatefailed = "Error: " . $stmt->error;
        }
    } else {
        $_SESSION['updatefailed'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $db->close();
}

  
  if (isset($_POST['oshi-login'])) {
    $username = $_SESSION["username"];
    $sql = "SELECT * FROM user WHERE username= '$username'";
    $result = $db->query($sql);
    $data = $result->fetch_assoc();
  
    if ($data['oshi'] == null) {
      $_SESSION['oshi_null'] = true;
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
    } else {
      header("Location: oshi.php");
      exit();
    }
  
    $db->close();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hololive User Profile</title>
    <link rel="stylesheet" href="profile1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/holodex.js/dist/holodex.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <style>
        body {
            background-color: #00BFFF;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            width: 100%;
        }
        .wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-bottom: 50px;
        }
        .register-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
        }
        .logo {
            width: 200px;
        }
        .update-text {
            margin-top : 10px;
            color: #00BFFF;
            font-weight: bold;
        }
        .update-button {
            background-color: #00BFFF;
            color: white;
            border: none;
            margin-top : 12px;
            margin-left : 2px;
        }
        .update-button:hover {
            background-color: #008CBA;
        }

        .container{
            position: fixed; /* Tambahkan properti position: fixed */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top" data-bs-theme="dark">
        <div class="container-fluid">
        <a class="navbar-brand" href="home.php">
            <img src="./gambar/Hololive_Production.png" class="img-fluid" alt="Hololive Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse ms-auto" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0 ms-auto">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="schedule.php">Schedule</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="branch.php">Filter by Branch</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="talent.php">Filter by Generation</a>
            </li>
            <li class="nav-item">
                <form action="profile.php" method="POST">
                  <button type="submit" name="oshi-login" class="nav-link active">Oshi</button>
                </form>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $_SESSION["username"]; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                <?php if(isset($_SESSION["is_login"]) && $_SESSION["is_login"] == true) { ?>
                    <li><a class="dropdown-item" href="profile.php">User Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                    <form action="profile.php" method="POST">
                        <button type="submit" name="logout" class="dropdown-item">Logout</button>
                    </form>
                    </li>
                <?php } else { ?>
                    <li><a class="dropdown-item" href="login.php">Login</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="register.php">Register</a></li>
                <?php } ?>
                </ul>
            </li>
            </ul>
        </div>
        </div>
    </nav>

    <div class="wrapper">
    <div class="container register-container mt-4">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="rounded-circle-container">
                    <?php if(isset($_SESSION['oshi']) && $_SESSION['oshi'] != null){
                        $oshiImage = str_replace(' ', '', $_SESSION['oshi']) . '.png'; ?>
                        <img src="./oshi/<?= $oshiImage; ?>" class="logo" alt="Oshi Logo">
                    <?php } else { ?>
                        <img id="oshiImage" src="./oshi/default.png" alt="Hololive" class="logo">
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-6">
                <h2 class="update-text">My Profile</h2>
                <form action="profile.php" id="formUpdate" method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" id="username" placeholder="Enter username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" value="<?php echo htmlspecialchars($password); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="oshi" class="form-label">Oshi</label>
                        <div class="input-group mb-3">
                        <label class="input-group-text" for="branchSelect">Branch</label>
                        <select class="form-select" id="branchSelect" required>
                            <option value ="" selected>Pilih Branch...</option>
                            <option value="HoloJP">Hololive Japan</option>
                            <option value="HoloID">Hololive Indonesia</option>
                            <option value="HoloEN">Hololive English</option>
                        </select>
                        </div>
                        <div class="input-group mb-3" id="generationSelectContainer">
                        <label class="input-group-text" for="generationSelect">Generation</label>
                        <select class="form-select" id="generationSelect" name="generation" required>
                            <option value ="" selected>Pilih Generasi...</option>
                        </select>
                        </div>
                        <div class="input-group mb-3" id="memberSelectContainer">
                        <label class="input-group-text" for="memberSelect">Hololive Member</label>
                        <select class="form-select" id="memberSelect" name="member" required>
                            <option value ="" selected>Pilih Member...</option>
                        </select>
                        </div>
                    </div>
                    <button type="button" name="update" id="updateButton" class="btn update-button">Update</button>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['updatefailed']) && $_SESSION['updatefailed'] == true) { ?>
                Swal.fire({
                    title: "Update Gagal!",
                    text: "Data tidak lengkap.",
                    icon: "error"
                });
                <?php unset($_SESSION['updatefailed']);
        } ?>
        <?php if (isset($_SESSION['updatesukses']) && $_SESSION['updatesukses'] == true) { ?>
                Swal.fire({
                    title: "Update Sukses!",
                    text: "Data telah diupdate.",
                    icon: "success"
                });
                <?php unset($_SESSION['updatesukses']);
        } ?>
        <?php if (isset($_SESSION['oshi_null']) && $_SESSION['oshi_null'] == true) { ?>
            Swal.fire({
                title: "Akses Gagal!",
                text: "Anda belum memilih oshi, silahkan pilih oshi pada halaman user profile.",
                icon: "error"
            });
            <?php unset($_SESSION['oshi_null']);
        } ?>

        document.addEventListener('DOMContentLoaded', function () {
            const updateButton = document.getElementById('updateButton');
            const form = document.getElementById('formUpdate');

            updateButton.addEventListener('click', function (event) {
                if (form.checkValidity()) {
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        text: "Data akan di update sesuai dengan yang anda isi!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let updateInput = document.createElement('input');
                            updateInput.type = 'hidden';
                            updateInput.name = 'update';
                            updateInput.value = 'true';
                            form.appendChild(updateInput);
                            form.submit();
                        }
                    });
                } else {
                        // Jika form tidak valid, tampilkan pesan error
                        form.reportValidity();
                }
            });
        });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const branchSelect = document.getElementById('branchSelect');
        const generationSelect = document.getElementById('generationSelect');
        const memberSelect = document.getElementById('memberSelect');

        const oshi = "<?php echo $oshi; ?>";
        const generations = {
            HoloJP: ["Generation 0", "Generation 1", "Generation 2", "Hololive Gamers", "Generation 3", "Generation 4", "Generation 5", "Generation 6", "Hololive DEV_IS : ReGLOSS"],
            HoloID: ["Generation 1", "Generation 2", "Generation 3"],
            HoloEN: ["Generation 1", "Generation 2", "Generation 3"]
        };

        const members = {
            HoloJP: {
                "Generation 0": ["Tokino Sora", "Roboco-san", "Sakura Miko", "Hoshimachi Suisei", "AZKi"],
                "Generation 1": ["Shirakami Fubuki", "Natsuiro Matsuri", "Akai Haato", "Aki Rosenthal"],
                "Generation 2": ["Minato Aqua", "Murasaki Shion", "Nakiri Ayame", "Yuzuki Choco", "Oozora Subaru"],
                "Hololive Gamers": ["Ookami Mio", "Nekomata Okayu", "Inugami Korone"],
                "Generation 3": ["Usada Pekora", "Shiranui Flare", "Shirogane Noel", "Houshou Marine"],
                "Generation 4": ["Amane Kanata", "Tsunomaki Watame", "Tokoyami Towa", "Himemori Luna"],
                "Generation 5": ["Yukihana Lamy", "Momosuzu Nene", "Shishiro Botan", "Omaru Polka"],
                "Generation 6": ["La+ Darknesss", "Takane Lui", "Hakui Koyori", "Sakamata Chloe", "Kazama Iroha"],
                "Hololive DEV_IS : ReGLOSS": ["Hiodoshi Ao", "Otonose Kanade", "Ichijou Ririka", "Juufuutei Raden", "Todoroki Hajime"]
            },
            HoloID: {
                "Generation 1": ["Ayunda Risu", "Moona Hoshinova", "Airani Iofifteen"],
                "Generation 2": ["Kureiji Ollie", "Anya Melfissa", "Pavolia Reine"],
                "Generation 3": ["Vestia Zeta", "Kobo Kanaeru", "Kaela Kovalskia"]
            },
            HoloEN: {
                "Generation 1": ["Gawr Gura", "Amelia Watson", "Mori Calliope", "Takanashi Kiara", "Ninomae Ina'nis"],
                "Generation 2": ["Nanashi Mumei", "Hakos Baelz", "IRyS", "Ceres Fauna", "Ouro Kronii"],
                "Generation 3": ["Nerissa Ravencroft", "Koseki Bijou", "Shiori Novella", "FUWAMOCO"]
            }
        };

        function fillGenerations(branch) {
            generationSelect.innerHTML = `<option value="" selected>Pilih Generasi...</option>`;
            generations[branch].forEach(gen => {
                const option = document.createElement('option');
                option.value = gen;
                option.textContent = gen;
                generationSelect.appendChild(option);
            });
        }

        function fillMembers(branch, generation) {
            memberSelect.innerHTML = `<option value="" selected>Pilih Member...</option>`;
            members[branch][generation].forEach(member => {
                const option = document.createElement('option');
                option.value = member;
                option.textContent = member;
                memberSelect.appendChild(option);
            });
        }

        if (oshi) {
            // Isi otomatis berdasarkan oshi dari database
            for (let branch in members) {
                for (let gen in members[branch]) {
                    if (members[branch][gen].includes(oshi)) {
                        branchSelect.value = branch;
                        fillGenerations(branch);
                        generationSelect.value = gen;
                        fillMembers(branch, gen);
                        memberSelect.value = oshi;
                        break;
                    }
                }
            }
        }

        branchSelect.addEventListener('change', function() {
            const branch = branchSelect.value;
            fillGenerations(branch);
            memberSelect.innerHTML = `<option value="" selected>Pilih Member...</option>`; // Reset memberSelect
        });

        generationSelect.addEventListener('change', function() {
            const branch = branchSelect.value;
            const generation = generationSelect.value;
            fillMembers(branch, generation);
        });
    });
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
