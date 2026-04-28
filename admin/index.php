<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login');
    exit;
}

$content_file = '../content.json';
if (!file_exists($content_file)) {
    die("Error: content.json not found in root directory.");
}

$content = json_decode(file_get_contents($content_file), true);
$cust = $content['customization'];
$message = '';

// Handle Save Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section_key'] ?? '';
    
    // --- SAVE SINGLE SECTION ---
    if (isset($_POST['save_section'])) {
        // Special case: designations (array of strings)
        if ($section === 'hero' && isset($_POST['designations'])) {
            $content['hero']['designations'] = array_filter(array_map('trim', explode("\n", $_POST['designations'])));
            unset($_POST['data']['designations']);
        }
        // Special case: bio (array of strings)
        if ($section === 'about' && isset($_POST['bio'])) {
            $content['about']['bio'] = array_filter(array_map('trim', explode("\n", $_POST['bio'])));
            unset($_POST['data']['bio']);
        }

        // Fix array indices for contact section to prevent JSON objects instead of arrays when deleting items
        if ($section === 'contact') {
            if (isset($_POST['data']['phones'])) $_POST['data']['phones'] = array_values($_POST['data']['phones']);
            if (isset($_POST['data']['offices'])) $_POST['data']['offices'] = array_values($_POST['data']['offices']);
            if (isset($_POST['data']['social'])) $_POST['data']['social'] = array_values($_POST['data']['social']);
        }

        foreach ($_POST['data'] as $key => $value) {
            $content[$section][$key] = $value;
        }
        
        file_put_contents($content_file, json_encode($content, JSON_PRETTY_PRINT));
        $message = "Section updated successfully!";
    }

    // --- ADD LIST ITEM ---
    if (isset($_POST['add_item'])) {
        $path = explode('.', $section);
        if (count($path) == 2) {
            $content[$path[0]][$path[1]][] = $_POST['new_data'];
        } else {
            $content[$section][] = $_POST['new_data'];
        }
        file_put_contents($content_file, json_encode($content, JSON_PRETTY_PRINT));
        $message = "Item added successfully!";
    }

    // --- EDIT LIST ITEM ---
    if (isset($_POST['edit_item'])) {
        $index = $_POST['item_index'];
        $path = explode('.', $section);
        if (count($path) == 2) {
            $content[$path[0]][$path[1]][$index] = $_POST['data'];
        } else {
            $content[$section][$index] = $_POST['data'];
        }
        file_put_contents($content_file, json_encode($content, JSON_PRETTY_PRINT));
        $message = "Item updated successfully!";
    }

    // --- DELETE LIST ITEM ---
    if (isset($_POST['delete_item'])) {
        $index = $_POST['item_index'];
        $path = explode('.', $section);
        if (count($path) == 2) {
            array_splice($content[$path[0]][$path[1]], $index, 1);
        } else {
            array_splice($content[$section], $index, 1);
        }
        file_put_contents($content_file, json_encode($content, JSON_PRETTY_PRINT));
        $message = "Item deleted successfully!";
    }
}

$active_tab = $_GET['tab'] ?? 'customization';

// Helper to get nested data
function get_nested($content, $tab) {
    $parts = explode('.', $tab);
    $data = $content;
    foreach ($parts as $p) {
        if (!isset($data[$p])) return null;
        $data = $data[$p];
    }
    return $data;
}

