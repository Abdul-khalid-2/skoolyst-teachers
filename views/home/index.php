<?php View::partial('layouts/header', ['title' => $title, 'description' => $description, 'canonical' => $canonical]); ?>

<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'WebSite',
    'name'     => 'Skoolyst Teachers',
    'url'      => Helpers::url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <h1>Find Qualified Teachers in Pakistan</h1>
            <p>Skoolyst Teachers helps School, College, University, Technical, Medical, Science, Arts and Computer Science educators create a beautiful online portfolio in minutes — then share one link with recruiters, schools, or students.</p>
            <div class="hero-actions">
                <a href="<?= Helpers::url('/register') ?>" class="btn btn-light">Create Your Free Portfolio</a>
                <a href="#directory" class="btn btn-outline-light">Browse Teachers</a>
            </div>
            <div class="hero-stats">
                <div><strong><?= (int) $pagination['total'] ?></strong><span>Teachers Registered</span></div>
                <div><strong>100% Free</strong><span>Default Template</span></div>
                <div><strong>1 Click</strong><span>Shareable Portfolio Link</span></div>
            </div>
        </div>

        <div class="hero-media">
            <button type="button" class="video-thumb" id="heroVideoTrigger" data-video-id="0tp_ng9HVR4" aria-haspopup="dialog" aria-controls="heroVideoModal" aria-label="Play intro video">
                <img src="<?= Helpers::asset('image/thumnal.png') ?>" alt="Skoolyst Teachers intro video thumbnail" loading="lazy">
                <span class="video-thumb-play" aria-hidden="true">
                    <i class="fa fa-play"></i>
                </span>
            </button>
        </div>
    </div>
</section>

<div class="video-modal" id="heroVideoModal" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Intro video">
    <div class="video-modal-backdrop" data-video-close></div>
    <div class="video-modal-dialog">
        <button type="button" class="video-modal-close" data-video-close aria-label="Close video">
            <i class="fa fa-xmark"></i>
        </button>
        <div class="video-modal-frame" id="heroVideoFrame"></div>
    </div>
</div>

