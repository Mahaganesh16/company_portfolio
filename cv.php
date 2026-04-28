<?php
require_once('functions.php');

// Map $p_content to $siteData for compatibility with the existing CV template
$siteData = [
    'profile' => [
        'name' => $p_content['hero']['name'],
        'title' => $p_content['customization']['sidebar_title'],
        'email' => $p_content['contact']['email'],
        'phone' => $p_content['contact']['phones'][0] ?? $p_content['customization']['sidebar_phone'],
        'address' => $p_content['contact']['mailing_address'] ?? $p_content['customization']['sidebar_address'],
        'image' => $p_content['hero']['image'],
        'summary' => $p_content['hero']['description']
    ],
    'about' => [
        'summary' => implode("\n\n", $p_content['about']['bio']),
        'education' => array_map(function($edu) {
            $parts = explode(' — ', $edu['year']);
            return [
                'degree' => $parts[1] ?? '',
                'title' => $edu['title'],
                'year' => $parts[0] ?? $edu['year'],
                'university' => $edu['desc']
            ];
        }, $p_content['about']['education'])
    ],
    'experience' => array_map(function($exp) {
        return [
            'title' => $exp['role'],
            'duration' => $exp['period'],
            'location' => $exp['location'],
            'desc' => $exp['desc']
        ];
    }, $p_content['experience']['journey']),
    'research' => [
        'intro' => $p_content['research']['intro'],
        'patents' => [
            'national' => $p_content['research']['patents_summary']['national_count'],
            'international' => $p_content['research']['patents_summary']['intl_count'],
            'utility' => $p_content['research']['patents_summary']['utility_design_count']
        ]
    ],
    'publications' => [
        'journals' => array_map(function($j) {
            return [
                'title' => $j['title'],
                'subtitle' => $j['source'] . ' — ' . ($j['authors'] ?? '')
            ];
        }, $p_content['publications']['journals'])
    ],
    'technical_skills' => array_map(function($s) {
        return [
            'category' => $s['category'],
            'items' => str_replace("\n", ", ", $s['items'])
        ];
    }, $p_content['skills'])
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curriculum Vitae - <?php echo $siteData['profile']['name']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary: #FF014F;
            --secondary: #333;
            --text: #444;
            --light: #f4f4f4;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: #fff; }
            .cv-container { box-shadow: none !important; width: 100% !important; margin: 0 !important; border:none !important; }
            a { text-decoration: none; color: #000; }
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            color: var(--text);
            line-height: 1.6;
            margin: 0;
            padding: 40px 0;
        }

        .cv-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            border: 1px solid #eee;
        }

        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(255,1,79,0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            text-decoration: none;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 30px;
            margin-bottom: 30px;
        }

        .header-info h1 {
            margin: 0;
            font-size: 32px;
            color: var(--secondary);
            text-transform: uppercase;
        }

        .header-info h4 {
            margin: 5px 0 15px;
            color: var(--primary);
            font-weight: 500;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 14px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid var(--light);
        }

        section {
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 18px;
            color: var(--secondary);
            text-transform: uppercase;
            border-left: 4px solid var(--primary);
            padding-left: 15px;
            margin-bottom: 20px;
            background: #fff9fa;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .item {
            margin-bottom: 15px;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            color: #222;
        }

        .item-sub {
            color: var(--primary);
            font-weight: 500;
            font-size: 14px;
        }

        .item-desc {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        ul.list {
            padding-left: 20px;
            margin: 5px 0;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .skill-cat {
            background: var(--light);
            padding: 10px;
            border-radius: 5px;
        }

        .skill-cat strong {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .skill-cat span {
            font-size: 12px;
            color: #555;
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            color: var(--secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-btn no-print"><i class="fa-solid fa-arrow-left"></i> Back to Site</a>

    <button class="print-btn no-print" onclick="downloadPDF()">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </button>

    <div class="cv-container">
        <header>
            <div class="header-info">
                <h1><?php echo $siteData['profile']['name']; ?></h1>
                <h4><?php echo $siteData['profile']['title']; ?></h4>
                <div class="contact-grid">
                    <div class="contact-item"><i class="fa-solid fa-envelope"></i> <?php echo $siteData['profile']['email']; ?></div>
                    <div class="contact-item"><i class="fa-solid fa-phone"></i> <?php echo $siteData['profile']['phone']; ?></div>
                    <div class="contact-item"><i class="fa-solid fa-location-dot"></i> <?php echo $siteData['profile']['address']; ?></div>
                    <div class="contact-item"><i class="fa-solid fa-globe"></i> Professional Portfolio</div>
                </div>
            </div>
            <img src="<?php echo $siteData['profile']['image'] ?? 'assets/images/logo/image.png'; ?>" alt="Profile" class="profile-img">
        </header>

        <section>
            <div class="section-title">Professional Summary</div>
            <p style="font-size: 15px;"><?php echo $siteData['about']['summary'] ?? $siteData['profile']['summary']; ?></p>
        </section>

        <section>
            <div class="section-title">Education</div>
            <?php foreach ($siteData['about']['education'] as $edu): ?>
                <?php if (isset($edu['status']) && $edu['status'] === 'inactive') continue; ?>
                <div class="item">
                    <div class="item-header">
                        <span><?php echo $edu['degree']; ?> - <?php echo $edu['title']; ?></span>
                        <span><?php echo $edu['year']; ?></span>
                    </div>
                    <div class="item-sub"><?php echo $edu['university']; ?></div>
                </div>
            <?php endforeach; ?>
        </section>

        <section>
            <div class="section-title">Professional Experience</div>
            <?php foreach ($siteData['experience'] as $exp): ?>
                <?php if (isset($exp['status']) && $exp['status'] === 'inactive') continue; ?>
                <div class="item">
                    <div class="item-header">
                        <span><?php echo $exp['title']; ?></span>
                        <span><?php echo $exp['duration']; ?></span>
                    </div>
                    <div class="item-sub"><?php echo $exp['location']; ?></div>
                    <div class="item-desc"><?php echo $exp['desc']; ?></div>
                </div>
            <?php endforeach; ?>
        </section>

        <section>
            <div class="section-title">Research & Innovation</div>
            <div class="item-desc" style="margin-bottom: 15px;"><?php echo $siteData['research']['intro']; ?></div>
            <div class="item-header" style="font-size: 14px;">Key Patents:</div>
            <ul class="list" style="font-size: 14px;">
                <li>National Patents: <?php echo $siteData['research']['patents']['national']; ?></li>
                <li>International Patents: <?php echo $siteData['research']['patents']['international']; ?></li>
                <li>Utility Models: <?php echo $siteData['research']['patents']['utility']; ?></li>
            </ul>
        </section>

        <section>
            <div class="section-title">Publications (Recent Journals)</div>
            <ul class="list" style="font-size: 13px;">
                <?php foreach (array_slice($siteData['publications']['journals'], 0, 8) as $j): ?>
                    <?php if (isset($j['status']) && $j['status'] === 'inactive') continue; ?>
                    <li><strong><?php echo $j['title']; ?></strong> - <?php echo $j['subtitle']; ?></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section>
            <div class="section-title">Technical Skills</div>
            <div class="skills-grid">
                <?php foreach ($siteData['technical_skills'] as $skill): ?>
                    <?php if (isset($skill['status']) && $skill['status'] === 'inactive') continue; ?>
                    <div class="skill-cat">
                        <strong><?php echo $skill['category']; ?></strong>
                        <span><?php echo $skill['items']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <footer style="margin-top: 50px; text-align: center; font-size: 12px; color: #aaa; border-top: 1px solid #eee; padding-top: 20px;">
            This CV is dynamically generated from <?php echo $siteData['profile']['name']; ?>'s Professional Portfolio.
        </footer>
    </div>

    <script>
        function downloadPDF() {
            const element = document.querySelector('.cv-container');
            const opt = {
                margin:       [10, 10, 10, 10],
                filename:     'CV_<?php echo str_replace(' ', '_', $siteData['profile']['name']); ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            const btn = document.querySelector('.print-btn');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...';
            btn.style.opacity = '0.7';
            btn.disabled = true;

            html2pdf().set(opt).from(element).save().then(() => {
                btn.innerHTML = originalContent;
                btn.style.opacity = '1';
                btn.disabled = false;
            }).catch(err => {
                console.error('PDF Error:', err);
                btn.innerHTML = originalContent;
                btn.style.opacity = '1';
                btn.disabled = false;
                alert('There was an error generating the PDF. Please try printing (Ctrl+P) instead.');
            });
        }
    </script>
</body>
</html>
