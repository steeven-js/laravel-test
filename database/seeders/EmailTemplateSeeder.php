<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Création des modèles d\'email...');
        $this->createDefaultTemplates();
    }

    private function createDefaultTemplates(): void
    {
        $templates = [
            [
                'id' => 1,
                'name' => 'Devis promotionnel',
                'category' => 'envoi_initial',
                'sub_category' => 'promotionnel',
                'subject' => '🎉 Offre spéciale - Votre devis {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

C'est avec enthousiasme que nous vous présentons notre devis n° {{devis_numero}} pour votre projet.

Cette offre spéciale d'un montant de {{devis_montant}} est valable jusqu'au {{devis_validite}} et comprend tous les services détaillés dans le document joint.

Pour toute signature avant la date d'expiration, bénéficiez d'un suivi personnalisé pendant le premier mois de déploiement !

N'hésitez pas à nous contacter pour échanger sur cette proposition.

Bien cordialement,

L'équipe Madin.IA",
                'is_default' => true,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant', 'devis_validite'],
                'description' => 'Template promotionnel avec offre spéciale',
            ],
            [
                'id' => 2,
                'name' => 'Devis concis et direct',
                'category' => 'envoi_initial',
                'sub_category' => 'concis_direct',
                'subject' => 'Devis {{devis_numero}} - {{entreprise_nom}}',
                'body' => "Bonjour {{client_nom}},

Veuillez trouver en pièce jointe notre devis n° {{devis_numero}} d'un montant de {{devis_montant}}, valable jusqu'au {{devis_validite}}.

Pour l'accepter, il vous suffit de nous le retourner signé ou de nous confirmer votre accord par retour de mail.

Cordialement,

Madin.IA",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant', 'devis_validite', 'entreprise_nom'],
                'description' => 'Template court et efficace',
            ],
            [
                'id' => 3,
                'name' => 'Devis standard professionnel',
                'category' => 'envoi_initial',
                'sub_category' => 'standard_professionnel',
                'subject' => 'Devis {{devis_numero}} - {{entreprise_nom}}',
                'body' => "Bonjour {{client_nom}},

Nous vous remercions pour votre confiance en Madin.IA.

Suite à notre échange, nous avons le plaisir de vous faire parvenir notre devis n° {{devis_numero}} d'un montant de {{devis_montant}}, valable jusqu'au {{devis_validite}}.

Vous trouverez tous les détails de notre proposition en pièce jointe. N'hésitez pas à nous contacter pour toute information complémentaire ou modification souhaitée.

Cordialement,

L'équipe Madin.IA

{{contact_telephone}}",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant', 'devis_validite', 'contact_telephone', 'entreprise_nom'],
                'description' => 'Template professionnel standard',
            ],
            [
                'id' => 4,
                'name' => 'Devis détaillé avec étapes',
                'category' => 'envoi_initial',
                'sub_category' => 'detaille_etapes',
                'subject' => 'Votre projet - Devis détaillé {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

Suite à notre analyse approfondie de vos besoins, nous vous adressons notre devis n° {{devis_numero}} d'un montant de {{devis_montant}}.

Notre proposition comprend :
- Une phase d'analyse et conception
- Le développement et l'implémentation
- Les tests et déploiement

Cette offre est valable jusqu'au {{devis_validite}}.

Nous restons à votre disposition pour discuter des modalités de mise en œuvre et répondre à vos questions.

Cordialement,

L'équipe Madin.IA

{{contact_email}}",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant', 'devis_validite', 'contact_email'],
                'description' => 'Template détaillé avec processus étape par étape',
            ],
            [
                'id' => 5,
                'name' => 'Devis personnalisé et chaleureux',
                'category' => 'envoi_initial',
                'sub_category' => 'personnalise_chaleureux',
                'subject' => 'Votre projet nous enthousiasme ! Devis {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

Nous sommes ravis de l'intérêt que vous portez à notre solution et vous remercions pour la confiance que vous nous accordez.

Comme promis, vous trouverez ci-joint notre devis n° {{devis_numero}} personnalisé selon vos besoins spécifiques, pour un montant de {{devis_montant}}.

Notre équipe est impatiente de collaborer avec vous sur ce projet et de vous accompagner dans sa réalisation.

Ce devis est valable jusqu'au {{devis_validite}}.

N'hésitez pas à nous appeler directement au {{contact_telephone}} pour toute question.

Très cordialement,

L'équipe Madin.IA",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant', 'devis_validite', 'contact_telephone'],
                'description' => 'Template chaleureux et personnalisé',
            ],
            [
                'id' => 6,
                'name' => 'Rappel avec offre spéciale',
                'category' => 'rappel',
                'sub_category' => 'rappel_offre_speciale',
                'subject' => '⏰ Derniers jours - Offre spéciale sur votre devis {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

Nous vous rappelons que notre devis n° {{devis_numero}} est toujours disponible et valable jusqu'au {{devis_validite}}.

Pour toute acceptation dans les 7 prochains jours, nous vous offrons une réduction de 5% sur le montant total de {{devis_montant}}.

Nous serions ravis de pouvoir démarrer cette collaboration avec vous.

Cordialement,

L'équipe Madin.IA",
                'is_default' => true,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_validite', 'devis_montant'],
                'description' => 'Rappel avec offre promotionnelle limitée',
            ],
            [
                'id' => 7,
                'name' => 'Rappel avec date d\'expiration',
                'category' => 'rappel',
                'sub_category' => 'rappel_date_expiration',
                'subject' => '⏳ Votre devis {{devis_numero}} expire bientôt',
                'body' => "Bonjour {{client_nom}},

Nous vous informons que votre devis n° {{devis_numero}} d'un montant de {{devis_montant}} arrive bientôt à expiration ({{devis_validite}}).

Souhaitez-vous que nous procédions à sa mise à jour ou que nous prolongions sa validité ?

N'hésitez pas à nous contacter pour en discuter.

Cordialement,

L'équipe Madin.IA",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant', 'devis_validite'],
                'description' => 'Rappel centré sur la date d\'expiration',
            ],
            [
                'id' => 8,
                'name' => 'Rappel standard',
                'category' => 'rappel',
                'sub_category' => 'rappel_standard',
                'subject' => 'Suivi de votre devis {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

Nous souhaitons vous rappeler que notre devis n° {{devis_numero}} d'un montant de {{devis_montant}} est toujours en attente de votre décision.

Ce devis est valable jusqu'au {{devis_validite}}.

N'hésitez pas à nous contacter pour toute question ou pour nous faire part de votre décision.

Cordialement,

L'équipe Madin.IA",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant', 'devis_validite'],
                'description' => 'Rappel simple et professionnel',
            ],
            [
                'id' => 9,
                'name' => 'Suivi standard',
                'category' => 'relance',
                'sub_category' => 'suivi_standard',
                'subject' => 'Nouvelles de votre projet - Devis {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

Nous faisons suite à notre devis n° {{devis_numero}} d'un montant de {{devis_montant}} que nous vous avons envoyé précédemment.

Avez-vous pu prendre connaissance de cette proposition ?

Nous sommes disponibles pour discuter des modalités de ce devis ou pour répondre à vos questions.

Ce devis reste valable jusqu'au {{devis_validite}}.

Cordialement,

L'équipe Madin.IA",
                'is_default' => true,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant', 'devis_validite'],
                'description' => 'Relance bienveillante et professionnelle',
            ],
            [
                'id' => 10,
                'name' => 'Suivi avec ajustements possibles',
                'category' => 'relance',
                'sub_category' => 'suivi_ajustements',
                'subject' => 'Votre devis {{devis_numero}} - Possibilité d\'ajustements',
                'body' => "Bonjour {{client_nom}},

Nous souhaitons faire un suivi concernant notre devis n° {{devis_numero}} envoyé précédemment.

Si certains éléments de notre proposition ne correspondent pas exactement à vos attentes ou à votre budget actuel, nous serions ravis d'en discuter pour trouver des ajustements possibles.

Notre objectif est de vous proposer une solution adaptée à vos besoins spécifiques.

N'hésitez pas à nous faire part de vos retours.

Cordialement,

L'équipe Madin.IA",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero'],
                'description' => 'Relance avec proposition d\'ajustements',
            ],
            [
                'id' => 11,
                'name' => 'Suivi avec demande de feedback',
                'category' => 'relance',
                'sub_category' => 'suivi_feedback',
                'subject' => 'Votre avis nous intéresse - Devis {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

Suite à l'envoi de notre devis n° {{devis_numero}} d'un montant de {{devis_montant}}, nous n'avons pas encore reçu de retour de votre part.

Afin de mieux répondre à vos attentes, nous serions intéressés par votre feedback sur notre proposition :
- Le devis répond-il à vos besoins actuels ?
- Les tarifs proposés sont-ils en adéquation avec votre budget ?
- Y a-t-il des éléments que vous souhaiteriez modifier ?

Nous restons à votre disposition pour échanger et adapter notre offre si nécessaire.

Cordialement,

L'équipe Madin.IA",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant'],
                'description' => 'Relance axée sur le feedback client',
            ],
            [
                'id' => 12,
                'name' => 'Confirmation avec demande d\'informations',
                'category' => 'confirmation',
                'sub_category' => 'confirmation_infos',
                'subject' => '🎉 Confirmation - Devis {{devis_numero}} accepté',
                'body' => "Bonjour {{client_nom}},

Nous vous remercions vivement pour l'acceptation de notre devis n° {{devis_numero}}.

Votre commande a été enregistrée avec succès sous la référence {{numero_commande}}.

Afin de préparer au mieux le démarrage de votre projet, nous aurions besoin de quelques informations complémentaires :
- Vos disponibilités pour une réunion de cadrage dans les prochains jours
- Les coordonnées des personnes impliquées dans le projet de votre côté
- Vos préférences concernant la fréquence des points d'avancement

Nous vous invitons à nous communiquer ces informations par retour de mail.

Merci encore pour votre confiance.

Cordialement,

L'équipe Madin.IA",
                'is_default' => true,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'numero_commande'],
                'description' => 'Confirmation avec collecte d\'informations pratiques',
            ],
            [
                'id' => 13,
                'name' => 'Confirmation avec étapes suivantes',
                'category' => 'confirmation',
                'sub_category' => 'confirmation_etapes',
                'subject' => '✅ Devis {{devis_numero}} validé - Voici la suite',
                'body' => "Bonjour {{client_nom}},

Excellente nouvelle ! Nous vous confirmons l'acceptation de notre devis n° {{devis_numero}} et vous remercions pour votre confiance.

Votre commande est maintenant enregistrée sous la référence {{numero_commande}}.

Voici les prochaines étapes :
1. Un chef de projet va vous contacter dans les 48h pour planifier une réunion de lancement
2. Nous établirons ensemble un calendrier détaillé du projet
3. Le développement démarrera selon le planning convenu
4. Des points d'avancement réguliers seront organisés

N'hésitez pas à nous contacter si vous avez des questions.

Nous sommes impatients de démarrer ce projet avec vous !

Cordialement,

L'équipe Madin.IA",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'numero_commande'],
                'description' => 'Confirmation avec planning détaillé',
            ],
            [
                'id' => 14,
                'name' => 'Confirmation standard',
                'category' => 'confirmation',
                'sub_category' => 'confirmation_standard',
                'subject' => 'Confirmation de votre commande - Devis {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

Nous vous remercions d'avoir accepté notre devis n° {{devis_numero}}.

Votre commande a été enregistrée sous la référence {{numero_commande}}.

Nous allons maintenant procéder aux étapes suivantes de votre projet conformément à notre proposition.

Notre équipe va vous contacter très prochainement pour planifier le démarrage des travaux.

Nous vous remercions pour votre confiance et nous réjouissons de cette collaboration.

Cordialement,

L'équipe Madin.IA",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'numero_commande'],
                'description' => 'Confirmation sobre et professionnelle',
            ],
            [
                'id' => 15,
                'name' => 'Confirmation acceptation - Standard',
                'category' => 'confirmation',
                'sub_category' => 'confirmation_standard',
                'subject' => 'Confirmation d\'acceptation de votre devis {{devis_numero}}',
                'body' => "Bonjour {{client_nom}},

Nous avons le plaisir de vous confirmer que vous avez accepté le devis {{devis_numero}} d'un montant de {{devis_montant_ttc}}.

Votre acceptation a été enregistrée et nous allons maintenant procéder aux étapes suivantes de votre projet.

Notre équipe va vous contacter très prochainement pour planifier le démarrage des travaux.

Nous vous remercions pour votre confiance.

Cordialement,
L'équipe {{entreprise_nom}}",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_montant_ttc', 'entreprise_nom'],
                'description' => 'Email de confirmation standard pour l\'acceptation d\'un devis',
            ],
            [
                'id' => 16,
                'name' => 'Confirmation acceptation - Détaillée',
                'category' => 'confirmation',
                'sub_category' => 'confirmation_etapes',
                'subject' => '✅ Votre devis {{devis_numero}} a été accepté - Prochaines étapes',
                'body' => "Bonjour {{client_nom}},

🎉 Excellente nouvelle ! Nous avons bien reçu votre acceptation du devis {{devis_numero}}.

📋 RÉCAPITULATIF DE VOTRE COMMANDE :
• Numéro de devis : {{devis_numero}}
• Objet : {{devis_objet}}
• Montant TTC : {{devis_montant_ttc}}
• Date d'acceptation : {{date_acceptation}}

🚀 PROCHAINES ÉTAPES :
1. Planification du projet avec notre équipe
2. Établissement de la facture selon les conditions convenues
3. Démarrage des travaux selon le planning établi

Notre équipe va vous contacter dans les 48h pour organiser le lancement de votre projet.

Merci pour votre confiance !

Cordialement,
L'équipe {{entreprise_nom}}",
                'is_default' => false,
                'is_active' => true,
                'variables' => ['client_nom', 'devis_numero', 'devis_objet', 'devis_montant_ttc', 'date_acceptation', 'entreprise_nom'],
                'description' => 'Email de confirmation détaillé avec prochaines étapes',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['id' => $template['id']],
                $template
            );
        }

        $this->command?->info('✅ Modèles d\'email créés avec succès !');
    }
}
