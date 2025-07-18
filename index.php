<?php
// Enable error reporting for development (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment if available
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Load language manager
require_once __DIR__ . '/languages.php';

// Get language preference with enhanced detection
$language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
$language_manager = new LanguageManager($language);

// Set language cookie if changed
if (isset($_GET['lang']) && $_GET['lang'] !== ($_COOKIE['guideai_language'] ?? 'en')) {
    setcookie('guideai_language', $_GET['lang'], time() + (86400 * 30), '/');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
  <!-- Google tag (gtag.js) - Load asynchronously -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-YKTVEZXCZ1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-YKTVEZXCZ1');
  </script>
  
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  
  <!-- Enhanced SEO and Social Meta Tags -->
  <meta name="robots" content="index, follow">
  <meta name="description" content="<?php echo $language_manager->get('meta_description'); ?>">
  <meta name="keywords" content="IEP, special education, ADHD, autism, learning disabilities, parent advocacy, school accommodations, IDEA rights">
  <meta name="author" content="GuideAI Team">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://getguideai.com/">
  <meta property="og:title" content="<?php echo $language_manager->get('og_title'); ?>">
  <meta property="og:description" content="<?php echo $language_manager->get('og_description'); ?>">
  <meta property="og:image" content="https://getguideai.com/assets/images/social-preview.png">
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://getguideai.com">
  <meta property="twitter:title" content="<?php echo $language_manager->get('twitter_title'); ?>">
  <meta property="twitter:description" content="<?php echo $language_manager->get('twitter_description'); ?>">
  <meta property="twitter:image" content="https://getguideai.com/assets/images/social-preview.png">
  
  <title><?php echo $language_manager->get('page_title'); ?></title>
  <link rel="icon" href="favicon.ico" />
  <link rel="manifest" href="manifest.json">
  
  <!-- Preload critical resources -->
  <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/assets/css/guideai.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  
  <!-- Fallback for browsers that don't support preload -->
  <noscript>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    

  </noscript>
  
  <!-- Non-critical CSS loaded asynchronously -->
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  
  <!-- Skip to main content for screen readers -->
  <a href="#main-content" class="skip-link"><?php echo $language_manager->get('skip_to_main'); ?></a>
  <link rel="stylesheet" href="assets/css/guideai.min.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light text-dark">

	<?php include 'includes/header.php';?>
	
	<?php include 'includes/hero-index.php';?>
  

  <!-- MAIN CONTENT -->
  <main id="main-content" class="flex-grow-1 container py-4" role="main">
    <div class="row g-4">
      <!-- Chat Column -->
      <div class="col-lg-8 d-flex flex-column">
        
        <!-- Enhanced Chat Card -->
        <div class="card shadow-sm d-flex flex-column">
          <!-- Card Header with Input Area at Top -->
          <div class="card-header bg-white border-bottom">
            <!-- Enhanced Input Area -->
            <form id="chatForm" role="search" aria-label="<?php echo $language_manager->get('ask_guideai_question'); ?>">
              <div class="input-wrapper">
                <label for="userInput" class="sr-only"><?php echo $language_manager->get('input_label'); ?></label>
                <textarea 
                  id="userInput"
                  name="user_input" 
                  class="message-input form-control" 
                  placeholder="<?php echo $language_manager->get('input_placeholder'); ?>"
                  aria-describedby="inputHelp inputInstructions"
                  maxlength="500"
                  rows="1"
                  required
                ></textarea>
              </div>
              
              <!-- Action Buttons Row -->
              <div class="action-buttons mt-2 d-flex gap-2 justify-content-between">
                <!-- Left side: Clear and Print -->
                <div class="d-flex gap-2">
                  <button 
                    id="clearBtn"
                    type="button" 
                    class="input-btn send-btn"
                    title="<?php echo $language_manager->get('clear_conversation'); ?>" 
                    aria-label="<?php echo $language_manager->get('clear_conversation'); ?>"
                  >
                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                    <span class="sr-only"><?php echo $language_manager->get('clear_chat'); ?></span>
                  </button>
                  <button 
                    id="printBtn"
                    type="button" 
                    class="input-btn send-btn"
                    title="<?php echo $language_manager->get('print_conversation'); ?>" 
                    aria-label="<?php echo $language_manager->get('print_conversation'); ?>"
                  >
                    <i class="fas fa-print" aria-hidden="true"></i>
                    <span class="sr-only"><?php echo $language_manager->get('print_chat'); ?></span>
                  </button>
                </div>
                
                <!-- Right side: Voice and Send -->
                <div class="d-flex gap-2">
                  <button 
                    id="voiceBtn"
                    type="button" 
                    class="input-btn voice-btn"
                    title="<?php echo $language_manager->get('use_voice_input'); ?>"
                    aria-label="<?php echo $language_manager->get('click_to_speak'); ?>"
                  >
                    <i class="fas fa-microphone" aria-hidden="true"></i>
                    <span class="sr-only"><?php echo $language_manager->get('voice_input'); ?></span>
                  </button>
                  <button 
                    type="submit" 
                    class="input-btn send-btn"
                    aria-label="<?php echo $language_manager->get('send_your_question'); ?>"
                  >
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    <span class="sr-only"><?php echo $language_manager->get('send_button'); ?></span>
                  </button>
                </div>
              </div>
              
              <div class="input-footer d-flex justify-content-end">
                <span id="charCount" class="char-counter">0/500</span>
              </div>
              
              <div id="inputInstructions" class="sr-only">
                <?php echo $language_manager->get('input_instructions'); ?>
              </div>
            </form>
          </div>
          
          <!-- Chat Messages Container -->
          <div id="chatMessages" 
               class="chat-container flex-grow-1" 
               role="log" 
               aria-live="polite" 
               aria-label="<?php echo $language_manager->get('conversation_with_guideai'); ?>"
               data-print-date=""
               data-print-time="">
            <!-- Welcome message -->
            <div class="chat-message bot">
              <div class="alert alert-primary border-0" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);">
                <h5 class="alert-heading">
                  Welcome to GuideAI
                </h5>
                <p class="mb-3">
                  I understand that navigating your child's special education journey can feel overwhelming at times. Whether you're preparing for an IEP meeting, seeking accommodations, or just need someone to talk to, I'm here to support you with patience, understanding, and practical guidance.
                </p>
                <div class="row g-3 mb-3">
                  <div class="col-md-6">
                    <div>
                      <strong>Your Rights Matter</strong><br>
                      <small class="text-muted">Understand your parental rights and advocacy options</small>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div>
                      <strong>Practical Support</strong><br>
                      <small class="text-muted">Get actionable advice and step-by-step guidance</small>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div>
                      <strong>Family-Centered</strong><br>
                      <small class="text-muted">Focus on what works best for your unique family</small>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div>
                      <strong>Clear Language</strong><br>
                      <small class="text-muted">No jargon - just clear, understandable guidance</small>
                    </div>
                  </div>
                </div>
                <hr>
                <p class="mb-0">
                  <strong>💡 Getting Started:</strong> Ask me anything about IEPs, accommodations, your rights, or any special education topic. I'm here to listen and help you advocate for your child's success.
                </p>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Prompts Sidebar -->
      <?php include 'includes/prompt-sidebar.php';?>
    </div>
  </main>

  <?php include 'includes/iep-prep-tool-ad.php';?>

  <?php include 'includes/footer.php';?>

  <?php include 'includes/emergency-modal.php';?>

  <!-- Screen reader live region for announcements -->
  <div id="sr-live-region" class="sr-only" aria-live="polite" aria-atomic="true"></div>

  <!-- JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- GuideAI Scripts -->
  <script src="assets/js/guideai.min.js?v=<?php echo time(); ?>"></script>
  <script src="assets/js/accessibility.min.js"></script>
  
  <!-- Critical initialization script - Load immediately -->
  <script>
    // Prevent form submission to avoid page refresh
    document.addEventListener('DOMContentLoaded', function() {
      // Let GuideAI handle form submission - no need for duplicate listeners
      console.log('DOM ready, GuideAI will handle form submission');
    });
    // Global error handler to catch syntax errors
    window.addEventListener('error', function(e) {
      console.error('Global error caught:', {
        message: e.message,
        filename: e.filename,
        lineno: e.lineno,
        colno: e.colno,
        error: e.error
      });
    });
    
    // Safe URL parameter parsing to prevent syntax errors
    function safeGetUrlParam(name) {
      try {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
      } catch (error) {
        console.warn('URL parameter parsing error:', error);
        return null;
      }
    }
    
    // Critical initialization that needs to run immediately
    window.currentLanguage = '<?php echo $language; ?>';
    
    // Export language manager to JavaScript
    window.languageManager = {
        get: function(key, params = []) {
            // Fallback translations for critical keys
            const fallbackTranslations = {
                'en': {
                    'enter_question': 'Please enter your question',
                    'question_too_long': 'Question is too long. Please keep it under 500 characters.',
                    'approaching_char_limit': 'Approaching character limit',
                    'char_limit_reached': 'Character limit reached',
                    'voice_not_available': 'Voice input is not available in your browser',
                    'listening': 'Listening...',
                    'input_placeholder': 'IEPs, accommodations, rights...ask us anything.',
                    'input_tip': 'Tip: Be specific about your child\'s age, disability, or situation for better help'
                },
                'es': {
                    'enter_question': 'Por favor ingresa tu pregunta',
                    'question_too_long': 'La pregunta es demasiado larga. Por favor manténla bajo 500 caracteres.',
                    'approaching_char_limit': 'Acercándose al límite de caracteres',
                    'char_limit_reached': 'Límite de caracteres alcanzado',
                    'voice_not_available': 'La entrada de voz no está disponible en tu navegador',
                    'listening': 'Escuchando...',
                    'input_placeholder': 'Pregunta sobre IEP, adaptaciones, tus derechos, o cualquier tema de educación especial...',
                    'input_tip': 'Consejo: Sé específico sobre la edad, discapacidad o situación de tu hijo para obtener mejor ayuda'
                }
            };
            
            const currentLang = window.currentLanguage || 'en';
            const translations = fallbackTranslations[currentLang] || fallbackTranslations['en'];
            
            return translations[key] || key;
        },
        getCurrentLanguage: function() {
            return window.currentLanguage || 'en';
        }
    };
    
    // Emergency modal function with better error handling
    function showEmergencyModal() {
      console.log('showEmergencyModal called');
      const modal = document.getElementById('emergencyModal');
      if (modal) {
        console.log('Emergency modal found, showing...');
        try {
          // Try Bootstrap modal first
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
          } else {
            // Fallback: show modal manually
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
          }
        } catch (error) {
          console.error('Error showing modal:', error);
          // Final fallback: show modal manually
          modal.style.display = 'block';
          modal.classList.add('show');
          document.body.classList.add('modal-open');
        }
      } else {
        console.error('Emergency modal not found!');
        alert('Emergency contacts are loading. Please try again in a moment.');
      }
    }
    
    // Test function to verify modal exists
    function testEmergencyModal() {
      const modal = document.getElementById('emergencyModal');
      console.log('Modal element:', modal);
      if (modal) {
        console.log('Modal classes:', modal.className);
        console.log('Modal style:', modal.style.display);
      }
    }
    
    // Make functions available globally immediately
    window.showEmergencyModal = showEmergencyModal;
    window.testEmergencyModal = testEmergencyModal;
    window.safeGetUrlParam = safeGetUrlParam;
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize language switcher
      if (typeof initializeLanguageSystem === 'function') {
        initializeLanguageSystem();
      }
      
      // Initialize accessibility system
      if (typeof GuideAIAccessibility !== 'undefined') {
        console.log('🚀 Initializing GuideAI Accessibility System...');
        window.guideAIAccessibility = new GuideAIAccessibility();
        console.log('✅ GuideAI Accessibility System initialized');
      } else {
        console.warn('⚠️ GuideAIAccessibility class not found');
      }
      
          // Initialize GuideAI chat system
    if (typeof GuideAI !== 'undefined') {
      console.log('🚀 Initializing GuideAI Chat System...');
      window.guideAI = new GuideAI();
      console.log('✅ GuideAI Chat System initialized');
    } else {
      console.warn('⚠️ GuideAI class not found');
      
      // Fallback: prevent form submission if GuideAI fails to load
      const chatForm = document.getElementById('chatForm');
      if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
          e.preventDefault();
          e.stopPropagation();
          console.log('GuideAI not available, preventing form submission');
          return false;
        });
      }
    }
      
      // Mark as initialized for diagnostic purposes
      window.GuideAIInitialized = true;
      
      // Test modal on page load
      setTimeout(testEmergencyModal, 1000);
      
      // Set up emergency modal event listeners
      const emergencyButtons = document.querySelectorAll('#drawerEmergencyBtn, [onclick*="showEmergencyModal"]');
      emergencyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          showEmergencyModal();
        });
      });
      
      // Set up modal close event listeners
      const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"], .btn-close');
      closeButtons.forEach(button => {
        button.addEventListener('click', function() {
          const modal = this.closest('.modal');
          if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            
            // Remove backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
              backdrop.remove();
            }
          }
        });
      });
    });
  </script>
</body>
</html>