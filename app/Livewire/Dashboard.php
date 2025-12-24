<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\InstallmentPayment;
use App\Models\Location;
use App\Models\Purchase;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // Counts
        $totalCustomers = Customer::count();
        $totalPurchases = Purchase::count();
        $totalLocations = Location::count();

        // Financials
        $totalSales = Purchase::sum('sales_price');
        $totalNet   = Purchase::sum('net_price');

        $profits = $totalSales - $totalNet;
        
        $totalDown  = Purchase::sum('down_price');
        $totalPaid  = InstallmentPayment::sum('amount');

        $totalDue = max(($totalNet - $totalDown) - $totalPaid, 0);
        $totalProfit = $totalSales - $totalNet;

        // Chart Data (Last 6 months)
        $chartLabels = [];
        $customerChartData = [];
        $purchaseChartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->format('M Y');

            $customerChartData[] = Customer::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();

            $purchaseChartData[] = Purchase::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        return view('livewire.dashboard', compact(
            'totalCustomers',
            'totalPurchases',
            'totalLocations',
            'totalSales',
            'totalPaid',
            'totalDue',
            'totalProfit',
            'chartLabels',
            'customerChartData',
            'purchaseChartData'
        ));
    }
}
