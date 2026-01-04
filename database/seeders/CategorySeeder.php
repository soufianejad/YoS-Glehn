<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Public Space Categories
        Category::updateOrCreate(['slug' => 'romans-africains'], [
            'name' => 'Romans Africains', 'slug' => 'romans-africains', 'space' => 'public', 'description' => 'Les plus grands romans du continent.',
        ]);
        Category::updateOrCreate(['slug' => 'contes-et-legendes'], [
            'name' => 'Contes et Légendes', 'slug' => 'contes-et-legendes', 'space' => 'public', 'description' => 'Plongez dans la richesse des traditions orales.',
        ]);
        Category::updateOrCreate(['slug' => 'poesie'], [
            'name' => 'Poésie', 'slug' => 'poesie', 'space' => 'public', 'description' => 'La beauté des mots et des émotions.',
        ]);

        // Educational Space Categories
        Category::updateOrCreate(['slug' => 'manuel-scolaire'], [
            'name' => 'Manuel Scolaire', 'slug' => 'manuel-scolaire', 'space' => 'educational', 'description' => 'Les livres du programme officiel.',
        ]);
        Category::updateOrCreate(['slug' => 'histoire-africaine'], [
            'name' => 'Histoire Africaine', 'slug' => 'histoire-africaine', 'space' => 'educational', 'description' => 'Pour comprendre le passé et construire l\'avenir.',
        ]);
        Category::updateOrCreate(['slug' => 'sciences-et-nature'], [
            'name' => 'Sciences et Nature', 'slug' => 'sciences-et-nature', 'space' => 'educational', 'description' => 'Découvrir le monde qui nous entoure.',
        ]);

        // Adult Space Categories
        Category::updateOrCreate(['slug' => 'litterature-erotique'], [
            'name' => 'Littérature Érotique', 'slug' => 'litterature-erotique', 'space' => 'adult', 'description' => 'Explorations des désirs et des passions.',
        ]);
        Category::updateOrCreate(['slug' => 'thriller-psychologique'], [
            'name' => 'Thriller Psychologique', 'slug' => 'thriller-psychologique', 'space' => 'adult', 'description' => 'Des histoires qui jouent avec vos nerfs.',
        ]);
    }
}
