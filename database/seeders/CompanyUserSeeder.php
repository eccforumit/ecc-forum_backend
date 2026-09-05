<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'utilisateur existe déjà
        $existingUser = User::where('email', 'entreprise@forum-ecc.ma')->first();

        if ($existingUser) {
            $this->command->info('L\'utilisateur entreprise@forum-ecc.ma existe déjà.');
            return;
        }

        // Créer l'utilisateur entreprise
        $user = User::create([
            'email' => 'entreprise@forum-ecc.ma',
            'password' => Hash::make('FORUM@2025'),
            'user_type' => 'company',
            'is_email_verified' => true,
            'is_active' => true,
            'is_staff' => false,
        ]);

        // Créer le profil entreprise associé
        CompanyProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Forum ECC',
            'industry' => 'Événementiel',
            'company_size' => '10-50',
            'company_description' => 'Organisation du Forum ECC - École Centrale Casablanca',
            'website' => 'https://forum-ecc.ma',
            'contact_first_name' => 'Admin',
            'contact_last_name' => 'Forum',
            'contact_phone' => '+212 5 00 00 00 00',
            'is_verified' => true,
        ]);

        $this->command->info('✅ Compte entreprise créé avec succès !');
        $this->command->info('📧 Email: entreprise@forum-ecc.ma');
        $this->command->info('🔑 Password: FORUM@2025');
    }
}

