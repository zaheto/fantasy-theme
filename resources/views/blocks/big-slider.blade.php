@if($big_slider)

  @if($boxed_slider)  <div class="container"> @endif

  <section class="swiper slider-home">

    <div class="swiper-wrapper">
    @foreach($big_slider as $slide)
      <div class="swiper-slide">
          <a href="{{ $slide['slider_link_big_slider']['url'] }}" title="{{ $slide['slider_link_big_slider']['title'] }}">
              <img src="{{ $slide['desktop_image_big_slider']['url'] }}" alt="{{ $slide['desktop_image_big_slider']['alt'] }}" class="desktop">
              <img src="{{ $slide['mobile_image_big_slider']['url'] }}" alt="{{ $slide['mobile_image_big_slider']['alt'] }}" class="mobile">
          </a>
      </div>
    @endforeach
    </div>

    {{-- <div class="swiper-pagination"></div> --}}

    <div class="swiper-button-prev slider-home-prev"></div>
    <div class="swiper-button-next slider-home-next"></div>

  </section>
  @if($boxed_slider) </div> @endif
@endif
