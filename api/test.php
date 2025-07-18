<?php
/**
 * Test API endpoint - Returns mock responses without requiring external API keys
 */

// Debug logging
error_log("Test API called at " . date('Y-m-d H:i:s'));
error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Accept-Language, Authorization');
header('Access-Control-Max-Age: 86400');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Handle GET requests for status check
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true,
        'status' => 'operational',
        'message' => 'Test API is working',
        'timestamp' => date('c')
    ]);
    exit(0);
}

// Only allow POST requests for chat
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'error' => 'Only POST method allowed'
    ]);
    exit(0);
}

// Parse input
$input = file_get_contents('php://input');
if (!$input) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => 'No input data received'
    ]);
    exit(0);
}

$data = json_decode($input, true);
if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid JSON format'
    ]);
    exit(0);
}

// Extract question
$question = trim($data['question'] ?? '');
if (!$question) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => 'Question is required'
    ]);
    exit(0);
}

// Generate intelligent mock response based on the question
$question_lower = strtolower($question);

// Define comprehensive response patterns with compassionate, family-centered tone
$responsePatterns = [
    // Greetings
    'hello|hi|hey' => 'Hello! I\'m GuideAI, your compassionate special education assistant. I understand that navigating your child\'s educational journey can feel overwhelming at times, and I\'m here to support you every step of the way. Whether you\'re feeling stressed about an upcoming IEP meeting or just need some guidance, I\'m here to help with patience and understanding. How can I assist you today?',
    
    // IEP related
    'iep|individualized education program|individual education plan' => 'IEPs (Individualized Education Programs) are personalized learning plans created specifically for your child. Think of them as a roadmap that outlines your child\'s unique needs, goals, and the support they\'ll receive. Here\'s what you need to know:

**Key Components:**
• Your child\'s current strengths and challenges
• Specific, measurable goals for the year
• Special services and support they\'ll receive
• Accommodations to help them succeed
• How progress will be measured

**Remember:** You are an equal partner in this process. Your input matters, and you have the right to ask questions and request changes. What specific aspect of IEPs would you like to explore together?',
    
    // ADHD related
    'adhd|attention deficit|hyperactivity' => 'Parenting a child with ADHD can be both rewarding and challenging. I want you to know that you\'re not alone, and there are many effective strategies to help your child thrive in school.

**Common Accommodations That Help:**
• Extra time on tests and assignments
• Sitting near the front of the classroom
• Frequent breaks during long tasks
• Organizational tools and reminders
• Positive behavior support plans

**Practical Steps You Can Take:**
1. Work closely with your child\'s teacher
2. Keep a consistent routine at home
3. Celebrate small victories
4. Connect with other parents for support

Would you like specific strategies for managing ADHD in the classroom, or do you have questions about getting the right accommodations in place?',
    
    // Autism related
    'autism|asd|autistic' => 'Every child with autism is unique, and their educational needs will be just as individual. I\'m here to help you understand how to create the best learning environment for your child.

**Common IEP Supports for Autism:**
• Speech and language therapy
• Social skills training
• Sensory accommodations (quiet spaces, noise-canceling headphones)
• Visual schedules and supports
• Structured routines and clear expectations

**What You Can Do:**
1. Document your child\'s specific needs and preferences
2. Share what works well at home
3. Ask for regular communication with teachers
4. Consider joining a parent support group

How can I help you with autism-specific educational planning? Are you preparing for an IEP meeting or looking for specific strategies?',
    
    // Learning disabilities
    'learning disability|dyslexia|dyscalculia|dysgraphia' => 'Learning disabilities don\'t mean your child isn\'t smart - they just learn differently. With the right support, your child can absolutely succeed in school and beyond.

**Common Accommodations:**
• Multisensory teaching methods
• Assistive technology (text-to-speech, speech-to-text)
• Modified assignments that match their learning style
• Specialized reading or math programs
• Extra time for processing information

**Supporting Your Child:**
• Focus on their strengths and interests
• Celebrate their progress, no matter how small
• Work with teachers to find what works best
• Consider tutoring or specialized programs

What type of learning disability are you asking about? I can provide more specific guidance and resources.',
    
    // Rights and advocacy
    'rights|advocacy|legal|due process' => 'As a parent, you have important rights under IDEA (Individuals with Disabilities Education Act). You are your child\'s best advocate, and understanding your rights helps you ensure they get the support they need.

**Your Key Rights:**
• Participate fully in all IEP meetings
• Request evaluations and assessments
• Disagree with school decisions and request mediation
• Access all your child\'s educational records
• Receive information in your preferred language

**Practical Advocacy Tips:**
1. Keep detailed records of all meetings and communications
2. Put important requests in writing
3. Know that you can bring someone with you to meetings
4. Don\'t be afraid to ask questions

Would you like to learn more about specific parental rights or get help with a particular situation?',
    
    // Accommodations
    'accommodation|modification|support' => 'Accommodations are changes that help your child access the same learning as their peers, just in a way that works better for them. Think of them as tools that level the playing field.

**Common Accommodations:**
• Extra time on tests and assignments
• Preferential seating (near the teacher, away from distractions)
• Assistive technology (computers, calculators, audio books)
• Visual aids and graphic organizers
• Breaks during long tasks

**Getting the Right Accommodations:**
1. Document your child\'s specific challenges
2. Ask teachers what they\'ve observed
3. Request accommodations in writing
4. Follow up to make sure they\'re being provided

What type of accommodation are you looking for? I can help you understand what might work best for your child\'s specific needs.',
    
    // Testing and evaluation
    'test|evaluation|assessment|diagnosis' => 'Educational evaluations can feel overwhelming, but they\'re designed to help your child get the support they need. The process should be collaborative and transparent.

**What to Expect:**
• Parent consent is required before any evaluation
• Comprehensive assessment of your child\'s strengths and needs
• Clear explanation of results in language you understand
• Discussion of eligibility for special education services

**Your Role in the Process:**
1. Ask questions about any part you don\'t understand
2. Share your observations about your child
3. Request evaluations in writing if needed
4. Get copies of all reports and results

Are you preparing for an evaluation, or do you have questions about results you\'ve received?',
    
    // Behavior and discipline
    'behavior|discipline|suspension|expulsion' => 'When behavior challenges arise, it can be incredibly stressful for families. Remember that students with disabilities have special protections, and the school must consider whether behavior is related to their disability.

**Your Child\'s Protections:**
• Schools must consider if behavior is related to the disability
• Functional behavior assessments may be required
• Positive behavior support plans should be developed
• Suspensions have limits for students with disabilities

**What You Can Do:**
1. Request a meeting to discuss the situation
2. Ask for a functional behavior assessment
3. Work with the school to develop a behavior plan
4. Document all incidents and communications

Are you dealing with a specific behavioral situation? I can help you understand your options and next steps.',
    
    // Transition planning
    'transition|high school|college|career' => 'Transition planning helps prepare your child for life after high school. It\'s never too early to start thinking about their future goals and dreams.

**Transition Planning Includes:**
• Post-secondary education options
• Employment and career exploration
• Independent living skills
• Community participation and social connections

**Starting the Conversation:**
1. Talk with your child about their interests and goals
2. Explore different post-secondary options
3. Connect with vocational rehabilitation services
4. Build independent living skills gradually

What transition goals are you considering? Whether it\'s college, vocational training, or employment, I can help you explore the options.',
    
    // General help
    'help|assist|support' => 'I\'m here to support you through every aspect of your child\'s special education journey. Whether you\'re feeling overwhelmed, confused, or just need someone to talk to, I understand that this path can be challenging.

**How I Can Help:**
• IEP development and meeting preparation
• Understanding your rights and the special education process
• Finding resources and support groups
• Advocating for your child\'s needs
• Connecting you with helpful organizations

**Remember:** You\'re doing an amazing job advocating for your child. It\'s okay to ask for help, and it\'s okay to take things one step at a time.

What specific area do you need help with? I\'m here to listen and provide practical guidance.'
];

