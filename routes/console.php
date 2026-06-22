<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('run-task-schedule')->everyMinute();

// Auto-delete AI virtual try-on result images after their TTL (privacy).
Schedule::command('tryon:prune')->hourly();
