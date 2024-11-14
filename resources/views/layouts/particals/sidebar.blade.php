<nav class="side-nav navbar navbar-expand-lg fixed-top">
  <a class="navbar-brand" href="{{ route('dashboard') }}">LMSTORE</a>
  <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse"
  aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarCollapse">
  <ul class="navbar-nav mr-auto sidenav" id="navAccordion">
    <li class="nav-item">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <i class="fa fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('app.configuration') }}">
          <i class="fa fa-cogs"></i>
          <span>Configuration</span></a>
        </li>


        <li class="nav-item">
          <a class="nav-link nav-link-collapse" href="#" id="hasSubItems" data-toggle="collapse"
          data-target="#collapseSubItems2" aria-controls="collapseSubItems2" aria-expanded="false">
          <i class="fa fa-shopping-cart"></i>
          <span>Customize Theme</span>
        </a>
        <ul class="nav-second-level collapse" id="collapseSubItems2" data-parent="#navAccordion">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('general.setting') }}">
              <span class="nav-link-text">General Settings</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('home.banner') }}">
              <span class="nav-link-text">Home Page Banner</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('home.widget') }}">
              <span class="nav-link-text">Home Page Widget</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('app.preview') }}">
              <span class="nav-link-text">App Preview</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('lablemanagement') }}">
          <i class="fa fa-calendar"></i>
          <span>Lable Management</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('pricingplan') }}">
          <i class="fa fa-folder"></i>
          <span>Pricing Plan</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('notification') }}">
          <i class="fa fa-book"></i>
          <span>Notification</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link nav-link-collapse" href="#" id="hasSubItems" data-toggle="collapse"
        data-target="#collapseSubItems3" aria-controls="collapseSubItems3" aria-expanded="false">
        <i class="far fa-gem"></i>
        <span>Help</span>
      </a>
      <ul class="nav-second-level collapse" id="collapseSubItems3" data-parent="#navAccordion">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('faq') }}">
            <span class="nav-link-text">FAQ</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('documents') }}">
            <span class="nav-link-text">Documents</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="fa fa-tachometer-alt"></i>
        <span>Support</span></a>
      </li>
    </ul>
    <div class="form-inline ml-auto mt-2 mt-md-0">
        <p>{{ Auth::user()->name }}</p>
    </div>
  </div>
</nav>