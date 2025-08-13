<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->infolist }}
    </div>

    <script>
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
                            detailsSection.style.display = 'none';
                            button.querySelector('span').textContent = '▼ Voir les détails';
                            button.querySelector('svg').classList.remove('rotate-180');
                        } else {
                            detailsSection.style.display = 'block';
                            button.querySelector('span').textContent = '▲ Masquer les détails';
                            button.querySelector('svg').classList.add('rotate-180');
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .historique-entry {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: white;
        }

        .historique-details {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f9fafb;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
        }

        .historique-toggle-details {
            transition: all 0.2s ease-in-out;
        }

        .historique-toggle-details:hover {
            background-color: #f3f4f6;
        }

        .historique-toggle-details svg {
            transition: transform 0.2s ease-in-out;
        }

        .historique-toggle-details svg.rotate-180 {
            transform: rotate(180deg);
        }
    </style>
</x-filament-panels::page>
