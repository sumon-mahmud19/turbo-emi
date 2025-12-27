<div class="container mx-auto py-4 space-y-6">

    <a wire:navigate href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">হোম মেনু</a>

    {{-- Customer Header --}}
    <h2 class="text-xl md:text-2xl font-bold text-center md:text-left">
        Customer Name: <span class="text-primary">{{ $customer->customer_name }}</span>
    </h2>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success shadow-lg">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current  h-6 w-6"
                     fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Purchase & EMI Summary --}}
    <div class="card bg-base-100 shadow-md">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                    <tr class="bg-primary text-primary-content">
                        <th>তারিখ</th>
                        <th>পণ্য</th>
                        <th>মূল্য</th>
                        <th>জমা</th>
                        <th>বাকি</th>
                        <th>কিস্তি</th>
                        <th>অ্যাকশন</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $grandTotalPrice = 0;
                        $grandTotalPaid = 0;
                        $grandTotalDue = 0;
                        $grandTotalDown = 0;
                    @endphp
                    @foreach ($customer->purchases as $purchase)
                        @php
                            $product = $purchase->product;
                            $totalPrice = $purchase->net_price;
                            $down = $purchase->down_price;
                            $totalPaid = $purchase->installments->sum('paid_amount');
                            $totalDue = $purchase->installments->sum(fn($i) => $i->amount - $i->paid_amount);
                            $totalDeposit = $totalPaid + $down;

                            $grandTotalPrice += $totalPrice;
                            $grandTotalPaid += $totalPaid;
                            $grandTotalDown += $down;
                            $grandTotalDue += $totalDue;
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d-m-Y') }}</td>
                            <td>{{ $product?->product_name ?? 'N/A' }}</td>
                            <td>{{ number_format($totalPrice, 2) }} ৳</td>
                            <td>{{ number_format($totalDeposit, 2) }} ৳</td>
                            <td>
                                <span class="{{ $totalDue > 0 ? 'text-error font-bold' : 'text-success font-bold' }}">
                                    {{ number_format($totalDue, 2) }} ৳
                                </span>
                            </td>
                            <form action="{{ route('installments.pay-multiple') }}" method="POST">
                                @csrf
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <td>
                                    <input type="number" name="payments[{{ $purchase->id }}]"
                                           class="input input-sm input-bordered w-full"
                                           value="0" min="0" max="{{ $totalDue }}" step="0.01"
                                        {{ $totalDue <= 0 ? 'disabled' : '' }}>
                                </td>
                                <td class="flex flex-col gap-2">
                                    @if (auth()->user()->hasRole('admin'))
                                        <button type="submit"
                                                class="btn btn-success btn-sm w-full"
                                            {{ $totalDue <= 0 ? 'disabled' : '' }}>
                                            Pay
                                        </button>
                                    </form>

                                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST"
                                          onsubmit="return confirm('আপনি কি নিশ্চিতভাবে মুছতে চান?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-error btn-sm w-full">
                                            ডিলিট
                                        </button>
                                    </form>
                                    @endif
                                </td>
                        </tr>
                    @endforeach

                    {{-- Totals Row --}}
                    <tr>
                        <td colspan="7">
                            <div class="bg-base-200 rounded-lg p-4 flex flex-col md:flex-row justify-around items-center gap-3 font-semibold">
                                <div>মোট মূল্য: <span class="font-bold">{{ number_format($grandTotalPrice, 2) }} ৳</span></div>
                                <div>মোট জমা: <span class="font-bold">{{ number_format($grandTotalPaid + $grandTotalDown, 2) }} ৳</span></div>
                                <div class="{{ $grandTotalDue > 0 ? 'text-error font-bold' : 'text-success font-bold' }}">
                                    মোট বাকি: {{ number_format($grandTotalDue, 2) }} ৳
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="card bg-base-100 shadow-md">
        <div class="card-body p-0">
            <div class="px-4 py-2 bg-primary text-primary-content font-bold text-lg">Payment History</div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                    <tr class="bg-base-200">
                        <th>তারিখ</th>
                        <th>পণ্য</th>
                        <th>জমা (৳)</th>
                        @role('admin')
                        <th>অ্যাকশন</th>
                        @endrole
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($paymentHistory as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') }}</td>
                            <td>{{ $payment->installment->purchase->product->product_name ?? 'N/A' }}</td>
                            <td class="text-success font-semibold">{{ number_format($payment->amount, 2) }}</td>
                            @role('admin')
                            <td class="flex flex-col gap-2 justify-center">
                                <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning btn-sm w-full">এডিট</a>
                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST"
                                      onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই পেমেন্ট মুছতে চান?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error btn-sm w-full">ডিলিট</button>
                                </form>
                            </td>
                            @endrole
                        </tr>
                    @empty
                        <tr>
                            <td colspan="@role('admin')4 @else 3 @endrole" class="text-center text-gray-500 italic py-4">
                                কোন পেমেন্ট পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

 <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>