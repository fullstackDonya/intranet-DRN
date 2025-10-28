// Analytics Customer - Script pour l'analyse comportementale des clients
document.addEventListener('DOMContentLoaded', function() {
    console.log('Analytics Customer page loaded');

    // Configuration des graphiques
    initCustomerAnalytics();
    loadCustomerData();
    setupFilterHandlers();
});

function initCustomerAnalytics() {
    // Graphique de segmentation des clients
    if (document.getElementById('customerSegmentChart')) {
        const ctx = document.getElementById('customerSegmentChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Nouveaux', 'Actifs', 'Fidèles', 'À risque', 'Inactifs'],
                datasets: [{
                    data: [25, 35, 20, 15, 5],
                    backgroundColor: [
                        '#28a745',
                        '#17a2b8',
                        '#ffc107',
                        '#fd7e14',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Graphique d'évolution de la satisfaction
    if (document.getElementById('satisfactionTrendChart')) {
        const ctx = document.getElementById('satisfactionTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Score de satisfaction',
                    data: [4.2, 4.3, 4.1, 4.5, 4.4, 4.6],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 3,
                        max: 5
                    }
                }
            }
        });
    }
}

function loadCustomerData() {
    // Chargement des données client via API
    fetch('api/customer-analytics.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCustomerMetrics(data.metrics);
                updateCustomerTable(data.customers);
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des données:', error);
            showDemoData();
        });
}

function updateCustomerMetrics(metrics) {
    // Mise à jour des métriques principales
    document.getElementById('totalCustomers').textContent = metrics.total || '1,247';
    document.getElementById('newCustomers').textContent = metrics.new || '89';
    document.getElementById('churnRate').textContent = (metrics.churn || 2.3) + '%';
    document.getElementById('avgLifetime').textContent = '€' + (metrics.lifetime || 1850);
}

function updateCustomerTable(customers) {
    const tableBody = document.getElementById('customersTableBody');
    if (!tableBody || !customers) return;

    tableBody.innerHTML = customers.map(customer => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-2">
                        <img src="${customer.avatar || 'assets/img/default-avatar.png'}" 
                             class="rounded-circle" width="32" height="32">
                    </div>
                    <div>
                        <div class="fw-bold">${customer.name}</div>
                        <small class="text-muted">${customer.email}</small>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-${getSegmentColor(customer.segment)}">${customer.segment}</span></td>
            <td>€${customer.totalSpent}</td>
            <td>${customer.orders}</td>
            <td>${customer.lastPurchase}</td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="me-2">${customer.satisfaction}/5</span>
                    <div class="progress" style="width: 60px; height: 8px;">
                        <div class="progress-bar" style="width: ${customer.satisfaction * 20}%"></div>
                    </div>
                </div>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="viewCustomerDetails('${customer.id}')">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function getSegmentColor(segment) {
    const colors = {
        'Nouveau': 'success',
        'Actif': 'info',
        'Fidèle': 'warning',
        'À risque': 'danger',
        'Inactif': 'secondary'
    };
    return colors[segment] || 'secondary';
}

function showDemoData() {
    console.log('Mode démo - Données d\'exemple chargées');
    updateCustomerMetrics({
        total: 1247,
        new: 89,
        churn: 2.3,
        lifetime: 1850
    });
}

function setupFilterHandlers() {
    // Gestionnaires pour les filtres
    const filters = ['segmentFilter', 'periodFilter', 'sortFilter'];
    filters.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', function() {
                applyFilters();
            });
        }
    });

    // Recherche en temps réel
    const searchInput = document.getElementById('customerSearch');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            applyFilters();
        }, 300));
    }
}

function applyFilters() {
    const segment = document.getElementById('segmentFilter')?.value;
    const period = document.getElementById('periodFilter')?.value;
    const sort = document.getElementById('sortFilter')?.value;
    const search = document.getElementById('customerSearch')?.value;

    console.log('Filtres appliqués:', { segment, period, sort, search });
    // Logique de filtrage des données
    loadCustomerData();
}

function viewCustomerDetails(customerId) {
    // Affichage des détails du client
    console.log('Affichage des détails pour le client:', customerId);
    // Redirection ou modal avec les détails
}

function exportCustomerData() {
    // Export des données client
    console.log('Export des données client...');
    // Logique d'export
}

// Fonction utilitaire pour debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
