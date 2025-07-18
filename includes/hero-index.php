<?php
// Load language manager if not already loaded
if (!isset($language_manager)) {
    require_once __DIR__ . '/../languages.php';
    $language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
    $language_manager = new LanguageManager($language);
}
?>

<!-- HERO SECTION -->
<section id="hero" class="hero-section text-center py-4" role="banner">
  <div class="hero-background"></div>
  <div class="container position-relative">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-8">
        <!-- Logo and Main Title -->
        <div class="hero-content animate__animated animate__fadeInDown animate__faster">
          <div class="logo-container mb-3">
      <img src="assets/images/logo.webp" 
                 alt="<?php echo $language_manager->get('hero_logo_alt'); ?>" 
                 class="hero-logo"
                 width="320" 
                 height="240"
           loading="eager"
           onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
      
      <!-- Fallback if logo doesn't load -->
            <div class="logo-fallback" style="display: none;">
        <div class="logo-placeholder">
          <span class="fw-bold text-white fs-1">GuideAI</span>
              </div>
        </div>
      </div>
      
          <h1 class="hero-title fw-bold text-white mb-3 animate__animated animate__fadeInUp animate__faster animate__delay-1s" style="font-size: 2rem;">
            <?php echo $language_manager->get('hero_title'); ?>
          </h1>
        </div>
        
        <!-- Feature Highlights -->
        <div class="hero-features animate__animated animate__fadeInUp animate__faster animate__delay-2s">
          <div class="row g-3 justify-content-center">
            <div class="col-6 col-md-3">
              <div class="feature-icon">
                <i class="fas fa-keyboard" aria-hidden="true"></i>
              </div>
              <p class="feature-text"><?php echo $language_manager->get('feature_type_send'); ?></p>
            </div>
            <div class="col-6 col-md-3">
              <div class="feature-icon">
                <i class="fas fa-microphone" aria-hidden="true"></i>
              </div>
              <p class="feature-text"><?php echo $language_manager->get('feature_voice_input'); ?></p>
            </div>
            <div class="col-6 col-md-3">
              <div class="feature-icon">
                <i class="fas fa-volume-up" aria-hidden="true"></i>
              </div>
              <p class="feature-text"><?php echo $language_manager->get('feature_read_aloud'); ?></p>
            </div>
            <div class="col-6 col-md-3">
              <div class="feature-icon">
                <i class="fas fa-universal-access" aria-hidden="true"></i>
              </div>
              <p class="feature-text"><?php echo $language_manager->get('feature_accessible'); ?></p>
            </div>
        </div>
        </div>
        
        <!-- Call to Action -->
        <div class="hero-cta mt-4 animate__animated animate__fadeInUp animate__faster animate__delay-3s">
          <a href="#main-content" class="btn btn-light btn-lg px-4 py-2 fw-semibold">
            <i class="fas fa-arrow-down me-2" aria-hidden="true"></i>
            <?php echo $language_manager->get('hero_cta_button'); ?>
          </a>
        </div>
        </div>
      </div>
    </div>
  </section>