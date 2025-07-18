<?php

/**
 * Resource Linking System for IEP and Special Education
 * Automatically detects mentioned resources and adds accurate links
 */
class ResourceLinkingSystem {
    
    private $federal_resources;
    private $state_resources;
    private $advocacy_resources;
    private $legal_resources;
    
    public function __construct() {
        $this->initializeFederalResources();
        $this->initializeStateResources();
        $this->initializeAdvocacyResources();
        $this->initializeLegalResources();
    }
    
    /**
     * Main function to enhance AI responses with resource links
     * $response_content is expected to be HTML after Parsedown processing.
     * Keyword detection will be done on the plain text version of $response_content.
     */
    public function enhanceResponseWithLinks($response_html_content, $user_location = null) {
        $final_html_content = $response_html_content;
        $found_resources = [];
        
        // Use plain text for keyword detection
        $plain_text_content = strtolower(strip_tags($response_html_content));

        // Detect and link federal resources
        $federal_links = $this->detectAndLinkFederalResources($plain_text_content);
        if (!empty($federal_links)) {
            $found_resources['federal'] = $federal_links;
        }
        
        // Detect and link state resources
        if ($user_location) {
            // Pass plain text for keyword detection within state-specific logic too if needed
            $state_links = $this->detectAndLinkStateResources($plain_text_content, $user_location);
            if (!empty($state_links)) {
                $found_resources = array_merge($found_resources, $state_links);
            }
        }
        
        // Detect and link advocacy resources
        $advocacy_links = $this->detectAndLinkAdvocacyResources($plain_text_content);
        if (!empty($advocacy_links)) {
            $found_resources['advocacy'] = $advocacy_links;
        }
        
        // Detect and link legal resources
        $legal_links = $this->detectAndLinkLegalResources($plain_text_content);
         if (!empty($legal_links)) {
            $found_resources['legal'] = $legal_links;
        }
        
        // Append resource section if resources were found
        if (!empty($found_resources)) {
            $final_html_content .= $this->buildResourceSection($found_resources);
        }
        
        return $final_html_content;
    }
    
    /**
     * Federal Resources Database
     */
    private function initializeFederalResources() {
        $this->federal_resources = [
            // Department of Education
            'idea' => [
                'name' => 'IDEA - Individuals with Disabilities Education Act',
                'url' => 'https://sites.ed.gov/idea/',
                'description' => 'Federal law ensuring special education services',
                'keywords' => ['idea', 'individuals with disabilities education act', 'federal law', 'special education law']
            ],
            'osep' => [
                'name' => 'Office of Special Education Programs (OSEP)',
                'url' => 'https://www2.ed.gov/about/offices/list/osers/osep/index.html',
                'description' => 'Federal oversight of special education',
                'keywords' => ['osep', 'office of special education programs']
            ],
            'section_504' => [
                'name' => 'Section 504 of the Rehabilitation Act',
                'url' => 'https://www.hhs.gov/civil-rights/for-individuals/disability/section-504-rehabilitation-act-of-1973/index.html',
                'description' => 'Civil rights law prohibiting disability discrimination',
                'keywords' => ['section 504', '504 plan', 'rehabilitation act']
            ],
            'ada' => [
                'name' => 'Americans with Disabilities Act (ADA)',
                'url' => 'https://www.ada.gov/',
                'description' => 'Civil rights law for people with disabilities',
                'keywords' => ['ada', 'americans with disabilities act']
            ],
            
            // Specific Programs and Services
            'early_intervention' => [
                'name' => 'Early Intervention Program (Part C)',
                'url' => 'https://www.cdc.gov/ncbddd/actearly/parents/states.html',
                'description' => 'Services for infants and toddlers with disabilities',
                'keywords' => ['early intervention', 'part c', 'birth to 3', 'infant', 'toddler']
            ],
            'transition_services' => [
                'name' => 'Transition Services Requirements',
                'url' => 'https://sites.ed.gov/idea/regs/b/a/300.43',
                'description' => 'Federal requirements for transition planning',
                'keywords' => ['transition services', 'transition planning', 'post-secondary']
            ],
            'due_process' => [
                'name' => 'Due Process Hearing Rights',
                'url' => 'https://sites.ed.gov/idea/regs/b/e/300.507',
                'description' => 'Procedural safeguards and hearing rights',
                'keywords' => ['due process', 'hearing', 'procedural safeguards', 'dispute resolution']
            ],
            
            // Resources and Guides
            'parent_guide' => [
                'name' => 'A Guide to the IEP (Department of Education)',
                'url' => 'https://www2.ed.gov/parents/needs/speced/iepguide/index.html',
                'description' => 'Comprehensive guide for parents',
                'keywords' => ['parent guide', 'iep guide', 'department of education guide']
            ],
            'nichcy' => [
                'name' => 'Center for Parent Information and Resources',
                'url' => 'https://www.parentcenterhub.org/',
                'description' => 'Federal parent training and information center',
                'keywords' => ['parent information center', 'nichcy', 'parent training']
            ]
        ];
    }
    
