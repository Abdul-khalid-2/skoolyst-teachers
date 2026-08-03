<div class="dash-card">
    <h3>Portfolio Template</h3>
    <p class="desc">Choose how your public portfolio looks. More templates are coming soon — for now, the Default template is free for everyone.</p>

    <form method="post" action="<?= Helpers::url('/dashboard/template') ?>">
        <input type="hidden" name="_csrf" value="<?= Helpers::csrfToken() ?>">

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:18px;">
            <label style="border:2px solid var(--primary);border-radius:12px;padding:14px;cursor:pointer;position:relative;">
                <input type="radio" name="template" value="default" checked style="position:absolute;top:12px;right:12px;">
                <div style="height:100px;border-radius:8px;background:linear-gradient(135deg,#0A2D52,#123f6e);margin-bottom:10px;"></div>
                <strong>Default</strong>
                <div style="font-size:12px;color:var(--success);font-weight:600;margin-top:4px;">Free · Active</div>
            </label>

            <div style="border:2px dashed var(--border);border-radius:12px;padding:14px;opacity:.6;">
                <div style="height:100px;border-radius:8px;background:#eef3f8;margin-bottom:10px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);">
                    <i class="fa fa-lock" style="font-size:22px;"></i>
                </div>
                <strong>Modern Minimal</strong>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Coming soon</div>
            </div>

            <div style="border:2px dashed var(--border);border-radius:12px;padding:14px;opacity:.6;">
                <div style="height:100px;border-radius:8px;background:#eef3f8;margin-bottom:10px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);">
                    <i class="fa fa-lock" style="font-size:22px;"></i>
                </div>
                <strong>Academic Classic</strong>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Coming soon</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:22px;">Save Template</button>
    </form>
</div>
