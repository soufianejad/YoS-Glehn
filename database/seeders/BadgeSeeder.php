<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Badge::updateOrCreate(['slug' => 'premier-livre-lu'], [
            'name' => 'Premier Livre Lu',
            'slug' => 'premier-livre-lu',
            'description' => 'Vous avez terminé votre premier livre ! Continuez comme ça.',
            'icon' => 'fas fa-book-reader',
            'color' => '#28a745',
        ]);

        Badge::updateOrCreate(['slug' => 'lecteur-assidu'], [
            'name' => 'Lecteur Assidu',
            'slug' => 'lecteur-assidu',
            'description' => 'Félicitations ! Vous avez lu 10 livres.',
            'icon' => 'fas fa-award',
            'color' => '#007bff',
        ]);

        Badge::updateOrCreate(['slug' => 'maitre-des-quiz'], [
            'name' => 'Maître des Quiz',
            'slug' => 'maitre-des-quiz',
            'description' => 'Vous avez réussi 5 quiz avec un score parfait.',
            'icon' => 'fas fa-graduation-cap',
            'color' => '#ffc107',
        ]);

        Badge::updateOrCreate(['slug' => 'curieux-des-contes'], [
            'name' => 'Curieux des Contes',
            'slug' => 'curieux-des-contes',
            'description' => 'Vous avez exploré la catégorie Contes et Légendes.',
            'icon' => 'fas fa-feather-alt',
            'color' => '#17a2b8',
        ]);

        Badge::updateOrCreate(['slug' => 'lecteur-du-mois'], [
            'name' => 'Lecteur du Mois',
            'slug' => 'lecteur-du-mois',
            'description' => 'Vous êtes le lecteur le plus actif ce mois-ci.',
            'icon' => 'fas fa-trophy',
            'color' => '#dc3545',
        ]);
    }
}
