<?php
session_start();

// Block anyone who is not logged in OR does not have the 'admin' role
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | GreeNgage</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">

    <aside class="admin-sidebar">
        <a href="dashboard.php" class="admin-brand">🌱 GreeNgage</a>
        <nav class="admin-nav" aria-label="Admin navigation">
            <a class="active" href="admin.php"><span>👥</span> User Management</a>
            <a href="dashboard.php"><span>⌂</span> Dashboard</a>
        </nav>
        <a class="sidebar-logout" href="logout.php">Logout</a>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <p class="admin-eyebrow">ADMIN PANEL</p>
                <h1>User Management</h1>
            </div>
            <a class="header-logout" href="logout.php">Logout</a>
        </header>

        <section class="stats-grid" aria-label="User statistics">
            <article class="stat-card">
                <div class="stat-icon">👥</div>
                <div><p>Total Users</p><strong id="totalUsers">—</strong></div>
            </article>
            <article class="stat-card">
                <div class="stat-icon admin">◆</div>
                <div><p>Administrators</p><strong id="adminUsers">—</strong></div>
            </article>
            <article class="stat-card">
                <div class="stat-icon regular">✓</div>
                <div><p>Regular Users</p><strong id="regularUsers">—</strong></div>
            </article>
        </section>

        <section class="users-card">
            <div class="users-toolbar">
                <div>
                    <h2>All Users</h2>
                    <p>Manage account roles and access.</p>
                </div>
                <input id="userSearch" type="search" placeholder="Search users..." aria-label="Search users">
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <tr><td colspan="5" class="loading-cell">Loading users...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        const currentAdminId = <?php echo (int)$_SESSION["user_id"]; ?>;

        let allUsers = [];

        // Fetch and display all users
        async function loadUsers() {
            const res = await fetch("admin_actions.php");
            allUsers = await res.json();

            document.getElementById("totalUsers").textContent = allUsers.length;
            document.getElementById("adminUsers").textContent = allUsers.filter(user => user.role === "admin").length;
            document.getElementById("regularUsers").textContent = allUsers.filter(user => user.role !== "admin").length;

            renderUsers(allUsers);
        }

        function renderUsers(users) {
            const tbody = document.getElementById("userTableBody");
            tbody.innerHTML = "";

            if (!users.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="loading-cell">No users found.</td></tr>';
                return;
            }

            users.forEach(user => {
                const isSelf = Number(user.id) === currentAdminId;
                const toggleLabel = user.role === "admin" ? "Demote" : "Make Admin";
                const roleClass = user.role === "admin" ? "role-admin" : "role-user";

                const row = document.createElement("tr");
                row.innerHTML = `
                    <td><span class="user-id">#${user.id}</span></td>
                    <td><strong class="user-name">${escapeHtml(user.name)}</strong></td>
                    <td>${escapeHtml(user.email)}</td>
                    <td><span class="role-badge ${roleClass}">${escapeHtml(user.role)}</span></td>
                    <td>
                        ${isSelf ? '<span class="current-user">Current account</span>' : `
                            <div class="action-buttons">
                                <button class="btn-toggle" onclick="updateUser('toggle_role', ${user.id}, '${escapeHtml(user.role)}')">${toggleLabel}</button>
                                <button class="btn-delete" onclick="updateUser('delete', ${user.id})">Delete</button>
                            </div>
                        `}
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        document.getElementById("userSearch").addEventListener("input", event => {
            const query = event.target.value.toLowerCase().trim();
            const filtered = allUsers.filter(user =>
                user.name.toLowerCase().includes(query) ||
                user.email.toLowerCase().includes(query) ||
                user.role.toLowerCase().includes(query)
            );
            renderUsers(filtered);
        });

        // Send promote/demote or delete request
        async function updateUser(action, userId, currentRole = "") {
            if (action === "delete" && !confirm("Are you sure you want to delete this user?")) {
                return;
            }

            const res = await fetch("admin_actions.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action, user_id: userId, current_role: currentRole })
            });

            const data = await res.json();
            if (data.error) {
                alert(data.error);
            } else {
                loadUsers();
            }
        }

        // Prevent XSS when rendering user names/emails
        function escapeHtml(text) {
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        }

        loadUsers();
    </script>

</body>
</html>
