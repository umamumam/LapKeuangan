<header class="pc-header">
    <div class="header-wrapper">
        <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="dropdown pc-h-item d-inline-flex d-md-none">
                    <a class="pc-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-search"></i>
                    </a>
                    <div class="dropdown-menu pc-h-dropdown drp-search">
                        <form class="px-3" onsubmit="return false;">
                            <div class="form-group mb-0 d-flex align-items-center">
                                <i data-feather="search"></i>
                                <input type="search" class="form-control border-0 shadow-none global-search-input"
                                    placeholder="Search here. . .">
                            </div>
                        </form>
                    </div>
                </li>
                <li class="pc-h-item d-none d-md-inline-flex">
                    <form class="header-search" onsubmit="return false;">
                        <i data-feather="search" class="icon-search"></i>
                        <input type="search" class="form-control global-search-input" placeholder="Search here. . .">
                    </form>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-mail"></i>
                    </a>
                    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h5 class="m-0">Message</h5>
                            <a href="#!" class="pc-head-link bg-transparent"><i class="ti ti-x text-danger"></i></a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative"
                            style="max-height: calc(100vh - 215px)">
                            <div class="list-group list-group-flush w-100">
                                <a class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('mantis/assets/images/user/avatar-2.jpg') }}"
                                                alt="user-image" class="user-avtar">
                                        </div>
                                        <div class="flex-grow-1 ms-1">
                                            <span class="float-end text-muted">3:00 AM</span>
                                            <p class="text-body mb-1">It's <b>Cristina Danny's</b> birthday today.
                                            </p>
                                            <span class="text-muted">2 min ago</span>
                                        </div>
                                    </div>
                                </a>
                                <a class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('mantis/assets/images/user/avatar-1.jpg') }}"
                                                alt="user-image" class="user-avtar">
                                        </div>
                                        <div class="flex-grow-1 ms-1">
                                            <span class="float-end text-muted">6:00 PM</span>
                                            <p class="text-body mb-1"><b>Aida Burg</b> commented on your post.</p>
                                            <span class="text-muted">5 August</span>
                                        </div>
                                    </div>
                                </a>
                                <a class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('mantis/assets/images/user/avatar-3.jpg') }}"
                                                alt="user-image" class="user-avtar">
                                        </div>
                                        <div class="flex-grow-1 ms-1">
                                            <span class="float-end text-muted">2:45 PM</span>
                                            <p class="text-body mb-1"><b>There was a failure in your setup.</b></p>
                                            <span class="text-muted">7 hours ago</span>
                                        </div>
                                    </div>
                                </a>
                                <a class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('mantis/assets/images/user/avatar-4.jpg') }}"
                                                alt="user-image" class="user-avtar">
                                        </div>
                                        <div class="flex-grow-1 ms-1">
                                            <span class="float-end text-muted">9:10 PM</span>
                                            <p class="text-body mb-1"><b>Cristina Danny</b> invited you to join a
                                                <b>Meeting</b>.
                                            </p>
                                            <span class="text-muted">Daily scrum meeting time</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="text-center py-2">
                            <a href="#!" class="link-primary">View all</a>
                        </div>
                    </div>
                </li>
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <img src="{{ asset('mantis/assets/images/user/avatar-2.jpg') }}" alt="user-image"
                            class="user-avtar">
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex mb-1">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('mantis/assets/images/user/avatar-2.jpg') }}" alt="user-image"
                                        class="user-avtar wid-35">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                    <span>{{ Auth::user()->email }}</span>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="pc-head-link bg-transparent border-0">
                                        <i class="ti ti-power text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <ul class="nav drp-tabs nav-fill nav-tabs" id="mydrpTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="drp-t1" data-bs-toggle="tab"
                                    data-bs-target="#drp-tab-1" type="button" role="tab" aria-controls="drp-tab-1"
                                    aria-selected="true"><i class="ti ti-user"></i> Profile</button>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link" id="drp-t2" data-bs-toggle="tab" data-bs-target="#drp-tab-2"
                                    type="button" role="tab" aria-controls="drp-tab-2" aria-selected="false"><i
                                        class="ti ti-settings"></i> Setting</button>
                            </li> --}}
                        </ul>
                        <div class="tab-content" id="mysrpTabContent">
                            <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel"
                                aria-labelledby="drp-t1" tabindex="0">
                                <a href="/profile" class="dropdown-item">
                                    <i class="ti ti-edit-circle"></i>
                                    <span>Edit Profile</span>
                                </a>
                                {{-- <a href="#!" class="dropdown-item">
                                    <i class="ti ti-user"></i>
                                    <span>View Profile</span>
                                </a>
                                <a href="#!" class="dropdown-item">
                                    <i class="ti ti-clipboard-list"></i>
                                    <span>Social Profile</span>
                                </a>
                                <a href="#!" class="dropdown-item">
                                    <i class="ti ti-wallet"></i>
                                    <span>Billing</span>
                                </a> --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item w-100 text-start">
                                        <i class="ti ti-power"></i>
                                        <span style="color: red">Logout</span>
                                    </button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="drp-tab-2" role="tabpanel" aria-labelledby="drp-t2"
                                tabindex="0">
                                <a href="#!" class="dropdown-item">
                                    <i class="ti ti-help"></i>
                                    <span>Support</span>
                                </a>
                                <a href="#!" class="dropdown-item">
                                    <i class="ti ti-user"></i>
                                    <span>Account Settings</span>
                                </a>
                                <a href="#!" class="dropdown-item">
                                    <i class="ti ti-lock"></i>
                                    <span>Privacy Center</span>
                                </a>
                                <a href="#!" class="dropdown-item">
                                    <i class="ti ti-messages"></i>
                                    <span>Feedback</span>
                                </a>
                                <a href="#!" class="dropdown-item">
                                    <i class="ti ti-list"></i>
                                    <span>History</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInputs = document.querySelectorAll(".global-search-input");

    searchInputs.forEach(input => {
        input.addEventListener("input", function () {
            const query = this.value.toLowerCase().trim();

            // Synchronize all search inputs (mobile & desktop)
            searchInputs.forEach(otherInput => {
                if (otherInput !== input) {
                    otherInput.value = this.value;
                }
            });

            // 1. Search in DataTables if any
            if (window.jQuery && $.fn.DataTable) {
                try {
                    const tables = $.fn.dataTable.tables({ visible: true, api: true });
                    if (tables && typeof tables.search === "function") {
                        tables.search(query).draw();
                    }
                } catch (e) {
                    console.log("DataTable search error:", e);
                }
            }

            // 2. Search in standard tables (non-DataTables)
            const standardTables = document.querySelectorAll("table:not(.dataTable)");
            standardTables.forEach(table => {
                const rows = table.querySelectorAll("tbody tr");
                rows.forEach(row => {
                    // Skip helper/empty rows
                    if (row.classList.contains("no-results-row") || (row.cells.length === 1 && row.textContent.includes("Tidak ditemukan"))) {
                        return;
                    }
                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            });

            // 3. Search in hover-cards (like partners or other custom card grids)
            const hoverCards = document.querySelectorAll(".hover-card");
            if (hoverCards.length > 0) {
                hoverCards.forEach(card => {
                    const col = card.closest("[class*='col-']");
                    const targetElement = col || card;
                    const text = card.textContent.toLowerCase();
                    if (text.includes(query)) {
                        targetElement.style.setProperty("display", "", "important");
                    } else {
                        targetElement.style.setProperty("display", "none", "important");
                    }
                });
            }

            // 4. Search in list-group items
            const listItems = document.querySelectorAll(".list-group-item");
            if (listItems.length > 0) {
                listItems.forEach(item => {
                    if (item.closest(".pc-header") || item.closest(".dropdown-menu")) {
                        return;
                    }
                    const text = item.textContent.toLowerCase();
                    if (text.includes(query)) {
                        item.style.setProperty("display", "", "important");
                    } else {
                        item.style.setProperty("display", "none", "important");
                    }
                });
            }
        });
    });
});
</script>