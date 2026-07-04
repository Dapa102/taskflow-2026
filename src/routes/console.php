<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reminders:deadline')->hourly();
Schedule::command('tasks:check-pm-escalation')->everySixHours();
