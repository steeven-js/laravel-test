/**
 * Gestion de l'historique des actions
 */
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons "Voir les détails"
    document.addEventListener('click', function(e) {
        if (e.target.closest('.historique-toggle-details')) {
            e.preventDefault();
            
            const button = e.target.closest('.historique-toggle-details');
            const historiqueId = button.getAttribute('data-historique-id');
            const detailsSection = document.querySelector(`.historique-details[data-historique-id="${historiqueId}"]`);
            
            if (detailsSection) {
                const isVisible = detailsSection.style.display !== 'none';
                
                if (isVisible) {
                    // Masquer les détails
                    detailsSection.style.display = 'none';
                    const label = button.querySelector('span');
                    if (label) label.textContent = '▼ Voir les détails';
                    const icon = button.querySelector('svg');
                    if (icon) icon.classList.remove('rotate-180');
                } else {
                    // Afficher les détails
                    detailsSection.style.display = 'block';
                    const label = button.querySelector('span');
                    if (label) label.textContent = '▲ Masquer les détails';
                    const icon = button.querySelector('svg');
                    if (icon) icon.classList.add('rotate-180');
                }
            }
        }
    });

    // Animation d'apparition des entrées d'historique
    const historiqueEntries = document.querySelectorAll('.historique-entry');
    historiqueEntries.forEach((entry, index) => {
        entry.style.opacity = '0';
        entry.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            entry.style.transition = 'all 0.3s ease-out';
            entry.style.opacity = '1';
            entry.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Gestion des filtres d'historique
    const actionFilters = document.querySelectorAll('input[name="action_filter"]');
    actionFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            const selectedActions = Array.from(actionFilters)
                .filter(f => f.checked)
                .map(f => f.value);
            
            filterHistoriqueEntries(selectedActions);
        });
    });
});

/**
 * Filtrer les entrées d'historique par type d'action
 */
function filterHistoriqueEntries(selectedActions) {
    const entries = document.querySelectorAll('.historique-entry');
    
    entries.forEach(entry => {
        const actionBadge = entry.querySelector('[data-action]');
        if (actionBadge) {
            const action = actionBadge.getAttribute('data-action');
            
            if (selectedActions.length === 0 || selectedActions.includes(action)) {
                entry.style.display = 'block';
                entry.style.opacity = '1';
            } else {
                entry.style.opacity = '0';
                setTimeout(() => {
                    entry.style.display = 'none';
                }, 300);
            }
        }
    });
}

/**
 * Rechercher dans l'historique
 */
function searchHistorique(query) {
    const entries = document.querySelectorAll('.historique-entry');
    const searchTerm = query.toLowerCase();
    
    entries.forEach(entry => {
        const text = entry.textContent.toLowerCase();
        const isVisible = text.includes(searchTerm);
        
        if (isVisible) {
            entry.style.display = 'block';
            entry.style.opacity = '1';
        } else {
            entry.style.opacity = '0';
            setTimeout(() => {
                entry.style.display = 'none';
            }, 300);
        }
    });
}

/**
 * Exporter l'historique en CSV
 */
function exportHistoriqueCSV() {
    const entries = document.querySelectorAll('.historique-entry');
    let csv = 'Action,Titre,Description,Par,Date,IP\n';
    
    entries.forEach(entry => {
        const action = entry.querySelector('[data-action]')?.textContent || '';
        const titre = entry.querySelector('[data-titre]')?.textContent || '';
        const description = entry.querySelector('[data-description]')?.textContent || '';
        const user = entry.querySelector('[data-user]')?.textContent || '';
        const date = entry.querySelector('[data-date]')?.textContent || '';
        const ip = entry.querySelector('[data-ip]')?.textContent || '';
        
        csv += `"${action}","${titre}","${description}","${user}","${date}","${ip}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'historique_actions.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Nettoyer l'historique (supprimer les anciennes entrées)
 */
function cleanupHistorique() {
    if (confirm('Êtes-vous sûr de vouloir nettoyer l\'historique ? Cette action est irréversible.')) {
        // Ici vous pouvez ajouter la logique pour nettoyer l'historique
        // Par exemple, appeler une route API pour supprimer les anciennes entrées
        console.log('Nettoyage de l\'historique...');
    }
}
