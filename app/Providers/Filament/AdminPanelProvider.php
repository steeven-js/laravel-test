<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Resources\ClientEmailResource;
use App\Filament\Resources\ClientResource;
use App\Filament\Resources\DevisResource;
use App\Filament\Resources\EmailTemplateResource;
use App\Filament\Resources\EntrepriseResource;
use App\Filament\Resources\FactureResource;
use App\Filament\Resources\MadiniaResource;
use App\Filament\Resources\NotificationResource as NotificationsResource;
use App\Filament\Resources\OpportunityResource;
use App\Filament\Resources\SecteurActiviteResource;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\Settings\NumeroSequenceResource;
use App\Filament\Resources\TicketResource;
use App\Filament\Resources\TodoResource;
use App\Filament\Resources\UserResource as UsersResource;
use App\Filament\Resources\UserRoleResource;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Auth;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->collapsibleNavigationGroups()
            ->sidebarCollapsibleOnDesktop()
            // ->topNavigation() // Désactivé: utiliser la navigation latérale
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $user = Auth::user();
                
                // Si pas d'utilisateur connecté, retourner navigation vide
                if (!$user || !($user instanceof User)) {
                    return $builder;
                }

                // Super admin voit tout
                if ($user->isSuperAdmin()) {
                    return $this->buildFullNavigation($builder);
                }

                // Pour les autres utilisateurs, filtrer selon les permissions
                return $this->buildFilteredNavigation($builder, $user);
            })
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Pages\PermissionsOverview::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                \App\Filament\Widgets\DashboardStats::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Construit la navigation complète pour les super admins
     */
    private function buildFullNavigation(NavigationBuilder $builder): NavigationBuilder
    {
        return $builder
            ->items([
                // Dashboard en premier
                NavigationItem::make('Dashboard')
                    ->icon('heroicon-o-home')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                    ->url(fn (): string => Pages\Dashboard::getUrl()),
            ])
            ->groups([
                NavigationGroup::make('CRM')
                    ->collapsible()
                    ->items([
                        ...ClientResource::getNavigationItems(),
                        ...EntrepriseResource::getNavigationItems(),
                        ...OpportunityResource::getNavigationItems(),
                    ]),

                NavigationGroup::make('Ventes')
                    ->collapsible()
                    ->items([
                        ...DevisResource::getNavigationItems(),
                        ...FactureResource::getNavigationItems(),
                    ]),

                NavigationGroup::make('Communication')
                    ->collapsible()
                    ->items([
                        ...EmailTemplateResource::getNavigationItems(),
                        ...ClientEmailResource::getNavigationItems(),
                        ...NotificationsResource::getNavigationItems(),
                    ]),

                NavigationGroup::make('Référentiels')
                    ->collapsible()
                    ->items([
                        ...ServiceResource::getNavigationItems(),
                        ...SecteurActiviteResource::getNavigationItems(),
                    ]),

                NavigationGroup::make('Support')
                    ->collapsible()
                    ->items([
                        ...TicketResource::getNavigationItems(),
                        ...TodoResource::getNavigationItems(),
                    ]),

                NavigationGroup::make('Réglages')
                    ->collapsible()
                    ->items([
                        ...MadiniaResource::getNavigationItems(),
                        ...NumeroSequenceResource::getNavigationItems(),
                    ]),

                NavigationGroup::make('Administration')
                    ->collapsible()
                    ->items([
                        ...UserRoleResource::getNavigationItems(),
                        ...UsersResource::getNavigationItems(),
                    ]),
            ]);
    }

    /**
     * Construit la navigation filtrée selon les permissions de l'utilisateur
     */
    private function buildFilteredNavigation(NavigationBuilder $builder, User $user): NavigationBuilder
    {
        $items = [
            // Dashboard toujours visible
            NavigationItem::make('Dashboard')
                ->icon('heroicon-o-home')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                ->url(fn (): string => Pages\Dashboard::getUrl()),
        ];

        $groups = [];

        // Groupe CRM
        $crmItems = [];
        if ($user->canView('clients')) {
            $crmItems = array_merge($crmItems, ClientResource::getNavigationItems());
        }
        if ($user->canView('entreprises')) {
            $crmItems = array_merge($crmItems, EntrepriseResource::getNavigationItems());
        }
        if ($user->canView('opportunities')) {
            $crmItems = array_merge($crmItems, OpportunityResource::getNavigationItems());
        }
        
        if (!empty($crmItems)) {
            $groups[] = NavigationGroup::make('CRM')
                ->collapsible()
                ->items($crmItems);
        }

        // Groupe Ventes
        $ventesItems = [];
        if ($user->canView('devis')) {
            $ventesItems = array_merge($ventesItems, DevisResource::getNavigationItems());
        }
        if ($user->canView('factures')) {
            $ventesItems = array_merge($ventesItems, FactureResource::getNavigationItems());
        }
        
        if (!empty($ventesItems)) {
            $groups[] = NavigationGroup::make('Ventes')
                ->collapsible()
                ->items($ventesItems);
        }

        // Groupe Communication
        $communicationItems = [];
        if ($user->canView('emailtemplates')) {
            $communicationItems = array_merge($communicationItems, EmailTemplateResource::getNavigationItems());
        }
        if ($user->canView('clientemails')) {
            $communicationItems = array_merge($communicationItems, ClientEmailResource::getNavigationItems());
        }
        if ($user->canView('notifications')) {
            $communicationItems = array_merge($communicationItems, NotificationsResource::getNavigationItems());
        }
        
        if (!empty($communicationItems)) {
            $groups[] = NavigationGroup::make('Communication')
                ->collapsible()
                ->items($communicationItems);
        }

        // Groupe Référentiels
        $referentielsItems = [];
        if ($user->canView('services')) {
            $referentielsItems = array_merge($referentielsItems, ServiceResource::getNavigationItems());
        }
        if ($user->canView('secteursactivite')) {
            $referentielsItems = array_merge($referentielsItems, SecteurActiviteResource::getNavigationItems());
        }
        
        if (!empty($referentielsItems)) {
            $groups[] = NavigationGroup::make('Référentiels')
                ->collapsible()
                ->items($referentielsItems);
        }

        // Groupe Support
        $supportItems = [];
        if ($user->canView('tickets')) {
            $supportItems = array_merge($supportItems, TicketResource::getNavigationItems());
        }
        if ($user->canView('todos')) {
            $supportItems = array_merge($supportItems, TodoResource::getNavigationItems());
        }
        
        if (!empty($supportItems)) {
            $groups[] = NavigationGroup::make('Support')
                ->collapsible()
                ->items($supportItems);
        }

        // Groupe Réglages
        $reglagesItems = [];
        if ($user->canView('madinia')) {
            $reglagesItems = array_merge($reglagesItems, MadiniaResource::getNavigationItems());
        }
        if ($user->canView('settings')) {
            $reglagesItems = array_merge($reglagesItems, NumeroSequenceResource::getNavigationItems());
        }
        
        if (!empty($reglagesItems)) {
            $groups[] = NavigationGroup::make('Réglages')
                ->collapsible()
                ->items($reglagesItems);
        }

        // Groupe Administration
        $adminItems = [];
        if ($user->canView('userroles') || $user->canManageRoles()) {
            $adminItems = array_merge($adminItems, UserRoleResource::getNavigationItems());
        }
        if ($user->canView('users')) {
            $adminItems = array_merge($adminItems, UsersResource::getNavigationItems());
        }
        
        if (!empty($adminItems)) {
            $groups[] = NavigationGroup::make('Administration')
                ->collapsible()
                ->items($adminItems);
        }

        return $builder
            ->items($items)
            ->groups($groups);
    }
}
