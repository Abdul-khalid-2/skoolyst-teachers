<?php
$edu   = Helpers::jsonDecode($teacher['educations']);
$exp   = Helpers::jsonDecode($teacher['experiences']);
$skl   = Helpers::jsonDecode($teacher['skills']);
$cert  = Helpers::jsonDecode($teacher['certifications']);
$proj  = Helpers::jsonDecode($teacher['projects']);
$lang  = Helpers::jsonDecode($teacher['languages']);
$awd   = Helpers::jsonDecode($teacher['awards']);
$social= Helpers::jsonDecode($teacher['social_links']);
$initial = Helpers::initial($teacher['full_name']);
$photo = $teacher['profile_photo'] ? Helpers::asset($teacher['profile_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($teacher['full_name']) . '&background=0A2D52&color=fff&size=400';
$shareUrl = Helpers::url('/p/' . $teacher['slug']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helpers::e($title) ?></title>
    <meta name="description" content="<?= Helpers::e($teacher['profession_title'] ?: 'Teacher') ?> — <?= Helpers::e(Helpers::strimwidth($teacher['bio'], 140)) ?>">

    <link rel="stylesheet" href="<?= Helpers::asset('css/skin/color-1.css') ?>">
    <link rel="stylesheet" href="<?= Helpers::asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= Helpers::asset('css/all.css') ?>" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="<?= Helpers::asset('css/skin/color-1.css') ?>" class="alternate-style" title="color-1" disabled>
    <link rel="stylesheet" href="<?= Helpers::asset('css/skin/color-2.css') ?>" class="alternate-style" title="color-2" disabled>
    <link rel="stylesheet" href="<?= Helpers::asset('css/skin/color-3.css') ?>" class="alternate-style" title="color-3" disabled>
    <link rel="stylesheet" href="<?= Helpers::asset('css/skin/color-4.css') ?>" class="alternate-style" title="color-4" disabled>
    <link rel="stylesheet" href="<?= Helpers::asset('css/skin/color-5.css') ?>" class="alternate-style" title="color-5" disabled>
    <link rel="stylesheet" href="<?= Helpers::asset('css/style.switcher.css') ?>">

    <style>
        .mobile-navbar { display: none; }
        @media (max-width: 768px) {
            .aside { display: none; }
            .mobile-navbar { display: block; position: fixed; top: 0; left: 0; width: 100%; background-color: var(--bg-black-100); color: var(--text-black-900); z-index: 999; border-bottom: 1px solid var(--bg-black-50); }
            .mobile-navbar-inner { display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; }
            .mobile-logo a { color: var(--text-black-900); font-size: 22px; font-weight: 700; text-decoration: none; }
            .mobile-menu-toggle { border: none; background: transparent; color: var(--skin-color); font-size: 24px; cursor: pointer; }
            .mobile-nav-dropdown { display: none; background-color: var(--bg-black-100); border-top: 1px solid var(--bg-black-50); }
            .mobile-nav-dropdown.active { display: block; }
            .mobile-nav-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; }
            .mobile-nav-links li a { display: flex; align-items: center; gap: 10px; padding: 15px 20px; color: var(--text-black-900); text-decoration: none; border-bottom: 1px solid var(--bg-black-50); }
            .mobile-nav-links li a.active, .mobile-nav-links li a:hover { color: var(--skin-color); }
            .main-containt { padding-left: 0 !important; padding-top: 70px; }
            .section { padding: 0 15px; }
            html { scroll-behavior: smooth; }
        }
        .owner-banner { background:#0A2D52;color:#fff;text-align:center;padding:10px 16px;font-size:13.5px; }
        .owner-banner a { color:#fff;text-decoration:underline;font-weight:600; }
        .share-floating { position:fixed; bottom:22px; right:22px; z-index:998; display:flex; flex-direction:column; gap:10px; align-items:flex-end; }
        .share-floating a, .share-floating button { background:var(--skin-color); color:#fff; border:none; padding:12px 18px; border-radius:30px; font-size:13.5px; font-weight:600; box-shadow:0 6px 18px rgba(0,0,0,.25); cursor:pointer; display:flex; align-items:center; gap:8px; text-decoration:none; }
    </style>
    <title><?= Helpers::e($title) ?></title>
</head>
<body>

<?php if ($isOwner): ?>
    <div class="owner-banner">
        This is a preview of your live portfolio. <a href="<?= Helpers::url('/dashboard') ?>">Edit your portfolio</a>
        <?php if (!$teacher['is_public']): ?> &nbsp;|&nbsp; <strong>Hidden from public directory</strong><?php endif; ?>
    </div>
<?php endif; ?>

<div class="mobile-navbar">
    <div class="mobile-navbar-inner">
        <div class="mobile-logo"><a href="#"><?= Helpers::e($teacher['full_name']) ?></a></div>
        <button class="mobile-menu-toggle" type="button" aria-label="Open menu">&#9776;</button>
    </div>
    <div class="mobile-nav-dropdown">
        <ul class="mobile-nav-links">
            <li><a href="#home" class="active"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#about"><i class="fa fa-user"></i> About</a></li>
            <?php if ($skl || $edu || $exp): ?><li><a href="#about"><i class="fa fa-list"></i> Details</a></li><?php endif; ?>
            <?php if ($proj): ?><li><a href="#portfolio"><i class="fa fa-briefcase"></i> Projects</a></li><?php endif; ?>
            <li><a href="#contact"><i class="fa fa-comments"></i> Contact</a></li>
        </ul>
    </div>
</div>

<div class="main-container">
    <div class="aside">
        <div class="logo"><a href="#"><?= Helpers::e(Helpers::firstName($teacher['full_name'])) ?></a></div>
        <div class="nav-toggler"><span><i class="fa fa-bars"></i></span></div>
        <ul class="nav">
            <li><a href="#home" class="active"><i class="fas fa-home"></i>home</a></li>
            <li><a href="#about"><i class="fa fa-user"></i>About</a></li>
            <?php if ($proj): ?><li><a href="#portfolio"><i class="fa fa-briefcase"></i>Projects</a></li><?php endif; ?>
            <li><a href="#contact"><i class="fa fa-comments"></i>Contact</a></li>
        </ul>
    </div>

    <div class="main-containt">

        <!-- HOME -->
        <section class="home section hidden-t" id="home">
            <div class="container padd-15">
                <div class="row">
                    <div class="home-info padd-15">
                        <h3 class="hello">Hello, I'm <span class="name"><?= Helpers::e($teacher['full_name']) ?></span></h3>
                        <?php
                        $typedStrings = array_filter([
                            $teacher['profession_title'],
                            $teacher['subject'] ? $teacher['subject'] . ' Teacher' : null,
                            $teacher['qualification'],
                        ]);
                        if (!$typedStrings) $typedStrings = ['Teacher'];
                        ?>
                        <h3 class="my-profession">I'm a <span class="typing" data-typed-strings="<?= Helpers::e(implode('|', $typedStrings)) ?>"><?= Helpers::e($typedStrings[0]) ?></span></h3>
                        <p><?= nl2br(Helpers::e(Helpers::strimwidth($teacher['bio'], 260, '...'))) ?></p>
                        <a href="#contact" class="btn hire-me">Contact Me</a>
                        <a href="<?= Helpers::url('/p/' . $teacher['slug'] . '/resume') ?>" class="btn" style="margin-left:10px;"><i class="fa fa-download"></i> Download Resume</a>
                    </div>
                    <div class="home-img padd-15">
                        <img src="<?= $photo ?>" alt="<?= Helpers::e($teacher['full_name']) ?>">
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT -->
        <section class="about section hidden-t" id="about">
            <div class="container">
                <div class="row"><div class="section-title padd-15"><h2>About me</h2></div></div>

                <div class="row">
                    <div class="about-content padd-15">
                        <div class="row">
                            <div class="about-text padd-15">
                                <h3>I'm <?= Helpers::e($teacher['full_name']) ?><?php if ($teacher['profession_title']): ?> and I'm a <span><?= Helpers::e($teacher['profession_title']) ?></span><?php endif; ?></h3>
                                <p><?= nl2br(Helpers::e($teacher['bio'])) ?: 'No bio added yet.' ?></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="personal-info padd-15">
                                <div class="row">
                                    <?php if ($teacher['qualification']): ?><div class="info-item padd-15"><p>Qualification : <span><?= Helpers::e($teacher['qualification']) ?></span></p></div><?php endif; ?>
                                    <?php if ($teacher['subject']): ?><div class="info-item padd-15"><p>Subject : <span><?= Helpers::e($teacher['subject']) ?></span></p></div><?php endif; ?>
                                    <?php if ($teacher['email']): ?><div class="info-item padd-15"><p>Email : <span><?= Helpers::e($teacher['email']) ?></span></p></div><?php endif; ?>
                                    <?php if ($teacher['phone']): ?><div class="info-item padd-15"><p>Phone : <span><?= Helpers::e($teacher['phone']) ?></span></p></div><?php endif; ?>
                                    <?php if ($teacher['city']): ?><div class="info-item padd-15"><p>City : <span><?= Helpers::e($teacher['city']) . ($teacher['country'] ? ', ' . Helpers::e($teacher['country']) : '') ?></span></p></div><?php endif; ?>
                                    <?php if ($teacher['years_experience']): ?><div class="info-item padd-15"><p>Experience : <span><?= (int)$teacher['years_experience'] ?> years</span></p></div><?php endif; ?>
                                    <?php if ($teacher['website']): ?><div class="info-item padd-15"><p>Website : <span><a href="<?= Helpers::e($teacher['website']) ?>" target="_blank"><?= Helpers::e($teacher['website']) ?></a></span></p></div><?php endif; ?>
                                    <?php if ($teacher['freelance_status']): ?><div class="info-item padd-15"><p>Availability : <span><?= $teacher['freelance_status'] === 'available' ? 'Available' : 'Not Available' ?></span></p></div><?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="buttons padd-15">
                                        <a href="<?= Helpers::url('/p/' . $teacher['slug'] . '/resume') ?>" class="btn">Download Resume</a>
                                        <a href="#contact" class="btn hire-me">Contact</a>
                                    </div>
                                </div>
                            </div>

                            <?php if ($skl): ?>
                            <div class="skill padd-15">
                                <div class="row">
                                    <?php foreach ($skl as $s): $pct = is_numeric($s['level'] ?? null) ? max(0, min(100, (int)$s['level'])) : 80; ?>
                                    <div class="skill-item padd-15">
                                        <h5><?= Helpers::e($s['name'] ?? '') ?></h5>
                                        <div class="progress"><div class="progress-in" style="width:<?= $pct ?>%;"></div><div class="skill-percent"><?= $pct ?>%</div></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($edu || $exp): ?>
                        <div class="row">
                            <?php if ($edu): ?>
                            <div class="education padd-15">
                                <h3 class="title">Education</h3>
                                <div class="row">
                                    <div class="timeline-box padd-15">
                                        <div class="timeline shadow-dark">
                                            <?php foreach ($edu as $e): ?>
                                            <div class="timeline-item">
                                                <div class="circle-dot"></div>
                                                <h3 class="timeline-date"><i class="fa fa-calendar"></i> <?= Helpers::e(($e['start_date'] ?? '') . (!empty($e['end_date']) ? ' - ' . $e['end_date'] : '')) ?></h3>
                                                <h4 class="timeline-title"><?= Helpers::e($e['degree'] ?? '') ?></h4>
                                                <div class="timeline-text">
                                                    <p><?= Helpers::e($e['institute'] ?? '') ?></p>
                                                    <?php if (!empty($e['description'])): ?><p><?= nl2br(Helpers::e($e['description'])) ?></p><?php endif; ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($exp): ?>
                            <div class="experience padd-15">
                                <h3 class="title">Experience</h3>
                                <div class="row">
                                    <div class="timeline-box padd-15">
                                        <div class="timeline shadow-dark">
                                            <?php foreach ($exp as $x): ?>
                                            <div class="timeline-item">
                                                <div class="circle-dot"></div>
                                                <h3 class="timeline-date"><i class="fa fa-calendar"></i> <?= Helpers::e(($x['start_date'] ?? '') . (!empty($x['end_date']) ? ' - ' . $x['end_date'] : '')) ?></h3>
                                                <h4 class="timeline-title"><?= Helpers::e($x['title'] ?? '') ?></h4>
                                                <p class="timeline-text"><?= Helpers::e($x['institute'] ?? '') ?><?= !empty($x['description']) ? '. ' . $x['description'] : '' ?></p>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>

                <?php if ($cert || $awd || $lang): ?>
                <div class="row">
                    <?php if ($cert): ?>
                    <div class="education padd-15">
                        <h3 class="title">Certifications</h3>
                        <div class="row">
                            <div class="timeline-box padd-15">
                                <div class="timeline shadow-dark">
                                    <?php foreach ($cert as $c): ?>
                                    <div class="timeline-item">
                                        <div class="circle-dot"></div>
                                        <h3 class="timeline-date"><i class="fa fa-calendar"></i> <?= Helpers::e($c['issue_date'] ?? '') ?></h3>
                                        <h4 class="timeline-title"><?= Helpers::e($c['title'] ?? '') ?></h4>
                                        <div class="timeline-text">
                                            <p><?= Helpers::e($c['issuer'] ?? '') ?></p>
                                            <?php if (!empty($c['credential_url'])): ?><p><a href="<?= Helpers::e($c['credential_url']) ?>" target="_blank">View Credential</a></p><?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($awd): ?>
                    <div class="experience padd-15">
                        <h3 class="title">Awards</h3>
                        <div class="row">
                            <div class="timeline-box padd-15">
                                <div class="timeline shadow-dark">
                                    <?php foreach ($awd as $a): ?>
                                    <div class="timeline-item">
                                        <div class="circle-dot"></div>
                                        <h3 class="timeline-date"><i class="fa fa-calendar"></i> <?= Helpers::e($a['date'] ?? '') ?></h3>
                                        <h4 class="timeline-title"><?= Helpers::e($a['title'] ?? '') ?></h4>
                                        <p class="timeline-text"><?= Helpers::e($a['issuer'] ?? '') ?><?= !empty($a['description']) ? '. ' . $a['description'] : '' ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($lang): ?>
                <div class="row">
                    <div class="section-title padd-15"><h2>Languages</h2></div>
                </div>
                <div class="row">
                    <?php foreach ($lang as $l): ?>
                    <div class="service-item padd-15">
                        <div class="service-item-inner">
                            <div class="icon"><i class="fa fa-language"></i></div>
                            <h4><?= Helpers::e($l['name'] ?? '') ?></h4>
                            <p><?= Helpers::e($l['proficiency'] ?? '') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- PORTFOLIO / PROJECTS -->
        <?php if ($proj): ?>
        <section class="portfolio section hidden-t" id="portfolio">
            <div class="container">
                <div class="row"><div class="section-title padd-15"><h2>Projects</h2></div></div>
                <div class="row">
                    <?php foreach ($proj as $p): ?>
                    <div class="portfolio-item padd-15">
                        <div class="portfolio-item-inner shadow-dark">
                            <div class="portfolio-info" style="padding:22px;">
                                <h4 class="portfolio-title"><?= Helpers::e($p['title'] ?? '') ?></h4>
                                <p class="portfolio-desc"><?= Helpers::e($p['description'] ?? '') ?></p>
                                <?php if (!empty($p['url'])): ?>
                                <a href="<?= Helpers::e($p['url']) ?>" target="_blank" rel="noopener noreferrer" class="portfolio-link">View Project <i class="fa fa-external-link-alt"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- CONTACT -->
        <section class="contact section" id="contact">
            <div class="container">
                <div class="row"><div class="section-title padd-15"><h2>Contact</h2></div></div>
                <div class="row">
                    <?php if ($teacher['phone']): ?>
                    <div class="contact-info-item padd-15"><div class="icon"><i class="fa fa-phone"></i></div><h4>Call</h4><p><?= Helpers::e($teacher['phone']) ?></p></div>
                    <?php endif; ?>
                    <?php if ($teacher['city']): ?>
                    <div class="contact-info-item padd-15"><div class="icon"><i class="fa fa-map-marker-alt"></i></div><h4>Location</h4><p><?= Helpers::e($teacher['city']) ?></p></div>
                    <?php endif; ?>
                    <div class="contact-info-item padd-15"><div class="icon"><i class="fa fa-envelope"></i></div><h4>Email</h4><p><?= Helpers::e($teacher['email']) ?></p></div>
                    <?php if ($social): foreach ($social as $sl): ?>
                    <div class="contact-info-item padd-15"><div class="icon"><i class="fa fa-share-nodes"></i></div><h4><?= Helpers::e($sl['platform'] ?? 'Link') ?></h4><p><a href="<?= Helpers::e($sl['url'] ?? '#') ?>" target="_blank">Visit</a></p></div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </section>

    </div>
</div>

<div class="share-floating">
    <button type="button" onclick="navigator.clipboard.writeText('<?= $shareUrl ?>');this.innerHTML='<i class=\'fa fa-check\'></i> Copied!';"><i class="fa fa-link"></i> Copy Portfolio Link</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.12/typed.min.js"></script>
<script src="<?= Helpers::asset('js/Script.js') ?>"></script>
<script src="<?= Helpers::asset('js/style-switcher.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var menuToggle = document.querySelector('.mobile-menu-toggle');
    var mobileDropdown = document.querySelector('.mobile-nav-dropdown');
    if (menuToggle && mobileDropdown) {
        menuToggle.addEventListener('click', function () { mobileDropdown.classList.toggle('active'); });
    }
    document.querySelectorAll('.mobile-nav-links a').forEach(function (l) {
        l.addEventListener('click', function () { mobileDropdown.classList.remove('active'); });
    });
});
</script>
</body>
</html>
