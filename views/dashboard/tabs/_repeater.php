<?php
/**
 * Expects (set by the including tab file):
 * $section     string   JSON column name (e.g. 'educations')
 * $heading     string
 * $description string
 * $fields      array    [['key'=>'title','label'=>'Title','type'=>'text','tip'=>'...','full'=>bool], ...]
 * $user        array
 */
$initial = Helpers::jsonDecode($user[$section] ?? '[]');
?>
<div class="dash-card">
    <h3><?= Helpers::e($heading) ?></h3>
    <p class="desc"><?= Helpers::e($description) ?></p>

    <form method="post" action="<?= Helpers::url('/dashboard/section/' . $section) ?>">
        <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">
        <input type="hidden" id="payload_<?= $section ?>" name="payload" value="">

        <div class="repeater" data-target="payload_<?= $section ?>"
             data-fields='<?= htmlspecialchars(json_encode($fields), ENT_QUOTES) ?>'
             data-initial='<?= htmlspecialchars(json_encode($initial), ENT_QUOTES) ?>'>
            <div class="repeater-list"></div>
            <button type="button" class="add-row-btn"><i class="fa fa-plus"></i> Add Another</button>
        </div>

        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-primary">Save <?= Helpers::e($heading) ?></button>
        </div>
    </form>
</div>
