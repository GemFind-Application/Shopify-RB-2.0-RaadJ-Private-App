<div class="modal fade" id="welcome-popup" tabindex="-1" role="dialog" aria-labelledby="welcome-popupLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <div class="welcome-img" style="background-image: url('{{ asset('images/imgpsh_fullsize.jpg')}}');"></div>
      <h5 class="modal-title" id="welcome-popupLabel">Welcome to LM STORE</h5>
 <!--      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button> -->
    </div>
    <div class="modal-body mCustomScrollbar" data-mcs-theme="dark">
      <p>LUCKI MEDIA is an internationally distributed digital marketing company based in Fayetteville, NC. With
        offices also in University Place, Washington, and now Gujarat, INDIA. We are rapidly experiencing growth and
        expanding our resources and services. Comprised of a small and select group of creative web development
      experts, we boast over 10 years of combined experience.</p>
      <p>We offers comprehensive marketing services including logo design, business cards, promotional materials,
        E-commerce, software systems, payment gateways, hosting, advertising, mobile app development, and more.
      Consider us your one stop shop for finding the best marketing solutions available.</p>
    </div>
    <div class="modal-footer">
      <form id="terms" class="terms-form" method="POST">
        <div class="terms-block">
          @csrf
          <label class="container">
            <input type="checkbox" id="select">
            <input type="hidden" name="hidden_id" value="{{ Auth::user()->id }}">
            <span class="checkmark"></span>
            I agreed to Terms and conditions
          </label>
          <div id="errmsg" class="error-msg" style="color: red;"></div>
        </div>
        <div class="terms-btn">
          <button type="submit" class="btn" class="close accept-terms" aria-label="Close" name="submit">Accept</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>