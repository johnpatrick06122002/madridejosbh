
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="../src/jquery.richtext.js"></script>
<script>
$(document).ready(function() {
$('.content').richText();
});
</script>


<!-- search ownner-->
<script>
  
  $('#search').keyup(function(){
    var search = $(this).val();
      $.ajax({
      url:"owners-list.php",
      method:"post",
      data:{query:search},
      success:function(data)
      {
        $('#ownerslist').html(data);
      }
    });

  });


</script>


</body>
</html>