<div class="hidden fixed top-0 right-0 p-4 bg-white shadow-lg" id="dashboard-menu-dashboard">
    <ul>
        <li><a href="/dashboard">Dashboard</a></li>
        <li><a href="/settings">Settings</a></li>
        <li>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </li>
        <!-- Add more menu items as needed -->
    </ul>
</div>