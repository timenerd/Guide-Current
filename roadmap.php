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
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title><?php echo $language_manager->get('roadmap_page_title'); ?></title>
  <meta name="description" content="<?php echo $language_manager->get('roadmap_meta_description'); ?>">
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

    .roadmap-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .roadmap-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(111, 66, 193, 0.15);
    }

    .status-completed {
      background: linear-gradient(45deg, #28a745, #20c997);
      color: white;
    }

    .status-in-progress {
      background: linear-gradient(45deg, #ffc107, #fd7e14);
      color: white;
    }

    .status-upcoming {
      background: linear-gradient(45deg, #6c757d, #adb5bd);
      color: white;
    }

    .roadmap-timeline {
      position: relative;
    }

    .roadmap-timeline::before {
      content: '';
      position: absolute;
      left: 30px;
      top: 0;
      bottom: 0;
      width: 3px;
      background: linear-gradient(to bottom, #28a745, #ffc107, #6c757d);
    }

    .timeline-item {
      position: relative;
      margin-left: 60px;
      margin-bottom: 2rem;
    }

    .timeline-icon {
      position: absolute;
      left: -45px;
      top: 20px;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 0.9rem;
    }

    /* Mobile responsive improvements */
    @media (max-width: 768px) {
      .roadmap-timeline::before {
        left: 20px;
      }

      .timeline-item {
        margin-left: 50px;
        margin-bottom: 1.5rem;
      }

      .timeline-icon {
        left: -35px;
        width: 25px;
        height: 25px;
        font-size: 0.8rem;
      }

      .roadmap-card {
        margin-bottom: 1rem;
      }

      .card-body {
        padding: 1rem;
      }

      .col-sm-6.col-lg-3 {
        margin-bottom: 1rem;
      }

      .bg-white.bg-opacity-25 {
        padding: 0.75rem !important;
      }

      .fa-2x {
        font-size: 1.5em;
      }
    }

    @media (max-width: 480px) {
      .roadmap-timeline::before {
        left: 15px;
      }

      .timeline-item {
        margin-left: 40px;
        margin-bottom: 1rem;
      }

      .timeline-icon {
        left: -30px;
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
      }

      .card-body {
        padding: 0.75rem;
      }

      .bg-white.bg-opacity-25 {
        padding: 0.5rem !important;
      }

      .h5 {
        font-size: 1rem;
      }

      .small {
        font-size: 0.8rem;
      }
    }
  </style>
    <link rel="stylesheet" href="assets/css/guideai.min.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

  <?php include 'includes/header.php'; ?>

  <!-- HERO SECTION -->
  <section class="text-center py-3 py-md-5 bg-purple text-white shadow-sm animate__animated animate__fadeInDown">
    <div class="container px-3 px-md-0">
      <i class="fas fa-route fa-3x mb-3 pulse-heart" aria-hidden="true"></i>
      <h1 class="fw-bold mb-2"><?php echo $language_manager->get('roadmap_title'); ?></h1>
      <p class="lead mb-0"><?php echo $language_manager->get('roadmap_subtitle'); ?></p>
      <div class="mt-3">
        <small class="opacity-75">
          <i class="fas fa-calendar me-2"></i>
          <?php echo $language_manager->get('last_updated'); ?>: <?php echo date('F Y'); ?>
        </small>
      </div>
    </div>
  </section>

  <!-- PROGRESS OVERVIEW -->
  <section class="py-4 bg-light">
    <div class="container">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="card border-0 bg-success text-white h-100">
            <div class="card-body text-center">
              <i class="fas fa-check-circle fa-2x mb-2"></i>
              <h5 class="card-title"><?php echo $language_manager->get('completed_count'); ?></h5>
              <p class="card-text small mb-0"><?php echo $language_manager->get('completed_desc'); ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 bg-warning text-white h-100">
            <div class="card-body text-center">
              <i class="fas fa-cog fa-spin fa-2x mb-2"></i>
              <h5 class="card-title"><?php echo $language_manager->get('in_progress_count'); ?></h5>
              <p class="card-text small mb-0"><?php echo $language_manager->get('in_progress_desc'); ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 bg-secondary text-white h-100">
            <div class="card-body text-center">
              <i class="fas fa-calendar-alt fa-2x mb-2"></i>
              <h5 class="card-title"><?php echo $language_manager->get('planned_count'); ?></h5>
              <p class="card-text small mb-0"><?php echo $language_manager->get('planned_desc'); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ROADMAP TIMELINE -->
  <section class="container flex-grow-1 py-4 px-3 px-md-0">

    <div class="roadmap-timeline">
      
      <!-- Completed Features -->
      <div class="timeline-item">
        <div class="timeline-icon bg-success">
          <i class="fas fa-check"></i>
        </div>
        <div class="card roadmap-card status-completed">
          <div class="card-header">
            <h3 class="h5 mb-0">
              <i class="fas fa-trophy me-2"></i>
              <?php echo $language_manager->get('completed_features'); ?>
            </h3>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-comments fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('core_chat_interface'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('core_chat_interface_desc'); ?></p>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-robot fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('multi_ai_integration'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('multi_ai_integration_desc'); ?></p>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-universal-access fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('accessibility_features'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('accessibility_features_desc'); ?></p>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-lightbulb fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('smart_prompts'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('smart_prompts_desc'); ?></p>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-save fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('chat_history'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('chat_history_desc'); ?></p>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-phone-alt fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('emergency_contacts'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('emergency_contacts_desc'); ?></p>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-mobile-alt fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('mobile_optimization'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('mobile_optimization_desc'); ?></p>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('iep_prep_tool_v1'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('iep_prep_tool_v1_desc'); ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- In Progress Features -->
      <div class="timeline-item">
        <div class="timeline-icon bg-warning">
          <i class="fas fa-cog fa-spin"></i>
        </div>
        <div class="card roadmap-card status-in-progress">
          <div class="card-header">
            <h3 class="h5 mb-0">
              <i class="fas fa-hammer me-2"></i>
              <?php echo $language_manager->get('in_progress_features'); ?>
            </h3>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-users fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('user_testing'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('user_testing_desc'); ?></p>
                  <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-white" style="width: 75%"></div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-link fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('resource_linking'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('resource_linking_desc'); ?></p>
                  <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-white" style="width: 60%"></div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-search fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('seo_optimization'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('seo_optimization_desc'); ?></p>
                  <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-white" style="width: 45%"></div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-chart-line fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('analytics_integration'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('analytics_integration_desc'); ?></p>
                  <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-white" style="width: 30%"></div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-language fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('multi_language_support'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('multi_language_support_desc'); ?></p>
                  <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-white" style="width: 20%"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Features -->
      <div class="timeline-item">
        <div class="timeline-icon bg-secondary">
          <i class="fas fa-calendar"></i>
        </div>
        <div class="card roadmap-card status-upcoming">
          <div class="card-header">
            <h3 class="h5 mb-0">
              <i class="fas fa-rocket me-2"></i>
              <?php echo $language_manager->get('planned_features'); ?>
            </h3>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-file-pdf fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('iep_document_parser'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('iep_document_parser_desc'); ?></p>
                  <small class="text-light"><?php echo $language_manager->get('q2_2025'); ?></small>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-shield-alt fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('hipaa_compliance'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('hipaa_compliance_desc'); ?></p>
                  <small class="text-light"><?php echo $language_manager->get('q2_2025'); ?></small>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-users-cog fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('family_accounts'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('family_accounts_desc'); ?></p>
                  <small class="text-light"><?php echo $language_manager->get('q3_2025'); ?></small>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('meeting_scheduler'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('meeting_scheduler_desc'); ?></p>
                  <small class="text-light"><?php echo $language_manager->get('q3_2025'); ?></small>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-graduation-cap fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('training_modules'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('training_modules_desc'); ?></p>
                  <small class="text-light"><?php echo $language_manager->get('q4_2025'); ?></small>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-network-wired fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('parent_network'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('parent_network_desc'); ?></p>
                  <small class="text-light"><?php echo $language_manager->get('q4_2025'); ?></small>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-bell fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('smart_notifications'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('smart_notifications_desc'); ?></p>
                  <small class="text-light"><?php echo $language_manager->get('q1_2026'); ?></small>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="bg-white bg-opacity-25 p-3 rounded">
                  <i class="fas fa-brain fa-2x mb-2"></i>
                  <h6><?php echo $language_manager->get('advanced_ai_features'); ?></h6>
                  <p class="small mb-0"><?php echo $language_manager->get('advanced_ai_features_desc'); ?></p>
                  <small class="text-light"><?php echo $language_manager->get('q1_2026'); ?></small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </section>

  <!-- FEEDBACK SECTION -->
  <section class="py-4 bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <div class="card border-purple shadow">
            <div class="card-header bg-purple text-white">
              <h3 class="h5 mb-0">
                <i class="fas fa-comment-dots me-2"></i>
                <?php echo $language_manager->get('help_shape_our_future'); ?>
              </h3>
            </div>
            <div class="card-body p-4">
              <p class="mb-3">
                <?php echo $language_manager->get('help_shape_our_future_desc'); ?>
              </p>
              <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="contact" class="btn btn-purple">
                  <i class="fas fa-envelope me-2"></i><?php echo $language_manager->get('send_feedback'); ?>
                </a>
                <a href="/" class="btn btn-outline-purple">
                  <i class="fas fa-comments me-2"></i><?php echo $language_manager->get('chat_with_guideai'); ?>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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
    
    // Add some interactive elements
    document.addEventListener('DOMContentLoaded', () => {
      // Animate progress bars
      const progressBars = document.querySelectorAll('.progress-bar');
      progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
          bar.style.transition = 'width 1s ease-in-out';
          bar.style.width = width;
        }, 500);
      });

      // Add hover effects to timeline items
      const timelineItems = document.querySelectorAll('.timeline-item');
      timelineItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
          item.style.transform = 'translateX(5px)';
        });
        item.addEventListener('mouseleave', () => {
          item.style.transform = 'translateX(0)';
        });
      });
    });
  </script>
</body>
</html>
