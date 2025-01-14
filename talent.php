<?php
include "./service/database.php";
session_start();

if (isset($_POST['logout'])) {
  $_SESSION["is_login"] = false;
  $_SESSION["is_logout"] = true;
  header("talent.php");
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
curl_setopt($ch, CURLOPT_URL, "https://holodex.net/api/v2/live?org=Hololive");
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

$genJP = array_fill(0, 9, true); // Default: Semua dicentang
$genID = array_fill(1, 3, true); // Default: Semua dicentang
$genEN = array_fill(1, 3, true); // Default: Semua dicentang

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $genJP = array();
    $genID = array();
    $genEN = array();

    for ($i = 0; $i <= 8; $i++) {
        $genJP[$i] = isset($_POST["genJP$i"]) ? true : false;
    }

    for ($i = 1; $i <= 3; $i++) {
        $genID[$i] = isset($_POST["genID$i"]) ? true : false;
    }

    for ($i = 1; $i <= 3; $i++) {
        $genEN[$i] = isset($_POST["genEN$i"]) ? true : false;
    }

    if(isset($_POST["clearAll"])) {
        $genJP = array_fill(0, 9, false);
        $genID = array_fill(1, 3, false);
        $genEN = array_fill(1, 3, false);
    }
}

$jpgen0 = ["Tokino Sora", "Roboco-san", "Sakura Miko", "Hoshimachi Suisei", "AZKi"];
$jpgen1 = ["Shirakami Fubuki", "Natsuiro Matsuri", "Akai Haato", "Aki Rosenthal"];
$jpgen2 = ["Minato Aqua", "Murasaki Shion", "Nakiri Ayame", "Yuzuki Choco", "Oozora Subaru"];
$jpgen3 = ["Ookami Mio", "Nekomata Okayu", "Inugami Korone"];
$jpgen4 = ["Usada Pekora", "Shiranui Flare", "Shirogane Noel", "Houshou Marine"];
$jpgen5 = ["Amane Kanata", "Tsunomaki Watame", "Tokoyami Towa", "Himemori Luna"];
$jpgen6 = ["Yukihana Lamy", "Momosuzu Nene", "Shishiro Botan", "Omaru Polka"];
$jpgen7 = ["La+ Darknesss", "Takane Lui", "Hakui Koyori", "Sakamata Chloe", "Kazama Iroha"];
$idgen1 = ["Ayunda Risu", "Moona Hoshinova", "Airani Iofifteen"];
$idgen2 = ["Kureiji Ollie", "Anya Melfissa", "Pavolia Reine"];
$idgen3 = ["Vestia Zeta", "Kobo Kanaeru", "Kaela Kovalskia"];
$engen1 = ["Gawr Gura", "Amelia Watson", "Mori Calliope", "Takanashi Kiara", "Ninomae Ina'nis"];
$engen2 = ["Nanashi Mumei", "Hakos Baelz", "IRyS", "Ceres Fauna", "Ouro Kronii"];
$engen3 = ["Nerissa Ravencroft", "Koseki Bijou", "Shiori Novella", "Fuwawa Abyssgard", "Mococo Abyssgard"];
$jpgen8 = ["Hiodoshi Ao", "Otonose Kanade", "Ichijou Ririka", "Juufuutei Raden", "Todoroki Hajime"];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1, width=device-width" />
  <link rel="stylesheet" href="talent.css">
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
                <form action="talent.php" method="POST">
                  <button type="submit" name="oshi-login" class="nav-link active">Oshi</button>
                </form>
            <?php } else { ?>
                <form action="talent.php" method="POST">
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
                <li><a class="dropdown-item" href="profile.php">User Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="talent.php" method="POST">
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
  <br> </br>
  <h1 class="text-center">Filter by Talent's Generation</h1>
  <br> </br>
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="row">
            <!-- Kolom Hololive JP -->
            <div class="col-md-4">
                <h2 class="text-center mb-3">Hololive JP</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Hololive JP Generation</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($i = 0; $i <= 8; $i++) { ?>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="genJP<?= $i ?>" value="1" <?php if($genJP[$i]) echo "checked"; ?>>
                                    <label class="form-check-label" for="genJP<?= $i ?>">
                                        <?php if($i == 8){ ?>
                                            Hololive DEV_IS : ReGLOSS
                                        <?php } else if ($i == 3){ ?>
                                            Hololive Gamers
                                        <?php } else if ($i == 4){ ?>
                                            Generation 3
                                        <?php } else if ($i == 5){ ?>
                                            Generation 4
                                        <?php } else if ($i == 6){ ?>
                                            Generation 5
                                        <?php } else if ($i == 7){ ?>
                                            Generation 6 - HoloX
                                        <?php } else { ?>
                                            Generation <?= $i ?>
                                        <?php } ?>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Kolom Hololive ID -->
            <div class="col-md-4">
                <h2 class="text-center mb-3">Hololive ID</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Hololive ID Generation</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($i = 1; $i <= 3; $i++) { ?>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="genID<?= $i ?>" value="1" <?php if($genID[$i]) echo "checked"; ?>>
                                    <label class="form-check-label" for="genID<?= $i ?>">
                                        Generation <?= $i ?>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Kolom Hololive EN -->
            <div class="col-md-4">
                <h2 class="text-center mb-3">Hololive EN</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Hololive EN Generation</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($i = 1; $i <= 3; $i++) { ?>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="genEN<?= $i ?>" value="1" <?php if($genEN[$i]) echo "checked"; ?>>
                                    <label class="form-check-label" for="genEN<?= $i ?>">
                                        Generation <?= $i ?>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tombol Filter -->
        <div class="row justify-content-center">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
                <button type="submit" class="btn btn-secondary" name="clearAll">Clear All</button>
            </div>
        </div>
    </form>

<!-- Talent Section -->
    <?php
    // Tampilkan div sesuai dengan status centang untuk Hololive JP
    for ($i = 0; $i <= 8; $i++) {
        if (isset($genJP[$i]) && $genJP[$i]) { ?>
          <br> </br>
          <div class="container-fluid">
              <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                  <div class="col-12 text-light fw-bold text-center">
                    <?php if($i == 8){ ?>
                      Hololive DEV_IS : ReGLOSS
                    <?php } else if ($i == 3){ ?>
                      Hololive Gamers
                    <?php } else if ($i == 4){ ?>
                      Hololive JP - Generation 3
                    <?php } else if ($i == 5){ ?>
                      Hololive JP - Generation 4
                    <?php } else if ($i == 6){ ?>
                      Hololive JP - Generation 5
                    <?php } else if ($i == 7){ ?>
                      Hololive JP - Generation 6 - HoloX
                    <?php } else { ?>
                      Hololive JP - Generation <?= $i ?>
                    <?php } ?>
                  </div>
              </div>
          </div>
          <div class="container">
          <br> </br>
                  <div class="row">
                      <?php
                          if ($i == 0){
                            $memberJP = $jpgen0;
                          } else if ($i == 1) {
                            $memberJP = $jpgen1;
                          } else if ($i == 2) {
                            $memberJP = $jpgen2;
                          } else if ($i == 3) {
                            $memberJP = $jpgen3;
                          } else if ($i == 4) {
                            $memberJP = $jpgen4;
                          } else if ($i == 5) {
                            $memberJP = $jpgen5;
                          } else if ($i == 6) {
                            $memberJP = $jpgen6;
                          } else if ($i == 7) {
                            $memberJP = $jpgen7;
                          } else if ($i == 8) {
                            $memberJP = $jpgen8;
                          }
                          $no_live_now = true;
                          
                          foreach($data as $live): 
                          $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                          $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                          $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                          if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberJP) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                          $no_live_now = false;
                      ?>
                          <div class="col-md-3 d-flex justify-content-center">
                              <div class="card" style="width: 18rem;">
                                  <img src="https://i.ytimg.com/vi/<?php echo $live['id']; ?>/hq720.jpg" class="card-img-top" alt="thumbnail">
                                  <div class="card-body">
                                      <h5 class="card-title"><?php echo htmlspecialchars($live['title']); ?></h5>
                                      <p class="card-text"><?php echo htmlspecialchars($live['channel']['name']); ?></p>
                                      <?php if ($live['status'] == "live"){ ?>
                                          <p class="card-text">
                                              <div class="spinner-grow text-danger" style="width: 1rem; height: 1rem;" role="status">
                                              <span class="visually-hidden">Loading...</span>
                                              </div>
                                          Live Now...
                                          </p>
                                      <?php } else if ($live['status'] == "upcoming") { ?>
                                          <p class="card-text" style="color: red;">Start at <?php echo $jadwalLive ?></p>
                                      <?php } ?>
                                      <a href="https://www.youtube.com/watch?v=<?php echo $live['id']; ?>" class="btn btn-primary">Kunjungi</a>
                                  </div>
                              </div>
                          </div>
                      <?php } endforeach;
                      if ($no_live_now == true){ ?>
                      <h2 class="mb-1 text-center"> Tidak Ada Live Stream </h2>
                      <?php } ?>
                  </div>
              </div>
        <?php }
    } 

    // Tampilkan div sesuai dengan status centang untuk Hololive JP
    for ($i = 1; $i <= 3; $i++) {
        if (isset($genID[$i]) && $genID[$i]) { ?>
          <br> </br>
          <div class="container-fluid">
              <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                  <div class="col-12 text-light fw-bold text-center">
                      Hololive ID - Generation <?= $i ?>
                  </div>
              </div>
          </div>
          <div class="container">
          <br> </br>
                  <div class="row">
                      <?php
                          if ($i == 1){
                            $memberID = $idgen1;
                          } else if ($i == 2) {
                            $memberID = $idgen2;
                          } else if ($i == 3) {
                            $memberID = $idgen3;
                          }
                          $no_live_now = true;
                          
                          foreach($data as $live): 
                          $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                          $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                          $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                          if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberID) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                          $no_live_now = false;
                      ?>
                          <div class="col-md-3 d-flex justify-content-center">
                              <div class="card" style="width: 18rem;">
                                  <img src="https://i.ytimg.com/vi/<?php echo $live['id']; ?>/hq720.jpg" class="card-img-top" alt="thumbnail">
                                  <div class="card-body">
                                      <h5 class="card-title"><?php echo htmlspecialchars($live['title']); ?></h5>
                                      <p class="card-text"><?php echo htmlspecialchars($live['channel']['name']); ?></p>
                                      <?php if ($live['status'] == "live"){ ?>
                                          <p class="card-text">
                                              <div class="spinner-grow text-danger" style="width: 1rem; height: 1rem;" role="status">
                                              <span class="visually-hidden">Loading...</span>
                                              </div>
                                          Live Now...
                                          </p>
                                      <?php } else if ($live['status'] == "upcoming") { ?>
                                          <p class="card-text" style="color: red;">Start at <?php echo $jadwalLive ?></p>
                                      <?php } ?>
                                      <a href="https://www.youtube.com/watch?v=<?php echo $live['id']; ?>" class="btn btn-primary">Kunjungi</a>
                                  </div>
                              </div>
                          </div>
                      <?php } endforeach;
                      if ($no_live_now == true){ ?>
                      <h2 class="mb-1 text-center"> Tidak Ada Live Stream </h2>
                      <?php } ?>
                  </div>
              </div>
        <?php }
    } 

    // Tampilkan div sesuai dengan status centang untuk Hololive EN
    for ($i = 1; $i <= 3; $i++) {
      if (isset($genEN[$i]) && $genEN[$i]) { ?>
        <br> </br>
        <div class="container-fluid">
            <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                <div class="col-12 text-light fw-bold text-center">
                  Hololive EN - Generation <?= $i ?>
                </div>
            </div>
        </div>
        <div class="container">
        <br> </br>
                <div class="row">
                    <?php
                        if ($i == 1){
                          $memberEN = $engen1;
                        } else if ($i == 2) {
                          $memberEN = $engen2;
                        } else if ($i == 3) {
                          $memberEN = $engen3;
                        }
                        $no_live_now = true;
                        
                        foreach($data as $live): 
                        $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                        $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                        $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                        if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberEN) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                        $no_live_now = false;
                    ?>
                        <div class="col-md-3 d-flex justify-content-center">
                            <div class="card" style="width: 18rem;">
                                <img src="https://i.ytimg.com/vi/<?php echo $live['id']; ?>/hq720.jpg" class="card-img-top" alt="thumbnail">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($live['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($live['channel']['name']); ?></p>
                                    <?php if ($live['status'] == "live"){ ?>
                                        <p class="card-text">
                                            <div class="spinner-grow text-danger" style="width: 1rem; height: 1rem;" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                            </div>
                                        Live Now...
                                        </p>
                                    <?php } else if ($live['status'] == "upcoming") { ?>
                                        <p class="card-text" style="color: red;">Start at <?php echo $jadwalLive ?></p>
                                    <?php } ?>
                                    <a href="https://www.youtube.com/watch?v=<?php echo $live['id']; ?>" class="btn btn-primary">Kunjungi</a>
                                </div>
                            </div>
                        </div>
                    <?php } endforeach;
                    if ($no_live_now == true){ ?>
                    <h2 class="mb-1 text-center"> Tidak Ada Live Stream </h2>
                    <?php } ?>
                </div>
            </div>
      <?php }
  } 
    ?>

  <footer class="bg-dark text-white pt-5 pb-4 mt-4">
    <div class="container text-center text-md-left">
      <div class="row text-center text-md-left">
        <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3 mb-5">
          <h5 class="text-uppercase mb-4 font-weight-bold text-decoration-underline">Hololive RPL PROJECT</h5>
          <img src="./gambar/Hololive_Production.png" class="img-fluid" alt="holo-footer">
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
