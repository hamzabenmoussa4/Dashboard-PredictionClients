<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // On désactive les checks des clés étrangères (utile pour truncate en MySQL)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // On vide les tables dans le bon ordre
        OrderItem::truncate();
        Order::truncate();
        Customer::truncate();

        // On réactive les checks des clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // On utilise Faker pour générer des données réalistes
        $faker = Faker::create('fr_FR');

        // Nombre de clients et de commandes
        $customersCount = 30;
        $ordersCount = 120;

        // Nombre de clients sans commandes (pour churn élevé)
        $customersWithoutOrders = 6;

        // On crée les clients
        $customers = [];

        for ($i = 0; $i < $customersCount; $i++) {

            // Création d'un client
            $customer = Customer::create([
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->optional()->phoneNumber(),
                'status' => 'active',
            ]);

            // On ajoute au tableau
            $customers[] = $customer;
        }

        // On prend les clients qui auront des commandes (on exclut les X premiers)
        $customersWithOrders = array_slice($customers, $customersWithoutOrders);

        // Si aucun client avec commandes, on s'arrête proprement
        if (count($customersWithOrders) === 0) {
            return;
        }

        // Liste de produits simples
        $productNames = [
            'Casque Bluetooth',
            'Clavier mécanique',
            'Souris gaming',
            'Webcam HD',
            'Écran 24 pouces',
            'Chargeur USB-C',
            'Support ordinateur',
            'Batterie externe',
            'Câble HDMI',
            'Enceinte portable',
            'Montre connectée',
            'Disque SSD 1TB',
        ];

        // On crée les commandes
        for ($o = 0; $o < $ordersCount; $o++) {

            // Client aléatoire
            $customer = $faker->randomElement($customersWithOrders);

            // Date de commande entre aujourd'hui et -180 jours
            $orderedAt = Carbon::now()->subDays($faker->numberBetween(0, 180))
                ->subHours($faker->numberBetween(0, 23))
                ->subMinutes($faker->numberBetween(0, 59));

            // Nombre d'items
            $itemsCount = $faker->numberBetween(1, 4);

            // Total commande calculé à partir des items
            $orderTotal = 0;

            // Création de la commande
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . $faker->unique()->numerify('########'),
                'status' => 'paid',
                'total_amount' => 0,
                'currency' => 'MAD',
                'ordered_at' => $orderedAt,
            ]);

            // Création des items
            for ($i = 0; $i < $itemsCount; $i++) {

                // Produit
                $productName = $faker->randomElement($productNames);

                // Quantité
                $quantity = $faker->numberBetween(1, 3);

                // Prix unitaire
                $unitPrice = $faker->randomFloat(2, 50, 1500);

                // Total ligne
                $lineTotal = $quantity * $unitPrice;

                // Création item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $productName,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                // Ajout au total commande
                $orderTotal += $lineTotal;
            }

            // Mise à jour total commande
            $order->total_amount = $orderTotal;

            // Sauvegarde
            $order->save();
        }

        // Bonus : 3 clients "VIP" (grosses commandes récentes)
        for ($v = 0; $v < 3; $v++) {

            // Client aléatoire
            $vipCustomer = $faker->randomElement($customersWithOrders);

            // 3 grosses commandes
            for ($k = 0; $k < 3; $k++) {

                // Date très récente
                $orderedAt = Carbon::now()->subDays($faker->numberBetween(0, 15));

                // Création commande VIP
                $order = Order::create([
                    'customer_id' => $vipCustomer->id,
                    'order_number' => 'VIP-' . $faker->unique()->numerify('########'),
                    'status' => 'paid',
                    'total_amount' => 0,
                    'currency' => 'MAD',
                    'ordered_at' => $orderedAt,
                ]);

                // Total VIP
                $orderTotal = 0;

                // 2 items chers
                for ($i = 0; $i < 2; $i++) {

                    $productName = $faker->randomElement($productNames);
                    $quantity = 1;
                    $unitPrice = $faker->randomFloat(2, 2000, 6000);
                    $lineTotal = $quantity * $unitPrice;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_name' => $productName,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'line_total' => $lineTotal,
                    ]);

                    $orderTotal += $lineTotal;
                }

                // Mise à jour total
                $order->total_amount = $orderTotal;
                $order->save();
            }
        }
    }
}
