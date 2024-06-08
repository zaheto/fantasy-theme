{{-- resources/views/woocommerce/content-quick-view-variable.blade.php --}}

@if($product)
    <div class="product" id="product-{{ $product->get_id() }}">
        <div class="images">
            {{-- {!! woocommerce_show_product_images() !!} --}}
        </div>
        <div class="summary entry-summary">
            <h1 class="product_title entry-title">{{ $product->get_name() }}</h1>
            {!! woocommerce_template_single_rating() !!}
            {!! woocommerce_template_single_price() !!}
            <div class="woocommerce-product-details__short-description">
                {{ $product->get_short_description() }}
            </div>
            {!! woocommerce_template_single_add_to_cart() !!}
            {!! woocommerce_template_single_meta() !!}
            {!! woocommerce_template_single_sharing() !!}
        </div>
    </div>
@else
    <p>{{ __('Product not found', 'fantasy') }}</p>
@endif
