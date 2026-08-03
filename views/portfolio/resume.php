<?php
$edu  = Helpers::jsonDecode($teacher['educations']);
$exp  = Helpers::jsonDecode($teacher['experiences']);
$skl  = Helpers::jsonDecode($teacher['skills']);
$cert = Helpers::jsonDecode($teacher['certifications']);
$social = Helpers::jsonDecode($teacher['social_links']);
$photo = $teacher['profile_photo'] ? Helpers::asset($teacher['profile_photo']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helpers::e($title) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
:root { --red:#0A2D52; --dark:#1a1a2e; --mid:#2d2d44; --light:#f8f9fa; --gray:#6c757d; --border:#e0e0e0; --white:#fff; }
body { font-family:'Inter',sans-serif; background:#eef0f4; color:var(--dark); padding:30px 15px; }
.print-bar { max-width:900px; margin:0 auto 18px; display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; }
.print-bar button { background:var(--red); color:#fff; border:none; padding:10px 24px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; letter-spacing:.5px; }
.print-bar button:hover { background:#123f6e; }
.print-tip { background:#fff8e1; border:1px solid #ffe082; border-radius:6px; padding:9px 16px; font-size:13px; color:#555; display:flex; align-items:center; gap:6px; }
.resume { max-width:900px; margin:0 auto; background:var(--white); border-radius:12px; overflow:hidden; box-shadow:0 8px 40px rgba(0,0,0,.13); }
.header { background:var(--dark); padding:28px 36px; display:flex; align-items:center; gap:28px; }
.header-photo, .header-photo-placeholder { width:110px; height:110px; border-radius:50%; object-fit:cover; border:4px solid var(--red); flex-shrink:0; }
.header-photo-placeholder { background:var(--mid); display:flex; align-items:center; justify-content:center; font-size:40px; color:var(--red); }
.header-info h1 { font-family:'Playfair Display',serif; font-size:32px; color:var(--white); letter-spacing:.5px; line-height:1.1; }
.header-info .title { font-size:15px; color:var(--red); font-weight:600; margin:6px 0 14px; letter-spacing:1.5px; text-transform:uppercase; }
.contact-row { display:flex; flex-wrap:wrap; gap:5px 14px; margin-top:2px; }
.contact-row a, .contact-row span { color:#e0e6f0!important; font-size:12.5px; text-decoration:none; display:flex; align-items:center; gap:5px; }
.contact-row i { color:var(--red); font-size:11px; flex-shrink:0; }
.body { display:grid; grid-template-columns:260px 1fr; }
.sidebar { background:var(--light); padding:32px 24px; border-right:1px solid var(--border); }
.main { padding:32px 36px; }
.sec-title { font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--red); border-bottom:2px solid var(--red); padding-bottom:5px; margin-bottom:16px; }
.skill-block { margin-bottom:24px; }
.skill-item { margin-bottom:10px; }
.skill-label { display:flex; justify-content:space-between; font-size:13px; font-weight:500; margin-bottom:4px; }
.skill-label span { color:var(--gray); font-size:12px; }
.bar-track { height:5px; background:var(--border); border-radius:3px; overflow:hidden; }
.bar-fill { height:100%; background:var(--red); border-radius:3px; }
.info-block { margin-bottom:24px; }
.info-row { display:flex; flex-direction:column; margin-bottom:10px; }
.info-row .label { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--gray); margin-bottom:2px; }
.info-row .value { font-size:13px; color:var(--dark); word-break:break-word; }
.info-row .value a { color:var(--red); text-decoration:none; }
.tags { display:flex; flex-wrap:wrap; gap:6px; }
.tag { background:var(--white); border:1px solid var(--border); border-radius:4px; padding:3px 8px; font-size:11px; font-weight:500; }
.timeline { margin-bottom:28px; }
.tl-item { position:relative; padding-left:18px; margin-bottom:20px; }
.tl-item::before { content:''; position:absolute; left:0; top:6px; width:8px; height:8px; border-radius:50%; background:var(--red); }
.tl-item::after { content:''; position:absolute; left:3.5px; top:14px; width:1px; height:calc(100% + 4px); background:var(--border); }
.tl-item:last-child::after { display:none; }
.tl-date { font-size:11px; font-weight:600; color:var(--red); letter-spacing:.5px; margin-bottom:2px; }
.tl-role { font-size:14px; font-weight:700; color:var(--dark); margin-bottom:1px; }
.tl-company { font-size:14px; color:var(--gray); margin-bottom:5px; font-style:italic; }
.tl-desc { font-size:14.5px; color:#444; line-height:1.6; }
.summary { font-size:15.5px; color:#444; line-height:1.75; margin-bottom:28px; padding:16px 20px; background:var(--light); border-left:3px solid var(--red); border-radius:0 6px 6px 0; }
.empty-note { color:var(--gray); font-size:13px; font-style:italic; }
@page { margin:8mm; size:A4; }
@media print {
  html,body { -webkit-print-color-adjust:exact!important; print-color-adjust:exact!important; }
  body { background:#fff; padding:0; font-size:12px; }
  .print-bar { display:none!important; }
  .resume { box-shadow:none; border-radius:0; max-width:100%; }
  .sec-title { page-break-after:avoid; break-after:avoid; }
  .tl-item { page-break-inside:avoid; break-inside:avoid; }
  a { color:inherit!important; text-decoration:none!important; }
}
@media (max-width:680px) {
  .header { flex-direction:column; text-align:center; padding:28px 20px; gap:16px; }
  .contact-row { justify-content:center; }
  .body { grid-template-columns:1fr; }
  .sidebar { border-right:none; border-bottom:1px solid var(--border); }
  .main { padding:24px 20px; }
}
</style>
</head>
<body>

<div class="print-bar">
    <div class="print-tip">💡 <strong>Tip:</strong> In print dialog, turn off "Headers and footers" for a clean PDF</div>
    <button onclick="window.print()"><i class="fa fa-download"></i>&nbsp; Download / Print PDF</button>
</div>

<div class="resume">
    <div class="header">
        <?php if ($photo): ?>
            <img src="<?= $photo ?>" alt="<?= Helpers::e($teacher['full_name']) ?>" class="header-photo">
        <?php else: ?>
            <div class="header-photo-placeholder"><i class="fa fa-user"></i></div>
        <?php endif; ?>
        <div class="header-info">
            <h1><?= Helpers::e($teacher['full_name']) ?></h1>
            <p class="title"><?= Helpers::e($teacher['profession_title'] ?: 'Teacher') ?></p>
            <div class="contact-row">
                <?php if ($teacher['phone']): ?><span><i class="fa fa-phone"></i><?= Helpers::e($teacher['phone']) ?></span><?php endif; ?>
                <span><i class="fa fa-envelope"></i><?= Helpers::e($teacher['email']) ?></span>
                <?php if ($teacher['city']): ?><span><i class="fa fa-map-marker-alt"></i><?= Helpers::e($teacher['city']) ?></span><?php endif; ?>
                <?php foreach ($social as $sl): if (empty($sl['url'])) continue; ?>
                    <a href="<?= Helpers::e($sl['url']) ?>" target="_blank"><i class="fa fa-link"></i><?= Helpers::e($sl['platform'] ?? 'Link') ?></a>
                <?php endforeach; ?>
                <?php if ($teacher['website']): ?><a href="<?= Helpers::e($teacher['website']) ?>" target="_blank"><i class="fa fa-globe"></i><?= Helpers::e($teacher['website']) ?></a><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="body">
        <div class="sidebar">
            <?php if ($skl): ?>
            <div class="skill-block">
                <div class="sec-title">Skills</div>
                <?php foreach ($skl as $s): $pct = is_numeric($s['level'] ?? null) ? max(0,min(100,(int)$s['level'])) : 80; ?>
                <div class="skill-item">
                    <div class="skill-label"><?= Helpers::e($s['name'] ?? '') ?> <span><?= $pct ?>%</span></div>
                    <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%"></div></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="info-block">
                <div class="sec-title">Personal Info</div>
                <?php if ($teacher['qualification']): ?><div class="info-row"><span class="label">Qualification</span><span class="value"><?= Helpers::e($teacher['qualification']) ?></span></div><?php endif; ?>
                <?php if ($teacher['subject']): ?><div class="info-row"><span class="label">Subject</span><span class="value"><?= Helpers::e($teacher['subject']) ?></span></div><?php endif; ?>
                <?php if ($teacher['years_experience']): ?><div class="info-row"><span class="label">Experience</span><span class="value"><?= (int)$teacher['years_experience'] ?> years</span></div><?php endif; ?>
                <?php if ($teacher['freelance_status']): ?><div class="info-row"><span class="label">Availability</span><span class="value" style="color:var(--red);font-weight:600;"><?= $teacher['freelance_status']==='available' ? 'Available' : 'Not Available' ?></span></div><?php endif; ?>
            </div>

            <?php if ($cert): ?>
            <div class="info-block">
                <div class="sec-title">Certificates</div>
                <div class="tags">
                    <?php foreach ($cert as $c): ?><span class="tag"><?= Helpers::e($c['title'] ?? '') ?></span><?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="main">
            <?php if ($teacher['bio']): ?>
                <div class="summary"><?= nl2br(Helpers::e($teacher['bio'])) ?></div>
            <?php endif; ?>

            <?php if ($exp): ?>
            <div class="sec-title">Experience</div>
            <div class="timeline">
                <?php foreach ($exp as $x): ?>
                <div class="tl-item">
                    <div class="tl-date"><?= Helpers::e(($x['start_date'] ?? '') . (!empty($x['end_date']) ? ' – ' . $x['end_date'] : '')) ?></div>
                    <div class="tl-role"><?= Helpers::e($x['title'] ?? '') ?></div>
                    <div class="tl-company"><?= Helpers::e($x['institute'] ?? '') ?></div>
                    <?php if (!empty($x['description'])): ?><div class="tl-desc"><?= Helpers::e($x['description']) ?></div><?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($edu): ?>
            <div class="sec-title">Education</div>
            <div class="timeline">
                <?php foreach ($edu as $e): ?>
                <div class="tl-item">
                    <div class="tl-date"><?= Helpers::e(($e['start_date'] ?? '') . (!empty($e['end_date']) ? ' – ' . $e['end_date'] : '')) ?></div>
                    <div class="tl-role"><?= Helpers::e($e['degree'] ?? '') ?></div>
                    <div class="tl-company"><?= Helpers::e($e['institute'] ?? '') ?></div>
                    <?php if (!empty($e['description'])): ?><div class="tl-desc"><?= Helpers::e($e['description']) ?></div><?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!$exp && !$edu): ?>
                <p class="empty-note">This teacher hasn't added experience or education details yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
