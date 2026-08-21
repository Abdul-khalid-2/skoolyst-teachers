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

    // ===== Actions dropdown (admin tables) =====
    document.querySelectorAll('.actions-dropdown-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var dropdown = toggle.closest('.actions-dropdown');
            var wasOpen = dropdown.classList.contains('open');
            document.querySelectorAll('.actions-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
            if (!wasOpen) dropdown.classList.add('open');
        });
    });
    document.addEventListener('click', function () {
        document.querySelectorAll('.actions-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.actions-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
        }
    });

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

    // ===== Hero video modal =====
    var videoTrigger = document.getElementById('heroVideoTrigger');
    var videoModal = document.getElementById('heroVideoModal');
    if (videoTrigger && videoModal) {
        var videoFrame = document.getElementById('heroVideoFrame');
        var lastFocused = null;

        function openVideoModal() {
            var videoId = videoTrigger.getAttribute('data-video-id');
            videoFrame.innerHTML = '<iframe src="https://www.youtube.com/embed/' + videoId +
                '?autoplay=1&rel=0" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';
            lastFocused = document.activeElement;
            videoModal.classList.add('is-open');
            videoModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            var closeBtn = videoModal.querySelector('.video-modal-close');
            if (closeBtn) closeBtn.focus();
        }

        function closeVideoModal() {
            videoModal.classList.remove('is-open');
            videoModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            videoFrame.innerHTML = ''; // stop playback
            if (lastFocused) lastFocused.focus();
        }

        videoTrigger.addEventListener('click', openVideoModal);

        videoModal.querySelectorAll('[data-video-close]').forEach(function (el) {
            el.addEventListener('click', closeVideoModal);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && videoModal.classList.contains('is-open')) {
                closeVideoModal();
            }
        });
    }
});
