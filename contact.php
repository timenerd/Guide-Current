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
  <title><?php echo $language_manager->get('contact_page_title'); ?></title>
  <meta name="description" content="<?php echo $language_manager->get('contact_meta_description'); ?>">
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

    .btn-purple {
      background-color: var(--purple);
      border-color: var(--purple);
      color: white;
    }
    .btn-purple:hover {
      background-color: #5a31a3;
      border-color: #5a31a3;
      color: white;
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

    /* Mobile responsive improvements */
    @media (max-width: 768px) {
      .display-5 {
        font-size: 2.5rem;
      }
      
      .lead {
        font-size: 1.1rem;
      }
      
      .card-body {
        padding: 1.5rem;
      }
      
      .p-4.p-md-5 {
        padding: 1.5rem !important;
      }
      
      .form-floating {
        margin-bottom: 1rem;
      }
      
      .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
      }
      
      .modal-dialog {
        margin: 0.5rem;
      }
      
      .modal-body {
        padding: 1rem;
      }
    }

    @media (max-width: 480px) {
      .display-5 {
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
      
      .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
      }
      
      .modal-dialog {
        margin: 0.25rem;
      }
      
      .modal-body {
        padding: 0.75rem;
      }
      
      .fa-2x {
        font-size: 1.5em;
      }
      
      .card-title {
        font-size: 1.1rem;
      }
      
      .card-text {
        font-size: 0.9rem;
      }
    }
  </style>
    <link rel="stylesheet" href="assets/css/guideai.min.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

  <?php include 'includes/header.php'; ?>

  <!-- Hero Section -->
  <section class="text-center py-4 py-md-5 bg-purple text-white">
    <div class="container">
      <i class="fas fa-envelope fa-3x mb-3 pulse-heart" aria-hidden="true"></i>
      <h1 class="display-5 fw-bold mb-3"><?php echo $language_manager->get('contact_title'); ?></h1>
      <p class="lead"><?php echo $language_manager->get('contact_subtitle'); ?></p>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="container py-4 py-md-5 flex-grow-1">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm animate__animated animate__fadeInUp">
          <div class="card-header bg-white border-bottom">
            <h2 class="h4 mb-0 text-purple">
              <i class="fas fa-comments me-2"></i><?php echo $language_manager->get('get_in_touch'); ?>
            </h2>
            <p class="text-muted mb-0 mt-2">
              <?php echo $language_manager->get('contact_description'); ?>
            </p>
          </div>
          
          <div class="card-body p-4 p-md-5">
            <?php
            $messageSent = false;
            $errorMessage = '';
            
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $name = strip_tags(trim($_POST['name'] ?? ''));
                $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                $subject = strip_tags(trim($_POST['subject'] ?? ''));
                $message = strip_tags(trim($_POST['message'] ?? ''));

                // Validation
                if (empty($name)) {
                    $errorMessage = $language_manager->get('error_enter_name');
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errorMessage = $language_manager->get('error_enter_valid_email');
                } elseif (empty($subject)) {
                    $errorMessage = $language_manager->get('error_enter_subject');
                } elseif (empty($message)) {
                    $errorMessage = $language_manager->get('error_enter_message');
                } else {
                    // Email configuration
                    $to = "support@guideai.com"; // Update with your actual email
                    $emailSubject = "GuideAI Contact Form: " . $subject;
                    
                    // Create email body
                    $emailBody = "Name: {$name}\n";
                    $emailBody .= "Email: {$email}\n";
                    $emailBody .= "Subject: {$subject}\n\n";
                    $emailBody .= "Message:\n{$message}\n\n";
                    $emailBody .= "---\n";
                    $emailBody .= "Sent from GuideAI Contact Form\n";
                    $emailBody .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
                    $emailBody .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
                    $emailBody .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
                    
                    // Email headers
                    $headers = "From: GuideAI Contact Form <noreply@guideai.com>\r\n";
                    $headers .= "Reply-To: {$email}\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                    $headers .= "X-Mailer: GuideAI Contact Form\r\n";
                    
                    // Send email (you may want to use a more robust email system in production)
                    if (mail($to, $emailSubject, $emailBody, $headers)) {
                        $messageSent = true;
                    } else {
                        $errorMessage = $language_manager->get('error_sending_message');
                    }
                }
            }
            
            if ($messageSent): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong><?php echo $language_manager->get('thank_you'); ?>!</strong> <?php echo $language_manager->get('message_sent_success'); ?>
                </div>
                
                <div class="text-center">
                    <i class="fas fa-heart fa-2x text-purple mb-3"></i>
                    <h5 class="text-purple"><?php echo $language_manager->get('message_received'); ?>!</h5>
                    <p class="text-muted">
                        <?php echo $language_manager->get('message_received_desc'); ?>
                    </p>
                    <a href="/" class="btn btn-purple">
                        <i class="fas fa-arrow-left me-2"></i><?php echo $language_manager->get('return_to_guideai'); ?>
                    </a>
                </div>
                
            <?php else: ?>
                
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>
                
                <form id="contactForm" method="post" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="<?php echo $language_manager->get('your_name'); ?>" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                                <label for="name">
                                    <i class="fas fa-user me-2"></i><?php echo $language_manager->get('your_name'); ?>
                                </label>
                                <div class="invalid-feedback"><?php echo $language_manager->get('please_provide_name'); ?></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="<?php echo $language_manager->get('your_email'); ?>" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i><?php echo $language_manager->get('your_email'); ?>
                                </label>
                                <div class="invalid-feedback"><?php echo $language_manager->get('please_provide_valid_email'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select class="form-select" id="subject" name="subject" required>
                            <option value=""><?php echo $language_manager->get('choose_topic'); ?>...</option>
                            <option value="<?php echo $language_manager->get('general_support'); ?>" <?php echo ($_POST['subject'] ?? '') === $language_manager->get('general_support') ? 'selected' : ''; ?>><?php echo $language_manager->get('general_support'); ?></option>
                            <option value="<?php echo $language_manager->get('technical_issue'); ?>" <?php echo ($_POST['subject'] ?? '') === $language_manager->get('technical_issue') ? 'selected' : ''; ?>><?php echo $language_manager->get('technical_issue'); ?></option>
                            <option value="<?php echo $language_manager->get('feature_request'); ?>" <?php echo ($_POST['subject'] ?? '') === $language_manager->get('feature_request') ? 'selected' : ''; ?>><?php echo $language_manager->get('feature_request'); ?></option>
                            <option value="<?php echo $language_manager->get('iep_guidance'); ?>" <?php echo ($_POST['subject'] ?? '') === $language_manager->get('iep_guidance') ? 'selected' : ''; ?>><?php echo $language_manager->get('iep_guidance'); ?></option>
                            <option value="<?php echo $language_manager->get('accessibility_help'); ?>" <?php echo ($_POST['subject'] ?? '') === $language_manager->get('accessibility_help') ? 'selected' : ''; ?>><?php echo $language_manager->get('accessibility_help'); ?></option>
                            <option value="<?php echo $language_manager->get('partnership_inquiry'); ?>" <?php echo ($_POST['subject'] ?? '') === $language_manager->get('partnership_inquiry') ? 'selected' : ''; ?>><?php echo $language_manager->get('partnership_inquiry'); ?></option>
                            <option value="<?php echo $language_manager->get('other'); ?>" <?php echo ($_POST['subject'] ?? '') === $language_manager->get('other') ? 'selected' : ''; ?>><?php echo $language_manager->get('other'); ?></option>
                        </select>
                        <label for="subject">
                            <i class="fas fa-tag me-2"></i><?php echo $language_manager->get('subject'); ?>
                        </label>
                        <div class="invalid-feedback"><?php echo $language_manager->get('please_choose_topic'); ?></div>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="message" name="message" rows="6" 
                                  placeholder="<?php echo $language_manager->get('your_message'); ?>" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        <label for="message">
                            <i class="fas fa-comment me-2"></i><?php echo $language_manager->get('your_message'); ?>
                        </label>
                        <div class="invalid-feedback"><?php echo $language_manager->get('please_enter_message'); ?></div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-purple btn-lg">
                            <i class="fas fa-paper-plane me-2"></i><?php echo $language_manager->get('send_message'); ?>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Additional Resources Section -->
  <section class="py-4 py-md-5 bg-light">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="text-purple"><?php echo $language_manager->get('other_ways_to_help'); ?></h2>
        <p class="text-muted"><?php echo $language_manager->get('multiple_ways_support'); ?></p>
      </div>
      
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 bg-white h-100 text-center">
            <div class="card-body">
              <i class="fas fa-comments fa-2x text-purple mb-3"></i>
              <h5 class="card-title"><?php echo $language_manager->get('chat_support'); ?></h5>
              <p class="card-text small"><?php echo $language_manager->get('get_immediate_help'); ?></p>
              <a href="/" class="btn btn-outline-purple btn-sm"><?php echo $language_manager->get('start_chat'); ?></a>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 bg-white h-100 text-center">
            <div class="card-body">
              <i class="fas fa-clipboard-list fa-2x text-purple mb-3"></i>
              <h5 class="card-title"><?php echo $language_manager->get('iep_prep_tool'); ?></h5>
              <p class="card-text small"><?php echo $language_manager->get('use_specialized_tool'); ?></p>
              <a href="https://iep-navigator-timenerd17.replit.app/" target="_blank" rel="noopener" class="btn btn-outline-purple btn-sm">
                <?php echo $language_manager->get('launch_tool'); ?>
              </a>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 bg-white h-100 text-center">
            <div class="card-body">
              <i class="fas fa-phone-alt fa-2x text-danger mb-3"></i>
              <h5 class="card-title"><?php echo $language_manager->get('emergency_help'); ?></h5>
              <p class="card-text small"><?php echo $language_manager->get('access_crisis_support'); ?></p>
              <button class="btn btn-outline-danger btn-sm" onclick="showEmergencyModal()">
                <?php echo $language_manager->get('emergency_contacts'); ?>
              </button>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 bg-white h-100 text-center">
            <div class="card-body">
              <i class="fas fa-info-circle fa-2x text-purple mb-3"></i>
              <h5 class="card-title"><?php echo $language_manager->get('about_guideai'); ?></h5>
              <p class="card-text small"><?php echo $language_manager->get('learn_more_mission'); ?></p>
              <a href="about" class="btn btn-outline-purple btn-sm"><?php echo $language_manager->get('learn_more'); ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Emergency Contacts Modal -->
  <div class="modal fade" id="emergencyModal" tabindex="-1" aria-labelledby="emergencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="emergencyModalLabel">
            <i class="fas fa-phone-alt me-2"></i><?php echo $language_manager->get('emergency_modal_title'); ?>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?php echo $language_manager->get('close'); ?>"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger">
            <h6 class="alert-heading">
              <i class="fas fa-exclamation-triangle me-2"></i><?php echo $language_manager->get('if_experiencing_crisis'); ?>:
            </h6>
            <p class="mb-0"><?php echo $language_manager->get('emergency_call_911'); ?></p>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                  <h6 class="mb-0"><?php echo $language_manager->get('crisis_support'); ?></h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <strong><?php echo $language_manager->get('suicide_prevention_lifeline'); ?></strong><br>
                    <a href="tel:988" class="text-decoration-none h5">988</a><br>
                    <small class="text-muted"><?php echo $language_manager->get('lifeline_description'); ?></small>
                  </div>
                  <div>
                    <strong><?php echo $language_manager->get('crisis_text_line'); ?></strong><br>
                    <?php echo $language_manager->get('text_hello_to'); ?> <a href="sms:741741" class="text-decoration-none">741741</a><br>
                    <small class="text-muted"><?php echo $language_manager->get('crisis_text_support'); ?></small>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                  <h6 class="mb-0"><?php echo $language_manager->get('disability_rights_legal'); ?></h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <strong><?php echo $language_manager->get('national_disability_rights'); ?></strong><br>
                    <a href="tel:202-408-9514" class="text-decoration-none h5">202-408-9514</a><br>
                    <small class="text-muted"><?php echo $language_manager->get('disability_rights_description'); ?></small>
                  </div>
                  <div>
                    <strong><?php echo $language_manager->get('parent_training_centers'); ?></strong><br>
                    <a href="https://www.parentcenterhub.org/find-your-center/" target="_blank" rel="noopener" class="text-decoration-none">
                      <?php echo $language_manager->get('find_local_center'); ?> →
                    </a><br>
                    <small class="text-muted"><?php echo $language_manager->get('state_specific_support'); ?></small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $language_manager->get('close'); ?></button>
        </div>
      </div>
    </div>
  </div>

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
    
    // Form validation
    (function () {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();

    // Show emergency modal function
    function showEmergencyModal() {
      const modal = new bootstrap.Modal(document.getElementById('emergencyModal'));
      modal.show();
    }

    // Auto-dismiss alerts after a few seconds
    document.addEventListener('DOMContentLoaded', () => {
      const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
      alerts.forEach(alert => {
        if (alert.classList.contains('alert-success')) {
          setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
          }, 5000);
        }
      });
    });
  </script>
</body>
</html>