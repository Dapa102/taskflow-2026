<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reminders:deadline')->hourly();
