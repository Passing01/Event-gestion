<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;

class ModeratorMarketplaceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $plan = strtolower($user->plan ?? 'free');

        if (!in_array($plan, ['premium', 'entreprise'])) {
            return view('moderator.marketplace.locked');
        }

        $events = $user->events()
            ->whereNotNull('closed_at')
            ->orderBy('date', 'desc')
            ->get();

        $totalSales = 0;
        $totalRevenue = 0;

        foreach ($events as $event) {
            if ($event->is_on_marketplace) {
                // Simulation déterministe basée sur l'ID de l'événement
                $salesCount = ($event->id * 4) % 12 + 3; // e.g. entre 3 et 14 ventes
                $price = $event->marketplace_price > 0 ? $event->marketplace_price : 14990;
                $event->simulated_sales = $salesCount;
                $event->simulated_price = $price;
                $event->simulated_revenue = $salesCount * $price;

                $totalSales += $salesCount;
                $totalRevenue += $event->simulated_revenue;
            } else {
                $event->simulated_sales = 0;
                $event->simulated_price = 0;
                $event->simulated_revenue = 0;
            }
        }

        // Calcul des commissions
        $commissionRate = $plan === 'premium' ? 0.25 : 0.50;
        $platformShare = $totalRevenue * $commissionRate;
        $moderatorShare = $totalRevenue * (1 - $commissionRate);

        return view('moderator.marketplace.index', compact(
            'events', 
            'totalSales', 
            'totalRevenue', 
            'platformShare', 
            'moderatorShare',
            'plan'
        ));
    }

    public function show($id)
    {
        $user = Auth::user();
        $plan = strtolower($user->plan ?? 'free');

        if (!in_array($plan, ['premium', 'entreprise'])) {
            return redirect()->route('dashboard.my-marketplace.index');
        }

        $event = $user->events()->whereNotNull('closed_at')->findOrFail($id);

        $price = $event->marketplace_price > 0 ? $event->marketplace_price : 14990;
        $salesCount = ($event->id * 4) % 12 + 3;
        $eventRevenue = $salesCount * $price;

        $commissionRate = $plan === 'premium' ? 0.25 : 0.50;
        $eventPlatformShare = $eventRevenue * $commissionRate;
        $eventModeratorShare = $eventRevenue * (1 - $commissionRate);

        // Liste de noms d'acheteurs déterministe (West-African)
        $buyerNames = [
            "Jean-Luc Kouadio", "Mariam Coulibaly", "Alassane Diallo", 
            "Emeka Nwosu", "Amina Bamba", "Koffi Mensah", 
            "Fatou Diop", "Youssouf Traoré", "Sarah Goldman",
            "Mamadou Touré", "Grace Osei", "Chinedu Okechuku",
            "Aïcha Sow", "David Ndlovu"
        ];

        $buyers = [];
        for ($i = 0; $i < $salesCount; $i++) {
            $buyerIndex = ($event->id * 7 + $i) % count($buyerNames);
            $buyerDate = $event->closed_at->addDays($i + 1)->addHours($i * 2);
            $buyers[] = [
                'name' => $buyerNames[$buyerIndex],
                'email' => strtolower(str_replace(' ', '.', $buyerNames[$buyerIndex])) . '@example.com',
                'amount' => $price,
                'date' => $buyerDate,
                'platform_share' => $price * $commissionRate,
                'moderator_share' => $price * (1 - $commissionRate),
            ];
        }

        // Tri chronologique inverse
        usort($buyers, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('moderator.marketplace.show', compact(
            'event',
            'buyers',
            'price',
            'salesCount',
            'eventRevenue',
            'eventPlatformShare',
            'eventModeratorShare',
            'plan'
        ));
    }
}
