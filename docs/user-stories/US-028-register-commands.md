# US-028: Register Commands in Service Provider

## Story
As a package developer, I want the service provider to register all artisan commands so that users can run `ud:import`, `ud:list`, and `ud:stats` after installing the package.

## Prerequisites
- US-003 (service provider shell exists)
- US-019 (ImportCommand exists)
- US-024 (ListCommand exists)
- US-025 (StatsCommand exists)

## Stack
- Laravel Service Provider `commands()`
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/UniversityDirectoryServiceProvider.php`
- [ ] In the `boot()` method, check if the app is running in console: `if ($this->app->runningInConsole())`
- [ ] Inside the check, register commands: `$this->commands([ImportCommand::class, ListCommand::class, StatsCommand::class])`
- [ ] Import all three command classes at the top
- [ ] Remove the TODO comment for commands

## Implementation Prompt
> Modify `src/UniversityDirectoryServiceProvider.php`. Import the three command classes: `Tisuchi\UniversityDirectory\Console\ImportCommand`, `Tisuchi\UniversityDirectory\Console\ListCommand`, `Tisuchi\UniversityDirectory\Console\StatsCommand`. In boot(), add `if ($this->app->runningInConsole()) { $this->commands([ImportCommand::class, ListCommand::class, StatsCommand::class]); }`. Remove any TODO comment about commands.

## Acceptance Criteria
- [ ] All three commands are registered: ImportCommand, ListCommand, StatsCommand
- [ ] Commands are only registered when running in console (`runningInConsole()` check)
- [ ] All command class imports are present
- [ ] `php artisan list` shows `ud:import`, `ud:list`, `ud:stats` after package installation
- [ ] No TODO comments remain for command registration

## File(s) to Create/Modify
- `src/UniversityDirectoryServiceProvider.php` (modify)
