<?php
// Load language manager if not already loaded
if (!isset($language_manager)) {
    require_once __DIR__ . '/../languages.php';
    $language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
    $language_manager = new LanguageManager($language);
}

// Get language-appropriate prompts
$prompts = $language_manager->getSamplePrompts();
?>

<aside class="col-lg-4" role="complementary" aria-label="<?php echo $language_manager->get('suggested_questions'); ?>">
        <div id="promptSidebar" class="card shadow-sm h-100 prompt-sidebar">
          <div class="card-header bg-white">
            <h3 class="h5 mb-0 text-purple">
              <i class="fas fa-lightbulb me-2" aria-hidden="true"></i>
              <?php echo $language_manager->get('prompts_title'); ?>
            </h3>
            <p class="small text-muted mb-0"><?php echo $language_manager->get('prompts_subtitle'); ?></p>
          </div>
          <ul id="promptList" class="list-group list-group-flush" role="list">
            <?php foreach ($prompts as $index => $prompt): ?>
            <li class="list-group-item list-group-item-action" 
                data-prompt="<?php echo htmlspecialchars($prompt['prompt'] ?? ''); ?>" 
                role="listitem" 
                tabindex="0"
                aria-label="<?php echo htmlspecialchars($prompt['prompt'] ?? ''); ?>">
              <i class="fas fa-<?php echo $index === 5 ? 'exclamation-triangle text-warning' : 'question-circle text-purple'; ?> me-2" aria-hidden="true"></i>
              <span><?php echo htmlspecialchars($prompt['prompt'] ?? ''); ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
          
          <!-- Emergency Contact Quick Access -->
          <div class="card-footer bg-light">
            <h4 class="h6 text-danger mb-2">
              <i class="fas fa-exclamation-circle me-2" aria-hidden="true"></i>
              <?php echo $language_manager->get('need_immediate_help'); ?>
            </h4>
            <p class="small text-muted mb-2">
              <?php echo $language_manager->get('crisis_support_desc'); ?>
            </p>
            <div class="d-grid">
              <button class="btn btn-outline-danger btn-sm" onclick="showEmergencyModal()" 
                      aria-label="<?php echo $language_manager->get('emergency_contacts_aria'); ?>">
                <i class="fas fa-phone-alt me-2" aria-hidden="true"></i>
                <?php echo $language_manager->get('emergency_contacts'); ?>
              </button>
            </div>
          </div>
        </div>
      </aside>