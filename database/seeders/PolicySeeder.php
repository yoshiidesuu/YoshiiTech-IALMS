<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Policy;
use Carbon\Carbon;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'title' => 'Student Code of Conduct',
                'content' => "# Student Code of Conduct\n\n## Introduction\n\nThis code of conduct outlines the expected behavior and standards for all students enrolled in our institution.\n\n## Academic Integrity\n\n### Plagiarism\n- Students must not submit work that is not their own\n- Proper citation is required for all sources\n- Collaboration must be approved by instructors\n\n### Cheating\n- Use of unauthorized materials during examinations is prohibited\n- Sharing answers or solutions is not permitted\n- Electronic devices must be used only as authorized\n\n## Behavioral Standards\n\n### Respect\n- Treat all members of the community with dignity\n- Maintain professional communication\n- Respect diverse perspectives and backgrounds\n\n### Attendance\n- Regular attendance is expected\n- Notify instructors of planned absences\n- Make up missed work as required\n\n## Consequences\n\nViolations of this code may result in:\n- Warning\n- Academic probation\n- Suspension\n- Expulsion\n\n## Appeals Process\n\nStudents have the right to appeal disciplinary actions through the established grievance procedure.",
                'category' => 'academic',
                'version' => '2.1',
                'status' => 'published',
                'published_at' => now()->subMonths(2),
                'effective_date' => now()->subMonths(2),
                'created_by' => 1,
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subMonths(2),
            ],
            [
                'title' => 'Faculty Handbook',
                'content' => "# Faculty Handbook\n\n## Welcome\n\nWelcome to our institution. This handbook provides essential information for faculty members.\n\n## Teaching Responsibilities\n\n### Course Preparation\n- Develop comprehensive syllabi\n- Prepare lesson plans and materials\n- Establish clear learning objectives\n\n### Classroom Management\n- Maintain professional environment\n- Encourage student participation\n- Address behavioral issues promptly\n\n### Assessment and Grading\n- Use fair and consistent grading criteria\n- Provide timely feedback\n- Maintain accurate records\n\n## Professional Development\n\n### Continuing Education\n- Participate in workshops and seminars\n- Stay current with field developments\n- Pursue advanced certifications\n\n### Research and Scholarship\n- Engage in scholarly activities\n- Publish research findings\n- Collaborate with colleagues\n\n## Administrative Duties\n\n### Committee Service\n- Participate in institutional governance\n- Serve on academic committees\n- Contribute to policy development\n\n### Student Advising\n- Provide academic guidance\n- Support student success\n- Maintain confidentiality\n\n## Benefits and Policies\n\n### Leave Policies\n- Sick leave entitlements\n- Vacation scheduling\n- Sabbatical opportunities\n\n### Professional Conduct\n- Ethical standards\n- Conflict of interest policies\n- Grievance procedures",
                'category' => 'faculty',
                'version' => '1.5',
                'status' => 'published',
                'published_at' => now()->subMonths(1),
                'effective_date' => now()->subMonths(1),
                'created_by' => 1,
                'created_at' => now()->subMonths(4),
                'updated_at' => now()->subMonths(1),
            ],
            [
                'title' => 'IT Security Policy',
                'content' => "# Information Technology Security Policy\n\n## Purpose\n\nThis policy establishes guidelines for the secure use of information technology resources.\n\n## Password Requirements\n\n### Complexity\n- Minimum 8 characters\n- Include uppercase and lowercase letters\n- Include numbers and special characters\n- Avoid common words or personal information\n\n### Management\n- Change passwords every 90 days\n- Do not reuse last 12 passwords\n- Use unique passwords for different systems\n- Report compromised passwords immediately\n\n## Data Protection\n\n### Classification\n- Public: No restrictions\n- Internal: Institution use only\n- Confidential: Restricted access\n- Restricted: Highest security level\n\n### Handling\n- Encrypt sensitive data\n- Use secure transmission methods\n- Implement access controls\n- Maintain audit trails\n\n## Network Security\n\n### Access Control\n- Use institutional credentials\n- Connect only to approved networks\n- Avoid public Wi-Fi for sensitive work\n- Report suspicious network activity\n\n### Device Management\n- Keep software updated\n- Use approved antivirus software\n- Enable automatic security updates\n- Report lost or stolen devices\n\n## Incident Response\n\n### Reporting\n- Report security incidents immediately\n- Contact IT security team\n- Document incident details\n- Preserve evidence\n\n### Response Procedures\n- Isolate affected systems\n- Assess damage and scope\n- Implement containment measures\n- Restore normal operations\n\n## Compliance\n\n### Monitoring\n- Regular security audits\n- Compliance assessments\n- Vulnerability scanning\n- Penetration testing\n\n### Training\n- Annual security awareness training\n- Role-specific security training\n- Incident response drills\n- Policy updates and communication",
                'category' => 'administrative',
                'version' => '3.0',
                'status' => 'published',
                'published_at' => now()->subWeeks(2),
                'effective_date' => now()->subWeeks(2),
                'created_by' => 1,
                'created_at' => now()->subMonths(6),
                'updated_at' => now()->subWeeks(2),
            ],
            [
                'title' => 'Emergency Procedures',
                'content' => "# Emergency Procedures\n\n## General Guidelines\n\nIn case of any emergency, remain calm and follow these procedures.\n\n## Fire Emergency\n\n### Immediate Actions\n1. Sound the fire alarm\n2. Evacuate the building immediately\n3. Use stairs, never elevators\n4. Proceed to designated assembly areas\n5. Wait for further instructions\n\n### Prevention\n- Keep fire exits clear\n- Know location of fire extinguishers\n- Report fire hazards immediately\n- Participate in fire drills\n\n## Medical Emergency\n\n### First Aid\n1. Assess the situation\n2. Call emergency services (911)\n3. Provide basic first aid if trained\n4. Stay with the person until help arrives\n5. Notify campus security\n\n### Serious Injuries\n- Do not move injured person\n- Control bleeding if possible\n- Keep person warm and comfortable\n- Clear airway if necessary\n\n## Severe Weather\n\n### Tornado Warning\n1. Move to lowest floor\n2. Stay away from windows\n3. Take cover in interior rooms\n4. Protect head and neck\n5. Wait for all-clear signal\n\n### Severe Thunderstorm\n- Stay indoors\n- Avoid windows and doors\n- Unplug electrical equipment\n- Stay away from water\n\n## Security Threats\n\n### Suspicious Activity\n1. Do not confront suspicious persons\n2. Call campus security immediately\n3. Provide detailed description\n4. Secure your area if safe to do so\n5. Follow official instructions\n\n### Lockdown Procedures\n- Lock all doors and windows\n- Turn off lights\n- Stay quiet and out of sight\n- Do not leave until given all-clear\n\n## Communication\n\n### Emergency Contacts\n- Campus Security: (555) 123-4567\n- Emergency Services: 911\n- Facilities Management: (555) 123-4568\n- Health Services: (555) 123-4569\n\n### Notification Systems\n- Emergency alert system\n- Campus-wide announcements\n- Email notifications\n- Text message alerts\n\n## Recovery\n\n### After Emergency\n- Account for all personnel\n- Assess damage and injuries\n- Contact appropriate authorities\n- Begin recovery procedures\n- Conduct post-incident review",
                'category' => 'general',
                'version' => '1.3',
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'effective_date' => now()->subDays(10),
                'created_by' => 1,
                'created_at' => now()->subMonths(8),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => 'Grade Appeal Process',
                'content' => "# Grade Appeal Process\n\n## Overview\n\nThis policy outlines the procedure for students to appeal grades they believe are incorrect or unfair.\n\n## Grounds for Appeal\n\n### Valid Reasons\n- Computational or clerical error\n- Failure to follow stated grading criteria\n- Discrimination or bias\n- Procedural violations\n\n### Invalid Reasons\n- Disagreement with instructor's judgment\n- Need for higher grade\n- Comparison with other students\n- Personal circumstances\n\n## Appeal Process\n\n### Step 1: Informal Resolution\n1. Contact the instructor within 10 business days\n2. Discuss concerns respectfully\n3. Review grading criteria and rubrics\n4. Attempt to reach mutual understanding\n\n### Step 2: Department Level\nIf informal resolution fails:\n1. Submit written appeal to department chair\n2. Include supporting documentation\n3. Department chair reviews within 5 business days\n4. Decision communicated in writing\n\n### Step 3: Academic Affairs\nIf department decision is unsatisfactory:\n1. Appeal to Academic Affairs within 5 business days\n2. Provide complete documentation\n3. Academic committee reviews case\n4. Final decision rendered within 10 business days\n\n## Documentation Required\n\n### Student Submission\n- Completed appeal form\n- Original assignment or exam\n- Grading rubric or criteria\n- Correspondence with instructor\n- Supporting evidence\n\n### Instructor Response\n- Grading rationale\n- Course syllabus\n- Assignment instructions\n- Grade distribution data\n- Previous communications\n\n## Timeline\n\n### Deadlines\n- Initial contact: 10 business days after grade posted\n- Department appeal: 5 business days after informal resolution\n- Academic Affairs appeal: 5 business days after department decision\n- Final decision: 10 business days after Academic Affairs review\n\n### Extensions\n- May be granted for extenuating circumstances\n- Must be requested in writing\n- Requires approval from Academic Affairs\n\n## Outcomes\n\n### Possible Results\n- Grade remains unchanged\n- Grade is corrected\n- Assignment is re-evaluated\n- Course grade is adjusted\n\n### Implementation\n- Changes made within 5 business days\n- Student notified of outcome\n- Transcript updated if necessary\n- Records maintained for audit\n\n## Rights and Responsibilities\n\n### Student Rights\n- Fair and impartial review\n- Access to relevant documents\n- Representation during hearings\n- Protection from retaliation\n\n### Student Responsibilities\n- Follow proper procedures\n- Provide accurate information\n- Meet all deadlines\n- Maintain respectful communication",
                'category' => 'academic',
                'version' => '1.0',
                'status' => 'draft',
                'published_at' => null,
                'effective_date' => null,
                'created_by' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(3),
            ],
        ];
        
        foreach ($policies as $policy) {
            Policy::create($policy);
        }
        
        $this->command->info('Policies seeded successfully.');
    }
}