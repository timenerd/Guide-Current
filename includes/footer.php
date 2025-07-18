<?php
// Load language manager if not already loaded
if (!isset($language_manager)) {
    require_once __DIR__ . '/../languages.php';
    $language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
    $language_manager = new LanguageManager($language);
}
?>

<!-- FOOTER -->
  <footer class="bg-purple text-white py-4 mt-auto" role="contentinfo">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-2 mb-md-0">
            <i class="fas fa-code me-2 text-info" aria-hidden="true"></i>
            &copy; <?php echo date('Y'); ?> GuideAI. <?php echo $language_manager->get('footer_built_with'); ?>
          </p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <div class="d-flex justify-content-center justify-content-md-end align-items-center flex-wrap gap-3">
            <a href="about" class="text-white text-decoration-none small">
              <i class="fas fa-info-circle me-1" aria-hidden="true"></i><?php echo $language_manager->get('nav_about'); ?>
            </a>
            <a href="contact" class="text-white text-decoration-none small">
              <i class="fas fa-envelope me-1" aria-hidden="true"></i><?php echo $language_manager->get('nav_contact'); ?>
            </a>
            <a href="roadmap" class="text-white text-decoration-none small">
              <i class="fas fa-map me-1" aria-hidden="true"></i><?php echo $language_manager->get('nav_roadmap'); ?>
            </a>
            <button class="btn btn-link text-white p-0 small" 
                    onclick="showEmergencyModal()"
                    aria-label="<?php echo $language_manager->get('emergency_contacts_aria'); ?>">
              <i class="fas fa-phone-alt me-1" aria-hidden="true"></i><?php echo $language_manager->get('emergency_help'); ?>
            </button>
          </div>
        </div>
      </div>
      <hr class="my-3 opacity-50">
      <div class="row">
        <div class="col-12 text-center">
          <p class="small mb-0 opacity-75">
            <strong><?php echo $language_manager->get('footer_empowering_families'); ?></strong><br>
            <?php echo $language_manager->get('footer_immediate_assistance'); ?>
          </p>
        </div>
      </div>
    </div>
  </footer>