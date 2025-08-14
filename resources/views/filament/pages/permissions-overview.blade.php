<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Utilisateurs</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ count($users) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rôles</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ count($roles) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ressources</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ count($permissionsByResource) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Permissions</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ count($allPermissions) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grille des permissions -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Grille des permissions par utilisateur
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Vue d'ensemble de toutes les permissions pour chaque utilisateur
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-700 z-10">
                                Utilisateur / Rôle
                            </th>
                            @foreach($permissionsByResource as $resource => $permissions)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-xs">{{ ucfirst($resource) }}</span>
                                        <span class="text-xs text-gray-400">({{ count($permissions) }})</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white dark:bg-gray-800 z-10">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->email }}
                                            </div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $user->userRole?->display_name ?? 'Aucun rôle' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($permissionsByResource as $resource => $permissions)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($permissions as $permission)
                                                @php
                                                    $hasPermission = $user->hasPermission($resource, $permission);
                                                    $isSuperAdmin = $user->isSuperAdmin();
                                                @endphp
                                                <div class="flex items-center">
                                                    @if($isSuperAdmin)
                                                        <div class="w-7 h-7 rounded-full flex items-center justify-center border-2 shadow-sm" style="background-color: #059669; color: white; border-color: #047857;" title="Super Admin - Toutes les permissions">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    @elseif($hasPermission)
                                                        @php
                                                            $permissionStyles = [
                                                                'view' => 'bg-blue-500 text-white border-blue-600',
                                                                'create' => 'bg-green-500 text-white border-green-600',
                                                                'edit' => 'bg-orange-500 text-white border-orange-600',
                                                                'delete' => 'bg-red-500 text-white border-red-600',
                                                                'export' => 'bg-purple-500 text-white border-purple-600',
                                                                'send' => 'bg-indigo-500 text-white border-indigo-600',
                                                                'assign' => 'bg-teal-500 text-white border-teal-600',
                                                                'transform_to_facture' => 'bg-yellow-500 text-white border-yellow-600',
                                                                'manage_roles' => 'bg-pink-500 text-white border-pink-600',
                                                                'import_csv' => 'bg-gray-500 text-white border-gray-600',
                                                                'view_all_stats' => 'bg-emerald-500 text-white border-emerald-600',
                                                                'view_own_stats' => 'bg-sky-500 text-white border-sky-600',
                                                                'generate_test_data' => 'bg-violet-500 text-white border-violet-600'
                                                            ];
                                                            $style = $permissionStyles[$permission] ?? 'bg-blue-500 text-white border-blue-600';
                                                        @endphp
                                                        @php
                                                            $colorMap = [
                                                                'view' => ['bg' => '#3B82F6', 'border' => '#2563EB'],
                                                                'create' => ['bg' => '#10B981', 'border' => '#059669'],
                                                                'edit' => ['bg' => '#F59E0B', 'border' => '#D97706'],
                                                                'delete' => ['bg' => '#EF4444', 'border' => '#DC2626'],
                                                                'export' => ['bg' => '#8B5CF6', 'border' => '#7C3AED'],
                                                                'send' => ['bg' => '#6366F1', 'border' => '#4F46E5'],
                                                                'assign' => ['bg' => '#14B8A6', 'border' => '#0D9488'],
                                                                'transform_to_facture' => ['bg' => '#EAB308', 'border' => '#CA8A04'],
                                                                'manage_roles' => ['bg' => '#EC4899', 'border' => '#DB2777'],
                                                                'import_csv' => ['bg' => '#6B7280', 'border' => '#4B5563'],
                                                                'view_all_stats' => ['bg' => '#10B981', 'border' => '#059669'],
                                                                'view_own_stats' => ['bg' => '#0EA5E9', 'border' => '#0284C7'],
                                                                'generate_test_data' => ['bg' => '#8B5CF6', 'border' => '#7C3AED']
                                                            ];
                                                            $colors = $colorMap[$permission] ?? $colorMap['view'];
                                                        @endphp
                                                        <div class="w-7 h-7 rounded-full flex items-center justify-center border-2 shadow-sm" style="background-color: {{ $colors['bg'] }}; color: white; border-color: {{ $colors['border'] }};" title="{{ ucfirst($permission) }}">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-7 h-7 rounded-full flex items-center justify-center border-2 shadow-sm" style="background-color: #D1D5DB; color: #4B5563; border-color: #9CA3AF;" title="Pas de permission">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Légende -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Légende
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Super Admin -->
                    <div class="flex items-center space-x-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center border-2 shadow-sm" style="background-color: #059669; color: white; border-color: #047857;">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Super Admin - Toutes les permissions</span>
                    </div>

                    <!-- Permission refusée -->
                    <div class="flex items-center space-x-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center border-2 shadow-sm" style="background-color: #D1D5DB; color: #4B5563; border-color: #9CA3AF;">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Permission refusée</span>
                    </div>

                    <!-- Types de permissions avec codes couleur -->
                    <div class="col-span-full">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Types de permissions :</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            <!-- View -->
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center border" style="background-color: #3B82F6; color: white; border-color: #2563EB;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Voir</span>
                            </div>

                            <!-- Create -->
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center border" style="background-color: #10B981; color: white; border-color: #059669;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Créer</span>
                            </div>

                            <!-- Edit -->
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center border" style="background-color: #F59E0B; color: white; border-color: #D97706;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Modifier</span>
                            </div>

                            <!-- Delete -->
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center border" style="background-color: #EF4444; color: white; border-color: #DC2626;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 8a1 1 0 012 0v3a1 1 0 11-2 0V8zm4 0a1 1 0 10-2 0v3a1 1 0 002 0V8z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Supprimer</span>
                            </div>

                            <!-- Export -->
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center border" style="background-color: #8B5CF6; color: white; border-color: #7C3AED;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Exporter</span>
                            </div>

                            <!-- Send -->
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center border" style="background-color: #6366F1; color: white; border-color: #4F46E5;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Envoyer</span>
                            </div>

                            <!-- Assign -->
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center border" style="background-color: #14B8A6; color: white; border-color: #0D9488;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Assigner</span>
                            </div>

                            <!-- Manage Roles -->
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center border" style="background-color: #EC4899; color: white; border-color: #DB2777;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Gérer rôles</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Résumé par rôle -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Résumé par rôle
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($roles as $role)
                        @php
                            $roleUsers = $users->where('user_role_id', $role->id);
                            $totalPermissions = 0;
                            foreach($permissionsByResource as $resource => $permissions) {
                                $totalPermissions += count($permissions);
                            }
                            $rolePermissions = collect($role->permissions)->flatten()->count();
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $role->display_name }}
                                </h4>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $roleUsers->count() }} utilisateur(s)
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                {{ $rolePermissions }} / {{ $totalPermissions }} permissions
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($rolePermissions / $totalPermissions) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
