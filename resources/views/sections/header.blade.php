@if(get_field('footer_phone', 'options'))
    <a href="tel:{{ get_field('footer_phone', 'options') }}" class="scroll-phone-number">
        <x-iconsax-lin-call-calling class="icon" />
    </a>
@endif

@php
    do_action('fantasy_before_site');
    do_action('fantasy_before_header');

    // Retrieve the layout design group
    $layout_design = get_field('layout_design', 'option');

    // Access sub-fields within the group
    $announce_bar_header_color = $layout_design['announce_bar_header_color'] ?? '#f2f2f2';
    $announce_bar_header_text_color = $layout_design['announce_bar_header_text_color'] ?? '#ffffff';
    $announce_bar_header = $layout_design['announce_bar_header'] ?? [];

    $header_design = $layout_design['choose_header_design'] ?? 'Header-1';
    $dark_header = $layout_design['dark_header'] ?? false;

    $header_class = 'header-1';
    $header_template = 'header1';

    if ($header_design == 'Header-2') {
        $header_class = 'header-2';
        $header_template = 'header2';
    } elseif ($header_design == 'Header-3') {
        $header_class = 'header-3';
        $header_template = 'header3';
    }

    // Add dark class if dark_header is checked
    if ($dark_header) {
        $header_class .= ' header-black';
    } else {
        $header_class .= ' header-white';
    }

@endphp

<header id="header" class="header {{ $header_class }}">
    @if(!is_page('checkout'))
        @php do_action('fantasy_announce_bar'); @endphp

        @if($announce_bar_header)
            <section class="announce-bar" style="background-color: {{ $announce_bar_header_color }};">
                <div class="container">
                    <ul>
                        @foreach($announce_bar_header as $item)
                            <li style="color: {{ $announce_bar_header_text_color }};">{!! $item['annonce_text'] !!}</li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        @include('sections.headers.' . $header_template)

    @endif
</header>

<div class="mobile-nav">
    <div class="icon-wrap">
        <a id="close-cart-popup"><x-iconsax-lin-add class="icon" /></a>
    </div>
    <div class="mobile-nav--inner">
        @if (has_nav_menu('main_menu'))
            <nav class="navbar">
                {!! wp_nav_menu([
                    'theme_location' => 'main_menu',
                    'container' => 'div',
                    'depth' => "3",
                    'menu_class' => 'nav flex-col flex header-main-nav',
                ]) !!}
            </nav>
        @endif

        @if (has_nav_menu('second_menu_right'))
            <nav class="navbar">
                {!! wp_nav_menu([
                    'theme_location' => 'second_menu_right',
                    'container' => 'div',
                    'depth' => "3",
                    'menu_class' => 'nav flex flex-col header-right-nav',
                ]) !!}
            </nav>
        @endif
    </div>
    <ul class="header--my-account">
        @if(is_user_logged_in())
            <li>
                <a href="{{ get_permalink(wc_get_page_id('myaccount')) }}">
                    <x-iconsax-lin-user-octagon class="icon" />
                    {{ __('Your account', 'fantasy') }}
                </a>
            </li>
        @else
            <li>
                <a href="{{ home_url('/login') }}">
                    <x-iconsax-lin-user-octagon class="icon" />
                    {{ __('Your account', 'fantasy') }}
                </a>
            </li>
        @endif
    </ul>
</div>

@if(!is_page('cart'))

@endif
