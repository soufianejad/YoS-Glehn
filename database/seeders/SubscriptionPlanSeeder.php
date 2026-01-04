<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Individual Plans
        SubscriptionPlan::updateOrCreate(['slug' => 'mensuel-individuel'], [
            'name' => 'Abonnement Mensuel',
            'slug' => 'mensuel-individuel',
            'description' => 'Accès complet à la bibliothèque publique pour un mois.',
            'type' => 'individual',
            'price' => 7000,
            'duration_days' => 30,
            'is_active' => true,
            'order' => 1,
        ]);

        SubscriptionPlan::updateOrCreate(['slug' => 'annuel-individuel'], [
            'name' => 'Abonnement Annuel',
            'slug' => 'annuel-individuel',
            'description' => 'Accès complet à la bibliothèque publique pour un an. Économisez avec notre offre annuelle!',
            'type' => 'individual',
            'price' => 50000,
            'duration_days' => 365,
            'is_active' => true,
            'order' => 2,
        ]);

        // School Plans
        SubscriptionPlan::updateOrCreate(['slug' => 'ecole-200'], [
            'name' => 'Forfait École - 200 Élèves',
            'slug' => 'ecole-200',
            'description' => 'Accès à l\'espace éducatif pour un maximum de 200 élèves.',
            'type' => 'school',
            'price' => 200000,
            'duration_days' => 30,
            'max_students' => 200,
            'quiz_access' => true,
            'is_active' => true,
            'order' => 3,
        ]);

        SubscriptionPlan::updateOrCreate(['slug' => 'ecole-450'], [
            'name' => 'Forfait École - 450 Élèves',
            'slug' => 'ecole-450',
            'description' => 'Accès à l\'espace éducatif pour un maximum de 450 élèves.',
            'type' => 'school',
            'price' => 350000,
            'duration_days' => 30,
            'max_students' => 450,
            'quiz_access' => true,
            'is_active' => true,
            'order' => 4,
        ]);

        SubscriptionPlan::updateOrCreate(['slug' => 'ecole-illimite'], [
            'name' => 'Forfait École - Illimité',
            'slug' => 'ecole-illimite',
            'description' => 'Accès à l\'espace éducatif pour un nombre illimité d\'élèves.',
            'type' => 'school',
            'price' => 650000,
            'duration_days' => 30,
            'max_students' => null,
            'quiz_access' => true,
            'is_active' => true,
            'order' => 5,
        ]);
    }
}
