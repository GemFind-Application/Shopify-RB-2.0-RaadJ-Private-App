  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-1.12.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <script src="https://malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.js"></script>
  <script src="{!! asset('js/select2.min.js') !!}"></script>
  <script src="{!! asset('js/main.js') !!}"></script> 
  <script src="{!! asset('js/custom.js') !!}"></script>
  <script src="{!! asset('js/validate.js') !!}"></script>
  <script>
    $(document).ready(function () {
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
  <script>
        /*$(function(){
          // this will get the full URL at the address bar
          var url = window.location.href; 
          // passes on every "a" tag 
          $(".navbar-collapse a").each(function() {
              // checks if its the same on the address bar
              if(url == (this.href)) { 
                  $(this).closest("li").addClass("active");
              }
          });
      })*/

$(document).ready(function() {
      /** add active class and stay opened when selected */
      var url = window.location;

      // for sidebar menu entirely but not cover treeview
      $('ul.navbar-nav a').filter(function() {
         return this.href == url;
      }).parent().addClass('active');
});
      /*// for treeview
      $('ul.nav-second-level a').filter(function() {
         return this.href == url;
      }).parentsUntil(".navbar-nav > .treeview-menu").addClass('active');
      });*/
  </script>

</body>

</html>