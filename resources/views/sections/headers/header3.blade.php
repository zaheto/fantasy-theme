@php
    $layout_design = get_field('layout_design', 'option');
    $logo_header = $layout_design['logo_header'] ?? '';

    // Access the logo size fields within the group
    $logo_size = $layout_design['logo_size'] ?? [];
    $desktop_logo_width = $logo_size['desktop_logo_width'] ?? 138; // Default value if not set
    $mobile_logo_width = $logo_size['mobile_logo_width'] ?? 102;  // Default value if not set

    // Add units to the width values
    $desktop_logo_width .= 'px';
    $mobile_logo_width .= 'px';
@endphp

<div class="inner-header ">

        <div class="header-left">

         <ul class="flex lg:hidden items-center gap-2 ">
          <li class="header-mobile-menu"><a href="javascript:;" id="mobile-menu" class="icon-wrap "><x-iconsax-lin-menu class="w-[28px] h-[28px] " /></a> </li>
          <li class="header-search"><a href="javascript:;" class="icon-wrap search-menu"><x-iconsax-lin-search-normal-1 class="w-[28px] h-[28px]" /></a></li>
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
          <a class="logo" href="{{ get_site_url('/') }}" style="min-width: {{ $mobile_logo_width }}; max-width: {{ $mobile_logo_width }};">
          @if($logo_header)
              <img src="{{ $logo_header }}" alt="{{ get_bloginfo('name', 'display') }}" style="width: 100%; max-width: {{ $desktop_logo_width }};" class="lg:max-w-{{ $desktop_logo_width }}">
          @endif
          </a>
        </div>

        <div class="header-right">
          <ul class="flex items-center gap-2">
            <li class="header-search"><a href="javascript:;" class="icon-wrap search-menu"><x-iconsax-lin-search-normal-1 class="w-[28px] h-[28px]" /></a></li>
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
        <div class="search-box">
          <?php aws_get_search_form( true ); ?>
        </div><!-- search-box -->
    </div><!-- container -->