    /**
     * State Resources Database (Oregon gets priority for grant opportunities)
     */
    private function initializeStateResources() {
        $this->state_resources = [
            // OREGON - PRIORITY STATE FOR GRANT OPPORTUNITIES
            'oregon' => [
                'dept_education' => [
                    'name' => 'Oregon Department of Education - Office of Enhancing Student Opportunities',
                    'url' => 'https://www.oregon.gov/ode/students-and-family/SpecialEducation/Pages/default.aspx',
                    'phone' => '503-947-5600',
                    'description' => 'Oregon state special education oversight and services'
                ],
                'disability_rights' => [
                    'name' => 'Disability Rights Oregon',
                    'url' => 'https://droregon.org/',
                    'phone' => '503-243-2081',
                    'description' => 'Legal advocacy and protection for Oregonians with disabilities'
                ],
                'parent_training' => [
                    'name' => 'Oregon Parent Training and Information Center (OPTIC)',
                    'url' => 'https://www.orptc.org/',
                    'phone' => '888-988-9315',
                    'description' => 'Parent training, information, and advocacy support'
                ],
                'advocacy_coalition' => [
                    'name' => 'Oregon Developmental Disabilities Coalition',
                    'url' => 'https://www.orddcoalition.org/',
                    'phone' => '503-581-8156',
                    'description' => 'Advocacy for people with developmental disabilities and their families'
                ],
                'autism_support' => [
                    'name' => 'Autism Society of Oregon',
                    'url' => 'https://autismsocietyoforegon.org/',
                    'phone' => '503-636-1676',
                    'description' => 'Support, advocacy, and resources for autism community'
                ],
                'early_intervention' => [
                    'name' => 'Oregon Early Intervention/Early Childhood Special Education',
                    'url' => 'https://www.oregon.gov/ode/students-and-family/SpecialEducation/EarlyIntervention/Pages/default.aspx',
                    'phone' => '503-947-5600',
                    'description' => 'Services for children birth to age 5 with disabilities'
                ],
                
                // OREGON COUNTIES
                'counties' => [
                    // Top 5 Most Populous Counties + Deschutes
                    'multnomah' => [
                        'name' => 'Multnomah County (Portland) - Disability Services',
                        'url' => 'https://www.multco.us/ads',
                        'phone' => '503-988-3646',
                        'description' => 'Portland metro area disability services and advocacy',
                        'esd' => [
                            'name' => 'Multnomah Education Service District',
                            'url' => 'https://www.mesd.k12.or.us/',
                            'phone' => '503-257-1500',
                            'description' => 'Regional education support including special education'
                        ]
                    ],
                    'washington' => [
                        'name' => 'Washington County - Disability Services',
                        'url' => 'https://www.co.washington.or.us/HHS/DAVS/',
                        'phone' => '503-846-4460',
                        'description' => 'Beaverton/Hillsboro area disability and veteran services',
                        'esd' => [
                            'name' => 'Washington County Education Service District',
                            'url' => 'https://www.wcesd.k12.or.us/',
                            'phone' => '503-614-1428',
                            'description' => 'Special education support for Washington County schools'
                        ]
                    ],
                    'clackamas' => [
                        'name' => 'Clackamas County - Disability Services',
                        'url' => 'https://www.clackamas.us/social-services/disability-services',
                        'phone' => '503-742-5300',
                        'description' => 'Social services and disability support',
                        'esd' => [
                            'name' => 'Clackamas Education Service District',
                            'url' => 'https://www.clackesd.k12.or.us/',
                            'phone' => '503-675-4000',
                            'description' => 'Regional special education and student services'
                        ]
                    ],
                    'lane' => [
                        'name' => 'Lane County - Developmental Disabilities Services',
                        'url' => 'https://www.lanecounty.org/cms/one.aspx?portalId=3922741&pageId=4079167',
                        'phone' => '541-682-3405',
                        'description' => 'Eugene area developmental disabilities support',
                        'esd' => [
                            'name' => 'Lane Education Service District',
                            'url' => 'https://www.lane.k12.or.us/',
                            'phone' => '541-461-8200',
                            'description' => 'Special education coordination for Lane County'
                        ]
                    ],
                    'marion' => [
                        'name' => 'Marion County - Developmental Disability Services',
                        'url' => 'https://www.co.marion.or.us/HW/DD',
                        'phone' => '503-566-2352',
                        'description' => 'Salem area developmental disability services',
                        'esd' => [
                            'name' => 'Willamette Education Service District',
                            'url' => 'https://www.wesd.org/',
                            'phone' => '503-588-5330',
                            'description' => 'Special education support for Marion County region'
                        ]
                    ],
                    // DESCHUTES COUNTY - Special focus for Bend area
                    'deschutes' => [
                        'name' => 'Deschutes County - Developmental Disabilities Program',
                        'url' => 'https://www.deschutes.org/health/page/developmental-disabilities-program',
                        'phone' => '541-617-4624',
                        'description' => 'Central Oregon developmental disabilities services and support',
                        'esd' => [
                            'name' => 'High Desert Education Service District',
                            'url' => 'https://www.hdesd.org/',
                            'phone' => '541-693-6801',
                            'description' => 'Regional special education services for Central Oregon'
                        ],
                        'local_advocacy' => [
                            'name' => 'Central Oregon Disability Support Network (CODSN)',
                            'url' => 'https://www.codsn.org/',
                            'phone' => '541-548-8559',
                            'description' => 'Local advocacy and support for disabilities in Central Oregon',
                            'keywords' => ['codsn', 'central oregon disability support network', 'disability support', 'deschutes county disability']
                        ],
                        'bend_specific' => [
                            'name' => 'Bend-La Pine Schools - Special Education',
                            'url' => 'https://www.bend.k12.or.us/departments/student-services/special-education',
                            'phone' => '541-355-1000',
                            'description' => 'Local school district special education services'
                        ]
                    ],
                    // CENTRAL OREGON COUNTIES - Using Central Oregon Disability Support Network
                    'crook' => [
                        'name' => 'Crook County - Developmental Disabilities Program',
                        'url' => 'https://www.co.crook.or.us/health/page/developmental-disabilities',
                        'phone' => '541-447-5165',
                        'description' => 'Crook County developmental disabilities services',
                        'esd' => [
                            'name' => 'High Desert Education Service District',
                            'url' => 'https://www.hdesd.org/',
                            'phone' => '541-693-6801',
                            'description' => 'Regional special education services for Central Oregon'
                        ],
                        'school_district' => [
                            'name' => 'Crook County School District',
                            'url' => 'https://www.crookcounty.k12.or.us/',
                            'phone' => '541-447-5664',
                            'description' => 'Local school district special education services'
                        ]
                    ],
                    'jefferson' => [
                        'name' => 'Jefferson County - Developmental Disabilities Program',
                        'url' => 'https://www.co.jefferson.or.us/health/page/developmental-disabilities',
                        'phone' => '541-475-4456',
                        'description' => 'Jefferson County developmental disabilities services',
                        'esd' => [
                            'name' => 'High Desert Education Service District',
                            'url' => 'https://www.hdesd.org/',
                            'phone' => '541-693-6801',
                            'description' => 'Regional special education services for Central Oregon'
                        ],
                        'school_district' => [
                            'name' => 'Jefferson County School District',
                            'url' => 'https://www.jcsd.k12.or.us/',
                            'phone' => '541-475-6192',
                            'description' => 'Local school district special education services'
                        ]
                    ],
                    'klamath' => [
                        'name' => 'Klamath County - Developmental Disabilities Program',
                        'url' => 'https://www.klamathcounty.org/health/page/developmental-disabilities',
                        'phone' => '541-883-5112',
                        'description' => 'Klamath County developmental disabilities services',
                        'esd' => [
                            'name' => 'Klamath County Education Service District',
                            'url' => 'https://www.kcesd.k12.or.us/',
                            'phone' => '541-883-4700',
                            'description' => 'Regional special education services for Klamath County'
                        ],
                        'school_district' => [
                            'name' => 'Klamath Falls City Schools',
                            'url' => 'https://www.kfalls.k12.or.us/',
                            'phone' => '541-883-4700',
                            'description' => 'Local school district special education services'
                        ]
                    ],
                    'lake' => [
                        'name' => 'Lake County - Developmental Disabilities Program',
                        'url' => 'https://www.lakecountyor.org/health/page/developmental-disabilities',
                        'phone' => '541-947-6044',
                        'description' => 'Lake County developmental disabilities services',
                        'esd' => [
                            'name' => 'Lake County Education Service District',
                            'url' => 'https://www.lcesd.k12.or.us/',
                            'phone' => '541-947-2341',
                            'description' => 'Regional special education services for Lake County'
                        ],
                        'school_district' => [
                            'name' => 'Lake County School District',
                            'url' => 'https://www.lake.k12.or.us/',
                            'phone' => '541-947-2341',
                            'description' => 'Local school district special education services'
                        ]
                    ],
                    'grant' => [
                        'name' => 'Grant County - Developmental Disabilities Program',
                        'url' => 'https://www.grantcounty-or.gov/health/page/developmental-disabilities',
                        'phone' => '541-575-0429',
                        'description' => 'Grant County developmental disabilities services',
                        'esd' => [
                            'name' => 'Grant County Education Service District',
                            'url' => 'https://www.gcesd.k12.or.us/',
                            'phone' => '541-575-0429',
                            'description' => 'Regional special education services for Grant County'
                        ],
                        'school_district' => [
                            'name' => 'Grant County School District',
                            'url' => 'https://www.grantesd.k12.or.us/',
                            'phone' => '541-575-0429',
                            'description' => 'Local school district special education services'
                        ]
                    ],
                    'harney' => [
                        'name' => 'Harney County - Developmental Disabilities Program',
                        'url' => 'https://www.co.harney.or.us/health/page/developmental-disabilities',
                        'phone' => '541-573-2274',
                        'description' => 'Harney County developmental disabilities services',
                        'esd' => [
                            'name' => 'Harney County Education Service District',
                            'url' => 'https://www.harneyesd.k12.or.us/',
                            'phone' => '541-573-2274',
                            'description' => 'Regional special education services for Harney County'
                        ],
                        'school_district' => [
                            'name' => 'Harney County School District',
                            'url' => 'https://www.harney.k12.or.us/',
                            'phone' => '541-573-2274',
                            'description' => 'Local school district special education services'
                        ]
                    ],
                    'wheeler' => [
                        'name' => 'Wheeler County - Developmental Disabilities Program',
                        'url' => 'https://www.co.wheeler.or.us/health/page/developmental-disabilities',
                        'phone' => '541-763-2745',
                        'description' => 'Wheeler County developmental disabilities services',
                        'esd' => [
                            'name' => 'Wheeler County Education Service District',
                            'url' => 'https://www.wheeleresd.k12.or.us/',
                            'phone' => '541-763-2745',
                            'description' => 'Regional special education services for Wheeler County'
                        ],
                        'school_district' => [
                            'name' => 'Wheeler County School District',
                            'url' => 'https://www.wheeler.k12.or.us/',
                            'phone' => '541-763-2745',
                            'description' => 'Local school district special education services'
                        ]
                    ]
                ],
                
                // OREGON GRANT OPPORTUNITIES
                'grant_resources' => [
                    'oregon_community_foundation' => [
                        'name' => 'Oregon Community Foundation - Disability Grants',
                        'url' => 'https://oregoncf.org/grants-scholarships/',
                        'phone' => '503-227-6846',
                        'description' => 'Grants for disability services and education programs'
                    ],
                    'oregon_dept_human_services' => [
                        'name' => 'Oregon DHS - Vocational Rehabilitation',
                        'url' => 'https://www.oregon.gov/dhs/EMPLOYMENT/VR/Pages/index.aspx',
                        'phone' => '877-277-0513',
                        'description' => 'Funding for disability-related education and employment'
                    ],
                    'oregon_health_authority' => [
                        'name' => 'Oregon Health Authority - Developmental Disabilities',
                        'url' => 'https://www.oregon.gov/oha/HSD/DD/Pages/index.aspx',
                        'phone' => '503-945-9789',
                        'description' => 'State funding for developmental disability services'
                    ]
                ],
                
                // OREGON LEGAL RESOURCES
                'legal_aid' => [
                    'name' => 'Legal Aid Services of Oregon',
                    'url' => 'https://lasoregon.org/',
                    'phone' => '503-684-1493',
                    'description' => 'Free legal services for low-income Oregonians'
                ],
                'oregon_law_center' => [
                    'name' => 'Oregon Law Center',
                    'url' => 'https://oregonlawcenter.org/',
                    'phone' => '503-473-8673',
                    'description' => 'Legal advocacy for low-income communities'
                ]
            ],
            
            'california' => [
                'dept_education' => [
                    'name' => 'California Department of Education - Special Education',
                    'url' => 'https://www.cde.ca.gov/sp/se/',
                    'phone' => '916-319-0800',
                    'description' => 'State special education oversight and resources'
                ],
                'disability_rights' => [
                    'name' => 'Disability Rights California',
                    'url' => 'https://www.disabilityrightsca.org/',
                    'phone' => '800-776-5746',
                    'description' => 'Legal advocacy for disability rights'
                ],
                'parent_training' => [
                    'name' => 'MATRIX Parent Network',
                    'url' => 'https://www.matrixparents.org/',
                    'phone' => '800-578-5512',
                    'description' => 'Parent training and information center'
                ]
            ],
            'texas' => [
                'dept_education' => [
                    'name' => 'Texas Education Agency - Special Education',
                    'url' => 'https://tea.texas.gov/academics/special-student-populations/special-education',
                    'phone' => '512-463-9414',
                    'description' => 'State special education oversight'
                ],
                'advocacy' => [
                    'name' => 'Disability Rights Texas',
                    'url' => 'https://www.disabilityrightstx.org/',
                    'phone' => '800-252-9108',
                    'description' => 'Legal advocacy and protection'
                ],
                'parent_center' => [
                    'name' => 'Partners Resource Network',
                    'url' => 'https://www.partnerstx.org/',
                    'phone' => '800-866-4726',
                    'description' => 'Parent information and training'
                ]
            ],
            'florida' => [
                'dept_education' => [
                    'name' => 'Florida Department of Education - Exceptional Student Education',
                    'url' => 'https://www.fldoe.org/academics/exceptional-student-edu/',
                    'phone' => '850-245-0475',
                    'description' => 'State special education services'
                ],
                'advocacy' => [
                    'name' => 'Disability Rights Florida',
                    'url' => 'https://disabilityrightsflorida.org/',
                    'phone' => '800-342-0823',
                    'description' => 'Protection and advocacy services'
                ]
            ],
            'new_york' => [
                'dept_education' => [
                    'name' => 'New York State Education Department - Special Education',
                    'url' => 'http://www.nysed.gov/special-education',
                    'phone' => '518-474-2714',
                    'description' => 'State special education oversight'
                ],
                'advocacy' => [
                    'name' => 'Advocates for Children of New York',
                    'url' => 'https://www.advocatesforchildren.org/',
                    'phone' => '212-947-9779',
                    'description' => 'Educational advocacy organization'
                ]
            ]
            // Add more states as needed
        ];
    }
    
