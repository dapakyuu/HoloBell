<?php
include "./service/database.php";
session_start();

if (isset($_SESSION["is_login"])) {
    header("location: home.php");
    exit();
}

if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username=? OR email=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['registerfailed'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $sql_insert = "INSERT INTO user (email, username, password) VALUES (?, ?, ?)";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bind_param("sss", $email, $username, $password);
        $result_insert = $stmt_insert->execute();
        
        if ($result_insert) {
            $_SESSION["is_registered"] = true;
            header("location: home.php");
            exit();
        } else {
            echo "Error: " . $db->error;
        }
    }

    $stmt->close();
    $db->close();
}

if (isset($_POST['oshi'])) {
    $_SESSION['oshi_gagal'] = true;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hololive Registration</title>
    <link rel="stylesheet" href="register.css">
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
        .sign-up-text {
            margin-top : 50px;
            color: #00BFFF;
            font-weight: bold;
        }
        .sign-up-button {
            background-color: #00BFFF;
            color: white;
            border: none;
            margin-top : 12px;
            margin-left : 2px;
        }
        .sign-up-button:hover {
            background-color: #008CBA;
        }
        .login-link {
            margin-left : 5px;
            color: #00BFFF;
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
                <?php if(isset($_SESSION["is_login"]) && $_SESSION["is_login"] == true) { ?>
                    <form action="register.php" method="POST">
                    <button type="submit" name="oshi-login" class="nav-link active">Oshi</button>
                    </form>
                <?php } else { ?>
                    <form action="register.php" method="POST">
                    <button type="submit" name="oshi" class="nav-link active">Oshi</button>
                    </form>
                <?php } ?>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                User
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="login.php">Login</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="register.php">Register</a></li>
                </ul>
            </li>
            </ul>
        </div>
        </div>
    </nav>

    <div class="wrapper">
    <div class="container register-container mt-5">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img src="./gambar/Hololive_Production.png" alt="Hololive" class="logo">
            </div>
            <div class="col-md-6">
                <h2 class="sign-up-text">SIGN UP</h2>
                <form action="register.php" method="POST" id="formRegister">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
                    </div>
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" id="username" placeholder="Enter username" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                    </div>
                    <button type="button" name="signup" class="btn sign-up-button"  id="signupButton">Sign Up</button>
                </form>
                <p class="mt-3">
                    <a href="login.php" class="login-link">Sudah punya akun?</a>
                </p>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['registerfailed']) && $_SESSION['registerfailed'] == true) { ?>
                Swal.fire({
                    title: "Register Gagal!",
                    text: "Email atau username sudah dipakai.",
                    icon: "error"
                });
                <?php unset($_SESSION['registerfailed']);
        } ?>
        <?php if (isset($_SESSION['oshi_gagal']) && $_SESSION['oshi_gagal'] == true) { ?>
          Swal.fire({
              title: "Akses Gagal!",
              text: "Anda belum melakukan login.",
              icon: "error"
          });
          <?php unset($_SESSION['oshi_gagal']);
        } ?>
        document.addEventListener('DOMContentLoaded', function () {
            const signupbutton = document.getElementById('signupButton');
            const form = document.getElementById('formRegister');

            signupbutton.addEventListener('click', function (event) {
                if (form.checkValidity()) {
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        text: "Data akan didaftarkan sesuai dengan yang anda isi!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let registerInput = document.createElement('input');
                            registerInput.type = 'hidden';
                            registerInput.name = 'signup';
                            registerInput.value = 'true';
                            form.appendChild(registerInput);
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
