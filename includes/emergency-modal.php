<?php
// Load language manager if not already loaded
if (!isset($language_manager)) {
    require_once __DIR__ . '/../languages.php';
    $language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
    $language_manager = new LanguageManager($language);
}
?>

<!-- Emergency Contacts Modal -->
  <div class="modal fade emergency-modal" id="emergencyModal" tabindex="-1" aria-labelledby="emergencyModalLabel" role="dialog" data-bs-backdrop="static" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h2 class="modal-title h5" id="emergencyModalLabel">
            <i class="fas fa-phone-alt me-2" aria-hidden="true"></i>
            <?php echo $language_manager->get('emergency_modal_title'); ?>
          </h2>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?php echo $language_manager->get('close_emergency_contacts'); ?>" onclick="console.log('Close button clicked')"></button>
        </div>
        <div class="modal-body">
          <!-- Emergency Contacts Content -->
          <div class="emergency-contacts">
            <div class="alert alert-danger mb-4">
              <h3 class="alert-heading h6">
                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                <?php echo $language_manager->get('emergency_crisis_warning'); ?>
              </h3>
              <p class="mb-0"><?php echo $language_manager->get('emergency_call_911'); ?></p>
            </div>

            <div class="row g-3">
              <!-- National Crisis Support -->
              <div class="col-md-6">
                <div class="card border-danger">
                  <div class="card-header bg-danger text-white">
                    <h4 class="card-title h6 mb-0">
                      <i class="fas fa-phone-alt me-2" aria-hidden="true"></i>
                      <?php echo $language_manager->get('crisis_support'); ?>
                    </h4>
                  </div>
                  <div class="card-body">
                    <div class="emergency-contact-item mb-3">
                      <h5 class="h6 text-danger"><?php echo $language_manager->get('suicide_prevention_lifeline'); ?></h5>
                      <p class="h4 mb-1">
                        <a href="tel:988" class="phone-number text-decoration-none">988</a>
                      </p>
                      <p class="small text-muted mb-2"><?php echo $language_manager->get('lifeline_description'); ?></p>
                      <p class="small">
                        <strong><?php echo $language_manager->get('text'); ?>:</strong> <?php echo $language_manager->get('text_hello_to'); ?> <a href="sms:741741" class="text-decoration-none">741741</a>
                      </p>
                    </div>
                    
                    <div class="emergency-contact-item">
                      <h5 class="h6 text-danger"><?php echo $language_manager->get('national_parent_helpline'); ?></h5>
                      <p class="h5 mb-1">
                        <a href="tel:1-855-427-2736" class="phone-number text-decoration-none">1-855-427-2736</a>
                      </p>
                      <p class="small text-muted"><?php echo $language_manager->get('parent_helpline_description'); ?></p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Disability Rights & Legal -->
              <div class="col-md-6">
                <div class="card border-warning">
                  <div class="card-header bg-warning text-dark">
                    <h4 class="card-title h6 mb-0">
                      <i class="fas fa-balance-scale me-2" aria-hidden="true"></i>
                      <?php echo $language_manager->get('disability_rights_legal'); ?>
                    </h4>
                  </div>
                  <div class="card-body">
                    <div class="emergency-contact-item mb-3">
                      <h5 class="h6 text-warning"><?php echo $language_manager->get('national_disability_rights'); ?></h5>
                      <p class="h5 mb-1">
                        <a href="tel:202-408-9514" class="phone-number text-decoration-none">202-408-9514</a>
                      </p>
                      <p class="small text-muted mb-2"><?php echo $language_manager->get('disability_rights_description'); ?></p>
                      <p class="small">
                        <a href="https://www.ndrn.org/find-your-agency/" target="_blank" rel="noopener" class="text-decoration-none">
                          <?php echo $language_manager->get('find_local_office'); ?> →
                        </a>
                      </p>
                    </div>
                    
                    <div class="emergency-contact-item">
                      <h5 class="h6 text-warning"><?php echo $language_manager->get('special_ed_legal_help'); ?></h5>
                      <p class="h5 mb-1">
                        <a href="tel:844-426-7222" class="phone-number text-decoration-none">844-426-7222</a>
                      </p>
                      <p class="small text-muted"><?php echo $language_manager->get('copaa_description'); ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4 p-3 bg-light rounded">
              <h4 class="h6 mb-2">
                <i class="fas fa-heart me-2 text-primary" aria-hidden="true"></i>
                <?php echo $language_manager->get('remember'); ?>:
              </h4>
              <ul class="small mb-0">
                <li><?php echo $language_manager->get('remember_advocate'); ?></li>
                <li><?php echo $language_manager->get('remember_ask_help'); ?></li>
                <li><?php echo $language_manager->get('remember_document'); ?></li>
                <li><?php echo $language_manager->get('remember_bring_advocate'); ?></li>
                <li><?php echo $language_manager->get('remember_self_care'); ?></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="console.log('Footer close button clicked')"><?php echo $language_manager->get('close'); ?></button>
        </div>
      </div>
    </div>
  </div>