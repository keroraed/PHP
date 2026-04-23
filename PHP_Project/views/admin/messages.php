<?php
$pageTitle = 'Admin Messages';
include __DIR__ . '/../layouts/head.php';
?>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="page-header">
        <div class="container-fluid">
            <h1><i class="fas fa-inbox me-2"></i>Admin Messages</h1>
            <p>Contact form submissions received from users</p>
        </div>
    </div>

    <div class="container-fluid mb-5">
        <div class="card-modern">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-envelope-open-text me-2"></i>Contact Submissions</h5>
                <button class="btn btn-primary-modern btn-sm" onclick="loadMessages()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table-modern mb-0" id="messagesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Loading messages...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>
    <?php include __DIR__ . '/../layouts/scripts.php'; ?>

    <script>
        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>\"']/g, function(ch) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '\"': '&quot;',
                    "'": '&#039;'
                })[ch];
            });
        }

        function formatDate(dateValue) {
            if (!dateValue) return '-';
            try {
                return new Date(dateValue.replace(' ', 'T')).toLocaleString();
            } catch (e) {
                return dateValue;
            }
        }

        async function loadMessages() {
            const tbody = document.querySelector('#messagesTable tbody');
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">Loading messages...</td></tr>';

            try {
                const response = await fetch('/api/admin/messages');
                const result = await response.json();

                if (!result.success) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load messages</td></tr>';
                    return;
                }

                const messages = result.data || [];
                if (!messages.length) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No messages found</td></tr>';
                    return;
                }

                tbody.innerHTML = messages.map(function(msg) {
                    const isUnread = Number(msg.is_read) === 0;
                    const statusClass = isUnread ? 'badge-warning' : 'badge-success';
                    const statusLabel = isUnread ? 'Unread' : 'Read';
                    const action = isUnread
                        ? `<button type="button" class="btn btn-primary-modern btn-sm" onclick="markMessageRead(${Number(msg.id)})">
                               <i class="fas fa-check me-1"></i> Mark Read
                           </button>`
                        : '<span class="text-muted">-</span>';

                    return `
                        <tr>
                            <td><strong>#${escapeHtml(msg.id)}</strong></td>
                            <td><span class="badge-modern ${statusClass}">${statusLabel}</span></td>
                            <td>${escapeHtml(msg.name)}</td>
                            <td><a href="mailto:${escapeHtml(msg.email)}">${escapeHtml(msg.email)}</a></td>
                            <td>${escapeHtml(msg.phone || '-')}</td>
                            <td>${escapeHtml(msg.subject)}</td>
                            <td style="max-width: 360px; white-space: pre-wrap;">${escapeHtml(msg.message)}</td>
                            <td>${escapeHtml(formatDate(msg.created_at))}</td>
                            <td>${action}</td>
                        </tr>
                    `;
                }).join('');
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load messages</td></tr>';
            }
        }

        async function markMessageRead(id) {
            if (!id) return;

            try {
                const response = await fetch(`/api/admin/messages/${id}/read`, { method: 'POST' });
                const result = await response.json();

                if (result.success) {
                    window.toast?.success('Message marked as read', 'Updated');
                    loadMessages();
                    return;
                }

                window.toast?.error(result.message || 'Failed to update message', 'Error');
            } catch (error) {
                window.toast?.error('Failed to update message', 'Error');
            }
        }

        document.addEventListener('DOMContentLoaded', loadMessages);
    </script>
</body>
</html>
