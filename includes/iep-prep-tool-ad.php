<?php
// Load language manager if not already loaded
if (!isset($language_manager)) {
    require_once __DIR__ . '/../languages.php';
    $language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
    $language_manager = new LanguageManager($language);
}
?>

<!-- IEP PREP TOOL HIGHLIGHT -->
  <section id="iep-prep-highlight" class="text-center py-5 bg-purple text-white shadow-sm animate__animated animate__fadeInUp" 
           role="region" aria-labelledby="iep-tool-heading">
    <div class="container px-4">
      <i class="fas fa-clipboard-list fa-3x mb-4" aria-hidden="true"></i>
      <h2 id="iep-tool-heading" class="h3 mb-3"><?php echo $language_manager->get('iep_prep_tool'); ?></h2>
      <h3 class="warning"><?php echo $language_manager->get('in_development'); ?></h3>
      <p class="lead mb-2 iep-prep-text"><?php echo $language_manager->get('iep_prep_description'); ?></p>
      <p class="small mb-4 iep-prep-text"><?php echo $language_manager->get('iep_prep_designed_for'); ?></p>
      <a href="https://getguideai.com/ipt/" 
         class="btn btn-light btn-lg px-4" 
         target="_blank" 
         rel="noopener"
         aria-label="<?php echo $language_manager->get('open_iep_tool_aria'); ?>">
        <i class="fas fa-external-link-alt me-2" aria-hidden="true"></i>
        <?php echo $language_manager->get('launch_iep_prep_tool'); ?>
      </a>
    </div>
  </section>