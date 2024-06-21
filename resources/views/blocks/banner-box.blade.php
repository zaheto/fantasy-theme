@if($add_banner)
<div class="container">
  <section class="flex flex-col md:flex-row gap-6">
    @foreach($add_banner as $slide)
      <a href="{{ $slide['banner_link']['url'] }}" title="{{ $slide['banner_link']['title'] }}" class="flex-1 block">
          <img src="{{ $slide['banner_image']['url'] }}" alt="{{ $slide['banner_image']['alt'] }}" class="w-full max-w-full block">
      </a>
    @endforeach
  </section>
</div>
@endif
