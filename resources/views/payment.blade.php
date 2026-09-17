@extends('layouts.app')
@section('content')
<div class="section max-w-4xl">
    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
        <div>
            <span class="badge">Secure checkout</span>
            <h1 class="serif mt-4 text-5xl">Complete your payment.</h1>
            <p class="mt-4 text-stone-600">Your order is created. Payment is confirmed only after server-side verification.</p>
            <div class="card mt-8">
                <div class="flex items-center justify-between"><div><div class="text-sm text-stone-500">Order</div><div class="font-bold">{{ $order->order_number }}</div></div><span id="status-pill" class="rounded-full bg-amber-50 px-3 py-1 text-sm text-amber-700">Payment pending</span></div>
                <button id="pay-button" class="btn-primary mt-8 w-full">Pay ₹{{ number_format($order->grand_total,2) }}</button>
                <p id="payment-status" class="mt-4 text-center text-sm text-stone-500"></p>
                <p class="mt-5 text-center text-xs text-stone-400">Payment is processed by Razorpay. {{ !empty($isMyClosq) ? 'My CLOSQ' : 'Gut Reset' }} does not store your card details.</p>
            </div>
        </div>
        <aside class="card h-fit">
            <h2 class="font-bold">Order summary</h2>
            <div class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span>₹{{ number_format($order->subtotal,2) }}</span></div>
                <div class="flex justify-between"><span>Discount</span><span>- ₹{{ number_format($order->discount_amount,2) }}</span></div>
                <div class="flex justify-between"><span>Shipping</span><span>₹{{ number_format($order->shipping_amount,2) }}</span></div>
                <div class="flex justify-between"><span>Tax</span><span>₹{{ number_format($order->tax_amount,2) }}</span></div>
                <div class="flex justify-between border-t pt-3 text-base font-bold"><span>Total</span><span>₹{{ number_format($order->grand_total,2) }}</span></div>
            </div>
        </aside>
    </div>
</div>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(async()=>{
 const button=document.getElementById('pay-button'), status=document.getElementById('payment-status'), pill=document.getElementById('status-pill');
 const csrf='{{ csrf_token() }}';
 const init=await fetch('/api/v1/orders/{{ $order->id }}/payment',{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}});
 const data=await init.json();
 if(!init.ok){button.disabled=true;status.textContent=data.message||'Payment initialization failed.';return;}
 button.onclick=()=>{
   button.disabled=true; status.textContent='Opening secure payment…';
   new Razorpay({
     key:data.key_id, amount:Math.round(data.amount*100), currency:data.currency,
     name:'{{ !empty($isMyClosq) ? "My CLOSQ" : "Gut Reset" }}', description:'{{ !empty($isMyClosq) ? "My CLOSQ" : "Gut Reset" }} order '+data.order_number, order_id:data.gateway_order_id,
     handler:async result=>{
       status.textContent='Verifying your payment…';
       const verify=await fetch('/api/v1/orders/{{ $order->id }}/payment/verify',{
         method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify(result)
       });
       if(verify.ok){pill.textContent='Paid';pill.className='rounded-full bg-green-50 px-3 py-1 text-sm text-green-700';window.location='{{ route('payment.success',$order) }}';}
       else {button.disabled=false;status.textContent='We could not verify the payment. Please try again or contact support.';}
     },
     modal:{ondismiss:()=>{button.disabled=false;status.textContent='Payment window closed. You can try again.';}}
   }).open();
 };
})();
</script>
@endsection
