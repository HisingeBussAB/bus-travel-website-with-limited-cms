<?php
include ('../../config/config.php');
include('../../includes/functions.php');

sec_session_start();

echo $_SESSION['FIX_TOKEN'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if ($_SESSION['FIX_TOKEN'] == FIXED_LOGIN_TOKEN) {

    //RECAPTA START Check I am not a robot recaptcha

    $post_data = http_build_query(
        array(
          'secret' => RECAPTCHA_SECRET,
          'response' => $_POST['g-recaptcha-response'],
          'remoteip' => $_SERVER['REMOTE_ADDR']
        )
      );
      $opts = array('http' =>
        array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => $post_data
        )
      );
      $context  = stream_context_create($opts);
      $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
      $result = json_decode($response);
      if (!$result->success) {
        http_response_code(403);
        echo "reCAPTCHA verification failed.";
        exit;
      }
      //RECAPTCA END




    $username = trim($_POST['user']);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
      http_response_code(403);
      echo "Något är fel med databasen<br>";
      print_r($conn);
      exit;
    };

    $sql = "SELECT username, pwd FROM " . $table_prefix . "logins WHERE username='" . $username . "' LIMIT 1;";
    echo $sql;

    $result = $conn->query($sql);
    if ($result == false || $result->num_rows <= 0) {
      http_response_code(403);
      echo "Användaren finns inte. Kontrollera användarnamnet.";
      $conn->close();
      exit;
    };
    $row = $result->fetch_array(MYSQLI_ASSOC);

    if ($row["username"] == $username) {
      if (password_verify(trim($_POST['pwd']), $row["pwd"])) {
      //login success
      $token = md5(time() . microtime() . LOGGED_IN_TOKEN_SALT);
      $usertoken = sha1($username . LOGGED_IN_USER_SALT);
      $_SESSION['LOGGED_IN_TOKEN'] = $token;
      $_SESSION['LOGGED_IN_USER'] = $usertoken;
      http_response_code(200);
      $conn->close();
      exit;
    } else {
      http_response_code(403);
      echo "Fel lösenord. Prova igen.";
      echo $row["username"]. "<br>";
      echo $pwd. "<br>";
      echo $row["pwd"]. "<br>";
      exit;
    }
    } else {
      http_response_code(403);
      echo "Användaren hämtades men matchar inte.";
      $conn->close();
      exit;
    }

  } else {
    http_response_code(403);
    echo "Ingen session token hittades. 403";
    exit;
  }

} else {
  http_response_code(403);
  echo "Förbjuden metod GET. 403";
  exit;
}


?>
