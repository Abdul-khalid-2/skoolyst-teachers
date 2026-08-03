<div class="dash-card">
    <h3>Profile Photo</h3>
    <p class="desc">A clear headshot helps recruiters recognize you. JPG/PNG/WEBP, up to 3MB.</p>
    <form method="post" action="<?= Helpers::url('/dashboard/photo') ?>" enctype="multipart/form-data" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
        <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
        <img src="<?= $user['profile_photo'] ? Helpers::asset($user['profile_photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['full_name']) . '&background=0A2D52&color=fff' ?>"
             style="width:76px;height:76px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-soft);">
        <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/webp" required>
        <button type="submit" class="btn btn-primary btn-sm">Upload Photo</button>
    </form>
</div>

<div class="dash-card">
    <h3>Basic Information</h3>
    <p class="desc">This appears at the top of your public portfolio.</p>

    <form method="post" action="<?= Helpers::url('/dashboard/basic') ?>">
        <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Full Name <span class="field-info" data-tip="Your name as it should appear publicly on your portfolio.">i</span></label>
                <input type="text" name="full_name" class="form-control" required value="<?= Helpers::e($user['full_name']) ?>">
            </div>
            <div class="form-group">
                <label>Professional Title <span class="field-info" data-tip="e.g. Mathematics Teacher, Assistant Professor of Physics">i</span></label>
                <input type="text" name="profession_title" class="form-control" value="<?= Helpers::e($user['profession_title']) ?>" placeholder="e.g. Senior Mathematics Teacher">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Teacher Category <span class="field-info" data-tip="Used to help institutions filter and find you in the directory.">i</span></label>
                <select name="teacher_type" class="form-control">
                    <?php
                    $types = ['school'=>'School Teacher','college'=>'College Teacher','university'=>'University Professor','technical'=>'Technical Instructor','medical'=>'Medical Faculty','science'=>'Science Teacher','mathematics'=>'Mathematics Teacher','arts'=>'Arts Teacher','computer_science'=>'Computer Science Teacher','general'=>'General Subject Teacher','other'=>'Other'];
                    foreach ($types as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $user['teacher_type'] === $k ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Primary Subject <span class="field-info" data-tip="The main subject you teach, e.g. Chemistry, English Literature.">i</span></label>
                <input type="text" name="subject" class="form-control" value="<?= Helpers::e($user['subject']) ?>" placeholder="e.g. Chemistry">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Highest Qualification <span class="field-info" data-tip="e.g. M.Phil, PhD, MSc, BEd">i</span></label>
                <input type="text" name="qualification" class="form-control" value="<?= Helpers::e($user['qualification']) ?>" placeholder="e.g. M.Phil Mathematics">
            </div>
            <div class="form-group">
                <label>Years of Experience</label>
                <input type="number" min="0" max="60" name="years_experience" class="form-control" value="<?= Helpers::e((string)($user['years_experience'] ?? '')) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= Helpers::e($user['phone']) ?>">
            </div>
            <div class="form-group">
                <label>Website / Blog</label>
                <input type="text" name="website" class="form-control" value="<?= Helpers::e($user['website']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>City <span class="field-info" data-tip="Used as a directory filter — helps local institutions find you.">i</span></label>
                <input type="text" name="city" class="form-control" value="<?= Helpers::e($user['city']) ?>">
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control" value="<?= Helpers::e($user['country']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Prefer not to say</option>
                    <option value="male" <?= $user['gender']==='male'?'selected':'' ?>>Male</option>
                    <option value="female" <?= $user['gender']==='female'?'selected':'' ?>>Female</option>
                    <option value="other" <?= $user['gender']==='other'?'selected':'' ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Birthday</label>
                <input type="date" name="birthday" class="form-control" value="<?= Helpers::e($user['birthday']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Availability</label>
            <select name="freelance_status" class="form-control">
                <option value="">Not specified</option>
                <option value="available" <?= $user['freelance_status']==='available'?'selected':'' ?>>Available for new opportunities</option>
                <option value="not_available" <?= $user['freelance_status']==='not_available'?'selected':'' ?>>Not currently available</option>
            </select>
        </div>

        <div class="form-group">
            <label>About / Bio <span class="field-info" data-tip="A short professional summary shown on your portfolio's About section.">i</span></label>
            <textarea name="bio" class="form-control" placeholder="Tell institutions about your teaching philosophy, strengths, and experience..."><?= Helpers::e($user['bio']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Basic Info</button>
    </form>
</div>
