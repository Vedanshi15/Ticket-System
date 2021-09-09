<?php
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->load("xml/user.xml");
$users = $doc->getElementsByTagName("user");
$error = "";
if (isset($_POST["signin"])) { //chcek button is set or not
	$username = $_POST["username"];
	$password = md5($_POST["password"]);
	foreach ($users as $user) { //iterate the user data
		$xmlUsername = $user->getElementsByTagName("username")->item(0)->nodeValue;
		$xmlPassword = $user->getElementsByTagName("password")->item(0)->nodeValue;
		$userId = $user->getElementsByTagName("userid")->item(0)->nodeValue;
		$usertype = $user->getElementsByTagName("usertype")->item(0)->nodeValue;
		if (($username == $xmlUsername) && ($password == $xmlPassword)) {
			session_start();
			//make session variables
			$_SESSION['status'] = "Logged In";
			$_SESSION['userId'] = $userId;
			$_SESSION['usertype'] = $usertype;
			$_SESSION['username'] = $username;
			//redirect according to usertype
			if (($usertype == "Admin")) {
				header("Location: admin.php");
			} else if (($usertype == "Client")) {
				header("Location: Client.php");
			}
			break;
		} else {
			$error = "Invalid username and/or password";
		}
	}
}
?>
<!doctype html>
<html lang="en">
<head>
  <title>Ticket System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="img js-fullheight" style="background-image: url(images/bg3.jpg);">
<nav class="navbar fixed-top navbar-light bg-light">
  <a class="navbar-brand" href="index.php">Ticket System</a>
</nav>
<section class="ftco-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 text-center mb-5">
        <p class="heading-section">Welcome to Ticket System</p>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-6 text-center mb-4">
        <h2 class="heading-section">Login</h2>
        <div class="table-responsive">
          <table class="table table-dark justify-content-center">
            <thead>
            <tr>
              <th scope="col">User Name</th>
              <th scope="col">Password</th>
              <th scope="col">User Type</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>Zoya</td>
              <td>Zoya15</td>
              <td>Admin</td>
            </tr>
            <tr>
              <td>Vedanshi</td>
              <td>Vedu15</td>
              <td>Client</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="login-wrap p-0">
          <form action="#" method="POST" class="signin-form">
            <div class="form-group">
              <label for="username"></label>
              <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
              <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div style="color:red; text-align:left;"><?= $error; ?></div>
            <div class="form-group">
              <button type="submit" name="signin" class="form-control btn btn-primary submit px-3">Sign In</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
require_once 'footer.php';
?>
</body>
</html>