<div class="container" id="directory">
    <div class="section-pad" style="padding-bottom:0;">
        <p>Browse verified profiles of school, college, university, science, computer, mathematics and private teachers from across Pakistan. Filter by subject, city, qualification or category to find the right educator, or open any portfolio to see their experience, skills and contact details.</p>
    </div>

    <?php if ($subjects || $cities || $qualifications): ?>
    <div class="section-pad" style="padding-top:0;padding-bottom:10px;">
        <?php if ($subjects): ?>
            <p style="margin-bottom:6px;"><strong>Popular subjects:</strong>
                <?php foreach ($subjects as $i => $s): ?><?= $i > 0 ? ', ' : ' ' ?><a href="<?= Helpers::url('/') . '?subject=' . urlencode($s) . '#directory' ?>"><?= Helpers::e($s) ?></a><?php endforeach; ?>
            </p>
        <?php endif; ?>
        <?php if ($cities): ?>
            <p style="margin-bottom:6px;"><strong>Popular cities:</strong>
                <?php foreach ($cities as $i => $c): ?><?= $i > 0 ? ', ' : ' ' ?><a href="<?= Helpers::url('/') . '?city=' . urlencode($c) . '#directory' ?>"><?= Helpers::e($c) ?></a><?php endforeach; ?>
            </p>
        <?php endif; ?>
        <?php if ($qualifications): ?>
            <p style="margin-bottom:6px;"><strong>Qualifications:</strong>
                <?php foreach ($qualifications as $i => $q): ?><?= $i > 0 ? ', ' : ' ' ?><a href="<?= Helpers::url('/') . '?qualification=' . urlencode($q) . '#directory' ?>"><?= Helpers::e($q) ?></a><?php endforeach; ?>
            </p>
        <?php endif; ?>
        <p style="margin-bottom:0;"><strong>Categories:</strong>
            <?php foreach ($teacherTypes as $key => $label): ?><a href="<?= Helpers::url('/') . '?type=' . urlencode($key) . '#directory' ?>" style="margin-right:8px;"><?= Helpers::e($label) ?></a><?php endforeach; ?>
        </p>
    </div>
    <?php endif; ?>

    <form class="filter-bar" method="get" action="<?= Helpers::url('/') ?>#directory">
        <input type="text" name="q" placeholder="Search by name or title..." value="<?= Helpers::e($filters['q']) ?>">
        <select name="subject">
            <option value="">All Subjects</option>
            <?php foreach ($subjects as $s): ?>
                <option value="<?= Helpers::e($s) ?>" <?= $filters['subject'] === $s ? 'selected' : '' ?>><?= Helpers::e($s) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="city">
            <option value="">All Cities</option>
            <?php foreach ($cities as $c): ?>
                <option value="<?= Helpers::e($c) ?>" <?= $filters['city'] === $c ? 'selected' : '' ?>><?= Helpers::e($c) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="qualification">
            <option value="">All Qualifications</option>
            <?php foreach ($qualifications as $q): ?>
                <option value="<?= Helpers::e($q) ?>" <?= $filters['qualification'] === $q ? 'selected' : '' ?>><?= Helpers::e($q) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="type">
            <option value="">All Categories</option>
            <?php foreach ($teacherTypes as $key => $label): ?>
                <option value="<?= $key ?>" <?= $filters['teacher_type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="section-pad" style="padding-top:20px;">
        <?php if (empty($teachers)): ?>
            <div class="empty-state">
                <i class="fa fa-magnifying-glass" style="font-size:34px;color:#c7d3e0;"></i>
                <p style="margin-top:14px;">No teachers match these filters yet. Try broadening your search, or be the first to <a href="<?= Helpers::url('/register') ?>" style="color:var(--primary);font-weight:600;">create your portfolio</a>.</p>
            </div>
        <?php else: ?>
            <div class="teacher-grid">
                <?php foreach ($teachers as $t): ?>
                    <a href="<?= Helpers::url('/p/' . $t['slug']) ?>" class="teacher-card">
                        <img class="avatar" src="<?= $t['profile_photo'] ? Helpers::asset($t['profile_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($t['full_name']) . '&background=0A2D52&color=fff' ?>" alt="<?= Helpers::e(Teacher::altText($t)) ?>">
                        <h4><?= Helpers::e($t['full_name']) ?></h4>
                        <div class="role"><?= Helpers::e($t['profession_title'] ?: 'Teacher') ?></div>
                        <div class="meta">
                            <?php if ($t['city']): ?><span><i class="fa fa-location-dot"></i> <?= Helpers::e($t['city']) ?></span><?php endif; ?>
                            <?php if ($t['subject']): ?><span><i class="fa fa-book"></i> <?= Helpers::e($t['subject']) ?></span><?php endif; ?>
                        </div>
                        <span class="btn btn-primary btn-sm">View Portfolio</span>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($pagination['last_page'] > 1): ?>
                <div class="pagination">
                    <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                        <?php
                            $qs = array_filter(array_merge($filters, ['page' => $p]));
                            $url = Helpers::url('/') . '?' . http_build_query($qs) . '#directory';
                        ?>
                        <a href="<?= $url ?>" class="<?= $p === $pagination['page'] ? 'active' : '' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<section class="section-pad" style="background:#fff;">
    <div class="container section-heading">
        <h2>Built for every kind of educator</h2>
        <p>School Teachers, College Teachers, University Professors, Technical Instructors, Medical Faculty, Science &amp; Mathematics Teachers, Arts Teachers, Computer Science Teachers, and more.</p>
    </div>
</section>

<section class="section-pad">
    <div class="container section-heading">
        <h2>Frequently Asked Questions</h2>
        <details style="margin-bottom:12px;">
            <summary style="cursor:pointer;font-weight:600;">Is it free to create a teacher portfolio on Skoolyst Teachers?</summary>
            <p style="margin-top:8px;">Yes, creating and sharing your portfolio on the default template is completely free.</p>
        </details>
        <details style="margin-bottom:12px;">
            <summary style="cursor:pointer;font-weight:600;">How do I search for a teacher by subject, city or qualification?</summary>
            <p style="margin-top:8px;">Use the filter bar above the directory, or the subject/city/qualification/category links just above it, to narrow the list.</p>
        </details>
        <details style="margin-bottom:12px;">
            <summary style="cursor:pointer;font-weight:600;">How can I contact a teacher listed here?</summary>
            <p style="margin-top:8px;">Open a teacher's portfolio and use the Contact section. Phone numbers are only revealed to logged-in visitors to protect teachers' privacy.</p>
        </details>
        <details style="margin-bottom:12px;">
            <summary style="cursor:pointer;font-weight:600;">Can a teacher make their profile private?</summary>
            <p style="margin-top:8px;">Yes, teachers can hide their portfolio from the public directory at any time from their dashboard settings.</p>
        </details>
    </div>
</section>

<?php View::partial('layouts/footer'); ?>
