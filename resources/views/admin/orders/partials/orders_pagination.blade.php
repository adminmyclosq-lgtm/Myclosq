@if($orders->hasPages())
    <div class="orders-pagination-wrapper">
        {{ $orders->links() }}
    </div>
@endif
