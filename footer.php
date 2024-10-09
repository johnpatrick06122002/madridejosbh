<br />
<br />
<br />

<footer class="page-footer font-small blue bg-secondary text-white">

  <!-- Copyright -->
  <div class="footer-copyright text-center py-3">© 2022 Copyright: MADRIE-BH
  </div>
  <!-- Copyright -->

</footer>



<script src="jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="popper.min.js"></script>
<script src="bootstrap.bundle.min.js"></script>
<script src="src/jquery.barrating.min.js"></script>


<script type="text/javascript">
  $(function() {
  $('.prev').click(function(event) {
    event.preventDefault();
    $('.wrap').animate({
      scrollLeft: "-=575px"
    }, "slow");
  });

   $('.next').click(function(event) {
    event.preventDefault();
    $('.wrap').animate({
     scrollLeft: "+=575px"
    }, "slow");
  });
});
</script>



<!-- star rating from index -->
<script type="text/javascript">
  $('.ratingshome').each(function(){
    var rating = $(this).attr('data-rating');

$(function() {
    $("[data-rating="+rating+"]").each(function() {
      $(this).barrating({
        theme: 'fontawesome-stars',
        initialRating: rating,
        readonly: 'true'
      });
    });


  });
  });
</script>


<!-- star rating from view.php -->
<script type="text/javascript">
  $('.ratings').each(function(){
    var rating = $(this).attr('data-fratings');

$(function() {
    $("[data-fratings="+rating+"]").each(function() {
      $(this).barrating({
        theme: 'fontawesome-stars',
        initialRating: rating,
        readonly: 'true'
      });
    });


  });
  });
</script>


<!-- to rate fedback -->
<script type="text/javascript">
$('.torate').barrating({
    theme: 'fontawesome-stars',
    initialRating: '0'
        });
</script>


</body>
</html>