$tab_data = get_nested($content, $active_tab);
$is_list = is_array($tab_data) && (isset($tab_data[0]) || empty($tab_data));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Shreetech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/vendor/bootstrap.min.css">
    <style>
        :root { --primary: #FD0155; --dark: #0f172a; --sidebar: #1e293b; --bg: #f8fafc; }
        body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; display: flex; height: 100vh; overflow: hidden; margin:0; }
        
        .sidebar { width: 260px; background: var(--sidebar); color: white; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-header { padding: 30px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar-header h4 { color: var(--primary); font-weight: 800; margin:0; font-size: 18px; }
        
        .nav-links { flex: 1; overflow-y: auto; padding: 20px 10px; }
        .nav-group-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin: 20px 0 10px 15px; letter-spacing: 1px; }
        .nav-item { padding: 12px 15px; display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; border-radius: 10px; margin-bottom: 4px; font-size: 14px; transition: 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.05); color: white; }
        .nav-item.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(253, 1, 85, 0.2); }
        .nav-item i { width: 18px; font-size: 16px; }

        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .top-bar { height: 70px; background: white; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 40px; flex-shrink: 0; }
        .content { padding: 40px; overflow-y: auto; flex: 1; }
        
        .card-cms { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px; border: 1px solid #e2e8f0; }
        .section-title { font-weight: 800; font-size: 22px; color: var(--dark); margin-bottom: 25px; }
        
        .form-label { font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 8px; }
        .form-control { border-radius: 10px; border: 1px solid #e2e8f0; padding: 10px 15px; font-size: 14px; margin-bottom: 20px; background: #f8fafc; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(253, 1, 85, 0.1); background: white; }
        
        .btn-primary-cms { background: var(--primary); color: white; border: none; padding: 10px 25px; border-radius: 10px; font-weight: 700; transition: 0.2s; }
        .btn-primary-cms:hover { background: #d00146; transform: translateY(-1px); }
        .btn-add { background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; margin-bottom: 20px; }
        
        .table-cms { width: 100%; border-collapse: collapse; }
        .table-cms th { text-align: left; padding: 12px; font-size: 11px; color: #64748b; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-cms td { padding: 12px; font-size: 12.5px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: top; line-height: 1.5; }
        .table-cms tr:hover { background: #f8fafc; }
        .badge-tab { font-size: 10px; padding: 2px 6px; border-radius: 4px; background: rgba(255,255,255,0.1); margin-left: auto; }
        .upload-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 2px dashed #cbd5e1; margin-top: 10px; display: none; }
        .upload-preview.active { display: block; }
        .nav-links::-webkit-scrollbar { width: 0; display: none; }

        .nav-group { margin-bottom: 5px; }
        .nav-group-header { padding: 12px 15px; display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; border-radius: 10px; font-size: 14px; transition: 0.2s; cursor: pointer; user-select: none; }
        .nav-group-header:hover { background: rgba(255,255,255,0.05); color: white; }
        .nav-group-header i.chevron { margin-left: auto; font-size: 10px; transition: 0.3s; }
        .nav-group.active .nav-group-header { color: white; background: rgba(255,255,255,0.05); }
        .nav-group.active .nav-group-header i.chevron { transform: rotate(180deg); }
        .nav-group-content { display: none; padding-left: 20px; margin-top: 5px; }
        .nav-group.active .nav-group-content { display: block; }
        .nav-sub-item { padding: 10px 15px; display: flex; align-items: center; gap: 12px; color: #64748b; text-decoration: none; border-radius: 8px; font-size: 13px; margin-bottom: 2px; transition: 0.2s; }
        .nav-sub-item:hover { color: white; background: rgba(255,255,255,0.03); }
        .nav-sub-item.active { color: var(--primary); font-weight: 600; background: rgba(253, 1, 85, 0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header" style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-graduation-cap" style="color: var(--primary); font-size: 20px;"></i>
            <h4 style="font-size: 14px;"><?= $cust['logo_text'] ?></h4>
        </div>
        <div class="nav-links">
            <a href="?tab=customization" class="nav-item <?= $active_tab=='customization'?'active':'' ?>"><i class="fa-solid fa-sliders"></i> General Customization</a>
            
            <!-- Home -->
            <div class="nav-group <?= strpos($active_tab, 'hero') !== false ? 'active' : '' ?>">
                <div class="nav-group-header" onclick="this.parentElement.classList.toggle('active')">
                    <i class="fa-solid fa-house"></i> Home <i class="fa-solid fa-chevron-down chevron"></i>
                </div>
                <div class="nav-group-content">
                    <a href="?tab=hero" class="nav-sub-item <?= $active_tab=='hero'?'active':'' ?>">Hero Section</a>
                </div>
            </div>

            <!-- About Me -->
            <div class="nav-group <?= strpos($active_tab, 'about') !== false ? 'active' : '' ?>">
                <div class="nav-group-header" onclick="this.parentElement.classList.toggle('active')">
                    <i class="fa-solid fa-user"></i> About Me <i class="fa-solid fa-chevron-down chevron"></i>
                </div>
                <div class="nav-group-content">
                    <a href="?tab=about" class="nav-sub-item <?= $active_tab=='about'?'active':'' ?>">Bio & Image</a>
                    <a href="?tab=about.details" class="nav-sub-item <?= $active_tab=='about.details'?'active':'' ?>">Personal Details</a>
                    <a href="?tab=about.education" class="nav-sub-item <?= $active_tab=='about.education'?'active':'' ?>">Education</a>
                </div>
            </div>

            <!-- Research -->
            <div class="nav-group <?= strpos($active_tab, 'research') !== false ? 'active' : '' ?>">
                <div class="nav-group-header" onclick="this.parentElement.classList.toggle('active')">
                    <i class="fa-solid fa-flask"></i> Research & Projects <i class="fa-solid fa-chevron-down chevron"></i>
                </div>
                <div class="nav-group-content">
                    <a href="?tab=research" class="nav-sub-item <?= $active_tab=='research'?'active':'' ?>">Research Info</a>
                    <a href="?tab=research.patents_summary" class="nav-sub-item <?= $active_tab=='research.patents_summary'?'active':'' ?>">Patents Summary</a>
                    <a href="?tab=research.areas" class="nav-sub-item <?= $active_tab=='research.areas'?'active':'' ?>">Research Areas</a>
                    <a href="?tab=research.thesis" class="nav-sub-item <?= $active_tab=='research.thesis'?'active':'' ?>">Scholars Guided</a>
                    <a href="?tab=research.shreetech" class="nav-sub-item <?= $active_tab=='research.shreetech'?'active':'' ?>">Industrial Projects</a>
                </div>
            </div>

            <!-- Publications -->
            <div class="nav-group <?= strpos($active_tab, 'publications') !== false || $active_tab == 'research.patents_summary' ? 'active' : '' ?>">
                <div class="nav-group-header" onclick="this.parentElement.classList.toggle('active')">
                    <i class="fa-solid fa-book"></i> Publications <i class="fa-solid fa-chevron-down chevron"></i>
                </div>
                <div class="nav-group-content">
                    <a href="?tab=publications.journals" class="nav-sub-item <?= $active_tab=='publications.journals'?'active':'' ?>">Journals</a>
                    <a href="?tab=publications.conferences" class="nav-sub-item <?= $active_tab=='publications.conferences'?'active':'' ?>">Intl. Conferences</a>
                    <a href="?tab=publications.national_conferences" class="nav-sub-item <?= $active_tab=='publications.national_conferences'?'active':'' ?>">National Conferences</a>
                    <a href="?tab=publications.books" class="nav-sub-item <?= $active_tab=='publications.books'?'active':'' ?>">Books Published</a>
                </div>
            </div>

            <!-- Experience -->
            <div class="nav-group <?= strpos($active_tab, 'experience') !== false ? 'active' : '' ?>">
                <div class="nav-group-header" onclick="this.parentElement.classList.toggle('active')">
                    <i class="fa-solid fa-briefcase"></i> Experience <i class="fa-solid fa-chevron-down chevron"></i>
                </div>
                <div class="nav-group-content">
                    <a href="?tab=experience.journey" class="nav-sub-item <?= $active_tab=='experience.journey'?'active':'' ?>">Work Experience</a>
                    <a href="?tab=experience.examinership" class="nav-sub-item <?= $active_tab=='experience.examinership'?'active':'' ?>">Examinership</a>
                </div>
            </div>

            <!-- Teaching -->
            <div class="nav-group <?= strpos($active_tab, 'teaching') !== false ? 'active' : '' ?>">
                <div class="nav-group-header" onclick="this.parentElement.classList.toggle('active')">
                    <i class="fa-solid fa-chalkboard-user"></i> Teaching <i class="fa-solid fa-chevron-down chevron"></i>
                </div>
                <div class="nav-group-content">
                    <a href="?tab=teaching.subjects" class="nav-sub-item <?= $active_tab=='teaching.subjects'?'active':'' ?>">Subjects Taught</a>
                    <a href="?tab=teaching.courses" class="nav-sub-item <?= $active_tab=='teaching.courses'?'active':'' ?>">Courses Handled</a>
                    <a href="?tab=teaching.mentoring" class="nav-sub-item <?= $active_tab=='teaching.mentoring'?'active':'' ?>">Academic Mentoring</a>
                    <a href="?tab=teaching.certifications" class="nav-sub-item <?= $active_tab=='teaching.certifications'?'active':'' ?>">Professional Certifications</a>
                    <a href="?tab=teaching.workshops_organized" class="nav-sub-item <?= $active_tab=='teaching.workshops_organized'?'active':'' ?>">Workshops Organized</a>
                    <a href="?tab=teaching.workshops_attended" class="nav-sub-item <?= $active_tab=='teaching.workshops_attended'?'active':'' ?>">Workshops Attended</a>
                </div>
            </div>

            <!-- Skills -->
            <a href="?tab=skills" class="nav-item <?= $active_tab=='skills'?'active':'' ?>"><i class="fa-solid fa-layer-group"></i> Skills & Categories</a>

            <!-- Contact -->
            <a href="?tab=contact" class="nav-item <?= $active_tab=='contact'?'active':'' ?>"><i class="fa-solid fa-address-book"></i> Contact & Social</a>

            <!-- Expert Talks -->
            <a href="?tab=talks" class="nav-item <?= $active_tab=='talks'?'active':'' ?>"><i class="fa-solid fa-microphone"></i> Expert Talks</a>
        </div>
        <div class="p-3">
            <a href="logout" class="nav-item" style="color: #fb7185;"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="top-bar">
            <h5 class="m-0">Dashboard / <?= ucfirst(str_replace('.', ' / ', $active_tab)) ?></h5>
            <a href="../index" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">View Site <i class="fa-solid fa-external-link ms-1"></i></a>
        </div>

        <div class="content">
            <?php if($message): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><i class="fa-solid fa-check-circle me-2"></i> <?= $message ?></div>
            <?php endif; ?>

            <?php if ($active_tab === 'contact'): ?>
                <!-- CUSTOM CONTACT VIEW -->
                <div class="card-cms">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="m-0">Contact & Social Info</h2>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="section_key" value="contact">
                        
                        <h5 class="mb-3 text-primary" style="font-weight: 700;">Basic Info</h5>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" name="data[email]" class="form-control" value="<?= htmlspecialchars($tab_data['email'] ?? '') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Mailing Address</label>
                            <textarea name="data[mailing_address]" class="form-control" rows="3"><?= htmlspecialchars($tab_data['mailing_address'] ?? '') ?></textarea>
                        </div>

                        <hr class="my-4 border-light">
                        <h5 class="mb-3 text-primary" style="font-weight: 700;">Phone Numbers</h5>
                        <div id="phones-container">
                            <?php foreach($tab_data['phones'] ?? [] as $idx => $phone): ?>
                                <div class="d-flex gap-2 mb-2 phone-row">
                                    <input type="text" name="data[phones][]" class="form-control mb-0" value="<?= htmlspecialchars($phone) ?>" placeholder="Phone number">
                                    <button type="button" class="btn btn-danger btn-sm px-3 rounded-3" onclick="this.parentElement.remove()"><i class="fa-solid fa-times"></i></button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mb-4 rounded-3" onclick="addPhone()"><i class="fa-solid fa-plus me-1"></i> Add Phone</button>

                        <hr class="my-4 border-light">
                        <h5 class="mb-3 text-primary" style="font-weight: 700;">Offices / Firms</h5>
                        <div id="offices-container">
                            <?php foreach($tab_data['offices'] ?? [] as $idx => $office): ?>
                                <div class="row mb-2 office-row">
                                    <div class="col-md-5">
                                        <input type="text" name="data[offices][<?= $idx ?>][name]" class="form-control mb-0" value="<?= htmlspecialchars($office['name'] ?? '') ?>" placeholder="Office Name">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="data[offices][<?= $idx ?>][location]" class="form-control mb-0" value="<?= htmlspecialchars($office['location'] ?? '') ?>" placeholder="Location">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger w-100 rounded-3 h-100" onclick="this.parentElement.parentElement.remove()"><i class="fa-solid fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mb-4 rounded-3" onclick="addOffice()"><i class="fa-solid fa-plus me-1"></i> Add Office</button>

                        <hr class="my-4 border-light">
                        <h5 class="mb-3 text-primary" style="font-weight: 700;">Social Links</h5>
                        <div id="social-container">
                            <?php foreach($tab_data['social'] ?? [] as $idx => $social): ?>
                                <div class="row mb-2 social-row">
                                    <div class="col-md-3">
                                        <input type="text" name="data[social][<?= $idx ?>][platform]" class="form-control mb-0" value="<?= htmlspecialchars($social['platform'] ?? '') ?>" placeholder="Platform">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="data[social][<?= $idx ?>][link]" class="form-control mb-0" value="<?= htmlspecialchars($social['link'] ?? '') ?>" placeholder="URL">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="data[social][<?= $idx ?>][icon]" class="form-control mb-0" value="<?= htmlspecialchars($social['icon'] ?? '') ?>" placeholder="Icon Class (e.g. fa-brands fa-twitter)">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger w-100 rounded-3 h-100" onclick="this.parentElement.parentElement.remove()"><i class="fa-solid fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mb-4 rounded-3" onclick="addSocial()"><i class="fa-solid fa-plus me-1"></i> Add Social Link</button>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" name="save_section" class="btn-primary-cms w-100 py-3 text-uppercase" style="font-size: 15px; letter-spacing: 1px;"><i class="fa-solid fa-save me-2"></i> Save All Contact Info</button>
                        </div>
                    </form>
                </div>
                
                <script>
                    let officeCount = <?= !empty($tab_data['offices']) ? max(array_keys($tab_data['offices'])) + 1 : 0 ?>;
                    let socialCount = <?= !empty($tab_data['social']) ? max(array_keys($tab_data['social'])) + 1 : 0 ?>;
                    
                    function addPhone() {
                        const div = document.createElement('div');
                        div.className = 'd-flex gap-2 mb-2 phone-row';
                        div.innerHTML = '<input type="text" name="data[phones][]" class="form-control mb-0" placeholder="Phone number"><button type="button" class="btn btn-danger btn-sm px-3 rounded-3" onclick="this.parentElement.remove()"><i class="fa-solid fa-times"></i></button>';
                        document.getElementById('phones-container').appendChild(div);
                    }
                    
                    function addOffice() {
                        const div = document.createElement('div');
                        div.className = 'row mb-2 office-row';
                        div.innerHTML = '<div class="col-md-5"><input type="text" name="data[offices]['+officeCount+'][name]" class="form-control mb-0" placeholder="Office Name"></div><div class="col-md-6"><input type="text" name="data[offices]['+officeCount+'][location]" class="form-control mb-0" placeholder="Location"></div><div class="col-md-1"><button type="button" class="btn btn-danger w-100 rounded-3 h-100" onclick="this.parentElement.parentElement.remove()"><i class="fa-solid fa-times"></i></button></div>';
                        document.getElementById('offices-container').appendChild(div);
                        officeCount++;
                    }
                    
                    function addSocial() {
                        const div = document.createElement('div');
                        div.className = 'row mb-2 social-row';
                        div.innerHTML = '<div class="col-md-3"><input type="text" name="data[social]['+socialCount+'][platform]" class="form-control mb-0" placeholder="Platform"></div><div class="col-md-5"><input type="text" name="data[social]['+socialCount+'][link]" class="form-control mb-0" placeholder="URL"></div><div class="col-md-3"><input type="text" name="data[social]['+socialCount+'][icon]" class="form-control mb-0" placeholder="Icon Class"></div><div class="col-md-1"><button type="button" class="btn btn-danger w-100 rounded-3 h-100" onclick="this.parentElement.parentElement.remove()"><i class="fa-solid fa-times"></i></button></div>';
                        document.getElementById('social-container').appendChild(div);
                        socialCount++;
                    }
                </script>
            <?php elseif ($is_list): ?>
                <!-- LIST VIEW -->
                <div class="card-cms">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="m-0"><?= ucfirst(basename($active_tab)) ?> List</h2>
                        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus me-1"></i> Add New</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table-cms">
                            <thead>
                                <tr>
                                    <?php 
                                    $sample = $tab_data[0] ?? [];
                                    if(is_array($sample)):
                                        foreach(array_keys($sample) as $k): ?>
                                            <th><?= ucfirst($k) ?></th>
                                        <?php endforeach; 
                                    else: ?>
                                        <th>Content</th>
                                    <?php endif; ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tab_data as $idx => $item): ?>
                                    <tr>
                                        <?php if(is_array($item)): ?>
                                            <?php foreach($item as $val): ?>
                                                <td><?= (strlen($val) > 60) ? substr(strip_tags($val), 0, 60).'...' : $val ?></td>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <td><?= (strlen($item) > 100) ? substr(strip_tags($item), 0, 100).'...' : $item ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editModal<?= $idx ?>"><i class="fa-solid fa-pencil text-primary"></i></button>
                                                <form method="POST" onsubmit="return confirm('Delete this item?');">
                                                    <input type="hidden" name="section_key" value="<?= $active_tab ?>">
                                                    <input type="hidden" name="item_index" value="<?= $idx ?>">
                                                    <button type="submit" name="delete_item" class="btn btn-sm btn-light"><i class="fa-solid fa-trash text-danger"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $idx ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content rounded-4 border-0">
                                                <form method="POST">
                                                    <div class="modal-header border-0 pb-0"><h5 class="modal-title font-weight-bold">Edit Item</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                    <div class="modal-body p-4">
                                                        <input type="hidden" name="section_key" value="<?= $active_tab ?>">
                                                        <input type="hidden" name="item_index" value="<?= $idx ?>">
                                                        <?php if(is_array($item)): ?>
                                                            <?php foreach($item as $key => $val): ?>
                                                                <label class="form-label"><?= ucfirst($key) ?></label>
                                                                <?php if($key === 'icon'): ?>
                                                                    <input type="text" name="data[<?= $key ?>]" class="form-control" value="<?= htmlspecialchars($val) ?>" placeholder="e.g. fa-solid fa-code">
                                                                <?php elseif((strpos($key, 'image') !== false || strpos($key, 'logo') !== false || strpos($key, 'photo') !== false) && $key !== 'logo_text'): ?>
                                                                    <div class="upload-container mb-3">
                                                                        <input type="hidden" name="data[<?= $key ?>]" class="image-path-input" value="<?= htmlspecialchars($val) ?>">
                                                                        <input type="file" class="d-none image-upload-input" accept="image/*">
                                                                        <button class="btn btn-outline-primary w-100 btn-upload-trigger py-2" type="button">
                                                                            <i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload from Computer
                                                                        </button>
                                                                        <div class="text-center">
                                                                            <img src="../<?= $val ?>" class="upload-preview active mt-2 mx-auto" style="width:100px; height:100px; border-radius:12px; border:2px solid #eee;">
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <textarea name="data[<?= $key ?>]" class="form-control mb-3" rows="3"><?= htmlspecialchars($val) ?></textarea>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <label class="form-label">Content</label>
                                                            <textarea name="data" class="form-control mb-3" rows="5"><?= $item ?></textarea>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0"><button type="submit" name="edit_item" class="btn-primary-cms w-100">Save Changes</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Modal -->
                <div class="modal fade" id="addModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 border-0">
                            <form method="POST">
                                <div class="modal-header border-0 pb-0"><h5 class="modal-title font-weight-bold">Add New Entry</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body p-4">
                                    <input type="hidden" name="section_key" value="<?= $active_tab ?>">
                                    <?php if(is_array($sample)): ?>
                                        <?php foreach(array_keys($sample) as $key): ?>
                                            <label class="form-label"><?= ucfirst($key) ?></label>
                                            <?php if($key === 'icon'): ?>
                                                <input type="text" name="new_data[<?= $key ?>]" class="form-control mb-3" placeholder="e.g. fa-solid fa-code">
                                            <?php elseif((strpos($key, 'image') !== false || strpos($key, 'logo') !== false || strpos($key, 'photo') !== false) && $key !== 'logo_text'): ?>
                                                <div class="upload-container mb-3">
                                                    <input type="hidden" name="new_data[<?= $key ?>]" class="image-path-input">
                                                    <input type="file" class="d-none image-upload-input" accept="image/*">
                                                    <button class="btn btn-outline-primary w-100 btn-upload-trigger py-3" type="button">
                                                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload from Computer
                                                    </button>
                                                    <div class="text-center">
                                                        <img src="" class="upload-preview mt-3 mx-auto" style="width:120px; height:120px; border-radius:15px; border:3px solid #eee;">
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <textarea name="new_data[<?= $key ?>]" class="form-control mb-3" rows="3" placeholder="Enter <?= $key ?>..."></textarea>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <label class="form-label">Content</label>
                                        <textarea name="new_data" class="form-control mb-3" rows="5" placeholder="Enter content..."></textarea>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer border-0 pt-0"><button type="submit" name="add_item" class="btn-primary-cms w-100">Add Entry</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- SINGLE SECTION VIEW -->
                <div class="card-cms">
                    <form method="POST">
                        <input type="hidden" name="section_key" value="<?= $active_tab ?>">
                        <?php foreach($tab_data as $key => $val): ?>
                            <?php 
                            if($active_tab === 'contact' && is_array($val)) continue; 
                            if($active_tab === 'research' && $key === 'patents_summary') continue; 
                            
                            // Check if it's a complex list that should be skipped here
                            if(is_array($val)) {
                                $is_flat = true;
                                foreach($val as $item) if(is_array($item)) { $is_flat = false; break; }
                                if(!$is_flat) continue;
                            }
                            ?>
                            <div class="mb-3">
                                <label class="form-label"><?= ucfirst(str_replace('_', ' ', $key)) ?></label>
                                <?php if(is_array($val)): // Flat list like Bio/Designations ?>
                                    <textarea name="<?= $key ?>" class="form-control" rows="6"><?= implode("\n", $val) ?></textarea>
                                    <small class="text-muted d-block mt-n3 mb-3">Enter one item per line.</small>
                                <?php elseif(is_string($val) && strlen($val) > 100): ?>
                                    <textarea name="data[<?= $key ?>]" class="form-control" rows="5"><?= $val ?></textarea>
                                <?php elseif((strpos($key, 'image') !== false || strpos($key, 'logo') !== false || strpos($key, 'photo') !== false) && $key !== 'logo_text'): ?>
                                    <div class="upload-container mb-4">
                                        <input type="hidden" name="data[<?= $key ?>]" class="image-path-input" value="<?= htmlspecialchars($val) ?>">
                                        <input type="file" class="d-none image-upload-input" accept="image/*">
                                        <button class="btn btn-outline-primary w-100 btn-upload-trigger py-3" type="button">
                                            <i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload from Computer
                                        </button>
                                        <div class="text-center">
                                            <img src="../<?= $val ?>" class="upload-preview active mt-3 mx-auto" style="width:150px; height:150px; border-radius:20px; border:4px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                                        </div>
                                    </div>
                                <?php elseif(!is_array($val)): ?>
                                    <input type="text" name="data[<?= $key ?>]" class="form-control" value="<?= htmlspecialchars($val) ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" name="save_section" class="btn-primary-cms"><i class="fa-solid fa-save me-2"></i> Save Changes</button>
                    </form>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-upload-trigger')) {
            const container = e.target.closest('.mb-3') || e.target.closest('.mb-4') || e.target.closest('.modal-body');
            const fileInput = container.querySelector('.image-upload-input');
            fileInput.click();
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('image-upload-input')) {
            const file = e.target.files[0];
            if (!file) return;

            const container = e.target.closest('.mb-3') || e.target.closest('.mb-4') || e.target.closest('.modal-body');
            const pathInput = container.querySelector('.image-path-input');
            const previewImg = container.querySelector('.upload-preview');

            const formData = new FormData();
            formData.append('image', file);

            fetch('process_upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    pathInput.value = data.path;
                    previewImg.src = '../' + data.path;
                    previewImg.classList.add('active');
                    alert('Image uploaded successfully!');
                } else {
                    alert('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during upload.');
            });
        }
    });
    </script>
</body>
</html>
