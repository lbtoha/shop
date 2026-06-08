<x-admin-app-layout>
    @push('styles')
        @vite(['resources/admin/custom/css/animate.min.css'])
    @endpush
    <main class="w-full bg-neutral-20 dark:bg-neutral-903 text-neutral-700 min-h-screen dark:text-neutral-20 pt-[60px] md:pt-[66px] duration-300">
        <div class="p-3 md:p-4 xxl:p-6">
            <div class="grid grid-cols-2 gap-4 xxl:gap-6">
                <!-- Users Statistics -->
                <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 xxl:grid-cols-4 gap-4 xxl:gap-6">
                    <div class="white-box">
                        <div class="flex justify-between items-center gap-3 mb-1 xxl:mb-5">
                            <div>
                                <p class="s-text mb-1">Total Users</p>
                                <p class="l-text font-semibold">25.5K</p>
                            </div>
                            <div class="size-11 rounded-full bg-primary f-center">
                                <i class="ph ph-users text-xl text-white"></i>
                            </div>
                        </div>
                        <a href="#" class="text-blue font-medium text-xs underline">View all</a>
                    </div>
                    <div class="white-box">
                        <div class="flex justify-between items-center gap-3 mb-1 xxl:mb-5">
                            <div>
                                <p class="s-text mb-1">Active Users</p>
                                <p class="l-text font-semibold">5.5K</p>
                            </div>
                            <div class="size-11 rounded-full bg-secondary f-center">
                                <i class="ph ph-user text-xl text-white"></i>
                            </div>
                        </div>
                        <a href="#" class="text-blue font-medium text-xs underline">View all</a>
                    </div>
                    <div class="white-box">
                        <div class="flex justify-between items-center gap-3 mb-1 xxl:mb-5">
                            <div>
                                <p class="s-text mb-1">Email Unverified Users</p>
                                <p class="l-text font-semibold">7.5K</p>
                            </div>
                            <div class="size-11 rounded-full bg-success f-center">
                                <i class="ph ph-envelope-simple text-xl text-white"></i>
                            </div>
                        </div>
                        <a href="#" class="text-blue font-medium text-xs underline">View all</a>
                    </div>
                    <div class="white-box">
                        <div class="flex justify-between items-center gap-3 mb-1 xxl:mb-5">
                            <div>
                                <p class="s-text mb-1">Mobile Verified Users</p>
                                <p class="l-text font-semibold">5.7K</p>
                            </div>
                            <div class="size-11 rounded-full bg-yellow-700 f-center">
                                <i class="ph ph-device-mobile text-xl text-white"></i>
                            </div>
                        </div>
                        <a href="#" class="text-blue font-medium text-xs underline">View all</a>
                    </div>
                </div>
                <!-- Deposits -->
                <div class="col-span-2 lg:col-span-1 white-box">
                    <div class="flex justify-between items-center gap-4 flex-wrap mb-4 xl:mb-6">
                        <p class="m-text font-medium">Deposits</p>
                        <div class="flex items-center gap-3">
                            <span class="text-sm max-md:hidden">Sort By : </span>
                            <select name="sort" class="nc-select">
                                <option value="day">Last 15 Days</option>
                                <option value="week">Last 1 Month</option>
                                <option value="year">Last 6 Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 3xl:grid-cols-3 gap-3">
                        <div class="flex justify-between items-center n10-box !p-2">
                            <div class="flex items-center gap-4">
                                <div class="size-9 rounded-lg bg-blue text-neutral-0 f-center">
                                    <i class="ph ph-hand-coins text-xl"></i>
                                </div>
                                <div>
                                    <p class="mb-1 m-text font-medium">$25,526.55</p>
                                    <p class="s-text">Total</p>
                                </div>
                            </div>
                            <a href="deposits.html"
                                class="size-6 rounded-md border border-blue text-blue hover:bg-blue duration-300 hover:text-neutral-0 f-center">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="flex justify-between items-center n10-box !p-2">
                            <div class="flex items-center gap-4">
                                <div class="size-9 rounded-lg bg-success text-neutral-0 f-center">
                                    <i class="ph ph-spinner-gap text-xl"></i>
                                </div>
                                <div>
                                    <p class="mb-1 m-text font-medium">256</p>
                                    <p class="s-text">Pending</p>
                                </div>
                            </div>
                            <a href="deposits.html"
                                class="size-6 rounded-md border border-blue text-blue hover:bg-blue duration-300 hover:text-neutral-0 f-center">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="flex justify-between items-center n10-box !p-2">
                            <div class="flex items-center gap-4">
                                <div class="size-9 rounded-lg bg-error text-neutral-0 f-center">
                                    <i class="ph ph-prohibit text-xl"></i>
                                </div>
                                <div>
                                    <p class="mb-1 m-text font-medium">45</p>
                                    <p class="s-text">Rejected</p>
                                </div>
                            </div>
                            <a href="deposits.html"
                                class="size-6 rounded-md border border-blue text-blue hover:bg-blue duration-300 hover:text-neutral-0 f-center">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div x-ref="depositRef"></div>
                </div>
                <!-- Withdrawals -->
                <div class="col-span-2 lg:col-span-1 white-box">
                    <div class="flex justify-between items-center gap-4 flex-wrap mb-4 xl:mb-6">
                        <p class="m-text font-medium">Withdrawals</p>
                        <div class="flex items-center gap-3">
                            <span class="text-sm max-md:hidden">Sort By : </span>
                            <select name="sort" class="nc-select">
                                <option value="day">Last 15 Days</option>
                                <option value="week">Last 1 Month</option>
                                <option value="year">Last 6 Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 3xl:grid-cols-3 gap-3">
                        <div class="flex justify-between items-center n10-box !p-2">
                            <div class="flex items-center gap-4">
                                <div class="size-9 rounded-lg bg-blue text-neutral-0 f-center">
                                    <i class="ph ph-hand-coins text-xl"></i>
                                </div>
                                <div>
                                    <p class="mb-1 m-text font-medium">$25,526.55</p>
                                    <p class="s-text">Total</p>
                                </div>
                            </div>
                            <a href="deposits.html"
                                class="size-6 rounded-md border border-blue text-blue hover:bg-blue duration-300 hover:text-neutral-0 f-center">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="flex justify-between items-center n10-box !p-2">
                            <div class="flex items-center gap-4">
                                <div class="size-9 rounded-lg bg-success text-neutral-0 f-center">
                                    <i class="ph ph-spinner-gap text-xl"></i>
                                </div>
                                <div>
                                    <p class="mb-1 m-text font-medium">256</p>
                                    <p class="s-text">Pending</p>
                                </div>
                            </div>
                            <a href="deposits.html"
                                class="size-6 rounded-md border border-blue text-blue hover:bg-blue duration-300 hover:text-neutral-0 f-center">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="flex justify-between items-center n10-box !p-2">
                            <div class="flex items-center gap-4">
                                <div class="size-9 rounded-lg bg-error text-neutral-0 f-center">
                                    <i class="ph ph-prohibit text-xl"></i>
                                </div>
                                <div>
                                    <p class="mb-1 m-text font-medium">45</p>
                                    <p class="s-text">Rejected</p>
                                </div>
                            </div>
                            <a href="deposits.html"
                                class="size-6 rounded-md border border-blue text-blue hover:bg-blue duration-300 hover:text-neutral-0 f-center">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div x-ref="withdrawRef"></div>
                </div>
                <!-- Invest history -->
                <div class="col-span-2 lg:col-span-1 white-box">
                    <div
                        class="flex justify-between items-center gap-2 pb-4 xl:pb-6 mb-4 xl:mb-6 border-b border-neutral-30 dark:border-neutral-500">
                        <p class="m-text font-medium">Invest History</p>
                        <div class="flex items-center gap-3">
                            <span class="text-sm max-md:hidden">Sort By : </span>
                            <select name="sort" class="nc-select">
                                <option value="day">Last 15 Days</option>
                                <option value="week">Last 1 Month</option>
                                <option value="year">Last 6 Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-full bg-blue text-neutral-0 f-center">
                                <i class="ph ph-chart-pie-slice text-xl"></i>
                            </div>
                            <div>
                                <p class="s-text mb-1">Total Investments</p>
                                <p class="m-text font-medium">$525,526.55</p>
                            </div>
                        </div>
                        <a href="#" class="size-6 rounded-md text-white bg-blue f-center"><i
                                class="ph ph-arrow-right"></i></a>
                    </div>
                    <div class="flex justify-between border border-neutral-30 dark:border-neutral-500 rounded-md">
                        <button
                            class="py-2 flex-1 bg-primary/5 border-r border-neutral-30 dark:border-neutral-500">1D</button>
                        <button class="py-2 flex-1 border-r border-neutral-30 dark:border-neutral-500">1W</button>
                        <button class="py-2 flex-1 border-r border-neutral-30 dark:border-neutral-500">1M</button>
                        <button class="py-2 flex-1 border-r border-neutral-30 dark:border-neutral-500">6M</button>
                        <button class="py-2 flex-1">1Y</button>
                    </div>
                    <div x-ref="investChartRef"></div>
                </div>
                <!-- invest info -->
                <div class="col-span-2 lg:col-span-1 grid grid-cols-1 md:grid-cols-2 gap-4 xxl:gap-6 grid-rows-2">
                    <div class="white-box">
                        <div class="flex justify-between items-center">
                            <p class="text-sm">Total Investments</p>
                            <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                        </div>
                        <div x-ref="totalInvestRef"></div>
                        <div class="flex justify-between items-center">
                            <p class="m-text font-medium">$54,552.52</p>
                            <span class="flex text-secondary text-xs gap-1 items-center"><i
                                    class="ph ph-arrow-up"></i> 75.7%</span>
                        </div>
                    </div>
                    <div class="white-box">
                        <div class="flex justify-between items-center">
                            <p class="text-sm">Total Interests</p>
                            <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                        </div>
                        <div x-ref="totalInterestRef"></div>
                        <div class="flex justify-between items-center">
                            <p class="m-text font-medium">$552.56</p>
                            <span class="flex text-success text-xs gap-1 items-center"><i
                                    class="ph ph-arrows-down-up"></i> 75.7%</span>
                        </div>
                    </div>
                    <div class="white-box">
                        <div class="flex justify-between items-center">
                            <p class="text-sm">Live Investments</p>
                            <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                        </div>
                        <div x-ref="liveInvestRef"></div>
                        <div class="flex justify-between items-center mt-4">
                            <p class="m-text font-medium">$552.56</p>
                            <span class="flex text-success text-xs gap-1 items-center"><i
                                    class="ph ph-arrows-down-up"></i> 75.7%</span>
                        </div>
                    </div>
                    <div class="white-box">
                        <div class="flex justify-between items-center">
                            <p class="text-sm">Completed Interests</p>
                            <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                        </div>
                        <div x-ref="completeInvestRef"></div>
                        <div class="flex justify-between items-center">
                            <p class="m-text font-medium">$552.56</p>
                            <span class="flex text-success text-xs gap-1 items-center"><i
                                    class="ph ph-arrows-down-up"></i> 75.7%</span>
                        </div>
                    </div>
                </div>
                <!-- Projects report -->
                <div class="col-span-2 white-box">
                    <div class="flex justify-between items-center gap-4 flex-wrap mb-4 xl:mb-6">
                        <p class="m-text font-medium">Projects Report</p>
                        <div class="flex items-center gap-3">
                            <span class="text-sm max-md:hidden">Sort By : </span>
                            <select name="sort" class="nc-select">
                                <option value="day">Last 15 Days</option>
                                <option value="week">Last 1 Month</option>
                                <option value="year">Last 6 Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xxl:grid-cols-4 gap-4 xxl:gap-6 mb-6">
                        <div class="white-box !bg-primary/5 !p-4 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="f-center white-box text-primary !p-3 text-xl">
                                    <i class="ph ph-file-text"></i>
                                </div>
                                <div>
                                    <p class="m-text font-medium">75.5k</p>
                                    <span class="text-xs text-neutral-400 dark:text-neutral-50">Total Projects</span>
                                </div>
                            </div>
                            <a href="#"
                                class="size-6 shadow-xl rounded-md bg-neutral-0 dark:bg-neutral-904 f-center text-primary text-lg">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="white-box !bg-primary/5 !p-4 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="f-center white-box text-primary !p-3 text-xl">
                                    <i class="ph ph-calendar-check"></i>
                                </div>
                                <div>
                                    <p class="m-text font-medium">75.5k</p>
                                    <span class="text-xs text-neutral-400 dark:text-neutral-50">Active Projects</span>
                                </div>
                            </div>
                            <a href="#"
                                class="size-6 shadow-xl rounded-md bg-neutral-0 dark:bg-neutral-904 f-center text-primary text-lg">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="white-box !bg-primary/5 !p-4 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="f-center white-box text-primary !p-3 text-xl">
                                    <i class="ph ph-calendar-x"></i>
                                </div>
                                <div>
                                    <p class="m-text font-medium">75.5k</p>
                                    <span class="text-xs text-neutral-400 dark:text-neutral-50">Closed Projects</span>
                                </div>
                            </div>
                            <a href="#"
                                class="size-6 shadow-xl rounded-md bg-neutral-0 dark:bg-neutral-904 f-center text-primary text-lg">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="white-box !bg-primary/5 !p-4 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="f-center white-box text-primary !p-3 text-xl">
                                    <i class="ph ph-calendar-plus"></i>
                                </div>
                                <div>
                                    <p class="m-text font-medium">75.5k</p>
                                    <span class="text-xs text-neutral-400 dark:text-neutral-50">Upcoming
                                        Projects</span>
                                </div>
                            </div>
                            <a href="#"
                                class="size-6 shadow-xl rounded-md bg-neutral-0 dark:bg-neutral-904 f-center text-primary text-lg">
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div x-ref="reportChartRef"></div>
                </div>
                <!-- Login info -->
                <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 xxl:grid-cols-3 gap-4 xxl:gap-6">
                    <div class="white-box">
                        <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                            <p class="m-text font-medium">Login By OS (Last 30 days)</p>
                            <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                        </div>
                        <div class="flex justify-center">
                            <div x-ref="osChartRef"></div>
                        </div>
                        <div
                            class="py-5 border-y border-neutral-30 dark:border-neutral-500 flex justify-center divide-x divide-neutral-30 dark:divide-neutral-500">
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-primary mb-4">Mac</li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-success">Linux</li>
                            </div>
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-secondary mb-4">Windows
                                </li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-error">Android</li>
                            </div>
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-info mb-4">Ios</li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-warning">ChromeOS</li>
                            </div>
                        </div>
                    </div>
                    <div class="white-box">
                        <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                            <p class="m-text font-medium">Login By Browser (Last 30 days)</p>
                            <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                        </div>
                        <div class="flex justify-center">
                            <div x-ref="browserRef"></div>
                        </div>
                        <div
                            class="py-5 border-y border-neutral-30 dark:border-neutral-500 flex justify-center divide-x divide-neutral-30 dark:divide-neutral-500">
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-primary mb-4">Mac</li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-success">Linux</li>
                            </div>
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-secondary mb-4">Windows
                                </li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-error">Android</li>
                            </div>
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-info mb-4">Ios</li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-warning">ChromeOS</li>
                            </div>
                        </div>
                    </div>
                    <div class="white-box">
                        <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                            <p class="m-text font-medium">Login By Country (Last 30 days)</p>
                            <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                        </div>
                        <div class="flex justify-center">
                            <div x-ref="countryRef"></div>
                        </div>
                        <div
                            class="py-5 border-y border-neutral-30 dark:border-neutral-500 flex justify-center divide-x divide-neutral-30 dark:divide-neutral-500">
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-primary mb-4">Mac</li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-success">Linux</li>
                            </div>
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-secondary mb-4">Windows
                                </li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-error">Android</li>
                            </div>
                            <div class="px-2 3xl:px-4">
                                <li class="md:list-disc max-md:list-none text-sm marker:text-info mb-4">Ios</li>
                                <li class="md:list-disc max-md:list-none text-sm marker:text-warning">ChromeOS</li>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Recent Transactions -->
                <div class="col-span-2 xxl:col-span-1 white-box">
                    <div class="flex justify-between items-center gap-4 flex-wrap mb-4 xl:mb-6">
                        <p class="m-text font-medium">Recent Transactions</p>
                        <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full responsive">
                            <thead>
                                <tr class="bg-primary/5 border-b border-neutral-30 dark:border-neutral-500">
                                    <th class="py-4 px-5">
                                        <div class="flex items-center gap-1 text-xs font-medium">
                                            User
                                            <div class="flex flex-col items-center">
                                                <span class="f-center -mb-1"><i
                                                        class="ph ph-caret-up text-xs"></i></span>
                                                <span class="f-center -mt-1"><i
                                                        class="ph ph-caret-down text-xs"></i></span>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="py-4 px-5">
                                        <div class="flex items-center gap-1 text-xs font-medium">
                                            Gateway | Date
                                            <div class="flex flex-col items-center">
                                                <span class="f-center -mb-1"><i
                                                        class="ph ph-caret-up text-xs"></i></span>
                                                <span class="f-center -mt-1"><i
                                                        class="ph ph-caret-down text-xs"></i></span>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="py-4 px-5">
                                        <div class="flex items-center gap-1 text-xs font-medium">
                                            Status
                                            <div class="flex flex-col items-center">
                                                <span class="f-center -mb-1"><i
                                                        class="ph ph-caret-up text-xs"></i></span>
                                                <span class="f-center -mt-1"><i
                                                        class="ph ph-caret-down text-xs"></i></span>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Gateway | Date" class="px-3 md:px-5 py-4">
                                        <div>
                                            <p class="s-text mb-1 font-medium">Bank Transfer</p>
                                            <span class="text-xs">2024-10-09 09:12 AM</span>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-warning/50 bg-warning/10 text-warning">Pending</span>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Gateway | Date" class="px-3 md:px-5 py-4">
                                        <div>
                                            <p class="s-text mb-1 font-medium">Bank Transfer</p>
                                            <span class="text-xs">2024-10-09 09:12 AM</span>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-success/50 bg-success/10 text-success">Approved</span>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Gateway | Date" class="px-3 md:px-5 py-4">
                                        <div>
                                            <p class="s-text mb-1 font-medium">Bank Transfer</p>
                                            <span class="text-xs">2024-10-09 09:12 AM</span>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-error/50 bg-error/10 text-error">Rejected</span>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Gateway | Date" class="px-3 md:px-5 py-4">
                                        <div>
                                            <p class="s-text mb-1 font-medium">Bank Transfer</p>
                                            <span class="text-xs">2024-10-09 09:12 AM</span>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-warning/50 bg-warning/10 text-warning">Pending</span>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Gateway | Date" class="px-3 md:px-5 py-4">
                                        <div>
                                            <p class="s-text mb-1 font-medium">Bank Transfer</p>
                                            <span class="text-xs">2024-10-09 09:12 AM</span>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-success/50 bg-success/10 text-success">Approved</span>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Gateway | Date" class="px-3 md:px-5 py-4">
                                        <div>
                                            <p class="s-text mb-1 font-medium">Bank Transfer</p>
                                            <span class="text-xs">2024-10-09 09:12 AM</span>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-error/50 bg-error/10 text-error">Rejected</span>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Gateway | Date" class="px-3 md:px-5 py-4">
                                        <div>
                                            <p class="s-text mb-1 font-medium">Bank Transfer</p>
                                            <span class="text-xs">2024-10-09 09:12 AM</span>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-warning/50 bg-warning/10 text-warning">Pending</span>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Gateway | Date" class="px-3 md:px-5 py-4">
                                        <div>
                                            <p class="s-text mb-1 font-medium">Bank Transfer</p>
                                            <span class="text-xs">2024-10-09 09:12 AM</span>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-success/50 bg-success/10 text-success">Approved</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Recent Users -->
                <div class="col-span-2 xxl:col-span-1 white-box">
                    <div class="flex justify-between items-center gap-4 flex-wrap mb-4 xl:mb-6">
                        <p class="m-text font-medium">Recent Users</p>
                        <a href="#" class="text-xs text-primary underline font-medium">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full responsive">
                            <thead>
                                <tr class="bg-primary/5 border-b border-neutral-30 dark:border-neutral-500">
                                    <th class="py-4 px-5">
                                        <div class="flex items-center gap-1 text-xs font-medium">
                                            User
                                            <div class="flex flex-col items-center">
                                                <span class="f-center -mb-1"><i
                                                        class="ph ph-caret-up text-xs"></i></span>
                                                <span class="f-center -mt-1"><i
                                                        class="ph ph-caret-down text-xs"></i></span>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="py-4 px-5">
                                        <div class="flex items-center gap-1 text-xs font-medium">
                                            Status
                                            <div class="flex flex-col items-center">
                                                <span class="f-center -mb-1"><i
                                                        class="ph ph-caret-up text-xs"></i></span>
                                                <span class="f-center -mt-1"><i
                                                        class="ph ph-caret-down text-xs"></i></span>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="py-4 px-5">
                                        <div class="flex items-center gap-1 text-xs font-medium">
                                            Action
                                            <div class="flex flex-col items-center">
                                                <span class="f-center -mb-1"><i
                                                        class="ph ph-caret-up text-xs"></i></span>
                                                <span class="f-center -mt-1"><i
                                                        class="ph ph-caret-down text-xs"></i></span>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-warning/50 bg-warning/10 text-warning">Pending</span>
                                    </td>
                                    <td data-th="Action" class="px-3 md:px-5 py-4">
                                        <button class="action-btn primary">
                                            <span class="f-center">
                                                <i class="ph ph-eye"></i>
                                            </span>
                                            Details
                                        </button>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-primary/50 bg-primary/10 text-primary">Verified</span>
                                    </td>
                                    <td data-th="Action" class="px-3 md:px-5 py-4">
                                        <button class="action-btn primary">
                                            <span class="f-center">
                                                <i class="ph ph-eye"></i>
                                            </span>
                                            Details
                                        </button>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-error/50 bg-error/10 text-error">Unverified</span>
                                    </td>
                                    <td data-th="Action" class="px-3 md:px-5 py-4">
                                        <button class="action-btn primary">
                                            <span class="f-center">
                                                <i class="ph ph-eye"></i>
                                            </span>
                                            Details
                                        </button>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-warning/50 bg-warning/10 text-warning">Pending</span>
                                    </td>
                                    <td data-th="Action" class="px-3 md:px-5 py-4">
                                        <button class="action-btn primary">
                                            <span class="f-center">
                                                <i class="ph ph-eye"></i>
                                            </span>
                                            Details
                                        </button>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-primary/50 bg-primary/10 text-primary">Verified</span>
                                    </td>
                                    <td data-th="Action" class="px-3 md:px-5 py-4">
                                        <button class="action-btn primary">
                                            <span class="f-center">
                                                <i class="ph ph-eye"></i>
                                            </span>
                                            Details
                                        </button>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-error/50 bg-error/10 text-error">Unverified</span>
                                    </td>
                                    <td data-th="Action" class="px-3 md:px-5 py-4">
                                        <button class="action-btn primary">
                                            <span class="f-center">
                                                <i class="ph ph-eye"></i>
                                            </span>
                                            Details
                                        </button>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-primary/50 bg-primary/10 text-primary">Verified</span>
                                    </td>
                                    <td data-th="Action" class="px-3 md:px-5 py-4">
                                        <button class="action-btn primary">
                                            <span class="f-center">
                                                <i class="ph ph-eye"></i>
                                            </span>
                                            Details
                                        </button>
                                    </td>
                                </tr>
                                <tr
                                    class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                                    <td data-th="User" class="px-3 md:px-5 py-4">
                                        <div class="flex max-lg:justify-end items-center gap-3">
                                            <img src="./assets/images/avatar-1.png" width="32" height="32"
                                                class="rounded-full max-md:hidden" alt="" />
                                            <div>
                                                <p class="s-text mb-1 font-medium">Kristin Watson</p>
                                                <span class="text-xs">demo@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Status" class="px-3 md:px-5 py-4">
                                        <span
                                            class="px-5 inline-flex py-2 text-xs rounded-full border border-error/50 bg-error/10 text-error">Unverified</span>
                                    </td>
                                    <td data-th="Action" class="px-3 md:px-5 py-4">
                                        <button class="action-btn primary">
                                            <span class="f-center">
                                                <i class="ph ph-eye"></i>
                                            </span>
                                            Details
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-admin-app-layout>
