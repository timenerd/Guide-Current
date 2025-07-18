<?php
// Load language manager
require_once 'languages.php';
$language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
$language_manager = new LanguageManager($language);
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-YKTVEZXCZ1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-YKTVEZXCZ1');
  </script>
  
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $language_manager->get('about_page_title'); ?></title>
  <meta name="description" content="<?php echo $language_manager->get('about_meta_description'); ?>">
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
  
  <style>
    /* Embedded purple theme styles */
    :root {
      --purple: #6f42c1;
      --light-bg: #ffffff;
      --mid-bg: #f4f4f8;
      --text-dark: #1e1e1e;
    }

    .text-purple { color: var(--purple) !important; }
    .bg-purple { background-color: var(--purple) !important; }

    .nav-guideai {
      background-color: var(--purple);
    }
    .nav-guideai .navbar-brand {
      color: var(--light-bg);
      font-weight: bold;
    }
    .nav-guideai .nav-link {
      color: var(--mid-bg) !important;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--light-bg);
      color: var(--text-dark);
    }

    .pulse-heart {
      animation: pulse-heart 2s infinite;
    }

    @keyframes pulse-heart {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    /* Prevent horizontal overflow */
    html, body {
      overflow-x: hidden !important;
    }

    /* Mobile responsive improvements */
    @media (max-width: 768px) {
      .display-4 {
        font-size: 2.5rem;
      }
      
      .lead {
        font-size: 1.1rem;
      }
      
      .fs-5 {
        font-size: 1rem !important;
      }
      
      .card-body {
        padding: 1.25rem;
      }
      
      .p-4.p-md-5 {
        padding: 1.5rem !important;
      }
    }

    @media (max-width: 480px) {
      .display-4 {
        font-size: 2rem;
      }
      
      .lead {
        font-size: 1rem;
      }
      
      .card-body {
        padding: 1rem;
      }
      
      .p-4.p-md-5 {
        padding: 1rem !important;
      }
      
      .fa-2x {
        font-size: 1.5em;
      }
    }
  </style>
    <link rel="stylesheet" href="assets/css/guideai.min.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-white text-dark">

<?php include 'includes/header.php'; ?>

<main class="flex-grow-1">
  <!-- HERO SECTION -->
  <section class="text-center py-3 py-md-5 bg-light border-bottom overflow-hidden animate__animated animate__fadeInDown">
    <div class="container">
      <i class="fas fa-heart fa-3x text-danger mb-3 pulse-heart" aria-hidden="true"></i>
      <h1 class="display-4 fw-bold text-purple mb-3"><?php echo $language_manager->get('about_title'); ?></h1>
      <p class="lead text-secondary"><?php echo $language_manager->get('about_tagline'); ?></p>
    </div>
  </section>

  <!-- MISSION STATEMENT -->
  <section class="py-4 py-md-5 overflow-hidden">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-10 animate__animated animate__fadeInUp">
          <div class="p-4 p-md-5 bg-white shadow-lg rounded border">
            <h2 class="h4 text-purple fw-bold mb-4">
              <i class="fas fa-compass me-2" aria-hidden="true"></i>
              <?php echo $language_manager->get('mission_statement'); ?>
            </h2>
            <p class="fs-5 mb-4">
              <?php echo $language_manager->get('mission_paragraph_1'); ?>
            </p>
            <p class="fs-5 mb-0">
              <?php echo $language_manager->get('mission_paragraph_2'); ?>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURE CARDS -->
  <section class="py-4 py-md-5 bg-light border-top overflow-hidden">
    <div class="container">
      <div class="row text-center mb-4 mb-md-5">
        <div class="col">
          <h2 class="fw-bold text-purple"><?php echo $language_manager->get('what_makes_unique'); ?></h2>
          <p class="text-muted"><?php echo $language_manager->get('built_by_experience'); ?></p>
        </div>
      </div>
      <div class="row g-4">

        <div class="col-md-4">
          <div class="card border-0 shadow h-100 animate__animated animate__fadeInLeft overflow-hidden">
            <div class="card-body text-center">
              <div class="text-purple mb-3"><i class="fas fa-users fa-2x"></i></div>
              <h5 class="fw-bold mb-3"><?php echo $language_manager->get('built_by_parents'); ?></h5>
              <p class="mb-0">
                <?php echo $language_manager->get('built_by_parents_desc'); ?>
              </p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow h-100 animate__animated animate__fadeInUp overflow-hidden">
            <div class="card-body text-center">
              <div class="text-purple mb-3"><i class="fas fa-robot fa-2x"></i></div>
              <h5 class="fw-bold mb-3"><?php echo $language_manager->get('intelligent_compassionate'); ?></h5>
              <p class="mb-0">
                <?php echo $language_manager->get('intelligent_compassionate_desc'); ?>
              </p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow h-100 animate__animated animate__fadeInRight overflow-hidden">
            <div class="card-body text-center">
              <div class="text-purple mb-3"><i class="fas fa-toolbox fa-2x"></i></div>
              <h5 class="fw-bold mb-3"><?php echo $language_manager->get('comprehensive_resources'); ?></h5>
              <p class="mb-0">
                <?php echo $language_manager->get('comprehensive_resources_desc'); ?>
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- GUIDE INITIATIVE SECTION -->
  <section class="py-4 py-md-5 overflow-hidden">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card border-purple shadow">
            <div class="card-header bg-purple text-white">
              <h2 class="h4 mb-0">
                <i class="fas fa-star me-2" aria-hidden="true"></i>
                <?php echo $language_manager->get('guide_initiative'); ?>
              </h2>
            </div>
            <div class="card-body p-4">
              <p class="lead mb-4"><?php echo $language_manager->get('guide_initiative_desc'); ?></p>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                      <strong>G</strong>
                    </div>
                    <div>
                      <h6 class="mb-1 text-purple"><?php echo $language_manager->get('guidance'); ?></h6>
                      <p class="mb-0 small"><?php echo $language_manager->get('guidance_desc'); ?></p>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                      <strong>U</strong>
                    </div>
                    <div>
                      <h6 class="mb-1 text-purple"><?php echo $language_manager->get('understanding'); ?></h6>
                      <p class="mb-0 small"><?php echo $language_manager->get('understanding_desc'); ?></p>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                      <strong>I</strong>
                    </div>
                    <div>
                      <h6 class="mb-1 text-purple"><?php echo $language_manager->get('information'); ?></h6>
                      <p class="mb-0 small"><?php echo $language_manager->get('information_desc'); ?></p>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                      <strong>D</strong>
                    </div>
                    <div>
                      <h6 class="mb-1 text-purple"><?php echo $language_manager->get('direction'); ?></h6>
                      <p class="mb-0 small"><?php echo $language_manager->get('direction_desc'); ?></p>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                      <strong>E</strong>
                    </div>
                    <div>
                      <h6 class="mb-1 text-purple"><?php echo $language_manager->get('empowerment'); ?></h6>
                      <p class="mb-0 small"><?php echo $language_manager->get('empowerment_desc'); ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CORE FEATURES SECTION -->
  <section class="py-4 py-md-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold text-purple">Core Features</h2>
        <p class="text-muted">Comprehensive support for your family's journey</p>
      </div>
      
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="card h-100 border-0 shadow">
            <div class="card-body">
              <div class="d-flex align-items-start">
                <div class="text-purple me-3">
                  <i class="fas fa-clipboard-list fa-2x"></i>
                </div>
                <div>
                  <h5 class="card-title text-purple">IEP Prep Tool</h5>
                  <p class="card-text">Interactive tool that walks you through IEP meeting preparation, helps you organize documentation, and identifies meaningful goals for your child.</p>
                  <ul class="small text-muted">
                    <li>Step-by-step meeting preparation</li>
                    <li>Document organization system</li>
                    <li>Goal-setting guidance</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-6">
          <div class="card h-100 border-0 shadow">
            <div class="card-body">
              <div class="d-flex align-items-start">
                <div class="text-purple me-3">
                  <i class="fas fa-lightbulb fa-2x"></i>
                </div>
                <div>
                  <h5 class="card-title text-purple">Smart Prompts</h5>
                  <p class="card-text">Quick-start buttons for common concerns like "How do I request an IEP meeting?" or "What are my rights under IDEA?"</p>
                  <ul class="small text-muted">
                    <li>Common IEP questions</li>
                    <li>Rights and advocacy topics</li>
                    <li>Disability-specific support</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-6">
          <div class="card h-100 border-0 shadow">
            <div class="card-body">
              <div class="d-flex align-items-start">
                <div class="text-purple me-3">
                  <i class="fas fa-map-marker-alt fa-2x"></i>
                </div>
                <div>
                  <h5 class="card-title text-purple">Local Resource Finder</h5>
                  <p class="card-text">Uses location data to recommend relevant government programs, nonprofits, and service providers in your area.</p>
                  <ul class="small text-muted">
                    <li>State-specific resources</li>
                    <li>Local advocacy organizations</li>
                    <li>Emergency contacts</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-6">
          <div class="card h-100 border-0 shadow">
            <div class="card-body">
              <div class="d-flex align-items-start">
                <div class="text-purple me-3">
                  <i class="fas fa-heart fa-2x"></i>
                </div>
                <div>
                  <h5 class="card-title text-purple">Supportive Tone</h5>
                  <p class="card-text">Designed to feel like a knowledgeable peer or guide, not a legal textbook or bureaucratic form.</p>
                  <ul class="small text-muted">
                    <li>Warm, encouraging language</li>
                    <li>Family-focused approach</li>
                    <li>Stress-reducing guidance</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ACCESSIBILITY COMMITMENT -->
  <section class="py-4 py-md-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <div class="p-4 bg-light rounded">
            <i class="fas fa-universal-access fa-3x text-purple mb-3" aria-hidden="true"></i>
            <h2 class="h4 text-purple mb-3">Accessibility Commitment</h2>
            <p class="mb-4">
              GuideAI is designed to be accessible to all families, including those with disabilities. We provide voice input, text-to-speech, high contrast modes, keyboard navigation, and screen reader support.
            </p>
            <div class="row g-3 text-start">
              <div class="col-md-6">
                <i class="fas fa-microphone text-purple me-2"></i>Voice input and speech recognition<br>
                <i class="fas fa-volume-up text-purple me-2"></i>Text-to-speech for all responses<br>
                <i class="fas fa-keyboard text-purple me-2"></i>Full keyboard navigation support
              </div>
              <div class="col-md-6">
                <i class="fas fa-adjust text-purple me-2"></i>High contrast and large font options<br>
                <i class="fas fa-eye text-purple me-2"></i>Dyslexia-friendly font choices<br>
                <i class="fas fa-headphones text-purple me-2"></i>Screen reader optimization
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/emergency-modal.php'; ?>
<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/accessibility.min.js"></script>
<script>
  // Initialize accessibility system
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof GuideAIAccessibility !== 'undefined') {
      new GuideAIAccessibility();
    }
  });
</script>
</body>
</html>