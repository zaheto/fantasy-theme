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
  <div class="search-box">
    <?php aws_get_search_form( true ); ?>
</div><!-- search-box -->
        <div class="header-left">
          <a href="javascript:;" id="desktop-menu" class="icon-wrap hidden lg:flex"><x-iconsax-lin-menu class="icon"/></a>
          <a href="javascript:;" id="mobile-menu" class="icon-wrap flex items-center content-center lg:hidden absolute top-1/2 -translate-y-1/2 left-1 z-50"><x-iconsax-lin-menu class="icon "/></a>
          <a href="javascript:;" class="icon-wrap search-menu"><x-iconsax-lin-search-normal-1 class=" w-[28px] h-[28px]"/></a>
        </div>
        <!-- END OF HEADER LEFT -->

        <div class="header-center">
          <a class="logo" href="{{ get_site_url('/') }}" style="min-width: {{ $mobile_logo_width }}; max-width: {{ $mobile_logo_width }};">
            @if($logo_header)
                <img src="{{ $logo_header }}" alt="{{ get_bloginfo('name', 'display') }}" style="width: 100%; max-width: {{ $desktop_logo_width }};" class="lg:max-w-{{ $desktop_logo_width }}">
            @endif
          </a>
        </div>
        <!-- END OF HEADER center -->

        <div class="header-right">
              <ul class="header--my-account">
                @if(is_user_logged_in())
                <li>
                  <a href="{{ get_permalink( wc_get_page_id( 'myaccount' ) ) }}" class="icon-wrap">
                    <x-iconsax-lin-user-octagon class="icon" />
                  </a>
                </li>
                @else
                <li>
                  <a href="{{ home_url('/login') }}"  class="icon-wrap">
                    <x-iconsax-lin-user-octagon class="icon" />
                  </a>
                </li>
                @endif

              </ul><!-- account -->

              @if (class_exists('WPCleverWoosw'))
              <ul class="header--wishlist">
                <li>
                    <a href="{{ esc_url(WPCleverWoosw::get_url()) }}">
                        <x-iconsax-lin-heart class="icon" />
                        {{-- <span class="count" data-count="{{ esc_attr(WPCleverWoosw::get_count()) }}"></span> --}}
                    </a>
                </li>
              </ul>
              @endif


              @php global $woocommerce; @endphp


              @php do_action( 'fantasy_minicart_header' ); @endphp



        </div>
        <!-- END OF HEADER RIGHT -->
    </div><!-- container -->

    <nav class="main-nav">
      <div class="container ">
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
    </nav>




