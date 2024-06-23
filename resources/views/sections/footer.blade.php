@php
    // Retrieve the layout design group
    $layout_design = get_field('layout_design', 'option');

    // Access sub-fields within the group
    $footer_design = $layout_design['choose_footer_design'] ?? 'Footer-1';
    $dark_footer = $layout_design['dark_footer'] ?? false;

    $footer_class = 'footer-1';
    $footer_template = 'footer1';

    if ($footer_design == 'Footer-2') {
        $footer_class = 'footer-2';
        $footer_template = 'footer2';
    } elseif ($footer_design == 'Footer-3') {
        $footer_class = 'footer-3';
        $footer_template = 'footer3';
    }

    // Add dark class if dark_footer is checked
    if ($dark_footer) {
        $footer_class .= ' footer-black';
    } else {
        $footer_class .= ' footer-white';
    }
@endphp


@if(!is_page('checkout'))
<section class="newsletter bg-main bg-newsletter-image bg-right bg-no-repeat bg-cover hidden">
  <div class="container flex items-center content-center justify-center py-20">
    <div class="bg-black/10 px-4 md:px-14 py-10 rounded-lg flex flex-col items-center content-center justify-center max-w-[760px]">
      <h3 class="text-white text-22 md:text-26 text-center font-bold mb-4 hidden">АБОНИРАЙ СЕ</h3>
      <p class="text-white text-center hidden">Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.</p>
      <div class="newsletter-form--wrap">{!! get_field('klaviyo_element', 'options') !!}</div>
    </div>
  </div>
</section>

@php do_action('before_footer_content'); @endphp

<footer class="footer {{ $footer_class }}">
  @include('sections.footers.' . $footer_template)
</footer>
@endif
