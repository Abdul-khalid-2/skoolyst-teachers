document.addEventListener('DOMContentLoaded', function () {

    // Top navbar mobile toggle
    var navToggle = document.querySelector('.navbar-toggle');
    var navLinks = document.querySelector('.app-nav-links');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function () { navLinks.classList.toggle('open'); });
    }

    // Dashboard sidebar mobile toggle
    var dashToggle = document.querySelector('.mobile-dash-toggle');
    var dashSidebar = document.querySelector('.dash-sidebar');
    if (dashToggle && dashSidebar) {
        dashToggle.addEventListener('click', function () { dashSidebar.classList.toggle('open'); });
    }

    // Auto-hide flash alerts
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; }, 4000);
    });

    // Copy share link buttons
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = document.querySelector(btn.getAttribute('data-copy'));
            if (!target) return;
            target.select();
            target.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(target.value).then(function () {
                var original = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = original; }, 1500);
            });
        });
    });

    // ===== Generic repeater builder for JSON sections =====
    // Usage: <div class="repeater" data-fields='[{"key":"title","label":"Title","type":"text","tip":"..."}]' data-initial='[...]'></div>
    document.querySelectorAll('.repeater').forEach(initRepeater);

    function initRepeater(container) {
        var fields = JSON.parse(container.getAttribute('data-fields') || '[]');
        var initial = JSON.parse(container.getAttribute('data-initial') || '[]');
        var listEl = container.querySelector('.repeater-list');
        var addBtn = container.querySelector('.add-row-btn');
        var hiddenInput = document.getElementById(container.getAttribute('data-target'));

        function buildRow(values) {
            values = values || {};
            var row = document.createElement('div');
            row.className = 'repeater-item';

            var html = '<button type="button" class="remove-row" title="Remove">&times;</button><div class="form-row" style="grid-template-columns: repeat(2,1fr);">';
            fields.forEach(function (f) {
                var val = values[f.key] !== undefined ? values[f.key] : '';
                var tip = f.tip ? '<span class="field-info" data-tip="' + f.tip.replace(/"/g, '&quot;') + '">i</span>' : '';
                var full = f.full ? 'grid-column: 1 / -1;' : '';
                if (f.type === 'textarea') {
                    html += '<div class="form-group" style="' + full + 'grid-column:1/-1;"><label>' + f.label + tip + '</label>' +
                        '<textarea class="form-control" data-key="' + f.key + '">' + escapeHtml(val) + '</textarea></div>';
                } else {
                    html += '<div class="form-group" style="' + full + '"><label>' + f.label + tip + '</label>' +
                        '<input type="' + (f.type || 'text') + '" class="form-control" data-key="' + f.key + '" value="' + escapeHtml(val) + '"></div>';
                }
            });
            html += '</div>';
            row.innerHTML = html;
            row.querySelector('.remove-row').addEventListener('click', function () {
                row.remove();
                syncHidden();
            });
            row.querySelectorAll('input, textarea').forEach(function (inp) {
                inp.addEventListener('input', syncHidden);
            });
            return row;
        }

        function escapeHtml(str) {
            str = String(str);
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function syncHidden() {
            var rows = [];
            listEl.querySelectorAll('.repeater-item').forEach(function (row) {
                var obj = {};
                row.querySelectorAll('[data-key]').forEach(function (inp) {
                    obj[inp.getAttribute('data-key')] = inp.value;
                });
                rows.push(obj);
            });
            hiddenInput.value = JSON.stringify(rows);
        }

        if (initial.length) {
            initial.forEach(function (item) { listEl.appendChild(buildRow(item)); });
        } else {
            listEl.appendChild(buildRow({}));
        }
        syncHidden();

        addBtn.addEventListener('click', function () {
            listEl.appendChild(buildRow({}));
            syncHidden();
        });

        // sync before the enclosing form submits
        var form = container.closest('form');
        if (form) form.addEventListener('submit', syncHidden);
    }
});
