
<header class="header">
    <div class="container-fluid">
      <div class="section-header text-left">
        <div class="section-header__heading">
          <h2 class="section-header__title">@yield('title')</h2>
          <P>@yield('subtitle')</P>
        </div>
        <div class="section-header__button">
          <a href="{{ route('admin.logout') }}">
            <button type="submit" value="Submit" class="btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
          </a>
        </div>
      </div>
    </div>
  </header>

  <div class="overlay" style="display: none">
    <div class="overlay__inner">
        <div class="overlay__content"><span class="spinner"></span></div>
    </div>
  </div>