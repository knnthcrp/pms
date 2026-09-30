<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

require_once "../config/database.php";

$sql = "
    SELECT 
        pl.LogID,
        pl.VehicleID,
        pl.SlotID,
        pl.UserID,
        pl.TimeIn,
        pl.TimeOut,
        pl.DurationMinutes,
        pl.Status,

        v.PlateNumber,
        v.Brand,
        v.Model,
        v.Color,
        v.NumberOfWheels,

        ps.Floor,
        ps.SlotNumber,

        u.Username AS RegisteredBy

    FROM parkinglogs pl

    LEFT JOIN vehicles v
        ON pl.VehicleID = v.VehicleID

    LEFT JOIN parkingslots ps
        ON pl.SlotID = ps.SlotID

    LEFT JOIN users u
        ON pl.UserID = u.UserID

    ORDER BY pl.LogID DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Database Error: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Logs - Parkzen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="../assets/css/tailwind.css?v=<?php echo time(); ?>">
</head>

<body class="bg-slate-50 min-h-screen font-['Inter'] antialiased text-slate-900 flex">

    <!-- Left Sidebar -->
    <aside class="w-56 shrink-0 min-h-screen bg-white border-r border-slate-200 flex flex-col justify-between py-6 px-3">
        <div>
            <!-- PZ Brand Header -->
            <div class="px-2 mb-4">
                <div class="inline-flex items-center gap-2.5">
                    <div class="size-9 bg-blue-600 rounded-[19px] outline outline-2 outline-offset-[-2px] outline-blue-200 flex items-center justify-center shrink-0">
                        <span class="text-white text-xs font-bold font-['Inter']">PZ</span>
                    </div>
                    <span class="text-slate-900 text-base font-bold font-['Inter'] tracking-wider">PARKZEN</span>
                </div>
                <div class="mt-4">
                    <div class="px-2.5 py-1.5 bg-blue-50 rounded-[999px] outline outline-1 outline-offset-[-1px] outline-blue-200 inline-flex items-center gap-2">
                        <div class="size-1.5 bg-blue-600 rounded-full"></div>
                        <span class="text-blue-700 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase"><?php echo htmlspecialchars($_SESSION['role'] ?? 'STAFF'); ?> VIEW</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex flex-col gap-[3px] mt-4">
                <!-- MENU Section -->
                <div class="px-2 pt-2 pb-1 text-slate-500 text-xs font-semibold font-['Inter'] tracking-widest uppercase">MENU</div>

                <a href="../superadmin/create_admin.php" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Manage Admins</span>
                </a>

                <a href="../admin/manage_staff.php" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Manage Staff</span>
                </a>

                <a href="#" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Staff Attendance</span>
                </a>

                <!-- PARKING Section -->
                <div class="px-2 pt-3 pb-1 text-slate-500 text-[9.50px] font-semibold font-['Inter'] tracking-wider uppercase">PARKING</div>

                <a href="../vehicles/index.php" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Manage Vehicles</span>
                </a>

                <a href="../parking_slots/index.php" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Parking Slots</span>
                </a>

                <!-- Parking Logs (ACTIVE STATE) -->
                <a href="index.php" class="px-3 py-2 bg-blue-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-blue-200 inline-flex items-center gap-3">
                    <div class="size-1.5 bg-blue-600 rounded-full"></div>
                    <span class="text-blue-700 text-xs font-semibold font-['Inter']">Parking Logs</span>
                </a>

                <a href="../vehicles/create.php" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Vehicle Entry</span>
                </a>

                <a href="#" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Vehicle Exit</span>
                </a>

                <!-- ANALYTICS Section -->
                <div class="px-2 pt-3 pb-1 text-slate-500 text-[9.50px] font-semibold font-['Inter'] tracking-wider uppercase">ANALYTICS</div>

                <a href="#" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Reports</span>
                </a>

                <a href="../superadmin/activity_logs.php" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Activity Logs</span>
                </a>

                <a href="#" class="px-3 py-2 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex items-center gap-3 transition-colors">
                    <div class="size-1.5 bg-slate-400 rounded-full"></div>
                    <span class="text-slate-500 text-xs font-medium font-['Inter']">Payments</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="px-2 pt-4 border-t border-slate-200">
            <a href="../logout.php" class="px-3 py-2 text-slate-500 hover:text-red-600 text-xs font-medium inline-flex items-center gap-2 transition-colors">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
        <!-- Top Navigation Bar -->
        <header class="h-20 px-8 py-5 bg-white border-b border-slate-200 flex items-center justify-between">
            <div class="text-slate-900 text-base font-semibold font-['Inter']">Parking Logs</div>
            <div class="text-xs text-slate-500">
                User: <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-8 overflow-y-auto">
            <!-- Header Titles -->
            <div>
                <h1 class="text-slate-900 text-xl font-bold font-['Inter']">Parking Logs</h1>
                <p class="text-slate-500 text-xs font-normal font-['Inter'] mt-1">Track all parking activities, vehicle entries, exits, and slot allocations</p>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-xl shadow-[0px_8px_20px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 p-3 mt-6 flex flex-wrap items-center gap-3">
                <div class="w-64 px-3 py-2 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 flex items-center">
                    <input type="text" id="searchLogs" placeholder="Search logs..." class="w-full bg-transparent text-slate-900 text-xs font-normal placeholder:text-slate-500 focus:outline-none">
                </div>

                <!-- Status Filter -->
                <div class="relative inline-flex items-center">
                    <select id="statusFilter" class="appearance-none px-3 py-2 pr-7 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 text-slate-900 text-xs font-normal font-['Inter'] cursor-pointer hover:bg-slate-50 focus:outline-none transition-colors">
                        <option value="">Status: All Statuses</option>
                        <option value="active">Status: Active</option>
                        <option value="completed">Status: Completed</option>
                    </select>
                    <div class="size-1.5 bg-slate-400 rounded-full absolute right-3 pointer-events-none"></div>
                </div>

                <!-- Floor Filter -->
                <div class="relative inline-flex items-center">
                    <select id="floorFilter" class="appearance-none px-3 py-2 pr-7 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 text-slate-900 text-xs font-normal font-['Inter'] cursor-pointer hover:bg-slate-50 focus:outline-none transition-colors">
                        <option value="">Floor: All Floors</option>
                    </select>
                    <div class="size-1.5 bg-slate-400 rounded-full absolute right-3 pointer-events-none"></div>
                </div>

                <!-- Date Filter -->
                <div class="relative inline-flex items-center">
                    <select id="dateFilter" class="appearance-none px-3 py-2 pr-7 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 text-slate-900 text-xs font-normal font-['Inter'] cursor-pointer hover:bg-slate-50 focus:outline-none transition-colors">
                        <option value="">Date: All Dates</option>
                        <option value="today">Date: Today</option>
                        <option value="week">Date: This Week</option>
                        <option value="month">Date: This Month</option>
                    </select>
                    <div class="size-1.5 bg-slate-400 rounded-full absolute right-3 pointer-events-none"></div>
                </div>

                <!-- Reset Filters Button (appears when filters are active) -->
                <button type="button" id="resetFilters" class="hidden px-2.5 py-1.5 text-xs text-slate-500 hover:text-blue-600 transition-colors">
                    Reset Filters
                </button>
            </div>

            <!-- Table Card Container -->
            <div class="bg-white rounded-2xl shadow-[0px_8px_20px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 p-6 mt-6">
                <div class="text-slate-900 text-base font-bold font-['Inter'] mb-5">System Parking Log</div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">LOG ID</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">PLATE NUMBER</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">VEHICLE</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">COLOR</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">WHEELS</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">FLOOR</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">SLOT</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">REGISTERED BY</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">TIME IN</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">TIME OUT</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">DURATION</th>
                                <th class="pb-3 px-3 text-slate-500 text-[10px] font-semibold font-['Inter'] tracking-wide uppercase whitespace-nowrap">STATUS</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <?php if ($result->num_rows > 0): ?>

                                <?php while ($row = $result->fetch_assoc()): ?>

                                    <tr class="log-row border-b border-slate-100 hover:bg-slate-50/70 transition-colors"
                                        data-status="<?php echo strtolower(htmlspecialchars($row['Status'] ?? '')); ?>"
                                        data-floor="<?php echo htmlspecialchars($row['Floor'] ?? ''); ?>"
                                        data-timein="<?php echo htmlspecialchars($row['TimeIn'] ?? ''); ?>">

                                        <td class="py-3.5 px-3 text-blue-700 text-xs font-semibold font-['Inter'] whitespace-nowrap">
                                            LOG-<?php echo $row['LogID']; ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-900 text-xs font-semibold font-['Inter'] whitespace-nowrap">
                                            <?php echo htmlspecialchars($row['PlateNumber'] ?? 'N/A'); ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-900 text-xs font-normal font-['Inter'] whitespace-nowrap">
                                            <?php
                                            echo htmlspecialchars(
                                                trim(
                                                    ($row['Brand'] ?? '') . " " . ($row['Model'] ?? '')
                                                )
                                            );
                                            ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-600 text-xs font-normal font-['Inter'] whitespace-nowrap">
                                            <?php echo htmlspecialchars($row['Color'] ?? 'N/A'); ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-600 text-xs font-normal font-['Inter'] whitespace-nowrap">
                                            <?php echo $row['NumberOfWheels'] ?? 'N/A'; ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-600 text-xs font-normal font-['Inter'] whitespace-nowrap">
                                            <?php echo $row['Floor'] ?? 'N/A'; ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-900 text-xs font-medium font-['Inter'] whitespace-nowrap">
                                            <?php echo htmlspecialchars($row['SlotNumber'] ?? 'N/A'); ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-900 text-xs font-normal font-['Inter'] whitespace-nowrap">
                                            <?php echo htmlspecialchars($row['RegisteredBy'] ?? 'N/A'); ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-500 text-xs font-normal font-['Inter'] whitespace-nowrap">
                                            <?php echo $row['TimeIn']; ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-500 text-xs font-normal font-['Inter'] whitespace-nowrap">
                                            <?php
                                            echo $row['TimeOut'] ?? '---';
                                            ?>
                                        </td>

                                        <td class="py-3.5 px-3 text-slate-600 text-xs font-normal font-['Inter'] whitespace-nowrap">
                                            <?php if ($row['DurationMinutes'] !== null): ?>
                                                <?php echo $row['DurationMinutes']; ?> minutes
                                            <?php else: ?>
                                                ---
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            <?php if ($row['Status'] === 'Active'): ?>
                                                <div class="px-2.5 py-1 bg-emerald-50 rounded-md outline outline-1 outline-offset-[-1px] outline-emerald-200 inline-flex items-center">
                                                    <span class="text-emerald-700 text-xs font-bold font-['Inter']">ACTIVE</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="px-2.5 py-1 bg-rose-50 rounded-md outline outline-1 outline-offset-[-1px] outline-red-200 inline-flex items-center">
                                                    <span class="text-red-700 text-xs font-bold font-['Inter']"><?php echo htmlspecialchars($row['Status']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="12" class="py-12 text-center text-slate-500 text-xs font-normal font-['Inter']">
                                        No parking logs found.
                                    </td>
                                </tr>

                            <?php endif; ?>

                            <!-- Dynamic No Results Message when filtering -->
                            <tr id="noResultsRow" style="display: none;">
                                <td colspan="12" class="py-12 text-center text-slate-500 text-xs font-normal font-['Inter']">
                                    No parking logs match the selected filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Pagination Row -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-['Inter']">
                <div id="logsCount">Showing <?php echo $result->num_rows; ?> logs</div>
                <div class="inline-flex items-center gap-1.5">
                    <div class="size-7 flex items-center justify-center bg-white rounded-md outline outline-1 outline-offset-[-1px] outline-slate-200 text-slate-500 text-xs font-normal cursor-pointer hover:bg-slate-50 transition-colors">&lt;</div>
                    <div class="size-7 flex items-center justify-center bg-blue-600 rounded-md outline outline-1 outline-offset-[-1px] outline-blue-600 text-white text-xs font-semibold font-['Inter']">1</div>
                    <div class="size-7 flex items-center justify-center bg-white rounded-md outline outline-1 outline-offset-[-1px] outline-slate-200 text-slate-500 text-xs font-normal cursor-pointer hover:bg-slate-50 transition-colors">2</div>
                    <div class="size-7 flex items-center justify-center bg-white rounded-md outline outline-1 outline-offset-[-1px] outline-slate-200 text-slate-500 text-xs font-normal cursor-pointer hover:bg-slate-50 transition-colors">3</div>
                    <div class="size-7 flex items-center justify-center bg-white rounded-md outline outline-1 outline-offset-[-1px] outline-slate-200 text-slate-500 text-xs font-normal cursor-pointer hover:bg-slate-50 transition-colors">&gt;</div>
                </div>
            </div>
        </main>
    </div>

    <!-- Live Client-side Filter Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchLogs');
            const statusFilter = document.getElementById('statusFilter');
            const floorFilter = document.getElementById('floorFilter');
            const dateFilter = document.getElementById('dateFilter');
            const resetBtn = document.getElementById('resetFilters');
            const rows = document.querySelectorAll('.log-row');
            const logsCountEl = document.getElementById('logsCount');
            const noResultsRow = document.getElementById('noResultsRow');
            const totalLogs = rows.length;

            // Dynamically populate floor options from table rows
            const floorSet = new Set();
            rows.forEach(r => {
                const f = r.getAttribute('data-floor');
                if (f && f !== 'N/A' && f.trim() !== '') {
                    floorSet.add(f.trim());
                }
            });

            Array.from(floorSet).sort((a, b) => a - b).forEach(f => {
                const opt = document.createElement('option');
                opt.value = f;
                opt.textContent = `Floor: Floor ${f}`;
                floorFilter.appendChild(opt);
            });

            function filterRows() {
                const query = (searchInput.value || '').toLowerCase().trim();
                const selectedStatus = (statusFilter.value || '').toLowerCase().trim();
                const selectedFloor = (floorFilter.value || '').trim();
                const selectedDate = dateFilter.value;

                // Toggle reset button visibility
                if (query !== '' || selectedStatus !== '' || selectedFloor !== '' || selectedDate !== '') {
                    resetBtn.classList.remove('hidden');
                } else {
                    resetBtn.classList.add('hidden');
                }

                const now = new Date();
                const todayStr = now.toISOString().split('T')[0];

                let visibleCount = 0;

                rows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
                    const rowFloor = (row.getAttribute('data-floor') || '').trim();
                    const rowTimeIn = (row.getAttribute('data-timein') || '').trim();

                    // Search query match (any column text)
                    const matchesQuery = query === '' || rowText.includes(query);

                    // Status match
                    const matchesStatus = selectedStatus === '' || rowStatus === selectedStatus;

                    // Floor match
                    const matchesFloor = selectedFloor === '' || rowFloor === selectedFloor;

                    // Date match
                    let matchesDate = true;
                    if (selectedDate === 'today') {
                        matchesDate = rowTimeIn.startsWith(todayStr);
                    } else if (selectedDate === 'week') {
                        if (rowTimeIn) {
                            const rowDate = new Date(rowTimeIn.replace(' ', 'T'));
                            const diffDays = (now - rowDate) / (1000 * 60 * 60 * 24);
                            matchesDate = diffDays >= 0 && diffDays <= 7;
                        } else {
                            matchesDate = false;
                        }
                    } else if (selectedDate === 'month') {
                        if (rowTimeIn) {
                            const currentMonth = todayStr.substring(0, 7);
                            matchesDate = rowTimeIn.startsWith(currentMonth);
                        } else {
                            matchesDate = false;
                        }
                    }

                    if (matchesQuery && matchesStatus && matchesFloor && matchesDate) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (noResultsRow) {
                    noResultsRow.style.display = visibleCount === 0 && totalLogs > 0 ? '' : 'none';
                }

                if (logsCountEl) {
                    logsCountEl.textContent = `Showing ${visibleCount} of ${totalLogs} logs`;
                }
            }

            // Event listeners for instant live filtering
            searchInput.addEventListener('input', filterRows);
            statusFilter.addEventListener('change', filterRows);
            floorFilter.addEventListener('change', filterRows);
            dateFilter.addEventListener('change', filterRows);

            resetBtn.addEventListener('click', function () {
                searchInput.value = '';
                statusFilter.value = '';
                floorFilter.value = '';
                dateFilter.value = '';
                filterRows();
            });
        });
    </script>
</body>

</html>
