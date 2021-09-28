<?php
session_start();
if ($_SESSION['status'] != "Logged In") {
	header("Location: ./index.php");
}
//using session variables values
$userId = $_SESSION['userId'];
$username = $_SESSION['username'];
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->load("xml/Ticket.xml");
$category = $doc->getElementsByTagName("category");
$postMessage = $doc->getElementsByTagName("postMessage");
$date = new DateTime("NOW");
$error = "";
//add new ticket
if (isset($_POST["ticketbtn"])) {
	$category = $_POST["category"];
	$msg = $_POST["msg"];
	var_dump($category);
	var_dump($msg);
	//validation
	if (empty($category) || empty($msg)) {
		$error = "Please fill all details.";
	} else {
		$error = "";
		//add new ticket
		$newTicket = $doc->createElement("ticket");
		$ticketid = $doc->createElement("ticketid", rand(1, 1000));
		$newTicket->appendChild($ticketid);
		$dateissued = $doc->createElement("dateissued", $date->format('Y-m-d\TH:i:s'));
		$newTicket->appendChild($dateissued);
		$category = $doc->createElement("category", $category);
		$newTicket->appendChild($category);
		$messages = $doc->createElement("messages");
		$message = $doc->createElement("message");
		$message->setAttribute("userid", $userId);
		$messagetext = $doc->createElement("messagetext", $msg);
		$datetime = $doc->createElement("dateposted", $date->format('Y-m-d\TH:i:s'));
		$message->appendChild($messagetext);
		$message->appendChild($datetime);
		$messages->appendChild($message);
		$newTicket->appendChild($messages);
		$status = $doc->createElement("status", "Ongoing");
		$newTicket->appendChild($status);
		$useridd = $doc->createElement("userid", $userId);
		$newTicket->appendChild($useridd);
		$doc->documentElement->appendChild($newTicket);
		$doc->save("xml/Ticket.xml");
	}
}
$xpath = new DOMXPath($doc);
var_dump($userId);
$userDoc = new DOMDocument();
$userDoc->preserveWhiteSpace = false;
$userDoc->formatOutput = true;
$userDoc->load("xml/user.xml");
$users = $userDoc->getElementsByTagName("user");
$tickets = $xpath->query("//ticket[userid=$userId]");
$rows = "";
//to print tickets
foreach ($tickets as $ticket) {
	$rows .= '<tr>';
	$tid = $ticket->getElementsByTagName("ticketid")->item(0)->nodeValue;
	$rows .= '<td>' . $tid . '</td>';
	$date = new DateTime($ticket->getElementsByTagName("dateissued")->item(0)->nodeValue);
	$rows .= '<td>' . $ticket->getElementsByTagName("category")->item(0)->nodeValue . '</td>';
	$rows .= '<td>' . $ticket->getElementsByTagName("status")->item(0)->nodeValue . '</td>';
	$rows .= '<td>' . date_format($date, 'Y-m-d,  H:i:s') . '</td>';
	$ticket_uid = $ticket->getElementsByTagName("userid")->item(0)->nodeValue;
	foreach ($users as $user) {
		$uid = $user->getElementsByTagName("userid")->item(0)->nodeValue;
		if ($ticket_uid == $uid) {
			$rows .= '<td>' . $user->getElementsByTagName("username")->item(0)->nodeValue . '</td>';
		}
	}
	$rows .= '<td>
                <form action="ticket.php" method="POST">
                  <input type="hidden" name="id" value="' . $tid . '"/>
                  <input type="submit" name="TicketDetail" value="View Ticket"/>
                </form>
              </td>';
	$rows .= '</tr>';
}
if (empty($rows)) {
	$rows = "<h3>Not found</h3>";
}
require_once 'header.php';
?>
<section class="ftco-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 text-center mb-5">
        <h2 class="heading-section">Welcome <?php print $username ?> !!</h2>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="login-wrap p-0">
          <h3 class="mb-4 text-center">Add Ticket</h3>
          <form method="POST">
            <div class="form-group">
              <label for="category"></label>
              <input type="text" id="category" name="category" class="form-control" placeholder="Enter subject"
                     required>
            </div>
            <div class="form-group">
              <label for="msg"></label>
              <input type="text" id="msg" name="msg" class="form-control" placeholder="Enter your message" required>
            </div>
            <div class="form-group">
              <button type="submit" id="ticketbtn" name="ticketbtn" class="form-control btn btn-primary submit px-3">
                Submit
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <h3 class="mb-4 text-center">Tickets</h3>
      <table class="table table-dark justify-content-center">
        <thead>
        <tr>
          <th scope="col">Ticket ID</th>
          <th scope="col">Category</th>
          <th scope="col">Status</th>
          <th scope="col">Date</th>
          <th scope="col">User Name</th>
          <th scope="col">View</th>
        </tr>
        </thead>
        <tbody>
		<?php
		echo $rows;
		?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?php
require_once 'footer.php';
?>

