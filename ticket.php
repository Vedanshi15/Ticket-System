<?php
session_start();
if ($_SESSION['status'] != "Logged In") {
	header("Location: ./index.php");
}
$TicketId = $_POST['id'];
$userid = $_SESSION['userId'];
$username = $_SESSION['username'];
$usertype = $_SESSION['usertype'];
$date = new DateTime("NOW");
$status = '';
//var_dump($userId);
if ($_SESSION['status'] != "Logged In") {
	header("Location: index.php");
}
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->load("xml/Ticket.xml");
$userDoc = new DOMDocument();
$userDoc->preserveWhiteSpace = false;
$userDoc->formatOutput = true;
$userDoc->load("xml/user.xml");
$xpath = new DOMXPath($doc);
$userTickets = $xpath->query("//ticket[ticketid=$TicketId]");
$users = $userDoc->getElementsByTagName("user");
//var_dump($userTickets);
if (isset($_POST["sendmsg"])) {
	$msg = $_POST["msg"];
	if (empty($msg)) {
		$error = "Please enter message.";
	} else {
		$error = "";
		// to add new message to messages element
		foreach ($userTickets as $ticket) {
			$messages = $ticket->getElementsByTagName("messages");
			$message = $doc->createElement("message");
			$message->setAttribute("userid", $userid);
			$messagetext = $doc->createElement("messagetext", $msg);
			$datetime = $doc->createElement("dateposted", $date->format('Y-m-d\TH:i:s'));
			$message->appendChild($messagetext);
			$message->appendChild($datetime);
			$messages->item(0)->appendChild($message);
			$doc->save("xml/Ticket.xml");
		}
	}
}
//It'll change ticket status
if (isset($_POST["ticketstatus"])) {
	$ticketstatus = $_POST["ticketstatus"];
	foreach ($userTickets as $ticket) {
		$ticket->getElementsByTagName("status")->item(0)->nodeValue = $ticketstatus;
		$doc->save("xml/Ticket.xml");
	}
}
$rows = "";
$rows0 = "";
foreach ($userTickets as $ticket) {
	$usertype = $_SESSION['usertype'];
	$len = sizeof($ticket->getElementsByTagName("message"));
	$rows .= '<tr>';
	$rows .= '<td>' . $ticket->getElementsByTagName("ticketid")->item(0)->nodeValue . '</td>';
	$rows .= '<td>' . $ticket->getElementsByTagName("category")->item(0)->nodeValue . '</td>';
	$status = $ticket->getElementsByTagName("status")->item(0)->nodeValue;
	if ($status == 'Ongoing' && $usertype == 'Admin') {
		$rows .= '<td>
                  <form method="POST" >
                    <input type="hidden" name="id" value="' . $ticket->getElementsByTagName("ticketid")->item(0)->nodeValue . '"/> 
                    <select name="ticketstatus" onchange="this.form.submit()">
                      <option selected="selected">' . $status . '</option>
                      <option value="Resolved">Resolved</option>
                    </select>
                  </form>
                </td>';
	} else {
		$rows .= '<td>' . $status . '</td>';
	}
	$rows .= '<td>' . $ticket->getElementsByTagName("dateissued")->item(0)->nodeValue . '</td>';
	//to print multiple message elements
	for ($i = 0; $i < $len; $i++) {
		if ($i == 0) {
			$msg = $ticket->getElementsByTagName("message")->item(0);
			$rows0 .= '<td>' . $ticket->getElementsByTagName("messagetext")->item($i)->nodeValue . '</td>';
			$rows0 .= '<td>' . $ticket->getElementsByTagName("dateposted")->item(0)->nodeValue . '</td>';
			$ticket_uid = $msg->attributes->getNamedItem("userid")->nodeValue;
			var_dump($ticket_uid);
			foreach ($users as $user) {

				$uid = $user->getElementsByTagName("userid")->item(0)->nodeValue;
				var_dump($uid);
				if ($ticket_uid == $uid) {
					//var_dump($user->getElementsByTagName("username")->item(0)->nodeValue);
					$rows0 .= '<td>' . $user->getElementsByTagName("username")->item(0)->nodeValue . '</td>';
				}
			}
			$rows0 .= '</tr>';
		}
		if ($i > 0) {
			$msg = $ticket->getElementsByTagName("message")->item($i);
			$rows0 .= '<tr><td>' . $ticket->getElementsByTagName("messagetext")->item($i)->nodeValue . '</td>';
			$rows0 .= '<td>' . $ticket->getElementsByTagName("dateposted")->item($i)->nodeValue . '</td>';
			$ticket_uid = $msg->attributes->getNamedItem("userid")->nodeValue;
			foreach ($users as $user) {
				$uid = $user->getElementsByTagName("userid")->item(0)->nodeValue;
				if ($ticket_uid == $uid) {
					$rows0 .= '<td>' . $user->getElementsByTagName("username")->item(0)->nodeValue . '</td>';
				}
			}
		}
	}
}
if (empty($rows)) {
	$rows = "<h3>No messages found</h3>";
}
require_once 'header.php';
?>
<section class="ftco-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 text-center mb-5">
        <h2 class="heading-section">Ticket Details</h2>
      </div>
    </div>
    <div class="table-responsive">
      <a class="p-3 bg-dark text-white" <?php if ($usertype == 'Admin'){ ?>href="admin.php"
		 <?php } if ($usertype == 'Client'){ ?>href="Client.php"<?php } ?>>Go Back</a>
      <table class="table table-dark justify-content-center">
        <thead>
        <tr>
          <th scope="col">Ticket ID</th>
          <th scope="col">Category</th>
          <th scope="col">Status</th>
          <th scope="col">Date Opened</th>
        </tr>
        </thead>
        <tbody>
		<?php
		echo $rows;
		?>
        </tbody>
      </table>
      <table class="table table-dark justify-content-center">
        <thead>
        <tr>
          <th scope="col">Messages</th>
          <th scope="col">Posted Date&Time</th>
          <th scope="col">Client</th>
        </tr>
        </thead>
        <tbody>
		<?php
		echo $rows0;
		?>
        </tbody>
      </table>
      <form method="POST">
        <div class="form-group">
          <label for="id"></label>
          <input type="hidden" name="id" value="<?php echo $TicketId ?>"/>
          <label for="msg"></label>
          <textarea id="msg" name="msg" class="form-control" cols="20" placeholder="Type your message here"
                    rows="5"></textarea>
        </div>
        <div class="form-group">
          <buttton type="submit" class="form-control btn btn-primary submit px-3"
                   name="sendmsg" <?php if ($status == "Close"): ?> disabled <?php endif; ?>>Send
          </buttton>
        </div>
      </form>
    </div>
  </div>
</section>
<?php
require_once 'footer.php';
?>