// Find the best matching response
$response = null;
foreach ($responsePatterns as $pattern => $mockResponse) {
    if (preg_match('/(' . $pattern . ')/', $question_lower)) {
        $response = $mockResponse;
        break;
    }
}

// If no specific pattern matches, provide a contextual response
if (!$response) {
    $response = 'Thank you for your question about "' . htmlspecialchars($question) . '". I\'m GuideAI, your compassionate special education assistant. I understand that every family\'s journey is unique, and I\'m here to support you with patience and understanding.

While I\'m currently in test mode, I can help you with:
• IEP questions and meeting preparation
• Understanding your child\'s rights
• Finding appropriate accommodations
• Advocacy strategies and resources
• Emotional support and guidance

Could you tell me more about what you\'re looking for? I\'m here to listen and provide practical, family-centered guidance.';
}

// Generate dynamic resource suggestions based on the question
$resourceSuggestions = [
    'iep' => [
        [
            'title' => 'Understanding IEPs - Parent Center Hub',
            'url' => 'https://www.parentcenterhub.org/iep-overview/',
            'description' => 'Comprehensive guide to Individualized Education Programs'
        ],
        [
            'title' => 'IEP Meeting Checklist',
            'url' => 'https://www.understood.org/en/school-learning/special-services/ieps/iep-meeting-checklist',
            'description' => 'Prepare for your IEP meeting with this helpful checklist'
        ]
    ],
    'adhd' => [
        [
            'title' => 'ADHD and School - CHADD',
            'url' => 'https://chadd.org/for-parents/overview/',
            'description' => 'Resources for parents of children with ADHD'
        ],
        [
            'title' => 'ADHD Accommodations Guide',
            'url' => 'https://www.additudemag.com/iep-accommodations-adhd/',
            'description' => 'Common accommodations for students with ADHD'
        ]
    ],
    'autism' => [
        [
            'title' => 'Autism Speaks - School Resources',
            'url' => 'https://www.autismspeaks.org/school-resources',
            'description' => 'Educational resources for autism support'
        ],
        [
            'title' => 'Autism IEP Goals',
            'url' => 'https://www.autismparentingmagazine.com/iep-goals-autism/',
            'description' => 'Sample IEP goals for students with autism'
        ]
    ],
    'rights' => [
        [
            'title' => 'Wrightslaw - Special Education Law',
            'url' => 'https://www.wrightslaw.com/',
            'description' => 'Comprehensive information about special education law'
        ],
        [
            'title' => 'IDEA Parent Guide',
            'url' => 'https://www.parentcenterhub.org/idea/',
            'description' => 'Understanding the Individuals with Disabilities Education Act'
        ]
    ]
];

// Select relevant resources based on question content
$selectedResources = [];
foreach ($resourceSuggestions as $keyword => $resources) {
    if (strpos($question_lower, $keyword) !== false) {
        $selectedResources = $resources;
        break;
    }
}

// Default resources if no specific match
if (empty($selectedResources)) {
    $selectedResources = [
        [
            'title' => 'Parent Center Hub',
            'url' => 'https://www.parentcenterhub.org/',
            'description' => 'Comprehensive special education resources for parents'
        ],
        [
            'title' => 'Understood.org',
            'url' => 'https://www.understood.org/',
            'description' => 'Resources for learning and thinking differences'
        ]
    ];
}

// Return success response
echo json_encode([
    'success' => true,
    'result' => [
        'mega_response' => $response,
        'related_readings' => $selectedResources,
        'location_resources' => [],
        'emergency_contacts' => []
    ],
    'timestamp' => date('c')
]); 