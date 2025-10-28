<?php
include 'includes/opportunities.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .pipeline-stage {
            min-height: 600px;
            background-color: #f8f9fc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .opportunity-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: move;
            transition: all 0.3s;
        }
        .opportunity-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .stage-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .amount-badge {
            font-size: 0.9em;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">

            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-handshake text-primary"></i> Pipeline des Opportunités
                    </h1>
                    <div class="btn-group">
                        <a href="opportunities-export.php" class="btn btn-outline-primary">
                            <i class="fas fa-file-export"></i> Exporter
                        </a>
                        <a href="opportunities-add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Opportunité
                        </a>
                    </div>
                </div>

                <!-- KPIs Pipeline -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Valeur Pipeline
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pipeline-value">
                                            €<?php echo number_format($pipeline_value, 2, ',', ' '); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Opportunités Actives
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-opportunities">
                                            <?php echo $active_opportunities; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-handshake fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Taux de Fermeture
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="close-rate">
                                            <?php echo $close_rate; ?>%
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Cycle Moyen (jours)
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-cycle">
                                            <?php echo $avg_cycle; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pipeline Kanban -->
                <div class="row">
                    <div class="col-md-2">
                        <div class="pipeline-stage" id="stage-prospecting">
                            <div class="stage-header text-center">
                                <h6 class="mb-1">Prospection</h6>
                                <small id="prospecting-count">0 opportunités</small>
                                <div class="mt-1">
                                    <small id="prospecting-value">€0</small>
                                </div>
                            </div>
                            <div class="opportunities-container" data-stage="prospecting">
                                <!-- Opportunités chargées dynamiquement -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="pipeline-stage" id="stage-qualification">
                            <div class="stage-header text-center">
                                <h6 class="mb-1">Qualification</h6>
                                <small id="qualification-count">0 opportunités</small>
                                <div class="mt-1">
                                    <small id="qualification-value">€0</small>
                                </div>
                            </div>
                            <div class="opportunities-container" data-stage="qualification"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="pipeline-stage" id="stage-needs_analysis">
                            <div class="stage-header text-center">
                                <h6 class="mb-1">Analyse</h6>
                                <small id="needs_analysis-count">0 opportunités</small>
                                <div class="mt-1">
                                    <small id="needs_analysis-value">€0</small>
                                </div>
                            </div>
                            <div class="opportunities-container" data-stage="needs_analysis"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="pipeline-stage" id="stage-proposal">
                            <div class="stage-header text-center">
                                <h6 class="mb-1">Proposition</h6>
                                <small id="proposal-count">0 opportunités</small>
                                <div class="mt-1">
                                    <small id="proposal-value">€0</small>
                                </div>
                            </div>
                            <div class="opportunities-container" data-stage="proposal"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="pipeline-stage" id="stage-negotiation">
                            <div class="stage-header text-center">
                                <h6 class="mb-1">Négociation</h6>
                                <small id="negotiation-count">0 opportunités</small>
                                <div class="mt-1">
                                    <small id="negotiation-value">€0</small>
                                </div>
                            </div>
                            <div class="opportunities-container" data-stage="negotiation"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="pipeline-stage" id="stage-closed-won" style="background-color: #d4edda;">
                            <div class="stage-header text-center" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                <h6 class="mb-1">Fermé Gagné</h6>
                                <small id="closed_won-count">0 opportunités</small>
                                <div class="mt-1">
                                    <small id="closed_won-value">€<?php echo number_format($won_value, 2, ',', ' '); ?></small>
                                </div>
                            </div>
                            <div class="opportunities-container" data-stage="closed_won"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="pipeline-stage" id="stage-closed-lost" style="background-color: #f8d7da;">
                            <div class="stage-header text-center" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                                <h6 class="mb-1">Fermé Perdu</h6>
                                <small id="closed_lost-count">0 opportunités</small>
                                <div class="mt-1">
                                    <small id="closed_lost-value">€<?php echo number_format($lost_value, 2, ',', ' '); ?></small>
                                </div>
                            </div>
                            <div class="opportunities-container" data-stage="closed_lost"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tu peux ajouter ici ton JS pour charger dynamiquement les cartes d'opportunités -->

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const API_LIST = 'api/opportunities-list.php';
            const API_UPDATE = 'api/opportunity-update-stage.php';
            const POLL_INTERVAL = 10000; // ms
        
            function safeFetch(url, opts = {}) {
                opts.credentials = 'same-origin';
                if (!opts.headers) opts.headers = {};
                return fetch(url, opts).then(r => r.json());
            }
        
            function createCard(op) {
                const div = document.createElement('div');
                div.className = 'opportunity-card';
                div.draggable = true;
                div.dataset.id = op.id;
                div.innerHTML = `
                    <div class="d-flex justify-content-between">
                        <div><strong>${escapeHtml(op.title)}</strong><br><small class="text-muted">${escapeHtml(op.company_name || '')}</small></div>
                        <div class="text-end"><span class="amount-badge">€${Number(op.amount||0).toFixed(2)}</span></div>
                    </div>
                `;
                div.addEventListener('dragstart', e => {
                    e.dataTransfer.setData('text/plain', op.id);
                    e.dataTransfer.effectAllowed = 'move';
                    div.classList.add('dragging');
                });
                div.addEventListener('dragend', e => {
                    div.classList.remove('dragging');
                });
                return div;
            }
        
            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
            }
        
            function clearContainers() {
                document.querySelectorAll('.opportunities-container').forEach(c => c.innerHTML = '');
            }
        
            function render(data) {
                if (!data || !data.stages) return;
                clearContainers();
                for (const stage in data.stages) {
                    const list = data.stages[stage];
                    const container = document.querySelector(`.opportunities-container[data-stage="${stage}"]`);
                    if (!container) continue;
                    list.forEach(op => container.appendChild(createCard(op)));
                    // update counts/values if present
                    const meta = data.meta && data.meta[stage] ? data.meta[stage] : {count: list.length, sum: list.reduce((s,i)=>s+Number(i.amount||0),0)};
                    const countEl = document.getElementById(`${stage}-count`);
                    const valueEl = document.getElementById(`${stage}-value`);
                    if (countEl) countEl.textContent = `${meta.count} opportunité${meta.count>1?'s':''}`;
                    if (valueEl) valueEl.textContent = `€${Number(meta.sum||0).toFixed(2)}`;
                }
                attachDropHandlers();
            }
        
            function attachDropHandlers() {
                document.querySelectorAll('.opportunities-container').forEach(container => {
                    container.addEventListener('dragover', e => {
                        e.preventDefault();
                        e.dataTransfer.dropEffect = 'move';
                        container.classList.add('drop-target');
                    });
                    container.addEventListener('dragleave', e => {
                        container.classList.remove('drop-target');
                    });
                    container.addEventListener('drop', e => {
                        e.preventDefault();
                        container.classList.remove('drop-target');
                        const id = e.dataTransfer.getData('text/plain');
                        const stage = container.dataset.stage;
                        if (!id || !stage) return;
                        // optimistic move
                        const card = document.querySelector(`.opportunity-card[data-id="${id}"]`);
                        if (card) container.appendChild(card);
                        // call API
                        safeFetch(API_UPDATE, {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({id: id, stage: stage})
                        }).then(res => {
                            if (!res || !res.success) {
                                // on error, reload full data
                                load();
                            } else {
                                // refresh counts after short delay
                                setTimeout(load, 500);
                            }
                        }).catch(() => load());
                    });
                });
            }
        
            let pollingTimer = null;
            function load() {
                safeFetch(API_LIST).then(res => {
                    if (res && res.success) render(res);
                }).catch(err => console.error('opps load error', err));
            }
        
            load();
            if (pollingTimer) clearInterval(pollingTimer);
            pollingTimer = setInterval(load, POLL_INTERVAL);
        
        });
    </script>
</body>
</html>