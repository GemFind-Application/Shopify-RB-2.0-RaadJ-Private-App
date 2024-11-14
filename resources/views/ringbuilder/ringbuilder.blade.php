<!doctype html>

<html lang="en">

   <head>

      <meta charset="utf-8"/>

      <link rel="icon" href="{{ asset('favicon.jpg') }}"/>

      <meta name="viewport" content="width=device-width,initial-scale=1"/>

      <title>RingBuilder Advance</title>

      <script type="text/javascript">

          var input = document.createElement('input');

          input.setAttribute('type', 'hidden');

          input.setAttribute('id', 'shop_domain');

          input.setAttribute('value', Shopify.shop);

          document.body.appendChild(input);

      </script>

      <script defer="defer" src="{{ asset('/static/js/main.js') }}"></script>

      <link href="{{ asset('/static/css/main.css') }}" rel="stylesheet">

   </head>

   <body>

     <!--  <div id="fb-root" class="fb_reset">

         <div style="position:absolute;top:-10000px;width:0;height:0">

            <div></div>

         </div>

      </div>

      <script async="" defer="" crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v9.0&appId=2069310279975989&autoLogAppEvents=1" nonce=""></script> -->

      <noscript>You need to enable JavaScript to run this app.</noscript>

      <div id="root"></div>

      <div class="gf-tool-container">
       <div class="tool-container" >
          <span
            class="gf-powered_by"
            id="gemfind_diamondtool_powered_by"
            style="
              text-align: right;
              display: none;      
              margin-bottom: 15px;
              color: #000;
            "
          >
            <a
              href="https://gemfind.com/"
              target="_blank"
              rel="nofollow"
              style="
                color: inherit;
                text-decoration: none;
                font-family: 'Lato', sans-serif;
              "
            >
              Powered By GemFind
            </a>
          </span>
          </div>
          </div>

   </body>

</html>