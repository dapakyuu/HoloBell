<?php
include "../service/database.php";
session_start();
$no_live_now = true;
$no_live_today = true;
$no_live_upcoming = true;

if (isset($_POST['logout'])) {
  $_SESSION["is_login"] = false;
  $_SESSION["is_logout"] = true;
  header("vspo.php");
}

if (isset($_POST['oshi'])) {
  $_SESSION['oshi_gagal'] = true;
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
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

$ch = curl_init();
// Setel URL dan opsi
curl_setopt($ch, CURLOPT_URL, "https://holodex.net/api/v2/live?org=VSpo");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-APIKEY: ae36c03a-ee50-477e-bd6c-a73657ae8ce3'
]);
// Eksekusi cURL
$response = curl_exec($ch);
// Periksa apakah ada kesalahan
if(curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
// Tutup cURL
curl_close($ch);

$data = json_decode($response, true);

$ch2 = curl_init();
// Setel URL dan opsi
curl_setopt($ch2, CURLOPT_URL, "https://holodex.net/api/v2/live?org=VSpo&type=placeholder&placeholderType=external-stream");
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'X-APIKEY: ae36c03a-ee50-477e-bd6c-a73657ae8ce3'
]);
// Eksekusi cURL
$response2 = curl_exec($ch2);
// Periksa apakah ada kesalahan
if(curl_errno($ch2)) {
    echo 'Error:' . curl_error($ch2);
}
// Tutup cURL
curl_close($ch2);

