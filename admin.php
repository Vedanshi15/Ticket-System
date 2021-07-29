<?php
  session_start();
  $username = $_SESSION['username'];
  $rows = "";
  $ticketDoc = new DOMDocument();
  $ticketDoc->preserveWhiteSpace = false;
  $ticketDoc->formatOutput = true;
  $ticketDoc->load("xml/Ticket.xml");
  $userDoc = new DOMDocument();
  $userDoc->preserveWhiteSpace = false;
  $userDoc->formatOutput = true;
  $userDoc->load("xml/user.xml");
  $tickets = $ticketDoc->getElementsByTagName("ticket");
  foreach ($tickets as $ticket) { //iterate ticket
    $rows .= '<tr>';
    $tid = $ticket->getElementsByTagName("ticketid")->item(0)->nodeValue;
    $rows .= '<td>' . $tid . '</td>';
    $date = new DateTime($ticket->getElementsByTagName("dateissued")->item(0)->nodeValue);
    $rows .= '<td>' . $ticket->getElementsByTagName("category")->item(0)->nodeValue . '</td>';
    $rows .= '<td>' . date_format($date, 'Y-m-d,  H:i:s') . '</td>';
    $rows .= '<td>' . $ticket->getElementsByTagName("status")->item(0)->nodeValue . '</td>';
    $rows .= '<td>' . $ticket->getElementsByTagName("userid")->item(0)->nodeValue . '</td>';
    $rows .= '<td>
                <form action="ticket.php" method="POST">
                  <input type="hidden" name="id" value="' . $tid . '"/>
                  <input type="submit" name="ticket" value="View Ticket"/>
                </form>
              </td>';
    $rows .= '</tr>';
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
    <div class="table-responsive">
      <h3 class="mb-4 text-center">Tickets</h3>
      <table class="table table-dark justify-content-center">
        <thead>
        <tr>
          <th scope="col">Ticket ID</th>
          <th scope="col">Category</th>
          <th scope="col">Date Opened</th>
          <th scope="col">Status</th>
          <th scope="col">Client ID</th>
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