    /**
     * National Advocacy Resources
     */
    private function initializeAdvocacyResources() {
        $this->advocacy_resources = [
            'arc' => [
                'name' => 'The Arc',
                'url' => 'https://thearc.org/',
                'phone' => '800-433-5255',
                'description' => 'National advocacy for people with intellectual and developmental disabilities',
                'keywords' => ['the arc', 'intellectual disability', 'developmental disability']
            ],
            'autism_society' => [
                'name' => 'Autism Society',
                'url' => 'https://autismsociety.org/',
                'phone' => '800-328-8476',
                'description' => 'Support and advocacy for autism community',
                'keywords' => ['autism society', 'autism support', 'autism advocacy']
            ],
            'chadd' => [
                'name' => 'CHADD - ADHD Support',
                'url' => 'https://chadd.org/',
                'phone' => '301-306-7070',
                'description' => 'Support for children and adults with ADHD',
                'keywords' => ['chadd', 'adhd', 'attention deficit']
            ],
            'learning_disabilities' => [
                'name' => 'Learning Disabilities Association',
                'url' => 'https://ldaamerica.org/',
                'phone' => '412-341-1515',
                'description' => 'Support for learning disabilities',
                'keywords' => ['learning disabilities', 'lda', 'dyslexia']
            ],
            'ndrn' => [
                'name' => 'National Disability Rights Network',
                'url' => 'https://www.ndrn.org/',
                'phone' => '202-408-9514',
                'description' => 'Protection and advocacy systems',
                'keywords' => ['disability rights network', 'protection and advocacy']
            ],
            'copaa' => [
                'name' => 'Council of Parent Attorneys and Advocates (COPAA)',
                'url' => 'https://www.copaa.org/',
                'phone' => '844-426-7222',
                'description' => 'Legal resources and attorney referrals',
                'keywords' => ['copaa', 'parent attorneys', 'legal help', 'attorney referral']
            ]
        ];
    }
    
