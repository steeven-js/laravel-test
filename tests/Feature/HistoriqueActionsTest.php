<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Historique;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoriqueActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur de test
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_creates_historique_on_client_creation()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        $this->assertDatabaseHas('historique', [
            'entite_type' => Client::class,
            'entite_id' => $client->id,
            'action' => 'creation',
            'titre' => 'Création de Client #' . $client->id,
            'description' => 'Nouvel enregistrement créé',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_creates_historique_on_client_modification()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        // Modifier le client
        $client->update([
            'email' => 'nouveau@example.com',
            'telephone' => '0987654321',
        ]);

        $this->assertDatabaseHas('historique', [
            'entite_type' => Client::class,
            'entite_id' => $client->id,
            'action' => 'modification',
            'titre' => 'Modification de Client #' . $client->id,
            'description' => 'Données mises à jour',
            'user_id' => $this->user->id,
        ]);

        // Vérifier les données avant/après
        $historique = Historique::where('action', 'modification')->first();
        $this->assertNotNull($historique->donnees_avant);
        $this->assertNotNull($historique->donnees_apres);
    }

    /** @test */
    public function it_creates_historique_on_client_deletion()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        $client->delete();

        $this->assertDatabaseHas('historique', [
            'entite_type' => Client::class,
            'entite_id' => $client->id,
            'action' => 'suppression',
            'titre' => 'Suppression de Client #' . $client->id,
            'description' => 'Enregistrement supprimé',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_record_custom_action()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        $client->enregistrerActionPersonnalisee(
            'envoi_email',
            'Email de bienvenue envoyé',
            'Email de bienvenue envoyé au client',
            ['template' => 'welcome', 'status' => 'sent']
        );

        $this->assertDatabaseHas('historique', [
            'entite_type' => Client::class,
            'entite_id' => $client->id,
            'action' => 'envoi_email',
            'titre' => 'Email de bienvenue envoyé',
            'description' => 'Email de bienvenue envoyé au client',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_record_status_change()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        $client->enregistrerChangementStatut(
            'actif',
            'inactif',
            'Client désactivé par l\'administrateur'
        );

        $this->assertDatabaseHas('historique', [
            'entite_type' => Client::class,
            'entite_id' => $client->id,
            'action' => 'changement_statut',
            'titre' => 'Changement de statut de Client #' . $client->id,
            'description' => 'Client désactivé par l\'administrateur',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_stores_user_information_correctly()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        $historique = Historique::where('entite_type', Client::class)->first();

        $this->assertEquals($this->user->id, $historique->user_id);
        $this->assertEquals($this->user->name, $historique->user_nom);
        $this->assertEquals($this->user->email, $historique->user_email);
    }

    /** @test */
    public function it_stores_technical_information()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        $historique = Historique::where('entite_type', Client::class)->first();

        $this->assertNotNull($historique->ip_address);
        $this->assertNotNull($historique->user_agent);
        $this->assertNotNull($historique->created_at);
    }

    /** @test */
    public function it_excludes_sensitive_fields_from_modification_history()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        // Modifier le client
        $client->update([
            'nom' => 'Nouveau Nom',
            'updated_at' => now(), // Ce champ ne devrait pas être dans l'historique
        ]);

        $historique = Historique::where('action', 'modification')->first();

        // Vérifier que updated_at n'est pas dans les données
        $this->assertArrayNotHasKey('updated_at', $historique->donnees_avant);
        $this->assertArrayNotHasKey('updated_at', $historique->donnees_apres);
    }

    /** @test */
    public function it_handles_empty_changes_correctly()
    {
        $client = Client::create([
            'nom' => 'Test',
            'prenom' => 'Client',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
            'pays' => 'France',
            'actif' => true,
        ]);

        // Modifier sans changement réel
        $client->update([
            'updated_at' => now(),
        ]);

        // Aucun historique de modification ne devrait être créé
        $this->assertDatabaseMissing('historique', [
            'entite_type' => Client::class,
            'entite_id' => $client->id,
            'action' => 'modification',
        ]);
    }
}
