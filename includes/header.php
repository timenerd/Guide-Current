<?php
// Load language manager if not already loaded
if (!isset($language_manager)) {
    require_once __DIR__ . '/../languages.php';
    $language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
    $language_manager = new LanguageManager($language);
}
?>

<!-- HEADER -->
  <header>
    <nav class="navbar navbar-expand-lg nav-guideai" role="navigation" aria-label="<?php echo $language_manager->get('main_navigation'); ?>">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/" aria-label="<?php echo $language_manager->get('guideai_home'); ?>">
          <span class="fw-bold">GuideAI</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="<?php echo $language_manager->get('toggle_navigation'); ?>">
          <span class="navbar-toggler-icon" aria-hidden="true"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="about" aria-label="<?php echo $language_manager->get('nav_about_aria'); ?>">
                <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
                <span><?php echo $language_manager->get('nav_about'); ?></span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="roadmap" aria-label="<?php echo $language_manager->get('nav_roadmap_aria'); ?>">
                <i class="fas fa-map me-1" aria-hidden="true"></i>
                <span><?php echo $language_manager->get('nav_roadmap'); ?></span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="contact" aria-label="<?php echo $language_manager->get('nav_contact_aria'); ?>">
                <i class="fas fa-envelope me-1" aria-hidden="true"></i>
                <span><?php echo $language_manager->get('nav_contact'); ?></span>
              </a>
            </li>
            <!--li class="nav-item">
              <a class="nav-link" href="https://iep-navigator-timenerd17.replit.app/" 
                 target="_blank" rel="noopener" aria-label="<?php echo $language_manager->get('nav_iep_tool_aria'); ?>">
                <i class="fas fa-external-link-alt me-1" aria-hidden="true"></i>
                <span><?php echo $language_manager->get('nav_iep_tool'); ?></span>
              </a>
            </li-->
          </ul>
        </div>
      </div>
    </nav>
  </header>

<!-- Accessibility Button - Static HTML for better mobile reliability -->
<button id="accessibilityToggle" 
        class="accessibility-float-btn" 
        title="Accessibility Options" 
        aria-label="Accessibility Options" 
        aria-expanded="false">
  <i class="fas fa-universal-access" aria-hidden="true"></i>
</button>

<!-- Accessibility Drawer -->
<div id="accessibilityDrawer" 
     class="accessibility-drawer" 
     role="dialog" 
     aria-labelledby="accessibilityTitle" 
     aria-hidden="true">
  <div class="drawer-content">
    <div class="drawer-header">
      <h2 id="accessibilityTitle" class="h6 mb-0">
        <span>Accessibility Options</span>
      </h2>
      <button class="btn-close btn-close-white" 
              id="closeAccessibilityDrawer" 
              aria-label="Close accessibility options">×</button>
    </div>
    
    <div class="drawer-body">
      <!-- Language Selection -->
      <div class="accessibility-section">
        <h3 class="h6 text-primary mb-2">
          <i class="fas fa-language me-2" aria-hidden="true"></i>
          <span>Language</span>
        </h3>
        <div class="btn-group w-100" role="group" aria-label="Language selection">
          <button type="button" class="btn btn-outline-primary btn-sm" data-lang="en">
            🇺🇸 English
          </button>
          <button type="button" class="btn btn-outline-primary btn-sm" data-lang="es">
            🇪🇸 Español
          </button>
        </div>
      </div>

      <!-- Visual Accessibility -->
      <div class="accessibility-section">
        <h3 class="h6 text-primary mb-2">
          <i class="fas fa-eye me-2" aria-hidden="true"></i>
          <span>Visual Options</span>
        </h3>
        <div class="d-grid gap-2">
          <button type="button" class="btn btn-outline-secondary btn-sm" 
                  id="toggleHighContrast" 
                  title="Toggle high contrast" 
                  aria-label="Toggle high contrast">
            <i class="fas fa-adjust me-2" aria-hidden="true"></i>
            <span>High Contrast</span>
          </button>
          <button type="button" class="btn btn-outline-secondary btn-sm" 
                  id="toggleLargeFonts" 
                  title="Toggle large fonts" 
                  aria-label="Toggle large fonts">
            <i class="fas fa-font me-2" aria-hidden="true"></i>
            <span>Large Text</span>
          </button>
          <button type="button" class="btn btn-outline-secondary btn-sm" 
                  id="toggleDyslexiaFont" 
                  title="Toggle dyslexia font" 
                  aria-label="Toggle dyslexia font">
            <i class="fas fa-spell-check me-2" aria-hidden="true"></i>
            <span>Dyslexia Font</span>
          </button>
        </div>
      </div>

      <!-- Audio Accessibility -->
      <div class="accessibility-section">
        <h3 class="h6 text-primary mb-2">
          <i class="fas fa-volume-up me-2" aria-hidden="true"></i>
          <span>Audio Options</span>
        </h3>
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="drawerReadAloudToggle">
          <label class="form-check-label" for="drawerReadAloudToggle">
            Read Aloud
          </label>
        </div>
      </div>

      <!-- Emergency Help -->
      <div class="accessibility-section">
        <button class="btn btn-danger w-100" 
                id="drawerEmergencyBtn" 
                aria-label="Emergency contacts">
          <i class="fas fa-phone-alt me-2" aria-hidden="true"></i>
          <span>Emergency Help</span>
        </button>
      </div>
    </div>
  </div>
</div>