    /**
     * Legal Resources
     */
    private function initializeLegalResources() {
        $this->legal_resources = [
            'wrightslaw' => [
                'name' => 'Wrightslaw',
                'url' => 'https://www.wrightslaw.com/',
                'description' => 'Special education law and advocacy information',
                'keywords' => ['wrightslaw', 'special education law', 'legal information']
            ],
            'legal_aid' => [
                'name' => 'Legal Services Corporation (Find Local Legal Aid)',
                'url' => 'https://www.lsc.gov/find-legal-aid',
                'description' => 'Find free legal help in your area',
                'keywords' => ['legal aid', 'free legal help', 'low income legal services']
            ],
            'resolve_disputes' => [
                'name' => 'CADRE - Dispute Resolution',
                'url' => 'https://www.cadreworks.org/',
                'phone' => '541-346-2040',
                'description' => 'Mediation and dispute resolution resources',
                'keywords' => ['cadre', 'mediation', 'dispute resolution']
            ]
        ];
    }
    
    /**
     * Detect and link federal resources mentioned in content
     */
    private function detectAndLinkFederalResources($plain_text_content) {
        $linked_resources = []; 
        
        foreach ($this->federal_resources as $key => $resource) {
            foreach ($resource['keywords'] as $keyword) {
                if (strpos($plain_text_content, $keyword) !== false) {
                    $linked_resources[] = $resource; 
                    break; 
                }
            }
        }
        
        return $linked_resources;
    }
    
