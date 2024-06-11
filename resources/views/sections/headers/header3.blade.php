<div class="inner-header ">
        <div class="search-box">
          <?php aws_get_search_form( true ); ?>
      </div><!-- search-box -->
        <div class="header-left">

         <ul class="flex lg:hidden items-center gap-2 ">
          <li  class="header-mobile-menu"><a href="javascript:;" id="mobile-menu" class="icon-wrap "><x-iconsax-lin-menu class="w-[28px] h-[28px] " /></a> </li>
          <li  class="header-search"><a href="javascript:;" id="search-menu" class="icon-wrap "><x-iconsax-lin-search-normal-1 class="w-[28px] h-[28px]" /></a></li>
         </ul>

          <div class="main-nav">

            @if (has_nav_menu('main_menu'))

            <nav class="navbar">
              {!!
                wp_nav_menu(array(
                    'theme_location'    => 'main_menu',
                    'container'         => 'div',
                    'depth'				      => "3",
                    'menu_class'        => 'nav flex -ml-4 header-main-nav',
                ));
              !!}
            </nav>

            @endif

            @if (has_nav_menu('second_menu_right'))

            <nav class="navbar">
              {!!
                wp_nav_menu(array(
                    'theme_location'    => 'second_menu_right',
                    'container'         => 'div',
                    'depth'				      => "3",
                    'menu_class'        => 'nav flex -mr-2 header-right-nav',
                ));
              !!}
            </nav>

            @endif


        </div>
        </div>
        <!-- END OF HEADER LEFT -->




       <div class="logo-wrap">
        <a class="logo " href="{{ get_site_url('/') }}">
          @if(get_field('logo_header', 'options'))
          <img src="{{ get_field('logo_header', 'options') }}" alt="{{ get_bloginfo('name', 'display') }}">
          @endif
        </a>
       </div>


        <div class="header-right">
          <ul class="flex items-center gap-2">
            <li class="header-search"><a href="javascript:;" id="search-menu" class="icon-wrap "><x-iconsax-lin-search-normal-1 class="w-[28px] h-[28px]" /></a></li>
            @if(is_user_logged_in())
            <li  class="header-account logged">
              <a href="{{ get_permalink( wc_get_page_id( 'myaccount' ) ) }}" class="icon-wrap">
                <x-iconsax-lin-user-octagon class="w-[28px] h-[28px]" />
              </a>
            </li>
            @else
            <li class="header-account">
              <a href="{{ home_url('/login') }}"  class="icon-wrap">
                <x-iconsax-lin-user-octagon class="w-[28px] h-[28px]" />
              </a>
            </li>
            @endif
            @if (class_exists('WPCleverWoosw'))
            <li  class="header-wishlist">
              <a href="{{ esc_url(WPCleverWoosw::get_url()) }}">
                  <x-iconsax-lin-heart class="w-[28px] h-[28px]" />
              </a>
            </li>
          @endif
          </ul>
          @php global $woocommerce; @endphp
          @php do_action( 'fantasy_minicart_header' ); @endphp

        </div>
        <!-- END OF HEADER RIGHT -->
    </div><!-- container -->






