<?php View::partial('layouts/header', ['title' => $title]); ?>

<section class="hero">
    <div class="container">
        <h1>Build a professional portfolio educational institutions actually notice.</h1>
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
</section>

<div class="container" id="directory">
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
                        <img class="avatar" src="<?= $t['profile_photo'] ? Helpers::asset($t['profile_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($t['full_name']) . '&background=0A2D52&color=fff' ?>" alt="<?= Helpers::e($t['full_name']) ?>">
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

<?php View::partial('layouts/footer'); ?>
