<?php
/**
 * GuideAI Language Manager
 * Comprehensive bilingual support for English/Spanish
 */

class LanguageManager {
    private $currentLanguage = 'en';
    private $translations = [];
    
    public function __construct($language = 'en') {
        $this->currentLanguage = $language;
        $this->loadTranslations();
    }
    
    /**
     * Load all translations
     */
    private function loadTranslations() {
        $this->translations = [
            'en' => $this->getEnglishTranslations(),
            'es' => $this->getSpanishTranslations()
        ];
    }
    
    /**
     * Get translated text
     */
    public function get($key, $params = []) {
        $text = $this->translations[$this->currentLanguage][$key] ?? 
                $this->translations['en'][$key] ?? 
                $key;
        
        // Replace parameters
        foreach ($params as $param => $value) {
            $text = str_replace('{' . $param . '}', $value, $text);
        }
        
        return $text;
    }
    
    /**
     * Set current language
     */
    public function setLanguage($language) {
        if (isset($this->translations[$language])) {
            $this->currentLanguage = $language;
        }
    }
    
    /**
     * Get current language
     */
    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }
    
    /**
     * Get language name for display
     */
    public function getLanguageName($code = null) {
        $code = $code ?? $this->currentLanguage;
        $names = [
            'en' => 'English',
            'es' => 'Español'
        ];
        return $names[$code] ?? $code;
    }
    
    /**
     * Get English translations (for JS export)
     */
    public function getEnglishTranslations() {
        return $this->getEnglishTranslationsArray();
    }
    
    /**
     * Get Spanish translations (for JS export)
     */
    public function getSpanishTranslations() {
        return $this->getSpanishTranslationsArray();
    }
    
    /**
     * English translations
     */
    private function getEnglishTranslationsArray() {
        return [
            // Meta & SEO
            'meta_description' => 'GuideAI provides compassionate, AI-powered support for families navigating IEP meetings and special education. Get personalized guidance for your child\'s educational journey.',
            'og_title' => 'GuideAI - Compassionate IEP & Special Education Support',
            'og_description' => 'AI-powered guidance for families navigating special education. Built with purpose for children with disabilities.',
            'twitter_title' => 'GuideAI - IEP & Special Education Support',
            'twitter_description' => 'Compassionate AI support for families navigating special education',
            'page_title' => 'GuideAI - Compassionate IEP & Special Education Support',
            
            // Navigation & Header
            'main_navigation' => 'Main navigation',
            'guideai_home' => 'GuideAI Home',
            'toggle_navigation' => 'Toggle navigation menu',
            'nav_chat' => 'Chat',
            'nav_chat_aria' => 'Chat with GuideAI',
            'nav_about' => 'About',
            'nav_about_aria' => 'About GuideAI',
            'nav_roadmap' => 'Roadmap',
            'nav_roadmap_aria' => 'Development roadmap',
            'nav_contact' => 'Contact',
            'nav_contact_aria' => 'Contact us',
            'nav_iep_tool' => 'IEP Tool',
            'nav_iep_tool_aria' => 'Open IEP Prep Tool in new window',
            'language_selector' => 'Language selector',
            'skip_to_main' => 'Skip to main content',
            
            // Accessibility
            'accessibility_options' => 'Accessibility Options',
            'open_accessibility_options' => 'Accessibility Options',
            'close_accessibility_options' => 'Close accessibility options',
            'accessibility_title' => 'Accessibility Options',
            'language_section_title' => 'Language / Idioma',
            'language_selection' => 'Language selection',
            'visual_section' => 'Visual',
            'audio_section' => 'Audio',
            'toggle_high_contrast' => 'Toggle high contrast mode',
            'toggle_large_fonts' => 'Toggle large fonts',
            'toggle_dyslexia_font' => 'Toggle dyslexia-friendly font',
            'toggle_read_aloud' => 'Toggle read aloud',
            'high_contrast' => 'High Contrast',
            'large_text' => 'Large Text',
            'dyslexia_font' => 'Dyslexia Font',
            'read_aloud' => 'Read Aloud',
            'emergency_help' => 'Emergency Help',
            'emergency_contacts_aria' => 'Emergency contacts and crisis support',
            'keyboard_shortcuts' => 'Keyboard Shortcuts',
            'focus_input' => 'Focus on input field',
            'focus_prompts' => 'Focus on suggested prompts',
            'emergency_contacts' => 'Open emergency contacts',
            'accessibility_options' => 'Accessibility Options',
            'toggle_high_contrast' => 'Toggle high contrast',
            'toggle_large_fonts' => 'Toggle large fonts',
            'toggle_dyslexia_font' => 'Toggle dyslexia font',
            'toggle_read_aloud' => 'Toggle read aloud',
            'close_or_stop' => 'Close modal or stop speech',
            'submit_form' => 'Submit form',
            'language_options' => 'Language Options',
            'testing_tools' => 'Testing Tools',
            'test_accessibility' => 'Test All Features',
            'test_screen_reader' => 'Test Screen Reader',
            'read_aloud_enabled' => 'Read aloud enabled',
            'read_aloud_disabled' => 'Read aloud disabled',
            'language_changed' => 'Language changed',
            'conversation_cleared' => 'Conversation cleared',
            'print_window_opened' => 'Print window opened',
            'voice_selection' => 'Voice Selection',
            'voice_alloy' => 'Alloy (Neutral)',
            'voice_echo' => 'Echo (Male)',
            'voice_fable' => 'Fable (Male)',
            'voice_onyx' => 'Onyx (Male)',
            'voice_nova' => 'Nova (Female)',
            'voice_shimmer' => 'Shimmer (Female)',
            'speed_control' => 'Speed Control',
            'test_voice' => 'Test Voice',
            'tts_playing' => 'Playing audio with {voice} voice',
            'tts_fallback' => 'Using browser speech synthesis',
            'tts_error' => 'TTS generation failed',
            
            // Hero Section
            'hero_title' => 'GuideAI is your compassionate companion for navigating IEPs and special education',
            'hero_subtitle' => 'Your compassionate AI companion for navigating IEPs and special education',
            'hero_tagline' => 'Built with purpose for families with disabled children',
            'hero_logo_alt' => 'GuideAI Logo - Compassionate support for special education families',
            'hero_cta_button' => 'Start Your Journey',
            'feature_type_send' => 'Type & Send',
            'feature_voice_input' => 'Voice Input',
            'feature_read_aloud' => 'Read Aloud',
            'feature_accessible' => 'Accessible',
            'type_send' => 'Type & Send',
            'voice_input' => 'Voice Input',
            'accessible' => 'Accessible',
            
            // Chat Interface
            'chat_title' => 'Chat with GuideAI',
            'clear_conversation' => 'Clear conversation history',
            'print_conversation' => 'Print conversation',
            'clear_chat' => 'Clear',
            'print_chat' => 'Print',
            'conversation_with_guideai' => 'Conversation with GuideAI',
            'ask_guideai_question' => 'Ask GuideAI a question',
            'input_label' => 'Ask your question about IEP or special education',
            'input_placeholder' => 'IEPs, accommodations, rights...ask us anything.',
            'input_tip' => 'Tip: Be specific about your child\'s age, disability, or situation for better help',
            'input_instructions' => 'Type your question about IEP or special education. Press Enter to send, or use Alt+2 to browse suggested prompts.',
            'use_voice_input' => 'Use voice input',
            'click_to_speak' => 'Click to speak your question',
            'send_your_question' => 'Send your question',
            'send_button' => 'Send',
            'characters' => 'characters',
            'connected' => 'Connected',
            'approaching_char_limit' => 'Approaching character limit',
            'char_limit_reached' => 'Character limit reached',
            'voice_not_available' => 'Voice input is not available in your browser',
            'enter_question' => 'Please enter your question',
            'question_too_long' => 'Question is too long. Please keep it under 500 characters.',
            'listening' => 'Listening...',
            
            // Welcome Messages
            'welcome_title' => 'Welcome to GuideAI!',
            'welcome_message' => 'I\'m here to help you navigate your child\'s special education journey with confidence and support.',
            'tip_label' => 'Tip',
            'welcome_tip' => 'You can ask questions about IEPs, accommodations, your rights, or use the voice input button to speak your questions!',
            
            // Prompts Sidebar
            'suggested_questions' => 'Suggested questions',
            'prompts_title' => 'Helpful Prompts',
            'prompts_subtitle' => 'Click any prompt to get started',
            'need_immediate_help' => 'Need Immediate Help?',
            'emergency_contacts' => 'Emergency Contacts',
            'crisis_support_desc' => 'Crisis support, disability rights, and urgent advocacy help',
            
            // IEP Prep Tool
            'iep_prep_title' => 'IEP Prep Tool',
            'iep_prep_tool' => 'IEP Prep Tool',
            'iep_prep_description' => 'Our specialized tool guides you through IEP preparation—draft agendas, compile documents, and craft questions—so you can advocate confidently for your child.',
            'iep_prep_designed_for' => 'Designed with families in mind.',
            'iep_prep_tagline' => 'Designed with families in mind.',
            'in_development' => 'In Development',
            'launch_iep_tool' => 'Launch IEP Prep Tool',
            'launch_iep_prep_tool' => 'Launch IEP Prep Tool',
            'open_iep_tool_aria' => 'Open IEP Prep Tool in new window',
            
            // Footer
            'footer_copyright' => '© 2025 GuideAI. Built with purpose.',
            'footer_built_with' => 'built with heart and purpose.',
            'footer_tagline' => 'Empowering families through compassionate technology.',
            'footer_empowering_families' => 'Empowering families through compassionate technology.',
            'footer_disclaimer' => 'For immediate assistance, contact your local disability rights organization.',
            'footer_immediate_assistance' => 'For immediate assistance, contact your local disability rights organization.',
            'about' => 'About',
            'contact' => 'Contact',
            'roadmap' => 'Roadmap',
            
            // About Page
            'about_page_title' => 'About GuideAI - Compassionate Support for Special Education Families',
            'about_meta_description' => 'Learn about GuideAI\'s mission to empower families navigating special education with compassionate AI support and practical tools.',
            'about_title' => 'About GuideAI',
            'about_tagline' => 'Every parenting journey needs a guide. GuideAI is yours.',
            'mission_statement' => 'Mission Statement',
            'mission_paragraph_1' => 'At GuideAI, we empower parents with tailored guidance and practical tools, simplifying educational processes and building confidence for every IEP meeting. Crafted by families for families, our compassionate AI combines expert insights with clear next steps, ensuring your child\'s unique needs are understood and addressed.',
            'mission_paragraph_2' => 'From your first meeting through major transitions, GuideAI transforms uncertainty into clarity, delivering the right information exactly when you need it. No more guesswork, just focused, actionable support every step of the way.',
            'what_makes_unique' => 'What Makes GuideAI Unique',
            'built_by_experience' => 'Everything you need, built by people who\'ve been there.',
            'built_by_parents' => 'Built by Parents',
            'built_by_parents_desc' => 'Developed by families who\'ve walked the path, GuideAI understands the real challenges parents face. Our insights come from lived experience, ensuring practical, empathetic support tailored to your journey.',
            'intelligent_compassionate' => 'Intelligent & Compassionate',
            'intelligent_compassionate_desc' => 'Combining advanced AI with a human-first approach, GuideAI delivers clear, jargon-free advice. Get concise answers, practical next steps, and empathetic guidance—anytime, anywhere.',
            'comprehensive_resources' => 'Comprehensive Resources',
            'comprehensive_resources_desc' => 'Beyond chat, access planners, checklists, and resources designed to streamline every step of IEP preparation. Everything you need in one place, with secure, HIPAA-compliant handling.',
            'guide_initiative' => 'The GUIDE Initiative',
            'guide_initiative_desc' => 'GuideAI is part of the larger GUIDE Initiative, standing for:',
            'guidance' => 'Guidance',
            'guidance_desc' => 'Personalized direction for your family\'s unique journey',
            'understanding' => 'Understanding',
            'understanding_desc' => 'Clear explanations of complex educational systems',
            'information' => 'Information',
            'information_desc' => 'Accurate, up-to-date resources when you need them',
            'direction' => 'Direction',
            'direction_desc' => 'Clear next steps to move forward with confidence',
            'empowerment' => 'Empowerment',
            'empowerment_desc' => 'Building your confidence to advocate effectively for your child',
            
            // Roadmap Page
            'roadmap_page_title' => 'GuideAI Roadmap - Our Journey Forward',
            'roadmap_meta_description' => 'Track GuideAI\'s development progress and upcoming features designed to support families navigating special education.',
            'roadmap_title' => 'GuideAI Roadmap',
            'roadmap_subtitle' => 'Tracking our progress and upcoming milestones',
            'last_updated' => 'Last updated',
            'completed_count' => '12 Completed',
            'completed_desc' => 'Core features launched and operational',
            'in_progress_count' => '5 In Progress',
            'in_progress_desc' => 'Currently under development',
            'planned_count' => '8 Planned',
            'planned_desc' => 'Upcoming features and improvements',
            'completed_features' => 'Completed Features',
            'core_chat_interface' => 'Core Chat Interface',
            'core_chat_interface_desc' => 'Fully functional chat UI with send, voice, and read-aloud features.',
            'multi_ai_integration' => 'Multi-AI Integration',
            'multi_ai_integration_desc' => 'OpenAI, Claude, and Gemini support with intelligent fallbacks.',
            'accessibility_features' => 'Accessibility Features',
            'accessibility_features_desc' => 'Voice input, TTS, high contrast, keyboard navigation.',
            'smart_prompts' => 'Smart Prompts',
            'smart_prompts_desc' => 'Interactive prompt list for common IEP and advocacy topics.',
            'chat_history' => 'Chat History',
            'chat_history_desc' => 'Persistent conversation storage and retrieval.',
            'emergency_contacts' => 'Emergency Contacts',
            'emergency_contacts_desc' => 'Crisis support and disability rights resources.',
            'mobile_optimization' => 'Mobile Optimization',
            'mobile_optimization_desc' => 'Responsive design for all devices and screen sizes.',
            'iep_prep_tool_v1' => 'IEP Prep Tool v1',
            'iep_prep_tool_v1_desc' => 'Initial version of meeting preparation assistant.',
            'in_progress_features' => 'Currently In Development',
            'user_testing' => 'User Testing',
            'user_testing_desc' => 'Gathering feedback from families to improve functionality.',
            'resource_linking' => 'Resource Linking',
            'resource_linking_desc' => 'Enhanced local resource detection and linking system.',
            'seo_optimization' => 'SEO Optimization',
            'seo_optimization_desc' => 'Improving search visibility for families in need.',
            'analytics_integration' => 'Analytics Integration',
            'analytics_integration_desc' => 'Usage tracking and error monitoring implementation.',
            'multi_language_support' => 'Multi-language Support',
            'multi_language_support_desc' => 'Spanish translation for broader family accessibility.',
            'planned_features' => 'Planned Features',
            'iep_document_parser' => 'IEP Document Parser',
            'iep_document_parser_desc' => 'Upload and analyze existing IEP documents for insights.',
            'hipaa_compliance' => 'HIPAA Compliance',
            'hipaa_compliance_desc' => 'Full healthcare data protection certification.',
            'family_accounts' => 'Family Accounts',
            'family_accounts_desc' => 'Secure accounts for saving progress and documents.',
            'meeting_scheduler' => 'Meeting Scheduler',
            'meeting_scheduler_desc' => 'Integration with school systems for IEP meeting coordination.',
            'training_modules' => 'Training Modules',
            'training_modules_desc' => 'Interactive courses on special education advocacy.',
            'parent_network' => 'Parent Network',
            'parent_network_desc' => 'Connect families with similar experiences and challenges.',
            'smart_notifications' => 'Smart Notifications',
            'smart_notifications_desc' => 'Reminders for meetings, deadlines, and important dates.',
            'advanced_ai_features' => 'Advanced AI Features',
            'advanced_ai_features_desc' => 'Predictive insights and personalized recommendations.',
            'help_shape_our_future' => 'Help Shape Our Future',
            'help_shape_our_future_desc' => 'Your feedback helps us prioritize features that matter most to families. What would help your special education journey?',
            'send_feedback' => 'Send Feedback',
            'chat_with_guideai' => 'Chat with GuideAI',
            'q2_2025' => 'Q2 2025',
            'q3_2025' => 'Q3 2025',
            'q4_2025' => 'Q4 2025',
            'q1_2026' => 'Q1 2026',
            
            // Contact Page
            'contact_page_title' => 'Contact GuideAI - We\'re Here to Help',
            'contact_meta_description' => 'Get in touch with GuideAI for support, feedback, or questions about special education and IEP assistance.',
            'contact_title' => 'Contact GuideAI',
            'contact_subtitle' => 'We\'re here to support your family\'s special education journey',
            'get_in_touch' => 'Get in Touch',
            'contact_description' => 'Have questions, feedback, or need support? We\'d love to hear from you.',
            'error_enter_name' => 'Please enter your name.',
            'error_enter_valid_email' => 'Please enter a valid email address.',
            'error_enter_subject' => 'Please enter a subject.',
            'error_enter_message' => 'Please enter your message.',
            'error_sending_message' => 'Sorry, there was an issue sending your message. Please try again later or contact us directly.',
            'thank_you' => 'Thank you',
            'message_sent_success' => 'Your message has been sent successfully. We\'ll get back to you as soon as possible.',
            'message_received' => 'Message Received',
            'message_received_desc' => 'We appreciate you reaching out. Our team will review your message and respond within 24-48 hours.',
            'return_to_guideai' => 'Return to GuideAI',
            'your_name' => 'Your Name',
            'your_email' => 'Your Email',
            'please_provide_name' => 'Please provide your name.',
            'please_provide_valid_email' => 'Please provide a valid email address.',
            'subject' => 'Subject',
            'choose_topic' => 'Choose a topic',
            'please_choose_topic' => 'Please select a subject.',
            'your_message' => 'Your Message',
            'please_enter_message' => 'Please enter your message.',
            'send_message' => 'Send Message',
            'general_support' => 'General Support',
            'technical_issue' => 'Technical Issue',
            'feature_request' => 'Feature Request',
            'iep_guidance' => 'IEP Guidance',
            'accessibility_help' => 'Accessibility Help',
            'partnership_inquiry' => 'Partnership Inquiry',
            'other' => 'Other',
            
            // Contact Page Additional Resources
            'other_ways_to_help' => 'Other Ways to Get Help',
            'multiple_ways_support' => 'Multiple ways to access support and resources',
            'chat_support' => 'Chat Support',
            'get_immediate_help' => 'Get immediate help through our AI chat system.',
            'start_chat' => 'Start Chat',
            'use_specialized_tool' => 'Use our specialized tool for meeting preparation.',
            'launch_tool' => 'Launch Tool',
            'access_crisis_support' => 'Access crisis support and disability rights resources.',
            'about_guideai' => 'About GuideAI',
            'learn_more_mission' => 'Learn more about our mission and features.',
            'learn_more' => 'Learn More',
            'if_experiencing_crisis' => 'If you\'re experiencing a crisis',
            'crisis_text_line' => 'Crisis Text Line',
            'crisis_text_support' => '24/7 crisis support via text',
            'parent_training_centers' => 'Parent Training Centers',
            'find_local_center' => 'Find your local center',
            'state_specific_support' => 'State-specific parent support',
            
            // Emergency & Crisis
            'emergency_title' => 'Emergency Contacts & Crisis Support',
            'emergency_modal_title' => 'Emergency Contacts & Crisis Support',
            'close_emergency_contacts' => 'Close emergency contacts',
            'emergency_contacts_opened' => 'Emergency contacts dialog opened',
            'emergency_crisis_warning' => 'If you\'re experiencing a crisis or emergency:',
            'emergency_call_911' => 'Please contact emergency services immediately by calling 911 or going to your nearest emergency room.',
            'crisis_support' => 'Crisis Support',
            'suicide_prevention_lifeline' => 'National Suicide Prevention Lifeline',
            'lifeline_description' => '24/7 free and confidential support',
            'text' => 'Text',
            'text_hello_to' => 'Text "HELLO" to',
            'national_parent_helpline' => 'National Parent Helpline',
            'parent_helpline_description' => 'Emotional support for parents',
            'disability_rights_legal' => 'Disability Rights & Legal',
            'national_disability_rights' => 'National Disability Rights Network',
            'disability_rights_description' => 'Legal advocacy for disability discrimination',
            'find_local_office' => 'Find your local office',
            'special_ed_legal_help' => 'Special Education Legal Help',
            'copaa_description' => 'COPAA - Council of Parent Attorneys',
            'remember' => 'Remember',
            'remember_advocate' => 'You are your child\'s best advocate',
            'remember_ask_help' => 'It\'s okay to ask for help - that\'s what these resources are for',
            'remember_document' => 'Document everything - dates, names, conversations',
            'remember_bring_advocate' => 'You have the right to bring an advocate to any school meeting',
            'remember_self_care' => 'Take care of yourself too - you can\'t pour from an empty cup',
            'close' => 'Close',
            
            // Loading & Status Messages
            'loading' => 'Loading...',
            'changing_language' => 'Changing language...',
            'loading_getting_info' => 'Getting helpful information for your family...',
            'loading_finding_resources' => 'Finding the best resources for your child...',
            'loading_preparing' => 'Preparing personalized guidance...',
            'loading_connecting' => 'Connecting you with the right support...',
            'loading_processing' => 'Processing your question with care...',
            
            // Notifications
            'notification_enter_question' => 'Please enter a question about your child\'s education or IEP needs.',
            'notification_question_too_long' => 'Please keep your question under 500 characters for the best response.',
            'prompt_selected' => 'Prompt selected',
            
            // Error Messages
            'error_trouble_getting_answer' => 'I apologize, but I\'m having trouble right now. Please try again in a moment, or use the suggested prompts.',
            
            // Related Topics
            'related_topics' => 'Related Topics You Might Find Helpful:',
            
            // Voice Input & Speech Recognition
            'voice_recording_started' => 'Voice recording started. Speak now.',
            'voice_recording_ended' => 'Voice recording ended',
            'voice_heard' => 'Heard: {transcript}',
            'voice_no_speech' => 'No speech detected. Please try again.',
            'voice_audio_error' => 'Audio capture error. Please check your microphone.',
            'voice_permission_denied' => 'Microphone permission denied.',
            'voice_recognition_error' => 'Speech recognition error.',
            'voice_not_supported' => 'Speech recognition is not available in this browser',
            'stop_recording' => 'Stop recording',
            'recording_stopped' => 'Recording stopped',
            
            // Print & Clear Functionality
            'print_window_opened' => 'Print window opened',
            'conversation_cleared' => 'Conversation cleared',
            'confirm_clear_conversation' => 'Are you sure you want to clear the entire conversation?',
            'print_conversation_title' => 'GuideAI Conversation',
            'print_date' => 'Date',
            'print_time' => 'Time',
            
            // Read Aloud
            'read_aloud_enabled' => 'Read aloud enabled',
            'read_aloud_disabled' => 'Read aloud disabled',
            
            // Sample Prompts (will be populated by getSamplePrompts method)
        ];
    }
    
    /**
     * Spanish translations
     */
    private function getSpanishTranslationsArray() {
        return [
            // Meta & SEO
            'meta_description' => 'GuideAI proporciona apoyo compasivo impulsado por IA para familias que navegan reuniones de IEP y educación especial. Obtén orientación personalizada para el viaje educativo de tu hijo.',
            'og_title' => 'GuideAI - Apoyo Compasivo para IEP y Educación Especial',
            'og_description' => 'Orientación impulsada por IA para familias navegando educación especial. Construido con propósito para niños con discapacidades.',
            'twitter_title' => 'GuideAI - Apoyo para IEP y Educación Especial',
            'twitter_description' => 'Apoyo compasivo de IA para familias navegando educación especial',
            'page_title' => 'GuideAI - Apoyo Compasivo para IEP y Educación Especial',
            
            // Navigation & Header
            'main_navigation' => 'Navegación principal',
            'guideai_home' => 'Inicio de GuideAI',
            'toggle_navigation' => 'Alternar menú de navegación',
            'nav_chat' => 'Chat',
            'nav_chat_aria' => 'Chatear con GuideAI',
            'nav_about' => 'Acerca de',
            'nav_about_aria' => 'Acerca de GuideAI',
            'nav_roadmap' => 'Hoja de Ruta',
            'nav_roadmap_aria' => 'Hoja de ruta de desarrollo',
            'nav_contact' => 'Contacto',
            'nav_contact_aria' => 'Contáctanos',
            'nav_iep_tool' => 'Herramienta IEP',
            'nav_iep_tool_aria' => 'Abrir Herramienta de Preparación de IEP en nueva ventana',
            'language_selector' => 'Selector de idioma',
            'skip_to_main' => 'Saltar al contenido principal',
            
            // Accessibility
            'accessibility_options' => 'Opciones de Accesibilidad',
            'open_accessibility_options' => 'Accessibility Options',
            'close_accessibility_options' => 'Cerrar opciones de accesibilidad',
            'accessibility_title' => 'Accessibility Options',
            'language_section_title' => 'Idioma / Language',
            'language_selection' => 'Selección de idioma',
            'visual_section' => 'Visual',
            'audio_section' => 'Audio',
            'toggle_high_contrast' => 'Alternar modo de alto contraste',
            'toggle_large_fonts' => 'Alternar fuentes grandes',
            'toggle_dyslexia_font' => 'Alternar fuente para dislexia',
            'toggle_read_aloud' => 'Alternar lectura en voz alta',
            'high_contrast' => 'Alto Contraste',
            'large_text' => 'Texto Grande',
            'dyslexia_font' => 'Fuente para Dislexia',
            'read_aloud' => 'Leer en Voz Alta',
            'emergency_help' => 'Ayuda de Emergencia',
            'emergency_contacts_aria' => 'Contactos de emergencia y apoyo en crisis',
            'keyboard_shortcuts' => 'Accesos Directos',
            'focus_input' => 'Enfoque en el campo de entrada',
            'focus_prompts' => 'Enfoque en sugerencias sugeridas',
            'emergency_contacts' => 'Contactos de Emergencia',
            'accessibility_options' => 'Accesibilidad',
            'toggle_high_contrast' => 'Contraste Alto',
            'toggle_large_fonts' => 'Fuentes Grandes',
            'toggle_dyslexia_font' => 'Fuente para Dislexia',
            'toggle_read_aloud' => 'Lectura en Voz Alta',
            'close_or_stop' => 'Cerrar modal o detener voz',
            'submit_form' => 'Enviar formulario',
            'language_options' => 'Opciones de Idioma',
            'testing_tools' => 'Herramientas de Prueba',
            'test_accessibility' => 'Probar Todas las Funciones',
            'test_screen_reader' => 'Probar Lector de Pantalla',
            
            // Hero Section
            'hero_title' => 'GuideAI es tu compañero compasivo para navegar por los IEPs y la educación especial',
            'hero_subtitle' => 'Tu compañero de IA compasivo para navegar los IEPs y especial',
            'hero_tagline' => 'Construido con propósito para familias con niños con discapacidades',
            'hero_logo_alt' => 'GuideAI Logo - Apoyo Compasivo para Familias con Niños con Discapacidades',
            'hero_cta_button' => 'Comienza Tu Viaje',
            'feature_type_send' => 'Escribir y Enviar',
            'feature_voice_input' => 'Entrada de Voz',
            'feature_read_aloud' => 'Leer en Voz Alta',
            'feature_accessible' => 'Accesible',
            'type_send' => 'Escribir y Enviar',
            'voice_input' => 'Entrada de Voz',
            'accessible' => 'Accesible',
            
            // Chat Interface
            'chat_title' => 'Chatear con GuideAI',
            'clear_conversation' => 'Limpiar historial de conversación',
            'print_conversation' => 'Imprimir conversación',
            'clear_chat' => 'Limpiar',
            'print_chat' => 'Imprimir',
            'conversation_with_guideai' => 'Conversación con GuideAI',
            'ask_guideai_question' => 'Hacer una pregunta a GuideAI',
            'input_label' => 'Haz tu pregunta sobre IEP o educación especial',
            'input_placeholder' => 'IEP, adaptaciones, derechos...pregúntanos lo que sea.',
            'input_tip' => 'Consejo: Sé específico sobre la edad, discapacidad o situación de tu hijo para obtener mejor ayuda',
            'input_instructions' => 'Escribe tu pregunta sobre IEP o educación especial. Presiona Enter para enviar, o usa Alt+2 para navegar las sugerencias.',
            'use_voice_input' => 'Usar entrada de voz',
            'click_to_speak' => 'Haz clic para hablar tu pregunta',
            'send_your_question' => 'Envía tu pregunta',
            'send_button' => 'Enviar',
            'characters' => 'caracteres',
            'connected' => 'Conectado',
            'approaching_char_limit' => 'Acercándose al límite de caracteres',
            'char_limit_reached' => 'Límite de caracteres alcanzado',
            'voice_not_available' => 'La entrada de voz no está disponible en tu navegador',
            'enter_question' => 'Por favor ingresa tu pregunta',
            'question_too_long' => 'La pregunta es demasiado larga. Por favor manténla bajo 500 caracteres.',
            'listening' => 'Escuchando...',
            
            // Welcome Messages
            'welcome_title' => '¡Bienvenido a GuideAI!',
            'welcome_message' => 'Estoy aquí para ayudarte a navegar el viaje de educación especial de tu hijo con confianza y apoyo.',
            'tip_label' => 'Consejo',
            'welcome_tip' => '¡Puedes hacer preguntas sobre IEP, adaptaciones, tus derechos, o usar el botón de entrada de voz para hablar tus preguntas!',
            
            // Prompts Sidebar
            'suggested_questions' => 'Preguntas sugeridas',
            'prompts_title' => 'Sugerencias Útiles',
            'prompts_subtitle' => 'Haz clic en cualquier sugerencia para comenzar',
            'need_immediate_help' => '¿Necesitas Ayuda Inmediata?',
            'emergency_contacts' => 'Contactos de Emergencia',
            'crisis_support_desc' => 'Apoyo en crisis, derechos de discapacidad y ayuda urgente de defensa',
            
            // IEP Prep Tool
            'iep_prep_title' => 'Herramienta de Preparación de IEP',
            'iep_prep_tool' => 'Herramienta de Preparación de IEP',
            'iep_prep_description' => 'Nuestra herramienta especializada te guía a través de la preparación del IEP—borradores de agendas, compilar documentos y formular preguntas—para que puedas abogar con confianza por tu hijo.',
            'iep_prep_designed_for' => 'Diseñado pensando en las familias.',
            'iep_prep_tagline' => 'Diseñado pensando en las familias.',
            'in_development' => 'En Desarrollo',
            'launch_iep_tool' => 'Lanzar Herramienta de Preparación de IEP',
            'launch_iep_prep_tool' => 'Lanzar Herramienta de Preparación de IEP',
            'open_iep_tool_aria' => 'Abrir Herramienta de Preparación de IEP en nueva ventana',
            
            // Footer
            'footer_copyright' => '© 2025 GuideAI. Construido con propósito.',
            'footer_built_with' => 'construido con corazón y propósito.',
            'footer_tagline' => 'Empoderando familias a través de tecnología compasiva.',
            'footer_empowering_families' => 'Empoderando familias a través de tecnología compasiva.',
            'footer_disclaimer' => 'Para asistencia inmediata, contacta tu organización local de derechos de discapacidad.',
            'footer_immediate_assistance' => 'Para asistencia inmediata, contacta tu organización local de derechos de discapacidad.',
            'about' => 'Acerca de',
            'contact' => 'Contacto',
            'roadmap' => 'Hoja de Ruta',
            
            // About Page
            'about_page_title' => 'Acerca de GuideAI - Apoyo Compasivo para Familias de Educación Especial',
            'about_meta_description' => 'Conoce la misión de GuideAI para empoderar familias navegando educación especial con apoyo compasivo de IA y herramientas prácticas.',
            'about_title' => 'Acerca de GuideAI',
            'about_tagline' => 'Cada viaje de crianza necesita una guía. GuideAI es la tuya.',
            'mission_statement' => 'Declaración de Misión',
            'mission_paragraph_1' => 'En GuideAI, empoderamos a los padres con orientación personalizada y herramientas prácticas, simplificando los procesos educativos y construyendo confianza para cada reunión de IEP. Creado por familias para familias, nuestra IA compasiva combina conocimientos expertos con pasos claros, asegurando que las necesidades únicas de tu hijo sean entendidas y atendidas.',
            'mission_paragraph_2' => 'Desde tu primera reunión hasta las transiciones importantes, GuideAI transforma la incertidumbre en claridad, entregando la información correcta exactamente cuando la necesitas. No más conjeturas, solo apoyo enfocado y accionable en cada paso del camino.',
            'what_makes_unique' => 'Qué Hace Único a GuideAI',
            'built_by_experience' => 'Todo lo que necesitas, construido por personas que han estado ahí.',
            'built_by_parents' => 'Construido por Padres',
            'built_by_parents_desc' => 'Desarrollado por familias que han caminado el camino, GuideAI entiende los verdaderos desafíos que enfrentan los padres. Nuestros conocimientos provienen de la experiencia vivida, asegurando apoyo práctico y empático adaptado a tu viaje.',
            'intelligent_compassionate' => 'Inteligente y Compasivo',
            'intelligent_compassionate_desc' => 'Combinando IA avanzada con un enfoque centrado en el ser humano, GuideAI entrega consejos claros y sin jerga. Obtén respuestas concisas, próximos pasos prácticos y orientación empática—en cualquier momento, en cualquier lugar.',
            'comprehensive_resources' => 'Recursos Integrales',
            'comprehensive_resources_desc' => 'Más allá del chat, accede a planificadores, listas de verificación y recursos diseñados para agilizar cada paso de la preparación del IEP. Todo lo que necesitas en un lugar, con manejo seguro y compatible con HIPAA.',
            'guide_initiative' => 'La Iniciativa GUIDE',
            'guide_initiative_desc' => 'GuideAI es parte de la Iniciativa GUIDE más amplia, que significa:',
            'guidance' => 'Orientación',
            'guidance_desc' => 'Dirección personalizada para el viaje único de tu familia',
            'understanding' => 'Comprensión',
            'understanding_desc' => 'Explicaciones claras de sistemas educativos complejos',
            'information' => 'Información',
            'information_desc' => 'Recursos precisos y actualizados cuando los necesitas',
            'direction' => 'Dirección',
            'direction_desc' => 'Próximos pasos claros para avanzar con confianza',
            'empowerment' => 'Empoderamiento',
            'empowerment_desc' => 'Construyendo tu confianza para abogar efectivamente por tu hijo',
            
            // Roadmap Page
            'roadmap_page_title' => 'Hoja de Ruta de GuideAI - Nuestro Camino Hacia Adelante',
            'roadmap_meta_description' => 'Rastrea el progreso de desarrollo de GuideAI y las próximas funciones diseñadas para apoyar a familias navegando educación especial.',
            'roadmap_title' => 'Hoja de Ruta de GuideAI',
            'roadmap_subtitle' => 'Rastreando nuestro progreso y próximos hitos',
            'last_updated' => 'Última actualización',
            'completed_count' => '12 Completadas',
            'completed_desc' => 'Funciones principales lanzadas y operativas',
            'in_progress_count' => '5 En Progreso',
            'in_progress_desc' => 'Actualmente en desarrollo',
            'planned_count' => '8 Planificadas',
            'planned_desc' => 'Próximas funciones y mejoras',
            'completed_features' => 'Funciones Completadas',
            'core_chat_interface' => 'Interfaz de Chat Principal',
            'core_chat_interface_desc' => 'Interfaz de chat completamente funcional con funciones de envío, voz y lectura en voz alta.',
            'multi_ai_integration' => 'Integración Multi-IA',
            'multi_ai_integration_desc' => 'Soporte para OpenAI, Claude y Gemini con respaldos inteligentes.',
            'accessibility_features' => 'Funciones de Accesibilidad',
            'accessibility_features_desc' => 'Entrada de voz, TTS, alto contraste, navegación por teclado.',
            'smart_prompts' => 'Sugerencias Inteligentes',
            'smart_prompts_desc' => 'Lista interactiva de sugerencias para temas comunes de IEP y defensa.',
            'chat_history' => 'Historial de Chat',
            'chat_history_desc' => 'Almacenamiento y recuperación persistente de conversaciones.',
            'emergency_contacts' => 'Contactos de Emergencia',
            'emergency_contacts_desc' => 'Recursos de apoyo en crisis y derechos de discapacidad.',
            'mobile_optimization' => 'Optimización Móvil',
            'mobile_optimization_desc' => 'Diseño responsivo para todos los dispositivos y tamaños de pantalla.',
            'iep_prep_tool_v1' => 'Herramienta de Preparación IEP v1',
            'iep_prep_tool_v1_desc' => 'Versión inicial del asistente de preparación de reuniones.',
            'in_progress_features' => 'Actualmente En Desarrollo',
            'user_testing' => 'Pruebas de Usuario',
            'user_testing_desc' => 'Recopilando comentarios de familias para mejorar la funcionalidad.',
            'resource_linking' => 'Vinculación de Recursos',
            'resource_linking_desc' => 'Sistema mejorado de detección y vinculación de recursos locales.',
            'seo_optimization' => 'Optimización SEO',
            'seo_optimization_desc' => 'Mejorando la visibilidad en búsquedas para familias que lo necesitan.',
            'analytics_integration' => 'Integración de Analíticas',
            'analytics_integration_desc' => 'Implementación de seguimiento de uso y monitoreo de errores.',
            'multi_language_support' => 'Soporte Multi-idioma',
            'multi_language_support_desc' => 'Traducción al español para mayor accesibilidad familiar.',
            'planned_features' => 'Funciones Planificadas',
            'iep_document_parser' => 'Analizador de Documentos IEP',
            'iep_document_parser_desc' => 'Subir y analizar documentos IEP existentes para obtener información.',
            'hipaa_compliance' => 'Cumplimiento HIPAA',
            'hipaa_compliance_desc' => 'Certificación completa de protección de datos de salud.',
            'family_accounts' => 'Cuentas Familiares',
            'family_accounts_desc' => 'Cuentas seguras para guardar progreso y documentos.',
            'meeting_scheduler' => 'Programador de Reuniones',
            'meeting_scheduler_desc' => 'Integración con sistemas escolares para coordinación de reuniones IEP.',
            'training_modules' => 'Módulos de Entrenamiento',
            'training_modules_desc' => 'Cursos interactivos sobre defensa de educación especial.',
            'parent_network' => 'Red de Padres',
            'parent_network_desc' => 'Conectar familias con experiencias y desafíos similares.',
            'smart_notifications' => 'Notificaciones Inteligentes',
            'smart_notifications_desc' => 'Recordatorios para reuniones, fechas límite y fechas importantes.',
            'advanced_ai_features' => 'Funciones Avanzadas de IA',
            'advanced_ai_features_desc' => 'Información predictiva y recomendaciones personalizadas.',
            'help_shape_our_future' => 'Ayuda a Dar Forma a Nuestro Futuro',
            'help_shape_our_future_desc' => 'Tus comentarios nos ayudan a priorizar las funciones que más importan a las familias. ¿Qué ayudaría en tu viaje de educación especial?',
            'send_feedback' => 'Enviar Comentarios',
            'chat_with_guideai' => 'Chatear con GuideAI',
            'q2_2025' => 'Q2 2025',
            'q3_2025' => 'Q3 2025',
            'q4_2025' => 'Q4 2025',
            'q1_2026' => 'Q1 2026',
            
            // Contact Page
            'contact_page_title' => 'Contactar GuideAI - Estamos Aquí para Ayudar',
            'contact_meta_description' => 'Ponte en contacto con GuideAI para apoyo, comentarios o preguntas sobre educación especial y asistencia IEP.',
            'contact_title' => 'Contactar GuideAI',
            'contact_subtitle' => 'Estamos aquí para apoyar el viaje de educación especial de tu familia',
            'get_in_touch' => 'Ponte en Contacto',
            'contact_description' => '¿Tienes preguntas, comentarios o necesitas apoyo? Nos encantaría saber de ti.',
            'error_enter_name' => 'Por favor ingresa tu nombre.',
            'error_enter_valid_email' => 'Por favor ingresa una dirección de correo electrónico válida.',
            'error_enter_subject' => 'Por favor ingresa un asunto.',
            'error_enter_message' => 'Por favor ingresa tu mensaje.',
            'error_sending_message' => 'Lo sentimos, hubo un problema enviando tu mensaje. Por favor intenta de nuevo más tarde o contáctanos directamente.',
            'thank_you' => 'Gracias',
            'message_sent_success' => 'Tu mensaje ha sido enviado exitosamente. Te responderemos lo antes posible.',
            'message_received' => 'Mensaje Recibido',
            'message_received_desc' => 'Apreciamos que te hayas puesto en contacto. Nuestro equipo revisará tu mensaje y responderá dentro de 24-48 horas.',
            'return_to_guideai' => 'Volver a GuideAI',
            'your_name' => 'Tu Nombre',
            'your_email' => 'Tu Correo Electrónico',
            'please_provide_name' => 'Por favor proporciona tu nombre.',
            'please_provide_valid_email' => 'Por favor proporciona una dirección de correo electrónico válida.',
            'subject' => 'Asunto',
            'choose_topic' => 'Elige un tema',
            'please_choose_topic' => 'Por favor selecciona un asunto.',
            'your_message' => 'Tu Mensaje',
            'please_enter_message' => 'Por favor ingresa tu mensaje.',
            'send_message' => 'Enviar Mensaje',
            'general_support' => 'Apoyo General',
            'technical_issue' => 'Problema Técnico',
            'feature_request' => 'Solicitud de Función',
            'iep_guidance' => 'Orientación IEP',
            'accessibility_help' => 'Ayuda de Accesibilidad',
            'partnership_inquiry' => 'Consulta de Asociación',
            'other' => 'Otro',
            
            // Contact Page Additional Resources
            'other_ways_to_help' => 'Otras Maneras de Obtener Ayuda',
            'multiple_ways_support' => 'Varias Maneras de Acceder a Apoyo y Recursos',
            'chat_support' => 'Apoyo de Chat',
            'get_immediate_help' => 'Obtén ayuda inmediata a través del sistema de chat de IA',
            'start_chat' => 'Iniciar Chat',
            'use_specialized_tool' => 'Usa nuestra herramienta especializada para preparación de reuniones',
            'launch_tool' => 'Lanzar Herramienta',
            'access_crisis_support' => 'Accede a apoyo en crisis y recursos de derechos de discapacidad',
            'about_guideai' => 'Acerca de GuideAI',
            'learn_more_mission' => 'Aprende más sobre nuestra misión y características',
            'learn_more' => 'Aprende Más',
            'if_experiencing_crisis' => 'Si estás experimentando una crisis',
            'crisis_text_line' => 'Línea de Texto de Crisis',
            'crisis_text_support' => 'Apoyo en crisis 24/7 a través de texto',
            'parent_training_centers' => 'Centros de Capacitación para Padres',
            'find_local_center' => 'Encuentra tu centro local',
            'state_specific_support' => 'Apoyo para padres estatales específicos',
            
            // Emergency & Crisis
            'emergency_title' => 'Contactos de Emergencia y Apoyo en Crisis',
            'emergency_modal_title' => 'Contactos de Emergencia y Apoyo en Crisis',
            'close_emergency_contacts' => 'Cerrar contactos de emergencia',
            'emergency_contacts_opened' => 'Diálogo de contactos de emergencia abierto',
            'emergency_crisis_warning' => 'Si estás experimentando una crisis o emergencia:',
            'emergency_call_911' => 'Por favor, contacta los servicios de emergencia inmediatamente llamando al 911 o visitando tu sala de emergencias más cercana.',
            'crisis_support' => 'Apoyo en Crisis',
            'suicide_prevention_lifeline' => 'Línea de Vida para Prevención del Suicidio Nacional',
            'lifeline_description' => 'Apoyo gratuito y confidencial 24/7',
            'text' => 'Texto',
            'text_hello_to' => 'Texto "HOLA" a',
            'national_parent_helpline' => 'Línea de Ayuda Nacional para Padres',
            'parent_helpline_description' => 'Apoyo emocional para padres',
            'disability_rights_legal' => 'Derechos de Discapacidad y Legal',
            'national_disability_rights' => 'Red Nacional de Derechos de Discapacidad',
            'disability_rights_description' => 'Defensa Legal contra Discriminación por Discapacidad',
            'find_local_office' => 'Encuentra tu oficina local',
            'special_ed_legal_help' => 'Ayuda Legal de Educación Especial',
            'copaa_description' => 'COPAA - Consejo de Abogados de Padres',
            'remember' => 'Recuerda',
            'remember_advocate' => 'Tú eres el mejor defensor de tu hijo',
            'remember_ask_help' => 'Está bien pedir ayuda - para eso están estos recursos',
            'remember_document' => 'Documenta todo - fechas, nombres, conversaciones',
            'remember_bring_advocate' => 'Tienes el derecho de traer un defensor a cualquier reunión escolar',
            'remember_self_care' => 'Cuídate también - no puedes dar de un vaso vacío',
            'close' => 'Cerrar',
            
            // Loading & Status Messages
            'loading' => 'Cargando...',
            'changing_language' => 'Cambiando idioma...',
            'loading_getting_info' => 'Obteniendo información útil para tu familia...',
            'loading_finding_resources' => 'Encontrando los mejores recursos para tu hijo...',
            'loading_preparing' => 'Preparando orientación personalizada...',
            'loading_connecting' => 'Conectándote con el apoyo adecuado...',
            'loading_processing' => 'Procesando tu pregunta con cuidado...',
            
            // Notifications
            'notification_enter_question' => 'Por favor ingresa una pregunta sobre la educación o necesidades de IEP de tu hijo.',
            'notification_question_too_long' => 'Por favor mantén tu pregunta bajo 500 caracteres para obtener la mejor respuesta.',
            'prompt_selected' => 'Sugerencia seleccionada',
            
            // Error Messages
            'error_trouble_getting_answer' => 'Me disculpo, pero estoy teniendo problemas ahora mismo. Por favor intenta de nuevo en un momento, o usa las sugerencias.',
            
            // Related Topics
            'related_topics' => 'Temas Relacionados que Podrían Ser Útiles:',
            
            // Voice Input & Speech Recognition
            'voice_recording_started' => 'Grabación de voz iniciada. Por favor habla tu pregunta.',
            'voice_recording_ended' => 'Grabación de voz terminada',
            'voice_heard' => 'Heard: {transcript}',
            'voice_no_speech' => 'No se detectó habla. Por favor intenta de nuevo.',
            'voice_audio_error' => 'Error de captura de audio. Por favor verifica tu micrófono.',
            'voice_permission_denied' => 'Permiso de micrófono denegado.',
            'voice_recognition_error' => 'Error de reconocimiento de voz.',
            'voice_not_supported' => 'Reconocimiento de voz no disponible en este navegador',
            'stop_recording' => 'Detener grabación',
            'recording_stopped' => 'Grabación detenida',
            
            // Print & Clear Functionality
            'print_window_opened' => 'Ventana de impresión abierta',
            'conversation_cleared' => 'Conversación limpiada',
            'confirm_clear_conversation' => '¿Estás seguro de que quieres limpiar toda la conversación?',
            'print_conversation_title' => 'Conversación con GuideAI',
            'print_date' => 'Fecha',
            'print_time' => 'Hora',
            
            // Read Aloud
            'read_aloud_enabled' => 'Lectura en voz alta habilitada',
            'read_aloud_disabled' => 'Lectura en voz alta deshabilitada',
            'language_changed' => 'Idioma cambiado',
            'conversation_cleared' => 'Conversación limpiada',
            'print_window_opened' => 'Ventana de impresión abierta',
            'voice_selection' => 'Selección de Voz',
            'voice_alloy' => 'Alloy (Neutral)',
            'voice_echo' => 'Echo (Masculino)',
            'voice_fable' => 'Fable (Masculino)',
            'voice_onyx' => 'Onyx (Masculino)',
            'voice_nova' => 'Nova (Femenino)',
            'voice_shimmer' => 'Shimmer (Femenino)',
            'speed_control' => 'Control de Velocidad',
            'test_voice' => 'Probar Voz',
            'tts_playing' => 'Reproduciendo audio con voz {voice}',
            'tts_fallback' => 'Usando síntesis de voz del navegador',
            'tts_error' => 'Error en generación de TTS',
            
            // Sample Prompts (Spanish versions)
            'prompt_prep_steps' => 'Necesito ayuda preparándome para la primera reunión de IEP de mi hijo',
            'prompt_adhd_accommodations' => '¿Qué adaptaciones están disponibles para un estudiante con TDAH?',
            'prompt_sample_agenda' => 'Genera una agenda de muestra para la reunión de IEP de mi hijo',
            'prompt_key_questions' => 'Lista preguntas clave para hacer a los maestros durante una discusión de IEP',
            'prompt_idea_rights' => 'Ayúdame a entender los derechos de mi hijo bajo IDEA',
            'prompt_not_following' => 'Mi escuela no está siguiendo el IEP de mi hijo. ¿Qué puedo hacer?',
            'prompt_virtual_learning' => 'Sugiere estrategias para apoyar adaptaciones de aprendizaje virtual',
            'prompt_find_advocates' => '¿Cómo encuentro defensores de educación especial locales?'
        ];
    }
    
    /**
     * Get AI prompt instructions for bilingual responses
     */
    public function getAIInstructions() {
        if ($this->currentLanguage === 'es') {
            return "IMPORTANTE: Responde SIEMPRE en español. Eres GuideAI, un asistente de IA compasivo que ayuda a familias hispanas con niños con discapacidades a navegar el sistema educativo estadounidense, incluyendo IEPs y educación especial. Usa un tono cálido, empático y culturalmente sensible. Explica términos en inglés cuando sea necesario (ej: 'IEP (Programa Educativo Individualizado)'). Proporciona información práctica y accesible, adaptada a la comunidad hispanohablante.";
        } else {
            return "You are GuideAI, a compassionate AI assistant helping families with disabled children navigate the education system, including IEPs and special education. Use a warm, empathetic tone and provide practical, actionable guidance.";
        }
    }
    
    /**
     * Generate language-appropriate sample prompts
     */
    public function getSamplePrompts() {
        if ($this->currentLanguage === 'es') {
            return [
                [
                    "id" => "prep_steps_es",
                    "prompt" => "Necesito ayuda preparándome para la primera reunión de IEP de mi hijo",
                    "keywords" => ["preparar", "reunión iep", "pasos", "primera vez"]
                ],
                [
                    "id" => "adhd_accommodations_es", 
                    "prompt" => "¿Qué adaptaciones están disponibles para un estudiante con TDAH?",
                    "keywords" => ["tdah", "adaptaciones", "estudiante", "deficit atencion"]
                ],
                [
                    "id" => "sample_agenda_es",
                    "prompt" => "Genera una agenda de muestra para la reunión de IEP de mi hijo",
                    "keywords" => ["agenda", "reunión iep", "muestra", "hijo"]
                ],
                [
                    "id" => "key_questions_es",
                    "prompt" => "Lista preguntas clave para hacer a los maestros durante una discusión de IEP",
                    "keywords" => ["preguntas", "maestros", "discusión iep", "preguntar"]
                ],
                [
                    "id" => "idea_rights_es",
                    "prompt" => "Ayúdame a entender los derechos de mi hijo bajo IDEA",
                    "keywords" => ["derechos", "idea", "resumir", "hijo", "ley federal"]
                ],
                [
                    "id" => "not_following_es",
                    "prompt" => "Mi escuela no está siguiendo el IEP de mi hijo. ¿Qué puedo hacer?",
                    "keywords" => ["escuela", "no siguiendo", "iep", "que hacer", "ayuda"]
                ],
                [
                    "id" => "virtual_learning_es",
                    "prompt" => "Sugiere estrategias para apoyar adaptaciones de aprendizaje virtual",
                    "keywords" => ["aprendizaje virtual", "estrategias", "apoyo", "adaptaciones", "en línea"]
                ],
                [
                    "id" => "find_advocates_es",
                    "prompt" => "¿Cómo encuentro defensores de educación especial locales?",
                    "keywords" => ["encontrar", "defensores", "educación especial", "locales", "ayuda"]
                ]
            ];
        } else {
            return [
                [
                    "id" => "prep_steps",
                    "prompt" => "I need help preparing for my child's first IEP meeting",
                    "keywords" => ["prepare", "iep meeting", "steps", "first time"]
                ],
                [
                    "id" => "adhd_accommodations",
                    "prompt" => "What accommodations are available for a student with ADHD?",
                    "keywords" => ["adhd", "accommodations", "student", "attention deficit"]
                ],
                [
                    "id" => "sample_agenda",
                    "prompt" => "Generate a sample agenda for my child's IEP meeting",
                    "keywords" => ["agenda", "iep meeting", "sample", "child's"]
                ],
                [
                    "id" => "key_questions",
                    "prompt" => "List key questions to ask teachers during an IEP discussion",
                    "keywords" => ["questions", "teachers", "iep discussion", "ask"]
                ],
                [
                    "id" => "idea_rights",
                    "prompt" => "Help me understand my child's rights under IDEA",
                    "keywords" => ["rights", "idea", "summarize", "child's", "federal law"]
                ],
                [
                    "id" => "not_following",
                    "prompt" => "My school isn't following my child's IEP. What can I do?",
                    "keywords" => ["school", "not following", "iep", "what to do", "help"]
                ],
                [
                    "id" => "virtual_learning",
                    "prompt" => "Suggest strategies for supporting virtual learning accommodations",
                    "keywords" => ["virtual learning", "strategies", "supporting", "accommodations", "online"]
                ],
                [
                    "id" => "find_advocates",
                    "prompt" => "How do I find local special education advocates?",
                    "keywords" => ["find", "advocates", "special education", "local", "help"]
                ]
            ];
        }
    }
}

// Global function for easy access (only declare if not already exists)
if (!function_exists('lang')) {
    function lang($key, $params = []) {
        global $language_manager;
        if (!isset($language_manager)) {
            $language_manager = new LanguageManager();
        }
        return $language_manager->get($key, $params);
    }
}

// Initialize language manager (only if not already initialized)
if (!isset($language_manager)) {
    $preferred_language = $_GET['lang'] ?? $_COOKIE['guideai_language'] ?? 'en';
    $language_manager = new LanguageManager($preferred_language);
    
    // Set language cookie if changed
    if (isset($_GET['lang']) && $_GET['lang'] !== ($_COOKIE['guideai_language'] ?? 'en')) {
        setcookie('guideai_language', $_GET['lang'], time() + (86400 * 30), '/');
    }
}
?>