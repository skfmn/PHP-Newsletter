<!-- Footer -->
  <footer id="footer">
    <div class="copyright">
      <a href="http://www.aspjunction.com">PHP Newsletter</a> Copyright &copy; 2003 - <?php echo date("Y") ?> <a href="http://www.phpjunction.com">PHP junction</a><br />An <a href="http://www.htmljunction.com">HTML Junction</a> Enterprise. All rights reserved.
    </div>
  </footer>

  <style>
    div.ui-menu-item-wrapper {color:#ff0000;}
    div.ui-helper-hidden-accessible:last-child {color:#ff0000;}
  </style>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="../assets/js/jquery.fancybox.js"></script>
  <script src="../assets/js/skel.min.js"></script>
  <script src="../assets/js/main.js"></script>
  <script src="../assets/js/javascript.js"></script>
  <script language="javascript" type="text/javascript">
    $(function () {
      var availableTags = [<?php 

        $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

      if (!$conn) {

        die("Connection failed: ".mysqli_connect_error());
      }

      $sql = "SELECT * FROM ".DBPREFIX."newsletter";

      $count = 0;
      $recordCount = 0;

      $result = $conn -> query($sql);
      if ($result -> num_rows > 0) {
        $recordCount = $result -> num_rows;
        while ($row = $result -> fetch_assoc()) {

          $newsTitle = $row["news_title"];

          $count = $count + 1;
          echo "'".$newsTitle."'";

          if ($count <> $recordCount) {

            echo ",";
          }
        }
      }
      mysqli_close($conn);
    ?>];
      $("#temptitle").autocomplete({
        source: availableTags
      });
    });

  </script>
</body>
</html>
