<?php
require_once 'includes/mission_add.php';

$page_title = "Nouvelle visite immobilière - DRN (Dubai)";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-building text-primary"></i> Nouvelle visite immobilière</h1>
                    <a href="missions.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Informations de la visite</h6>
                            </div>
                            <div class="card-body">
                                <form method="post" action="./includes/mission_add.php" id="mission-form" novalidate autocomplete="off">
                                    <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token'] ?? ''); ?>">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="mission_name" class="form-label">Nom de la visite *</label>
                                            <input type="text" name="name" id="mission_name" class="form-control" required maxlength="255" value="" placeholder="ex: Visite - Dubai Marina 2BR">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="folder_id" class="form-label">Dossier</label>
                                            <select name="folder_id" id="folder_id" class="form-select">
                                                <option value="">Sélectionner un dossier</option>
                                                <?php foreach ($folders as $f): ?>
                                                    <option value="<?php echo intval($f['id']); ?>"><?php echo h($f['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="property_id" class="form-label">Bien (catalogue)</label>
                                            <select name="property_id" id="property_id" class="form-select">
                                                <option value="">-- Aucun --</option>
                                                <?php foreach (($properties ?? []) as $p): ?>
                                                    <?php
                                                        $label = $p['name'];
                                                        if (!empty($p['community'])) { $label .= ' · '.$p['community']; }
                                                        if (!empty($p['building'])) { $label .= ' · '.$p['building']; }
                                                        if (!empty($p['unit_ref'])) { $label .= ' · '.$p['unit_ref']; }
                                                    ?>
                                                    <option value="<?php echo intval($p['id']); ?>"><?php echo h($label); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text">Sélectionnez un bien si présent dans le catalogue `properties`.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label">Date de début (optionnel)</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control" value="">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label">Date de fin (optionnel)</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control" value="">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="status_id" class="form-label">Statut</label>
                                            <select name="status_id" id="status_id" class="form-select">
                                                <option value="">-- Choisir --</option>
                                                <?php foreach ($statuses as $s): ?>
                                                    <option value="<?php echo intval($s['id']); ?>"><?php echo h($s['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="assigned_to" class="form-label">Assigné à</label>
                                            <select name="assigned_to" id="assigned_to" class="form-select">
                                                <option value="">-- Aucun --</option>
                                                <?php foreach ($users as $u): ?>
                                                    <option value="<?php echo intval($u['id']); ?>" <?php if(isset($assigned_to) && $assigned_to == $u['id']) echo 'selected'; ?>>
                                                        <?php echo h($u['username']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label for="description" class="form-label">Notes (préférences client, détails logement, accès, etc.)</label>
                                            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Ex: Client préfère Marina/Palm, budget 2M AED, 2 chambres, étage élevé"></textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="departure" class="form-label">Point de rencontre</label>
                                            <input type="text" name="departure" id="departure" class="form-control" maxlength="255" value="" placeholder="Ex: Lobby - Emaar Beachfront Tower A">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="arrival" class="form-label">Bien (adresse)</label>
                                            <input type="text" name="arrival" id="arrival" class="form-control" maxlength="255" value="" placeholder="Ex: Dubai Marina, Marina Gate 1, Apt 2304">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="datetime" class="form-label">Date/Heure de visite</label>
                                            <input type="datetime-local" name="datetime" id="datetime" class="form-control" value="">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="driver" class="form-label">Agent immobilier</label>
                                            <input type="text" name="driver" id="driver" class="form-control" maxlength="255" value="" placeholder="Ex: Sarah Al Maktoum">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="vehicle" class="form-label">Unité (appartement/villa)</label>
                                            <input type="text" name="vehicle" id="vehicle" class="form-control" maxlength="255" value="" placeholder="Ex: Apt 2304 / Villa 12">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="prix" class="form-label">Budget (AED)</label>
                                            <input type="number" name="prix" id="prix" class="form-control" step="0.01" value="0.00" placeholder="Ex: 2000000">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="quantity" class="form-label">Quantité</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" value="">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="type" class="form-label">Type</label>
                                            <input type="text" name="type" id="type" class="form-control" maxlength="50" value="viewing" placeholder="Ex: viewing, inspection, handover">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="project" class="form-label">Programme / Projet</label>
                                            <input type="text" name="project" id="project" class="form-control" maxlength="255" value="" placeholder="Ex: Emaar Beachfront">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="product" class="form-label">Type de bien</label>
                                            <input type="text" name="product" id="product" class="form-control" maxlength="255" value="" placeholder="Ex: Apartment 2BR, Townhouse, Villa">
                                        </div>

                                        <div class="mt-4 text-end">
                                            <button type="reset" class="btn btn-secondary me-2">Annuler</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Enregistrer
                                            </button>
                                        </div>

                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Aide</h6>
                            </div>
                            <div class="card-body">
                                <h6>Conseils pour une visite immobilière :</h6>
                                <ul class="small">
                                    <li>Le nom de la visite est obligatoire</li>
                                    <li>Associez la visite à un dossier si possible</li>
                                    <li>Précisez le bien (adresse) et le point de rencontre</li>
                                    <li>Planifiez la date/heure et l'agent en charge</li>
                                    <li>Ajoutez des notes: budget AED, nombre de chambres, préférences</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<script>
// Petites aides de saisie: si nom vide, proposer à partir de l'adresse
document.getElementById('mission-form').addEventListener('submit', function(e){
    var name = document.getElementById('mission_name').value.trim();
    if(!name){
        const addr = (document.getElementById('arrival').value || '').trim();
        if(addr){
            document.getElementById('mission_name').value = 'Visite - ' + addr;
        } else {
            e.preventDefault();
            alert('Le nom de la visite est requis.');
            document.getElementById('mission_name').focus();
            return false;
        }
    }
    return true;
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