    /**
     * Detect and link state-specific resources (Oregon prioritized)
     */
    private function detectAndLinkStateResources($plain_text_content, $user_location) {
        $linked_resources_by_category = []; 
        $state_code = $this->detectUserState($user_location);
        
        if ($state_code && isset($this->state_resources[$state_code])) {
            $current_state_data = $this->state_resources[$state_code];
            $state_display_list = [];
            
            // Always include core state resources if they exist
            if (isset($current_state_data['dept_education'])) $state_display_list[] = $current_state_data['dept_education'];
            if (isset($current_state_data['disability_rights'])) $state_display_list[] = $current_state_data['disability_rights'];
            if (isset($current_state_data['parent_training'])) $state_display_list[] = $current_state_data['parent_training'];
            if (isset($current_state_data['advocacy_coalition'])) $state_display_list[] = $current_state_data['advocacy_coalition'];
            
            if (!empty($state_display_list)) {
                 $linked_resources_by_category['state'] = $state_display_list;
            }

            // OREGON SPECIFIC ENHANCEMENTS
            if ($state_code === 'oregon') {
                $county_code = $this->detectOregonCounty($user_location);
                if ($county_code && isset($current_state_data['counties'][$county_code])) {
                    $county_info = $current_state_data['counties'][$county_code];
                    $county_display_list = [];
                    $county_display_list[] = [
                        'name' => $county_info['name'], 'url' => $county_info['url'],
                        'phone' => $county_info['phone'] ?? null, 'description' => $county_info['description']
                    ];
                    if(isset($county_info['esd'])) {
                        $county_display_list[] = [
                            'name' => $county_info['esd']['name'], 'url' => $county_info['esd']['url'],
                            'phone' => $county_info['esd']['phone'] ?? null, 'description' => $county_info['esd']['description']
                        ];
                    }
                    if(isset($county_info['school_district'])) {
                        $county_display_list[] = [
                            'name' => $county_info['school_district']['name'], 'url' => $county_info['school_district']['url'],
                            'phone' => $county_info['school_district']['phone'] ?? null, 'description' => $county_info['school_district']['description']
                        ];
                    }
                    
                    // Add specific parent support group based on county
                    $parent_support_group = $this->getOregonParentSupportGroup($county_code);
                    $county_display_list[] = $parent_support_group;
                    
                    if ($county_code === 'deschutes') {
                        if(isset($county_info['local_advocacy'])) {
                            $county_display_list[] = $county_info['local_advocacy'];
                        }
                        if(isset($county_info['bend_specific'])) {
                             $county_display_list[] = $county_info['bend_specific'];
                        }
                    }
                    if(!empty($county_display_list)) $linked_resources_by_category['county'] = $county_display_list;
                } else {
                    // If no specific county detected, still add the appropriate parent support group
                    // Default to FACT Oregon for unknown counties
                    $parent_support_group = $this->getOregonParentSupportGroup('other');
                    $linked_resources_by_category['oregon_parent_support'] = [$parent_support_group];
                }
                
                if (isset($current_state_data['grant_resources'])) {
                    $linked_resources_by_category['grants'] = array_values($current_state_data['grant_resources']);
                }
                
                $oregon_legal_list = [];
                if (isset($current_state_data['legal_aid'])) $oregon_legal_list[] = $current_state_data['legal_aid'];
                if (isset($current_state_data['oregon_law_center'])) $oregon_legal_list[] = $current_state_data['oregon_law_center'];
                if(!empty($oregon_legal_list)) $linked_resources_by_category['legal_oregon'] = $oregon_legal_list;

                if (stripos($plain_text_content, 'early intervention') !== false || stripos($plain_text_content, 'birth to 3') !== false || stripos($plain_text_content, 'preschool') !== false) {
                    if(isset($current_state_data['early_intervention'])) $linked_resources_by_category['state'][] = $current_state_data['early_intervention'];
                }
                if (stripos($plain_text_content, 'autism') !== false) {
                     if(isset($current_state_data['autism_support'])) $linked_resources_by_category['state'][] = $current_state_data['autism_support'];
                }
                 // Ensure state array only has unique entries after additions
                if(isset($linked_resources_by_category['state'])){
                    $linked_resources_by_category['state'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $linked_resources_by_category['state']))));
                }
            }
        }
        
        return $linked_resources_by_category;
    }
    
    /**
     * Detect and link advocacy resources
     */
    private function detectAndLinkAdvocacyResources($content) {
        $linked_resources = [];
        $content_lower = strtolower($content);
        
        foreach ($this->advocacy_resources as $key => $resource) {
            foreach ($resource['keywords'] as $keyword) {
                if (strpos($content_lower, $keyword) !== false) {
                    $linked_resources[] = $resource;
                    break;
                }
            }
        }
        return $linked_resources;
    }
    
    /**
     * Detect and link legal resources
     */
    private function detectAndLinkLegalResources($content) {
        $linked_resources = [];
        $content_lower = strtolower($content);
        $legal_keywords = ['due process', 'attorney', 'lawyer', 'legal', 'mediation', 'lawsuit', 'rights violation', 'procedural safeguards'];
        
        $found_general_legal_keyword = false;
        foreach ($legal_keywords as $keyword) {
            if (strpos($content_lower, $keyword) !== false) {
                $found_general_legal_keyword = true;
                break;
            }
        }

        if ($found_general_legal_keyword) {
            // Add general legal resources if any general legal keyword is found
            foreach ($this->legal_resources as $resource_key => $resource_info) {
                 // Avoid duplicating Wrightslaw if already picked by federal IDEA keywords (though less likely now with direct keyword checks)
                $is_wrightslaw_already_added = false;
                if (isset($linked_resources['federal'])) {
                    foreach($linked_resources['federal'] as $fed_res) {
                        if (strtolower($fed_res['name']) === 'wrightslaw') {
                            $is_wrightslaw_already_added = true;
                            break;
                        }
                    }
                }
                if ($resource_key === 'wrightslaw' && $is_wrightslaw_already_added) continue;
                $linked_resources[] = $resource_info;
            }
        }
        return $linked_resources;
    }
    
    /**
     * Build formatted resource section (Oregon-enhanced)
     */
    private function buildResourceSection($found_resources) {
        if (empty($found_resources)) {
            return '';
        }

        $html = '<hr class="mt-4 mb-3">';
        $html .= '<div class="ai-resource-section">';
        // General title for the whole resource box, can be kept or removed based on preference.
        // $html .= '<h5 class="mb-3"><i class="fas fa-link me-2"></i>Relevant Resources</h5>';

        $local_categories = [];
        $other_categories = [];

        // Sort resources into local and other categories
        foreach ($found_resources as $category_key => $resources) {
            if (empty($resources)) continue;
            if ($category_key === 'keywords') continue; // Skip keywords array

            if (strpos($category_key, 'state_') === 0 || 
                strpos($category_key, 'county_') === 0 || 
                $category_key === 'oregon_grant_resources' ||
                // Add specific known state keys if they don't follow state_ prefix, e.g. 'oregon' (if it's a key)
                // Based on initializeStateResources, 'oregon' itself isn't a key for a list of linkable resources directly in $found_resources,
                // but rather a container for state-specific resources that are then extracted.
                // The keys in $found_resources for state resources are more like 'state_OR' or dynamic ones.
                // Let's assume keys like 'state_OR', 'county_multnomah_OR', 'oregon_grant_resources' exist.
                // A more robust check might be needed if category keys are very diverse.
                // For now, checking for 'state_', 'county_', and 'oregon_grant_resources' should cover it.
                 $category_key === 'state_OR' || // Example if 'state_OR' becomes a direct key
                 $category_key === 'washington_state_resources' // Example for another state
                ) {
                $local_categories[$category_key] = $resources;
            } else {
                $other_categories[$category_key] = $resources;
            }
        }
        
        $has_rendered_any_section = false;

        // Render Local & State Resources first
        if (!empty($local_categories)) {
            $html .= '<h5 class="mb-3 mt-2"><i class="fas fa-map-marker-alt me-2"></i>Local & State Resources</h5>';
            $html .= '<ul class="list-unstyled mb-3">'; // Outer list for local categories
            foreach ($local_categories as $category_key => $resources_list) {
                $category_title = ucfirst(str_replace('_', ' ', $category_key));
                 if ($category_key === 'oregon_grant_resources') {
                    $category_title = 'Oregon Grant & Support Opportunities';
                } elseif (str_ends_with($category_key, '_county_resources')) {
                    $county_name = ucfirst(str_replace('_county_resources', '', $category_key));
                    $category_title = $county_name . ' County Resources';
                } elseif (strpos($category_key, 'state_') === 0) {
                    // Example: 'state_OR' becomes 'Oregon State Resources'
                    $state_code = strtoupper(substr($category_key, 6, 2)); // Assumes format like 'state_OR_...'
                    // This title generation might need refinement based on actual keys
                    $category_title = $state_code . ' State-Level Resources'; // Simplified for now
                     // A more robust way to get the state name might be needed if keys are not consistent
                }


                // Each category (like 'state_OR_dept_education') becomes an h6 within the Local section
                $html .= '<h6 class="text-capitalize mb-2 mt-3 ps-2 border-start border-3 border-secondary">' . htmlspecialchars($category_title) . '</h6>';
                $html .= '<ul class="list-unstyled ms-3 mb-3">'; // Inner list for resources under this category

                foreach ($resources_list as $resource_item) { // Renamed $resources to $resources_list to avoid conflict
                    if (!is_array($resource_item) || empty($resource_item['name'])) continue;

                    $html .= '<li class="mb-2 ps-2">';
                    if (isset($resource_item['url'])) {
                        $html .= '<i class="fas fa-external-link-alt me-2 text-muted"></i><strong><a href="' . htmlspecialchars($resource_item['url']) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($resource_item['name']) . '</a></strong>';
                    } else {
                        $html .= '<i class="fas fa-info-circle me-2 text-muted"></i><strong>' . htmlspecialchars($resource_item['name']) . '</strong>';
                    }

                    if (isset($resource_item['description']) && !empty($resource_item['description'])) {
                        $html .= '<p class="mb-0 ms-4 ps-1 small text-muted">' . htmlspecialchars($resource_item['description']) . '</p>';
                    }
                    if (isset($resource_item['phone']) && !empty($resource_item['phone'])) {
                        $phone_digits = preg_replace('/[^0-9+]/', '', $resource_item['phone']);
                        $html .= '<p class="mb-0 ms-4 ps-1 small text-muted"><i class="fas fa-phone-alt me-1"></i> <a href="tel:' . $phone_digits . '">' . htmlspecialchars($resource_item['phone']) . '</a></p>';
                    }
                    if (isset($resource_item['type']) && !empty($resource_item['type'])) {
                         $html .= '<p class="mb-0 ms-4 ps-1 small text-muted"><em>Type: ' . htmlspecialchars($resource_item['type']) . '</em></p>';
                    }
                    if (isset($resource_item['eligibility']) && !empty($resource_item['eligibility'])) {
                         $html .= '<p class="mb-0 ms-4 ps-1 small text-muted"><em>Eligibility: ' . htmlspecialchars($resource_item['eligibility']) . '</em></p>';
                    }
                    $html .= '</li>';
                }
                $html .= '</ul>'; // End inner list for resources_list
            }
            $html .= '</ul>'; // End outer list for local_categories
            $has_rendered_any_section = true;
        }

        // Render other categories (Federal, Advocacy, Legal)
        $category_icons = [
            'federal' => 'fas fa-landmark',
            'advocacy' => 'fas fa-bullhorn',
            'legal' => 'fas fa-gavel'
        ];

        foreach ($other_categories as $category_key => $resources_list) {
            if ($has_rendered_any_section) {
                $html .= '<hr class="mt-3 mb-3">';
            } else {
                 // If no local resources, the first "other" section shouldn't have a preceding HR from the main $html init
                 // but might need one if it's not the absolute first thing in the box.
                 // The initial HR is fine. This logic might be redundant.
            }
            
            $category_title = ucfirst(str_replace('_', ' ', $category_key));
            $icon_class = $category_icons[$category_key] ?? 'fas fa-list-ul'; // Default icon

            $html .= '<h5 class="mb-3 mt-2"><i class="' . $icon_class . ' me-2"></i>' . htmlspecialchars($category_title) . ' Resources</h5>';
            $html .= '<ul class="list-unstyled mb-3">';

            foreach ($resources_list as $resource_item) { // Renamed from $resources
                 if (!is_array($resource_item) || empty($resource_item['name'])) continue;

                $html .= '<li class="mb-2 ps-2">';
                if (isset($resource_item['url'])) {
                    $html .= '<i class="fas fa-external-link-alt me-2 text-muted"></i><strong><a href="' . htmlspecialchars($resource_item['url']) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($resource_item['name']) . '</a></strong>';
                } else {
                    $html .= '<i class="fas fa-info-circle me-2 text-muted"></i><strong>' . htmlspecialchars($resource_item['name']) . '</strong>';
                }

                if (isset($resource_item['description']) && !empty($resource_item['description'])) {
                    $html .= '<p class="mb-0 ms-4 ps-1 small text-muted">' . htmlspecialchars($resource_item['description']) . '</p>';
                }
                if (isset($resource_item['phone']) && !empty($resource_item['phone'])) {
                    $phone_digits = preg_replace('/[^0-9+]/', '', $resource_item['phone']);
                    $html .= '<p class="mb-0 ms-4 ps-1 small text-muted"><i class="fas fa-phone-alt me-1"></i> <a href="tel:' . $phone_digits . '">' . htmlspecialchars($resource_item['phone']) . '</a></p>';
                }
                 if (isset($resource_item['type']) && !empty($resource_item['type'])) {
                     $html .= '<p class="mb-0 ms-4 ps-1 small text-muted"><em>Type: ' . htmlspecialchars($resource_item['type']) . '</em></p>';
                }
                if (isset($resource_item['eligibility']) && !empty($resource_item['eligibility'])) {
                     $html .= '<p class="mb-0 ms-4 ps-1 small text-muted"><em>Eligibility: ' . htmlspecialchars($resource_item['eligibility']) . '</em></p>';
                }
                $html .= '</li>';
            }
            $html .= '</ul>';
            $has_rendered_any_section = true;
        }
        
        // Original footer for the resource section
        $html .= '<p class="small fst-italic text-muted mt-3 mb-0">Resources are provided for informational purposes. Please verify their suitability for your specific needs. Listings are not exhaustive.</p>';
        $html .= '</div>'; // End .ai-resource-section
        
        // Fallback for Parsedown not rendering
        if (strpos($html, '[link]') !== false || strpos($html, '[name]') !== false) {
            $html .= '<p class="small fst-italic text-danger mt-2 mb-0">(Markdown not fully rendered - Parsedown placeholder detected. Please ensure the Parsedown library is correctly installed and functional.)</p>';
        }

        return $html;
    }
    
    /**
     * Finds phone numbers in an HTML string and converts them to clickable tel: links.
     * Uses DOMDocument to safely modify only text nodes.
     * @param string $html_content The HTML content to process.
     * @return string The HTML content with phone numbers linked.
     */
    public function linkPhoneNumbersInHtml($html_content) {
        if (empty(trim($html_content))) {
            return $html_content;
        }

        $doc = new DOMDocument();
        // Suppress errors from invalid HTML, loadHTML expects well-formed HTML.
        // LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD helps to avoid <html><body> tags if processing a fragment.
        @$doc->loadHTML('<meta charset="utf-8" />' . $html_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($doc);
        // Find all text nodes that are not inside an <a> tag already, nor in <script> or <style>
        $textNodes = $xpath->query('//text()[not(ancestor::a) and not(ancestor::script) and not(ancestor::style)]');

        // Regex to find North American phone numbers
        $phoneRegex = '/(?<!\d)(?:\+?1[\s.-]?)?(?:\(?([2-9]\d{2})\)?[\s.-]?)?([2-9]\d{2})[\s.-]?(\d{4})(?!\d)/';

        foreach ($textNodes as $textNode) {
            $textContent = $textNode->nodeValue;
            
            $parentNode = $textNode->parentNode;
            if (!$parentNode) continue;

            $newNodesFragment = $doc->createDocumentFragment(); // Use a fragment to append new nodes
            $lastPos = 0;

            preg_match_all($phoneRegex, $textContent, $matches, PREG_OFFSET_CAPTURE);

            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    $phoneNumber = $match[0];
                    $offset = $match[1];

                    // Add text before the current match
                    if ($offset > $lastPos) {
                        $newNodesFragment->appendChild($doc->createTextNode(substr($textContent, $lastPos, $offset - $lastPos)));
                    }

                    // Create the <a> tag for the phone number
                    $telLink = $doc->createElement('a');
                    $digits = preg_replace('/[^0-9+]/', '', $phoneNumber);
                    $telLink->setAttribute('href', 'tel:' . $digits);
                    $telLink->appendChild($doc->createTextNode($phoneNumber)); // Use original for display
                    $newNodesFragment->appendChild($telLink);

                    $lastPos = $offset + strlen($phoneNumber);
                }

                // Add any remaining text after the last match
                if ($lastPos < strlen($textContent)) {
                    $newNodesFragment->appendChild($doc->createTextNode(substr($textContent, $lastPos)));
                }

                // Replace the old text node with the new series of text and <a> nodes
                $parentNode->replaceChild($newNodesFragment, $textNode);
            }
        }

        $output_html = $doc->saveHTML();
        // Remove the meta tag we added
        $output_html = str_replace('<meta charset="utf-8"/>', '', $output_html);
        $output_html = str_replace('<meta charset="utf-8">', '', $output_html); 

        // Minimal cleanup for fragments if loadHTML added body/html tags
        if (strpos($html_content, '<html>') === false && strpos($output_html, '<html>') !== false) {
             if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $output_html, $bodyContent)) {
                $output_html = $bodyContent[1];
            } else {
                 $output_html = preg_replace(['~^<!DOCTYPE.*?>~is', '~<html[^>]*>~is', '~<head[^>]*>.*?</head>~is', '~<body[^>]*>~is', '~</body>~is', '~</html>~is'], '', $output_html);
            }
        }
        
        $final_output = trim($output_html);
        return $final_output !== '' ? $final_output : $html_content;
    }
    
    /**
     * Detect user's state from location data (Oregon prioritized)
     */
    private function detectUserState($location) {
        if (!$location || !is_string($location)) return null;
        
        $location_lower = strtolower($location);
        
        // Prioritize Oregon for grant opportunities & detailed county data
        $states_priority = [
             'oregon' => [
                'oregon', 'or', 'portland', 'eugene', 'salem', 'bend', 'corvallis', 'medford',
                'hillsboro', 'beaverton', 'tigard', 'lake oswego', 'milwaukie', 'gresham',
                'multnomah county', 'multnomah', 'washington county', 'clackamas county', 'clackamas',
                'lane county', 'marion county', 'deschutes county', 'deschutes', 'jackson county',
                'yamhill county', 'polk county', 'lincoln county', 'central oregon'
            ]
        ];
         $states_other = [
            'california' => ['california', 'ca', 'los angeles', 'san francisco', 'sacramento', 'san diego'],
            'texas' => ['texas', 'tx', 'houston', 'dallas', 'austin', 'san antonio'],
            'florida' => ['florida', 'fl', 'miami', 'orlando', 'tampa', 'jacksonville'],
            'new_york' => ['new york', 'ny', 'nyc', 'buffalo', 'albany', 'rochester'],
            'washington' => ['washington state', 'wa', 'seattle', 'spokane', 'tacoma', 'vancouver wa'] // differentiate from DC
        ];

        foreach ($states_priority as $state_code => $identifiers) {
            foreach ($identifiers as $identifier) {
                if (strpos($location_lower, $identifier) !== false) return $state_code;
            }
        }
        foreach ($states_other as $state_code => $identifiers) {
            foreach ($identifiers as $identifier) {
                if (strpos($location_lower, $identifier) !== false) return $state_code;
            }
        }
        return null;
    }
    
    /**
     * Detect Oregon county from location
     */
    private function detectOregonCounty($location) {
        if (!$location) return null;
        
        $location_text = strtolower($location);
        
        // Central Oregon counties that use Central Oregon Disability Support Network
        $central_oregon_counties = [
            'wheeler', 'grant', 'harney', 'lake', 'klamath', 'deschutes', 'crook', 'jefferson'
        ];
        
        // Check for specific counties
        foreach ($central_oregon_counties as $county) {
            if (strpos($location_text, $county) !== false) {
                return $county;
            }
        }
        
        // Check for common city names that indicate counties
        $city_county_mapping = [
            'bend' => 'deschutes',
            'redmond' => 'deschutes',
            'sisters' => 'deschutes',
            'prineville' => 'crook',
            'madras' => 'jefferson',
            'burns' => 'harney',
            'john day' => 'grant',
            'mitchell' => 'wheeler',
            'lakeview' => 'lake',
            'klamath falls' => 'klamath'
        ];
        
        foreach ($city_county_mapping as $city => $county) {
            if (strpos($location_text, $city) !== false) {
                return $county;
            }
        }
        
        return null;
    }
    
    /**
     * Get the appropriate parent support group for Oregon counties
     */
    private function getOregonParentSupportGroup($county) {
        // Central Oregon counties that use Central Oregon Disability Support Network
        $central_oregon_counties = [
            'wheeler', 'grant', 'harney', 'lake', 'klamath', 'deschutes', 'crook', 'jefferson'
        ];
        
        if (in_array(strtolower($county), $central_oregon_counties)) {
            return [
                'name' => 'Central Oregon Disability Support Network',
                'url' => 'https://www.codsn.org/',
                'phone' => '541-548-0190',
                'description' => 'Support network for families with disabilities in Central Oregon counties'
            ];
        } else {
            return [
                'name' => 'FACT Oregon',
                'url' => 'https://factoregon.org/',
                'phone' => '503-786-6082',
                'description' => 'Family and Community Together Oregon - statewide parent support organization'
            ];
        }
    }
    
    /**
     * Get emergency contacts based on situation (Oregon-enhanced)
     */
    public function getEmergencyContacts($situation_type = 'general') {
        $contacts = [
            'general' => [
                'name' => 'Disability Rights Oregon Emergency Line',
                'phone' => '503-243-2081',
                'description' => 'For urgent disability rights issues in Oregon'
            ],
            'crisis' => [
                'name' => 'National Suicide Prevention Lifeline',
                'phone' => '988',
                'url' => 'https://988lifeline.org/', // Added URL
                'description' => 'For mental health crises'
            ],
            'abuse_oregon' => [ // Made specific to Oregon for clarity
                'name' => 'Oregon Child Abuse Hotline',
                'phone' => '855-503-7233',
                'description' => 'For reporting child abuse or neglect in Oregon'
            ],
             'abuse_national' => [
                'name' => 'Childhelp National Child Abuse Hotline',
                'phone' => '1-800-422-4453',
                'url' => 'https://www.childhelp.org/hotline/',
                'description' => 'National hotline for reporting child abuse'
            ],
            'oregon_advocacy_immediate' => [ // Renamed for clarity
                'name' => 'Oregon Advocacy Center (Disability Rights Oregon)',
                'phone' => '503-243-2081', // Same as general DRO line
                'description' => 'Immediate advocacy support for Oregon families with disabilities'
            ]
        ];
        
        return $contacts[$situation_type] ?? $contacts['general'];
    }
    
    /**
     * Special Oregon grant opportunity detection
     */
    public function detectGrantOpportunities($content, $context = []) {
        $grant_keywords = [
            'funding', 'grant', 'financial support', 'program funding',
            'nonprofit', 'organization', 'startup', 'initiative',
            'community program', 'special education program', 'financial aid', 'scholarship'
        ];
        
        $content_lower = strtolower($content);
        $has_grant_potential = false;
        
        foreach ($grant_keywords as $keyword) {
            if (strpos($content_lower, $keyword) !== false) {
                $has_grant_potential = true;
                break;
            }
        }
        
        $user_state = null;
        if(isset($context['location'])){
            $user_state = $this->detectUserState($context['location']);
        }

        // Prioritize Oregon for grant info if location is Oregon or grant potential is high
        if ($user_state === 'oregon' && ($has_grant_potential || ($context['seeking_grants'] ?? false) ) ) {
            return $this->getOregonGrantResources(); // Returns a structured array of Oregon grants
        }
        
        return []; // Return empty array if not Oregon or no grant potential detected
    }
    
    /**
     * Get comprehensive Oregon grant resources
     * This is kept as a public method if direct access to Oregon grants is needed
     */
    public function getOregonGrantResources() {
        return [
            'state_grants' => [
                [
                    'name' => 'Oregon Community Foundation - Special Needs Grants',
                    'url' => 'https://oregoncf.org/grants-scholarships/grants/community-grants/',
                    'phone' => '503-227-6846',
                    'description' => 'Community grants for special education and disability programs',
                    'deadline_info' => 'Rolling applications, quarterly review cycles'
                ],
                [
                    'name' => 'Oregon Department of Human Services - DD Grants',
                    'url' => 'https://www.oregon.gov/dhs/SENIORS-DISABILITIES/DD/Pages/community-funding.aspx',
                    'phone' => '503-945-6398',
                    'description' => 'Developmental disabilities community funding opportunities'
                ],
                [
                    'name' => 'Oregon Health Authority - Family Support Grants',
                    'url' => 'https://www.oregon.gov/oha/HSD/DD/Pages/family-support.aspx',
                    'phone' => '503-945-9789',
                    'description' => 'Family support and respite care funding'
                ]
            ],
            'federal_oregon' => [
                [
                    'name' => 'USDA Rural Development - Oregon Office',
                    'url' => 'https://www.rd.usda.gov/or',
                    'phone' => '503-414-3300',
                    'description' => 'Rural community development grants (includes Central Oregon)'
                ],
                [
                    'name' => 'Oregon Small Business Administration',
                    'url' => 'https://www.sba.gov/offices/district/or/portland',
                    'phone' => '503-326-2682',
                    'description' => 'Small business loans and grants for social enterprises'
                ]
            ],
            'private_foundations' => [
                [
                    'name' => 'The Collins Foundation',
                    'url' => 'https://www.collinsfoundation.org/',
                    'phone' => '503-227-7171',
                    'description' => 'Oregon-based foundation supporting education and human services'
                ],
                [
                    'name' => 'Meyer Memorial Trust',
                    'url' => 'https://mmt.org/',
                    'phone' => '503-228-5512',
                    'description' => 'Oregon and Alaska foundation focusing on social change'
                ],
                [
                    'name' => 'Ford Family Foundation',
                    'url' => 'https://www.tfff.org/',
                    'phone' => '541-957-5574',
                    'description' => 'Rural Oregon communities focus, based in Roseburg'
                ]
            ],
            'regional_central_oregon' => [
                [
                    'name' => 'Central Oregon Health Council - Community Grants',
                    'url' => 'https://cohealthcouncil.org/',
                    'phone' => '541-585-1185',
                    'description' => 'Health and social services grants for Central Oregon'
                ],
                [
                    'name' => 'Deschutes County Community Development',
                    'url' => 'https://www.deschutes.org/cd/page/community-development-block-grant-program',
                    'phone' => '541-388-6575',
                    'description' => 'Community Development Block Grants for Deschutes County'
                ],
                [
                    'name' => 'United Way of Deschutes County',
                    'url' => 'https://www.unitedwaydeschutes.org/',
                    'phone' => '541-382-2021',
                    'description' => 'Local grants for education and family stability programs'
                ]
            ]
        ];
    }
}
?> 