$twitch = json_decode($response2, true);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1, width=device-width" />
  <link rel="stylesheet" href="../schedule.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400&display=swap" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/holodex.js/dist/holodex.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .card {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top" data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="../home.php">
        <img src="../gambar/Hololive_Production.png" class="img-fluid" alt="Hololive Logo">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse ms-auto" id="navbarSupportedContent">
        <ul class="navbar-nav mb-2 mb-lg-0 ms-auto">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="../schedule.php">Schedule</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="../branch.php">Filter by Branch</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="../talent.php">Filter by Generation</a>
          </li>
          <li class="nav-item">
            <?php if(isset($_SESSION["is_login"]) && $_SESSION["is_login"] == true) { ?>
                <form action="vspo.php" method="POST">
                  <button type="submit" name="oshi-login" class="nav-link active">Oshi</button>
                </form>
            <?php } else { ?>
                <form action="vspo.php" method="POST">
                  <button type="submit" name="oshi" class="nav-link active">Oshi</button>
                </form>
            <?php } ?>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php if(isset($_SESSION["is_login"]) && $_SESSION["is_login"] == true) {
                  echo $_SESSION["username"];
              } else { ?>
                  User
              <?php } ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if(isset($_SESSION["is_login"]) && $_SESSION["is_login"] == true) { ?>
                <li><a class="dropdown-item" href="../profile.php">User Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="vspo.php" method="POST">
                      <button type="submit" name="logout" class="dropdown-item">Logout</button>
                  </form>
                </li>
              <?php } else { ?>
                <li><a class="dropdown-item" href="../login.php">Login</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="../register.php">Register</a></li>
              <?php } ?>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <br> </br>
  <h1 class="text-center">Schedule VSPO Members</h1>
  <br> </br>
  <div class="container-fluid">
    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
      <div class="col-12 text-light fw-bold text-center">
        Live Now
      </div>
    </div>
  </div>
  <div class="container">
  <br> </br>
        <div class="row">
            <?php foreach($data as $live): 
                if($live['channel']['org'] == "VSpo" && $live['status'] == "live") {
                  $no_live_now = false;
              ?>
                <div class="col-md-3 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <img src="https://i.ytimg.com/vi/<?php echo $live['id']; ?>/hq720.jpg" class="card-img-top" alt="thumbnail">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($live['title']); ?></h5>
                            <p class="card-text">Channel : <?php echo htmlspecialchars($live['channel']['english_name']); ?></p>
                            <p class="card-text">
                            <div class="spinner-grow text-danger" style="width: 1rem; height: 1rem;" role="status">
                              <span class="visually-hidden">Loading...</span>
                            </div>
                              Live Now...
                            </p>
                            <a href="https://www.youtube.com/watch?v=<?php echo $live['id']; ?>" class="btn btn-primary">Kunjungi</a>
                        </div>
                    </div>
                </div>
            <?php } endforeach;
            foreach($twitch as $livetwitch): 
                if($livetwitch['channel']['org'] == "VSpo" && $livetwitch['status'] == "live") {
                  $no_live_now = false;
              ?>
                <div class="col-md-3 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <img src=<?php echo $livetwitch['thumbnail']; ?> class="card-img-top" alt="thumbnail">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($livetwitch['title']); ?></h5>
                            <p class="card-text">Channel : <?php echo htmlspecialchars($livetwitch['channel']['english_name']); ?></p>
                            <p class="card-text">
                            <div class="spinner-grow text-danger" style="width: 1rem; height: 1rem;" role="status">
                              <span class="visually-hidden">Loading...</span>
                            </div>
                              Live Now... (External Stream)
                            </p>
                            <a href=<?php echo $livetwitch['link']; ?> class="btn btn-primary">Kunjungi</a>
                        </div>
                    </div>
                </div>
            <?php } endforeach;
            if ($no_live_now == true){ ?>
              <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
              <br> </br>
            <?php } ?>
        </div>
    </div>
  <div class="container-fluid">
    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
      <div class="col-12 text-light fw-bold text-center">
        Today's Upcoming
      </div>
    </div>
  </div>
  <div class="container">
  <br> </br>
        <div class="row">
            <?php foreach($data as $live): 
                $currentDate = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                $curDate = $currentDate->format('Y-m-d');
                $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                $hariIni = $utc_plus_7_time->format('Y-m-d');
                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                if($live['channel']['org'] == "VSpo" && $live['status'] == "upcoming" && $hariIni == $curDate) {
                  $no_live_today = false;
              ?>
                <div class="col-md-3 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <img src="https://i.ytimg.com/vi/<?php echo $live['id']; ?>/hq720.jpg" class="card-img-top" alt="thumbnail">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($live['title']); ?></h5>
                            <p class="card-text">Channel : <?php echo htmlspecialchars($live['channel']['english_name']); ?></p>
                            <p class="card-text" style="color: red;">Start at <?php echo $jadwalLive ?></p>
                            <a href="https://www.youtube.com/watch?v=<?php echo $live['id']; ?>" class="btn btn-primary">Kunjungi</a>
                        </div>
                    </div>
                </div>
            <?php } endforeach;
            foreach($twitch as $livetwitch): 
                $currentDate = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                $curDate = $currentDate->format('Y-m-d');
                $utc_plus_7_time = new DateTime($livetwitch['start_scheduled'], new DateTimeZone('UTC'));
                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                $hariIni = $utc_plus_7_time->format('Y-m-d');
                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                if($livetwitch['channel']['org'] == "VSpo" && $livetwitch['status'] == "upcoming" && $hariIni == $curDate) {
                  $no_live_today = false;
              ?>
                <div class="col-md-3 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <img src=<?php echo $livetwitch['thumbnail']; ?> class="card-img-top" alt="thumbnail">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($livetwitch['title']); ?></h5>
                            <p class="card-text">Channel : <?php echo htmlspecialchars($livetwitch['channel']['english_name']); ?></p>
                            <p class="card-text" style="color: red;">Start at <?php echo $jadwalLive ?> (External Stream)</p>
                            <a href=<?php echo $livetwitch['link']; ?> class="btn btn-primary">Kunjungi</a>
                        </div>
                    </div>
                </div>
            <?php } endforeach;
            if ($no_live_today == true){ ?>
              <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
              <br> </br>
            <?php } ?>
        </div>
    </div>
  <div class="container-fluid">
    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
      <div class="col-12 text-light fw-bold text-center">
        More Upcoming
      </div>
    </div>
  </div>
  <div class="container">
  <br> </br>
        <div class="row">
            <?php foreach($data as $live): 
                $currentDate = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                $curDate = $currentDate->format('Y-m-d');
                $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                $hariIni = $utc_plus_7_time->format('Y-m-d');
                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                if($live['channel']['org'] == "VSpo" && $live['status'] == "upcoming" && $hariIni > $curDate) {
                  $no_live_upcoming = false;
              ?>
                <div class="col-md-3 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <img src="https://i.ytimg.com/vi/<?php echo $live['id']; ?>/hq720.jpg" class="card-img-top" alt="thumbnail">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($live['title']); ?></h5>
                            <p class="card-text">Channel : <?php echo htmlspecialchars($live['channel']['english_name']); ?></p>
                            <p class="card-text" style="color: red;">Start at <?php echo $jadwalLive ?></p>
                            <a href="https://www.youtube.com/watch?v=<?php echo $live['id']; ?>" class="btn btn-primary">Kunjungi</a>
                        </div>
                    </div>
                </div>
            <?php } endforeach;
            foreach($twitch as $livetwitch): 
                $currentDate = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                $curDate = $currentDate->format('Y-m-d');
                $utc_plus_7_time = new DateTime($livetwitch['start_scheduled'], new DateTimeZone('UTC'));
                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                $hariIni = $utc_plus_7_time->format('Y-m-d');
                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                if($livetwitch['channel']['org'] == "VSpo" && $livetwitch['status'] == "upcoming" && $hariIni > $curDate) {
                  $no_live_today = false;
              ?>
                <div class="col-md-3 d-flex justify-content-center">
                    <div class="card" style="width: 18rem;">
                        <img src=<?php echo $livetwitch['thumbnail']; ?> class="card-img-top" alt="thumbnail">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($livetwitch['title']); ?></h5>
                            <p class="card-text">Channel : <?php echo htmlspecialchars($livetwitch['channel']['english_name']); ?></p>
                            <p class="card-text" style="color: red;">Start at <?php echo $jadwalLive ?> (External Stream)</p>
                            <a href=<?php echo $livetwitch['link']; ?> class="btn btn-primary">Kunjungi</a>
                        </div>
                    </div>
                </div>
            <?php } endforeach;
            if ($no_live_upcoming == true){ ?>
              <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
            <?php } ?>
        </div>
    </div>
  <footer class="bg-dark text-white pt-5 pb-4 mt-4">
    <div class="container text-center text-md-left">
      <div class="row text-center text-md-left">
        <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3 mb-5">
          <h5 class="text-uppercase mb-4 font-weight-bold text-decoration-underline">Hololive RPL PROJECT</h5>
          <img src="../gambar/Hololive_Production.png" class="img-fluid" alt="holo-footer">
        </div>
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-decoration-underline">Our Team</h5>
          <p>
            <a class="text-white" style="text-decoration: none;">Daffa Al-Fathir Ismail</a>
          </p>
          <p>
            <a class="text-white" style="text-decoration: none;">Anatasia Aulienda Subandri</a>
          </p>
          <p>
            <a class="text-white" style="text-decoration: none;">Rajih Nibras Maulana</a>
          </p>
        </div>
        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-decoration-underline">Contact Us</h5>
          <p>
            <a href="https://www.instagram.com/daffa_alfathir_/" class="text-white" style="text-decoration: none;">
              <img src="./gambar/Group 44.png" class="img-fluid" alt="holo-footer">
            </a>
          </p>
        </div>
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-decoration-underline">Address</h5>
          <p>
            <i class="fas fa-home mr-3"></i> Jl. Pandanwangi Jl. Cibiru Indah 3, Cibiru Wetan, Kec. Cileunyi, Kabupaten Bandung, Jawa Barat 40625
          </p>
        </div>
      </div>
      <hr class="mb-4">
      <div class="row align-items-center">
        <div class="col-12">
          <p class="text-white text-center"> © 2024 Kelompok 3 Kelas 4A Teknik Komputer UPI di Cibiru</p>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
        <?php if (isset($_SESSION['first_login']) && $_SESSION['first_login'] == true) { ?>
            Swal.fire({
                title: "Halo <?=$_SESSION["username"]?>!",
                text: "Selamat datang di HoloBell, semoga harimu menyenangkan.",
                icon: "success"
            });
            <?php $_SESSION['first_login'] = false;
        } ?>
        <?php if (isset($_SESSION['is_logout']) && $_SESSION['is_logout'] == true) { ?>
            Swal.fire({
                title: "Anda Berhasil Logout!",
                text: "Sampai Jumpa <?= $_SESSION["username"] ?>!",
                icon: "success"
            });
            <?php $_SESSION['is_logout'] = false;
            session_unset();
            session_destroy();
        } ?>
        <?php if (isset($_SESSION['is_registered']) && $_SESSION['is_registered'] == true) { ?>
            Swal.fire({
                title: "Anda Berhasil Melakukan Registrasi!",
                text: "Selamat datang di HoloBell, <?=$_SESSION["username"]?>.",
                icon: "success"
            });
            <?php $_SESSION['is_registered'] = false;
        } ?>
        <?php if (isset($_SESSION['oshi_gagal']) && $_SESSION['oshi_gagal'] == true) { ?>
          Swal.fire({
              title: "Akses Gagal!",
              text: "Anda belum melakukan login.",
              icon: "error"
          });
          <?php unset($_SESSION['oshi_gagal']);
        } ?>
        <?php if (isset($_SESSION['oshi_null']) && $_SESSION['oshi_null'] == true) { ?>
            Swal.fire({
                title: "Akses Gagal!",
                text: "Anda belum memilih oshi, silahkan pilih oshi pada halaman user profile.",
                icon: "error"
            });
            <?php unset($_SESSION['oshi_null']);
        } ?>
    </script>
</body>
</html>