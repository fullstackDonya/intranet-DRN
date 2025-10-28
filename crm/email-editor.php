<?php
require_once __DIR__ . '/includes/verify_subscriptions.php';

$page_title = "Éditeur d'Email";
$customer_id = $_SESSION['customer_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://cdn.tiny.cloud/1/f2fjr1wuldb591g88p0bctbj3sh27xrd5nyt12nbapoe5q1z/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

  <link href="assets/css/custom.css" rel="stylesheet">
  <style>
    .tox .tox-edit-area__iframe { background-color: #ffffff; }
    .tox .tox-toolbar, .tox .tox-toolbar__overflow, .tox .tox-toolbar-overlord { background: #f8fafc; border-bottom: 1px solid #e5e7eb; }
    .tox .tox-tbtn { color: #111827; }
    .tox .tox-tbtn--enabled, .tox .tox-tbtn:active { background: #e5e7eb; }
  </style>
</head>
<body>
<div class="wrapper">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="main-content">
    <div class="container-fluid py-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 m-0">Éditeur d'Email</h1>
        <a href="campaigns-email.php" class="btn btn-outline-secondary">Retour</a>
      </div>

      <div class="row">
        <div class="col-lg-4">
          <div class="card mb-3">
            <div class="card-header">Modèles</div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Type</label>
                <select class="form-select" id="templateType">
                  <option value="">Tous</option>
                  <option value="newsletter">Newsletter</option>
                  <option value="promotional">Promotionnelle</option>
                  <option value="transactional">Transactionnelle</option>
                  <option value="welcome">Bienvenue</option>
                  <option value="automation">Automatisée</option>
                </select>
              </div>
              <div id="templatesList" class="list-group small" style="max-height: 420px; overflow:auto;"></div>
              <div class="d-grid gap-2 mt-3">
                <button class="btn btn-sm btn-primary" id="newTemplateBtn">Nouveau modèle</button>
                <button class="btn btn-sm btn-danger" id="deleteTemplateBtn" disabled>Supprimer</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
              <div class="d-flex gap-2 align-items-center">
                <input class="form-control form-control-sm" id="tplName" placeholder="Nom du modèle" style="width: 240px;">
                <input class="form-control form-control-sm" id="tplSubject" placeholder="Objet" style="width: 280px;">
              </div>
              <div>
                <button class="btn btn-sm btn-success" id="saveTemplateBtn">Enregistrer</button>
              </div>
            </div>
            <div class="card-body">
              <textarea id="editor" rows="18"></textarea>
              <div class="d-flex align-items-center gap-2 mt-3">
                <input type="file" id="imageFile" class="form-control form-control-sm" accept="image/*" style="max-width: 300px;">
                <button class="btn btn-sm btn-outline-primary" id="uploadImageBtn">Uploader l'image</button>
                <span class="text-muted small" id="uploadStatus"></span>
              </div>
              <div class="form-text mt-2">Placeholders pris en charge: {{company_name}}, {{contact_first_name}}, {{unsubscribe_url}}.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- corrected TinyMCE CDN (was typo cdn.tiyn.cloud) -->


<script src="assets/js/email-editor.js"></script>
</body>
</html>