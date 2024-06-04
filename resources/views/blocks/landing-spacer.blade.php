@php
    $spacerDesktop = $block_data['landing_spacer_desktop'] ?? 0;
    $spacerTablet = $block_data['landing_spacer_tablet'] ?? 0;
    $spacerMobile = $block_data['landing_spacer_mobile'] ?? 0;
@endphp

<style>
    .spacer-section {
        height: var(--spacer-mobile); /* Default to mobile */
    }

    @media (min-width: 768px) {
        .spacer-section {
            height: var(--spacer-tablet);
        }
    }

    @media (min-width: 1024px) {
        .spacer-section {
            height: var(--spacer-desktop);
        }
    }
</style>

<div class="spacer-section"
    style="
        --spacer-desktop: {{ $spacerDesktop }}px;
        --spacer-tablet: {{ $spacerTablet }}px;
        --spacer-mobile: {{ $spacerMobile }}px;
    ">
</div>
