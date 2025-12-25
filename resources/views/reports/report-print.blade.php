<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>{{ $customer->customer_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        th,
        td {
            font-size: 12.5px;
            border: 1px solid #ccc;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            line-height: 9px;
            height: 10px;
        }

        thead {
            background-color: #f8f9fa;
        }

        tbody tr:nth-child(even) {
            background-color: #fff;
        }
    </style>
</head>

<body>

    @if ($customer->purchases->count() <= 0)
        <table>
            <tr>
                <td
                    style="display:flex; align-items:center; justify-content:center; border:none; padding-top:5rem; font-size:20px;">
                    Not Found Report!
                </td>
            </tr>
        </table>
    @else
        @php
            $sum = $customer->purchases->sum(fn($p) => $p->net_price);
            $emiMonths = $customer->purchases->first()?->emi_plan ?? 1;
            $monthlyInstallment = round($sum / $emiMonths);
            $minRows = 14;
        @endphp

        {{-- ================= Table 1 ================= --}}
        @php
            $count = 1;
            $totalSaving = 0;
            $totalEmiPrice = 0;
            $color = '#ddd';
        @endphp

        <table>
            <thead>
                <tr>
                    <th colspan="2">পৃ:নং: {{ $customer->customer_id }}</th>
                    <th colspan="4">ফোন নং: {{ $customer->customer_phone }}</th>
                    <th colspan="4">কিস্তির পরিমান: {{ $monthlyInstallment }} টাকা</th>
                    <th rowspan="2">
                        <img src="{{ $customer->customer_image }}"
                            style="border-radius:50%; width:80px; height:80px; object-fit:cover;">
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <th colspan="6">নাম: {{ $customer->customer_name }}</th>
                    <th colspan="4">কিস্তির মেয়াদ:
                        {{ $customer->purchases->first()?->emi_plan ?? 'N/A' }} মাস
                    </th>
                </tr>

                <tr>
                    <th colspan="5">
                        ঠিকানা: {{ $customer->location->name ?? 'N/A' }}
                        ({{ $customer->landlord_name }})
                    </th>
                    <th colspan="6">
                        কিস্তির তারিখ:
                        {{ $customer->purchases->first()?->created_at->addMonth()->format('F Y') ?? 'N/A' }}
                    </th>
                </tr>

                {{-- Header Row (Same as Table 1) --}}
                <tr>
                    <td style="border:2px solid {{ $color }}"><b>ক্রমিক নং</b></td>
                    <td colspan="2" style="border:2px solid {{ $color }}"><b>পণ্যের বিবরণ</b></td>
                    <td style="border:2px solid {{ $color }}"><b>কিস্তি মূল্য</b></td>
                    <td style="border:2px solid {{ $color }}"><b>মোট মূল্য</b></td>
                    <td style="border:2px solid {{ $color }}"><b>তারিখ</b></td>
                    <td style="border:2px solid {{ $color }}"><b>জমা</b></td>
                    <td style="border:2px solid {{ $color }}"><b>মোট জমা</b></td>
                    <td style="border:2px solid {{ $color }}"><b>বাকি</b></td>
                    <td style="border:2px solid {{ $color }}"><b>আউটর স্বাক্ষর</b></td>
                    <td style="border:2px solid {{ $color }}"><b>মন্তব্য</b></td>
                </tr>

                @foreach ($customer->purchases as $purchase)
                    @php
                        $totalPrice = (float) $purchase->net_price;
                        $totalEmiPrice += $totalPrice;

                        $down = (float) $purchase->down_price;
                        $paid = (float) $purchase->installments->sum('paid_amount');
                        $totalDeposit = $down + $paid;
                        $totalSaving += $totalDeposit;

                        $totalDue = (float) $purchase->installments->sum(fn($i) => $i->amount - $i->paid_amount);
                    @endphp

                    <tr>
                        <td>{{ $count++ }}</td>
                        <td colspan="2">{{ $purchase->product->product_name ? 'N/A' }}</td>
                        <td>{{ number_format($totalPrice, 2) }} ৳</td>
                        <td>{{ number_format($totalEmiPrice, 2) }} ৳</td>
                        <td>{{ $purchase->created_at->format('d-m-Y') }}</td>
                        <td>{{ number_format($totalDeposit, 2) }} ৳</td>
                        <td>{{ number_format($totalSaving, 2) }} ৳</td>
                        <td>{{ number_format($totalDue, 2) }} ৳</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach

                {{-- Empty rows (same as Table 1) --}}
                @for ($i = count($customer->purchases); $i < $minRows; $i++)
                    <tr>
                        <td style="height:15px">{{ $count++ }}</td>
                        <td colspan="2"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor

                {{-- Signature --}}
                <tr>
                    <td colspan="4" style="border:none; padding-top:1rem;">ক্রেতার স্বাক্ষর..........</td>
                    <td colspan="6" style="text-align:right; border:none; padding-top:1rem;">
                        বিক্রেতার স্বাক্ষর...........
                    </td>
                </tr>
            </tbody>
        </table>
        {{-- ================= Table 2 ================= --}}
        @php
            $count = 1;
            $totalSaving = 0;
            $totalEmiPrice = 0;
            $color = '#ddd';
        @endphp

        <table>
            <thead>
                <tr>
                    <th colspan="2">পৃ:নং: {{ $customer->customer_id }}</th>
                    <th colspan="4">ফোন নং: {{ $customer->customer_phone }}</th>
                    <th colspan="4">কিস্তির পরিমান: {{ $monthlyInstallment }} টাকা</th>
                    <th rowspan="2">
                        <img src="{{ $customer->customer_image }}"
                            style="border-radius:50%; width:80px; height:80px; object-fit:cover;">
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <th colspan="6">নাম: {{ $customer->customer_name }}</th>
                    <th colspan="4">কিস্তির মেয়াদ:
                        {{ $customer->purchases->first()?->emi_plan ?? 'N/A' }} মাস
                    </th>
                </tr>

                <tr>
                    <th colspan="5">
                        ঠিকানা: {{ $customer->location->name ?? 'N/A' }}
                        ({{ $customer->landlord_name }})
                    </th>
                    <th colspan="6">
                        কিস্তির তারিখ:
                        {{ $customer->purchases->first()?->created_at->addMonth()->format('F Y') ?? 'N/A' }}
                    </th>
                </tr>

                {{-- Header Row (Same as Table 1) --}}
                <tr>
                    <td style="border:2px solid {{ $color }}"><b>ক্রমিক নং</b></td>
                    <td colspan="2" style="border:2px solid {{ $color }}"><b>পণ্যের বিবরণ</b></td>
                    <td style="border:2px solid {{ $color }}"><b>কিস্তি মূল্য</b></td>
                    <td style="border:2px solid {{ $color }}"><b>মোট মূল্য</b></td>
                    <td style="border:2px solid {{ $color }}"><b>তারিখ</b></td>
                    <td style="border:2px solid {{ $color }}"><b>জমা</b></td>
                    <td style="border:2px solid {{ $color }}"><b>মোট জমা</b></td>
                    <td style="border:2px solid {{ $color }}"><b>বাকি</b></td>
                    <td style="border:2px solid {{ $color }}"><b>আউটর স্বাক্ষর</b></td>
                    <td style="border:2px solid {{ $color }}"><b>মন্তব্য</b></td>
                </tr>

                @foreach ($customer->purchases as $purchase)
                    @php
                        $totalPrice = (float) $purchase->net_price;
                        $totalEmiPrice += $totalPrice;

                        $down = (float) $purchase->down_price;
                        $paid = (float) $purchase->installments->sum('paid_amount');
                        $totalDeposit = $down + $paid;
                        $totalSaving += $totalDeposit;

                        // $totalDue = (float) $purchase->installments->sum(fn($i) => $i->amount - $i->paid_amount);

                         $totalDue = max($totalPrice - $totalDeposit, 0);
                    @endphp

                    <tr>
                        <td>{{ $count++ }}</td>
                        <td colspan="2">{{ $purchase->product->product_name }}</td>
                        <td>{{ number_format($totalPrice, 2) }} ৳</td>
                        <td>{{ number_format($totalEmiPrice, 2) }} ৳</td>
                        <td>{{ $purchase->created_at->format('d-m-Y') }}</td>
                        <td>{{ number_format($totalDeposit, 2) }} ৳</td>
                        <td>{{ number_format($totalSaving, 2) }} ৳</td>
                        <td>{{ number_format($totalDue, 2) }} ৳</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach

                {{-- Empty rows (same as Table 1) --}}
                @for ($i = count($customer->purchases); $i < $minRows; $i++)
                    <tr>
                        <td style="height:15px">{{ $count++ }}</td>
                        <td colspan="2"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor

                {{-- Signature --}}
                <tr>
                    <td colspan="4" style="border:none; padding-top:1rem;">ক্রেতার স্বাক্ষর..........</td>
                    <td colspan="6" style="text-align:right; border:none; padding-top:1rem;">
                        বিক্রেতার স্বাক্ষর...........
                    </td>
                </tr>
            </tbody>
        </table>

       
      

    @endif

</body>

</html>
