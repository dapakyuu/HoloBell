<?php
include "./service/database.php";
session_start();
$no_live_jp = true;
$no_live_id = true;
$no_live_en = true;
$no_live_DEV_IS = true;
$no_stars_jp = true;
$no_stars_en = true;

if (isset($_POST['logout'])) {
  $_SESSION["is_login"] = false;
  $_SESSION["is_logout"] = true;
  header("branch.php");
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
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1, width=device-width" />
  <link rel="stylesheet" href="branch.css">
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
                <form action="branch.php" method="POST">
                  <button type="submit" name="oshi-login" class="nav-link active">Oshi</button>
                </form>
            <?php } else { ?>
                <form action="branch.php" method="POST">
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
                  <form action="branch.php" method="POST">
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
  <h1 class="text-center">Filter by Branch</h1>
  <br> </br>
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="container-fluid">
        <ul class="list-group">
            <li class="list-group-item">
                <input class="form-check-input me-1" type="checkbox" value="1" name="hololiveJP" id="hololiveJP">
                <label class="form-check-label stretched-link" for="hololiveJP">Hololive Japan</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1" type="checkbox" value="2" name="hololiveID" id="hololiveID">
                <label class="form-check-label stretched-link" for="hololiveID">Hololive Indonesia</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1" type="checkbox" value="3" name="hololiveEN" id="hololiveEN">
                <label class="form-check-label stretched-link" for="hololiveEN">Hololive English</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1" type="checkbox" value="4" name="hololiveDEV_IS" id="hololiveDEV_IS">
                <label class="form-check-label stretched-link" for="hololiveDEV_IS">Hololive DEV_IS</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1" type="checkbox" value="5" name="holostarsJP" id="HolostarsJP">
                <label class="form-check-label stretched-link" for="HolostarsJP">Holostars Japan</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1" type="checkbox" value="6" name="holostarsEN" id="HolostarsEN">
                <label class="form-check-label stretched-link" for="HolostarsEN">Holostars English</label>
            </li>
        </ul>
    </div>

    <!-- Tombol Filter -->
    <div class="row justify-content-center mt-3">
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
            <button type="submit" class="btn btn-secondary" name="clearAll">Clear All</button>
        </div>
    </div>
    </form>
    <br> </br>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Cek apakah tombol clearAll ditekan
        if (isset($_POST['clearAll'])) {
            $_POST = array();
        } else {
            // Tampilkan div berdasarkan checkbox yang dicentang
            if (isset($_POST['hololiveJP']) && $_POST['hololiveJP']) { ?>
                <div class="container-fluid">
                    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                        <div class="col-12 text-light fw-bold text-center">
                            Hololive Japan
                        </div>
                    </div>
                </div>
                <div class="container">
                <br> </br>
                        <div class="row">
                            <?php 
                                $memberHoloJP = [
                                    "Tokino Sora", "Roboco-san", "Sakura Miko", "Hoshimachi Suisei", "AZKi",
                                    "Shirakami Fubuki", "Natsuiro Matsuri", "Akai Haato", "Aki Rosenthal",
                                    "Minato Aqua", "Murasaki Shion", "Nakiri Ayame", "Yuzuki Choco", "Oozora Subaru",
                                    "Ookami Mio", "Nekomata Okayu", "Inugami Korone",
                                    "Usada Pekora", "Shiranui Flare", "Shirogane Noel", "Houshou Marine",
                                    "Amane Kanata", "Tsunomaki Watame", "Tokoyami Towa", "Himemori Luna",
                                    "Yukihana Lamy", "Momosuzu Nene", "Shishiro Botan", "Omaru Polka",
                                    "La+ Darknesss", "Takane Lui", "Hakui Koyori", "Sakamata Chloe", "Kazama Iroha"
                                ];
                                
                                foreach($data as $live): 
                                $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                                if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberHoloJP) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                                $no_live_jp = false;
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
                            if ($no_live_jp == true){ ?>
                            <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
                            <br> </br>
                            <?php } ?>
                        </div>
                    </div>
            <?php }
            if (isset($_POST['hololiveID']) && $_POST['hololiveID']) { ?>
                <br> </br>
                <div class="container-fluid">
                    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                        <div class="col-12 text-light fw-bold text-center">
                            Hololive Indonesia
                        </div>
                    </div>
                </div>
                <div class="container">
                <br> </br>
                        <div class="row">
                            <?php 
                                $memberHoloID = [
                                    "Ayunda Risu", "Moona Hoshinova", "Airani Iofifteen",
                                    "Kureiji Ollie", "Anya Melfissa", "Pavolia Reine",
                                    "Vestia Zeta", "Kobo Kanaeru", "Kaela Kovalskia"
                                ];
                                
                                foreach($data as $live): 
                                $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                                if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberHoloID) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                                $no_live_id = false;
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
                            if ($no_live_id == true){ ?>
                            <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
                            <br> </br>
                            <?php } ?>
                        </div>
                    </div>
            <?php }
            if (isset($_POST['hololiveEN']) && $_POST['hololiveEN']) { ?>
                <br> </br>
                <div class="container-fluid">
                    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                        <div class="col-12 text-light fw-bold text-center">
                            Hololive English
                        </div>
                    </div>
                </div>
                <div class="container">
                <br> </br>
                        <div class="row">
                            <?php 
                                $memberHoloEN = [
                                    "Gawr Gura", "Amelia Watson", "Mori Calliope", "Takanashi Kiara", "Ninomae Ina'nis",
                                    "Nanashi Mumei", "Hakos Baelz", "IRyS", "Ceres Fauna", "Ouro Kronii",
                                    "Nerissa Ravencroft", "Koseki Bijou", "Shiori Novella", "Fuwawa Abyssgard", "Mococo Abyssgard"
                                ];
                                
                                foreach($data as $live): 
                                $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                                if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberHoloEN) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                                $no_live_en = false;
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
                            if ($no_live_en == true){ ?>
                            <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
                            <br> </br>
                            <?php } ?>
                        </div>
                    </div>
            <?php }
            if (isset($_POST['hololiveDEV_IS']) && $_POST['hololiveDEV_IS']) { ?>
                <br> </br>
                <div class="container-fluid">
                    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                        <div class="col-12 text-light fw-bold text-center">
                            Hololive DEV_IS
                        </div>
                    </div>
                </div>
                <div class="container">
                <br> </br>
                        <div class="row">
                            <?php 
                                $memberHoloDEV_IS = [
                                    "Hiodoshi Ao", "Otonose Kanade", "Ichijou Ririka", "Juufuutei Raden", "Todoroki Hajime"
                                ];
                                
                                foreach($data as $live): 
                                $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                                if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberHoloDEV_IS) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                                $no_live_DEV_IS = false;
                            ?>
                                <div class="col-md-3">
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
                            if ($no_live_DEV_IS == true){ ?>
                            <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
                            <br> </br>
                            <?php } ?>
                        </div>
                    </div>
            <?php }
            if (isset($_POST['holostarsJP']) && $_POST['holostarsJP']) { ?>
                <br> </br>
                <div class="container-fluid">
                    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                        <div class="col-12 text-light fw-bold text-center">
                            Holostars Japan
                        </div>
                    </div>
                </div>
                <div class="container">
                <br> </br>
                        <div class="row">
                            <?php 
                                $memberStarsJP = [
                                    "Hanasaki Miyabi", "Kanade Izuru", "Aruran-deisu", "Rikka",
                                    "Astel Leda", "Kishido Temma", "Yukoku Roberu",
                                    "Kageyama Shien", "Aragami Oga",
                                    "Yatogami Fuma", "Utsugi Uyu", "Hizaki Gamma", "Minase Rio",
                                ];
                                
                                foreach($data as $live): 
                                $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                                if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberStarsJP) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                                $no_stars_jp = false;
                            ?>
                                <div class="col-md-3">
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
                            if ($no_stars_jp == true){ ?>
                            <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
                            <br> </br>
                            <?php } ?>
                        </div>
                    </div>
            <?php }
            if (isset($_POST['holostarsEN']) && $_POST['holostarsEN']) { ?>
                <br> </br>
                <div class="container-fluid">
                    <div class="row align-items-center" style="background-color: #00BFFF; height: 50px;">
                        <div class="col-12 text-light fw-bold text-center">
                            Holostars English
                        </div>
                    </div>
                </div>
                <div class="container">
                <br> </br>
                        <div class="row">
                            <?php 
                                $memberStarsEN = [
                                    "Regis Altare", "Axel Syrios",
                                    "Gavis Bettel", "Machina X Flayon", "Banzoin Hakka", "Josuiji Shinri",
                                    "Jurard T Rexford", "Goldbullet", "Octavio", "Crimzon Ruze"
                                ];
                                
                                foreach($data as $live): 
                                $utc_plus_7_time = new DateTime($live['start_scheduled'], new DateTimeZone('UTC'));
                                $utc_plus_7_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                $jadwalLive = $utc_plus_7_time->format('H:i, l, d F Y');
                                if ($live['channel']['org'] == "Hololive" && isset($live['channel']['english_name']) && in_array($live['channel']['english_name'], $memberStarsEN) && ($live['status'] == "live" || $live['status'] == "upcoming")) {
                                $no_stars_en = false;
                            ?>
                                <div class="col-md-3">
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
                            if ($no_stars_en == true){ ?>
                            <h2 class="mb-5 text-center"> Tidak Ada Live Stream </h2>
                            <br> </br>
                            <?php } ?>
                        </div>
                    </div>
            <?php }
        }
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
