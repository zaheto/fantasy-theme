@if(get_field('footer_phone', 'options'))
    <a href="tel:{{ get_field('footer_phone', 'options') }}" class="scroll-phone-number">
        <x-iconsax-lin-call-calling class="icon" />
    </a>
@endif

@php
    do_action('fantasy_before_site');
    do_action('fantasy_before_header');

    // Directly retrieve the color value for the announce bar
    $announce_bar_header_color = get_field('announce_bar_header_color', 'option') ?? '#f2f2f2'; // Default to a light grey if not set

    $header_design = get_field('choose_header_design', 'option');
    $header_class = 'header-1';
    $header_template = 'header1';

    if ($header_design == 'Header-2') {
        $header_class = 'header-2';
        $header_template = 'header2';
    } elseif ($header_design == 'Header-3') {
        $header_class = 'header-3';
        $header_template = 'header3';
    }

@endphp

<header id="header" class="header header-white {{ $header_class }}">
    @if(!is_page('checkout'))
        @php do_action('fantasy_announce_bar'); @endphp

        @if(get_field('announce_bar_header', 'option'))
            <section class="announce-bar" style="background-color: {{ $announce_bar_header_color }};">
                <div class="container">
                    <ul>
                        @foreach(get_field('announce_bar_header', 'option') as $item)
                            <li>{!! $item['annonce_text'] !!}</li>
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
    {{-- <section class="cart-popup">
        <div class="cart-popup--inner">
            <div class="cart-popup-info">
                @include('woocommerce.cart.cart-custom')
            </div>
        </div>
    </section>

    <section id="quick-view-modal" class="quick-view-modal">
        @include('woocommerce.cart.quick-view-modal')
    </section> --}}
@endif
