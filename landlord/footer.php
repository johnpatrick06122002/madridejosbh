


<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="../src/jquery.richtext.js"></script>
<script>
$(document).ready(function() {
$('.content').richText();
});
</script>



<!-- search all-->
<script>
  
  $('#search').keyup(function(){
    var search = $(this).val();
    var landlordid = '<?php echo $login_session; ?>';
      $.ajax({
      url:"bookers-list.php",
      method:"post",
      data:{query:search, landlordid:landlordid},
      success:function(data)
      {
        $('#bookerslist').html(data);
      }
    });

  });


</script>


<!-- graph -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script>

var xValues = <?php echo json_encode($dates) ?>;
var yValues = <?php echo json_encode($dailytotal) ?>;
var barColors = "#f4d547";

new Chart("myChartGraph", {
  type: "bar",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    legend: {display: false},
    title: {
      display: true,
      text: "Engagement of this Month"
    }
  }
});
</script>

<script>
    $(document).ready(function() {
        $("[data-fancybox]").fancybox({
            // Options here
            buttons: [
                "zoom",
                "close"
            ]
        });
    });
</script>
</body>
</html>