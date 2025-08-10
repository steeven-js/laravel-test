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
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
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
                return $builder
                    ->items([
                        // Dashboard en premier
                        \Filament\Navigation\NavigationItem::make('Dashboard')
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
            })
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->databaseNotifications()
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
}
